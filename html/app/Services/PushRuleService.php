<?php

namespace App\Services;

use App\Models\PushRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 앱 푸시 자동 발송 규칙의 대상 회원을 고르고 발송한다.
 *
 * 대상은 언제나 "활성 회원 + 마케팅 정보 수신 동의 + 알림을 켠 기기 보유" 중에서 고른다.
 * (PushService 가 발송할 때 다시 거르는 조건과 같다. 여기서 미리 걸러야 관리자 화면의 대상 수가 맞는다)
 *
 * 기간 조건(최근 N일)은 날짜 단위로 센다. 11/17 에 "최근 7일" 이면 11/10 00:00 이후의 활동을 본다.
 */
class PushRuleService
{
    /** @var PushService */
    protected $push;

    public function __construct(PushService $push)
    {
        $this->push = $push;
    }

    /**
     * 지금 조건에 맞는 회원 수. (관리자 화면의 "지금 기준 대상")
     *
     * @param  string   $target PushRule::TARGET_*
     * @param  int|null $days
     * @return int
     */
    public function countAudience($target, $days)
    {
        return $this->audienceQuery($target, $days, Carbon::now())->count();
    }

    /**
     * 지금 조건에 맞는 회원 수와 앞쪽 일부 회원. (관리자 화면의 예상 대상자 목록, 최근 가입 순)
     *
     * @param  string   $target PushRule::TARGET_*
     * @param  int|null $days
     * @param  int      $limit  목록에 담을 최대 회원 수
     * @return array ['count' => int, 'members' => [{user_id, name, email}]]
     */
    public function previewAudience($target, $days, $limit)
    {
        $query = $this->audienceQuery($target, $days, Carbon::now());
        $count = (clone $query)->count();

        $members = $query
            ->orderByDesc('m.idx')
            ->limit($limit)
            ->get(['m.mq_user_id', 'm.mq_user_name', 'm.mq_user_email'])
            ->map(function ($m) {
                return [
                    'user_id' => $m->mq_user_id,
                    'name'    => $m->mq_user_name,
                    'email'   => $m->mq_user_email,
                ];
            })
            ->all();

        return ['count' => $count, 'members' => $members];
    }

    /**
     * 조건에 맞는 회원 아이디 목록.
     *
     * @param  PushRule $rule
     * @param  Carbon   $now
     * @return string[]
     */
    public function audienceUserIds(PushRule $rule, Carbon $now)
    {
        return $this->audienceQuery($rule->mq_target, $rule->mq_target_days, $now)
            ->pluck('m.mq_user_id')
            ->all();
    }

    /**
     * @param  string   $target
     * @param  int|null $days
     * @param  Carbon   $now
     * @return \Illuminate\Database\Query\Builder
     */
    protected function audienceQuery($target, $days, Carbon $now)
    {
        $query = DB::table('mq_member as m')
            ->where('m.mq_status', 1)
            ->where('m.mq_marketing_agree', 1)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('mq_push_tokens as t')
                    ->whereColumn('t.member_idx', 'm.idx')
                    ->where('t.mq_status', 1);
            });

        if ($target === PushRule::TARGET_ALL) {
            return $query;
        }

        $since = $now->copy()->startOfDay()->subDays(max(1, (int) $days));

        // 가입한 지 N일이 안 된 회원은 "N일 동안 안 했다" 고 할 수 없으므로 뺀다.
        $query->where('m.mq_reg_date', '<', $since);

        if ($target === PushRule::TARGET_NO_SCRAP) {
            return $query->whereNotExists(function ($q) use ($since) {
                $q->select(DB::raw(1))
                    ->from('mq_board_scrap as s')
                    ->whereColumn('s.mq_user_id', 'm.mq_user_id')
                    ->where('s.mq_status', 1)
                    ->where('s.mq_reg_date', '>=', $since);
            });
        }

        if ($target === PushRule::TARGET_NO_VISIT) {
            // 마지막으로 들어온 때: 로그인 시각과 하루 첫 접속 기록(mq_member_exp) 중 늦은 쪽.
            // 로그인 상태가 오래 유지되는 회원은 mq_last_login_date 가 갱신되지 않아서 둘 다 본다.
            return $query
                ->leftJoin('mq_member_exp as e', 'e.mq_user_id', '=', 'm.mq_user_id')
                ->whereRaw(
                    "GREATEST(COALESCE(m.mq_last_login_date, '1970-01-01'), COALESCE(e.mq_last_visit_date, '1970-01-01')) < ?",
                    [$since->toDateTimeString()]
                );
        }

        throw new \InvalidArgumentException('알 수 없는 대상: ' . $target);
    }

    /**
     * 발송 시각이 된 규칙을 모두 보낸다. (php artisan push:run-rules, 1분마다)
     *
     * @param  Carbon|null $now
     * @param  bool        $dryRun true 면 보내지 않고 무엇을 보낼지만 돌려준다
     * @return array[] 규칙별 결과 [{rule, slot, result, target_count, members}]
     */
    public function runDue(Carbon $now = null, $dryRun = false)
    {
        $now = $now ?: Carbon::now();
        $results = [];

        foreach (PushRule::where('mq_status', PushRule::STATUS_ON)->orderBy('idx')->get() as $rule) {
            $slot = $rule->dueSlot($now);
            if ($slot === null) {
                continue;
            }

            try {
                $results[] = $this->run($rule, $slot, $now, $dryRun);
            } catch (\Throwable $e) {
                // 한 규칙이 실패해도 다른 규칙은 보낸다. 이 회차는 다음 실행(1분 뒤)에 다시 시도한다.
                Log::error('앱 푸시 자동 규칙 발송 실패', ['rule_idx' => $rule->idx, 'error' => $e->getMessage()]);
                $results[] = ['rule' => $rule, 'slot' => $slot, 'result' => 'error', 'target_count' => 0, 'members' => 0];
            }
        }

        return $results;
    }

    /**
     * 규칙 한 회차를 보낸다.
     *
     * @param  PushRule $rule
     * @param  Carbon   $slot   이 회차의 발송 시각 (중복 방지 키에 쓴다)
     * @param  Carbon   $now
     * @param  bool     $dryRun
     * @return array
     */
    protected function run(PushRule $rule, Carbon $slot, Carbon $now, $dryRun)
    {
        $userIds = $this->audienceUserIds($rule, $now);

        if ($dryRun) {
            return ['rule' => $rule, 'slot' => $slot, 'result' => 'dry_run', 'target_count' => 0, 'members' => count($userIds)];
        }

        $prepared = $this->push->prepareForRule(
            $rule->idx,
            $userIds,
            $rule->mq_title,
            $rule->mq_body,
            $this->push->normalizeLink($rule->mq_link) ?: $this->push->normalizeLink('/'),
            'rule:' . $rule->idx . ':' . $slot->format('Y-m-d H:i')
        );

        // 발송 여부와 관계없이 이 회차는 처리한 것으로 남긴다. (설정이 없을 때 1분마다 다시 시도하지 않게)
        $rule->mq_last_run_date = $now;
        if ($prepared['log'] !== null) {
            $rule->mq_last_log_idx = $prepared['log']->idx;
        }
        $rule->save();

        if ($prepared['result'] === 'not_configured') {
            Log::warning('앱 푸시 자동 규칙: FCM 설정이 없어 보내지 않음', ['rule_idx' => $rule->idx]);
        }

        if ($prepared['result'] === 'accepted') {
            $this->push->deliver($prepared['log']);
        }

        return [
            'rule'         => $rule,
            'slot'         => $slot,
            'result'       => $prepared['result'],
            'target_count' => $prepared['target_count'],
            'members'      => count($userIds),
        ];
    }
}
