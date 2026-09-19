<?php

namespace App\Services;

use App\Models\ExpHistory;
use App\Models\MemberExp;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

/**
 * 회원 등급 / 경험치 서비스
 *
 * 경험치를 주는 입구는 grant() 하나뿐이다. 컨트롤러마다 조건문을 흩뿌리지 않는다.
 * 중복 지급 방지는 mq_exp_history 의 유니크 키가 담당하므로, 같은 (회원, 사유, 대상)
 * 조합으로 몇 번을 불러도 한 번만 들어간다.
 *
 * 지급 실패가 본 기능을 막아서는 안 된다. 호출부는 grant() 를 트랜잭션 밖에서
 * 부르고, 이 클래스는 예외를 밖으로 던지지 않는다 (로그만 남긴다).
 */
class ExpService
{
    /** 연속 접속일을 거슬러 올라갈 최대 일수 (무한 루프 / 과도한 조회 방지) */
    const STREAK_LOOKBACK_DAYS = 400;

    /** 등급이 올랐을 때 화면에 한 번 알려주기 위한 세션 키 */
    const GRADE_UP_SESSION_KEY = 'exp_grade_up';

    /**
     * 경험치를 지급한다.
     *
     * @param string $userId
     * @param string $actionCode config/exp.php 의 actions 키
     * @param string|int $refKey 중복 지급 방지 키 (대상 idx, 날짜 등)
     * @return int 실제로 지급된 경험치 (이미 받았거나 상한이면 0)
     */
    public function grant($userId, $actionCode, $refKey)
    {
        try {
            if (!$userId) {
                return 0;
            }

            $action = self::action($actionCode);

            if (!$action) {
                Log::warning('[exp] 알 수 없는 지급 코드', ['action' => $actionCode]);
                return 0;
            }

            if ($this->reachedDailyLimit($userId, $actionCode, $action)) {
                return 0;
            }

            $before = $this->totalExp($userId);

            try {
                ExpHistory::create([
                    'mq_user_id'     => $userId,
                    'mq_action_code' => $actionCode,
                    'mq_ref_key'     => (string) $refKey,
                    'mq_exp'         => (int) $action['exp'],
                    'mq_reg_date'    => Carbon::now(),
                ]);
            } catch (QueryException $e) {
                // 유니크 충돌 = 이미 준 것. 정상 흐름이므로 조용히 넘어간다.
                if ($this->isDuplicateKey($e)) {
                    return 0;
                }

                throw $e;
            }

            $this->syncMember($userId, $before);

            return (int) $action['exp'];

        } catch (\Throwable $e) {
            Log::error('[exp] 지급 실패', [
                'user' => $userId,
                'action' => $actionCode,
                'ref' => $refKey,
                'message' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * 오늘 첫 접속을 기록하고 연속 접속 보너스를 처리한다.
     *
     * @param string $userId
     * @return void
     */
    public function recordVisit($userId)
    {
        try {
            if (!$userId) {
                return;
            }

            $this->grant($userId, 'visit.daily', Carbon::now()->toDateString());

            $streak = $this->calculateVisitStreak($userId);

            if ($streak <= 0) {
                return;
            }

            // 7일 / 30일 단위로 딱 떨어지는 날에만 보너스를 준다.
            // ref_key 에 회차를 넣어 같은 구간에서 두 번 받지 않도록 한다.
            if ($streak % 7 === 0) {
                $this->grant($userId, 'visit.streak7', 'w' . intdiv($streak, 7));
            }

            if ($streak % 30 === 0) {
                $this->grant($userId, 'visit.streak30', 'm' . intdiv($streak, 30));
            }

            $this->touchStreak($userId, $streak);

        } catch (\Throwable $e) {
            Log::error('[exp] 접속 기록 실패', ['user' => $userId, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 화면에 뿌릴 등급 요약
     *
     * @param string|null $userId
     * @return array|null 비로그인이면 null
     */
    public function summary($userId)
    {
        if (!$userId) {
            return null;
        }

        $row = MemberExp::find($userId);
        $total = $row ? (int) $row->mq_total_exp : 0;

        $grade = $this->gradeFor($total);
        $next = $this->nextGradeFor($total);

        $gained = $total - $grade['exp'];
        $needed = $next ? $next['exp'] - $grade['exp'] : 0;

        return [
            'total'      => $total,
            'grade'      => $grade,
            'next'       => $next,
            'remain'     => $next ? max(0, $next['exp'] - $total) : 0,
            // 최고 등급이면 진행 바를 가득 채운다
            'percent'    => $needed > 0 ? min(100, (int) round($gained / $needed * 100)) : 100,
            'streak'     => $row ? (int) $row->mq_current_streak : 0,
            'bestStreak' => $row ? (int) $row->mq_best_streak : 0,
        ];
    }

    /**
     * 최근 획득 내역
     *
     * @param string $userId
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function recentHistory($userId, $limit = 5)
    {
        if (!$userId) {
            return collect();
        }

        return ExpHistory::ownedBy($userId)
            ->orderBy('mq_reg_date', 'desc')
            ->orderBy('idx', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * 행동 정의를 꺼낸다.
     *
     * 'scrap.create' 처럼 키 자체에 점이 있어서 config() 의 dot notation 으로는
     * 읽을 수 없다. 반드시 배열을 통째로 받아 색인해야 한다.
     *
     * @param string $actionCode
     * @return array|null
     */
    public static function action($actionCode)
    {
        $actions = config('exp.actions', []);

        return isset($actions[$actionCode]) ? $actions[$actionCode] : null;
    }

    /**
     * 누적 경험치에 해당하는 등급
     *
     * @param int $total
     * @return array
     */
    public function gradeFor($total)
    {
        $grades = config('exp.grades', []);
        $current = $grades[0];

        foreach ($grades as $grade) {
            if ($total >= $grade['exp']) {
                $current = $grade;
            }
        }

        return $current;
    }

    /**
     * 다음 등급 (최고 등급이면 null)
     *
     * @param int $total
     * @return array|null
     */
    public function nextGradeFor($total)
    {
        foreach (config('exp.grades', []) as $grade) {
            if ($total < $grade['exp']) {
                return $grade;
            }
        }

        return null;
    }

    /**
     * 원장을 다시 읽어 캐시(mq_member_exp)를 맞춘다.
     *
     * 증분으로 더하지 않고 매번 합계를 다시 구한다. 증분은 실패 / 재시도가 섞이면
     * 반드시 틀어지는데, 회원당 원장 행 수가 적어서 다시 세도 충분히 싸다.
     *
     * @param string $userId
     * @param int|null $beforeTotal 등급 상승 판정용 직전 누적치
     * @return void
     */
    public function syncMember($userId, $beforeTotal = null)
    {
        $total = $this->totalExp($userId);
        $grade = $this->gradeFor($total);

        $row = MemberExp::find($userId);
        $now = Carbon::now();

        if (!$row) {
            $row = new MemberExp();
            $row->mq_user_id = $userId;
            $row->mq_reg_date = $now;
        }

        $gradeChanged = $row->mq_grade_code !== $grade['code'];

        $row->mq_total_exp = $total;
        $row->mq_grade_code = $grade['code'];
        $row->mq_update_date = $now;

        if ($gradeChanged) {
            $row->mq_grade_date = $now;
        }

        $row->save();

        if ($beforeTotal !== null && $gradeChanged && $total > $beforeTotal) {
            $this->flashGradeUp($userId, $grade);
        }
    }

    /**
     * 접속 원장에서 연속 접속일을 센다.
     *
     * 오늘 접속 기록이 없으면 어제부터 센다. 오늘 아직 안 들어왔다고 해서
     * 기록이 끊긴 것은 아니기 때문이다.
     *
     * @param string $userId
     * @return int
     */
    public function calculateVisitStreak($userId)
    {
        $from = Carbon::now()->subDays(self::STREAK_LOOKBACK_DAYS)->startOfDay();

        // DATE() 는 select 절에만 쓰고 where 는 범위 조건이라 인덱스를 그대로 탄다
        $dates = ExpHistory::ownedBy($userId)
            ->where('mq_action_code', 'visit.daily')
            ->where('mq_reg_date', '>=', $from)
            ->selectRaw('DATE(mq_reg_date) as visit_date')
            ->distinct()
            ->pluck('visit_date')
            ->map(function ($date) {
                return Carbon::parse($date)->toDateString();
            })
            ->flip()
            ->all();

        $today = Carbon::now()->startOfDay();
        $cursor = isset($dates[$today->toDateString()]) ? $today : $today->copy()->subDay();

        if (!isset($dates[$cursor->toDateString()])) {
            return 0;
        }

        $streak = 0;

        while (isset($dates[$cursor->toDateString()]) && $streak < self::STREAK_LOOKBACK_DAYS) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    /**
     * 연속 접속일을 캐시에 반영한다.
     *
     * @param string $userId
     * @param int $streak
     * @return void
     */
    private function touchStreak($userId, $streak)
    {
        $row = MemberExp::find($userId);

        if (!$row) {
            return;
        }

        $row->mq_current_streak = $streak;
        $row->mq_best_streak = max((int) $row->mq_best_streak, $streak);
        $row->mq_last_visit_date = Carbon::now()->toDateString();
        $row->mq_update_date = Carbon::now();
        $row->save();
    }

    /**
     * @param string $userId
     * @return int
     */
    private function totalExp($userId)
    {
        return (int) ExpHistory::ownedBy($userId)->sum('mq_exp');
    }

    /**
     * 오늘 이 행동으로 받을 수 있는 횟수를 다 썼는지
     *
     * @param string $userId
     * @param string $actionCode
     * @param array $action
     * @return bool
     */
    private function reachedDailyLimit($userId, $actionCode, array $action)
    {
        $limit = isset($action['daily_limit']) ? (int) $action['daily_limit'] : 0;

        if ($limit <= 0) {
            return false;
        }

        $start = Carbon::now()->startOfDay();
        $end = $start->copy()->addDay();

        $count = ExpHistory::ownedBy($userId)
            ->where('mq_action_code', $actionCode)
            ->where('mq_reg_date', '>=', $start)
            ->where('mq_reg_date', '<', $end)
            ->count();

        return $count >= $limit;
    }

    /**
     * 본인이 등급을 올린 경우에만 축하 문구를 한 번 띄운다.
     *
     * 좋아요처럼 남의 행동으로 내 경험치가 오르는 경우가 있어서, 지금 요청을 보낸
     * 사람과 지급 대상이 같을 때만 세션에 담는다.
     *
     * @param string $userId
     * @param array $grade
     * @return void
     */
    private function flashGradeUp($userId, array $grade)
    {
        if (!auth()->check() || auth()->user()->mq_user_id !== $userId) {
            return;
        }

        session()->flash(self::GRADE_UP_SESSION_KEY, $grade);
    }

    /**
     * 유니크 키 충돌인지 확인 (MySQL 1062)
     *
     * @param QueryException $e
     * @return bool
     */
    private function isDuplicateKey(QueryException $e)
    {
        return isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
    }
}
