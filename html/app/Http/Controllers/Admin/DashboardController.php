<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushLog;
use App\Services\AdminBoardRegistry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * 관리자 페이지 첫 화면. 숫자 4개와 최근 게시글 / 최근 푸시 발송.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $lastWeekStart = $weekStart->copy()->subWeek();

        $stats = [
            'members'        => DB::table('mq_member')->where('mq_status', '!=', 2)->count(),
            'joined_today'   => DB::table('mq_member')->where('mq_reg_date', '>=', $today)->count(),
            // 로그인 회원의 하루 첫 접속 기록 (RecordDailyVisit)
            'visitors_today' => DB::table('mq_exp_history')
                ->where('mq_action_code', 'visit.daily')
                ->where('mq_reg_date', '>=', $today)
                ->count(),
            'visitors_yesterday' => DB::table('mq_exp_history')
                ->where('mq_action_code', 'visit.daily')
                ->whereBetween('mq_reg_date', [$today->copy()->subDay(), $today])
                ->count(),
            'scrap_week'      => DB::table('mq_board_scrap')->where('mq_status', 1)->where('mq_reg_date', '>=', $weekStart)->count(),
            'scrap_last_week' => DB::table('mq_board_scrap')->where('mq_status', 1)
                ->where('mq_reg_date', '>=', $lastWeekStart)
                ->where('mq_reg_date', '<', $lastWeekStart->copy()->addSeconds(Carbon::now()->diffInSeconds($weekStart)))
                ->count(),
            // 알림을 켠 기기가 있는 활성 회원 / 그중 마케팅 수신 동의 (자동 규칙이 보낼 수 있는 회원)
            'push_members' => $this->pushMemberQuery()->count(DB::raw('DISTINCT m.idx')),
            'push_marketing_members' => $this->pushMemberQuery()->where('m.mq_marketing_agree', 1)->count(DB::raw('DISTINCT m.idx')),
        ];

        $recentPosts = DB::query()
            ->fromSub(AdminBoardRegistry::unionQuery(), 'p')
            ->where('mq_status', 1)
            ->orderByDesc('mq_reg_date')
            ->limit(6)
            ->get();

        $recentLogs = PushLog::with('rule')->orderByDesc('idx')->limit(6)->get();

        return view('admin.dashboard', compact('stats', 'recentPosts', 'recentLogs'));
    }

    protected function pushMemberQuery()
    {
        return DB::table('mq_member as m')
            ->join('mq_push_tokens as t', 't.member_idx', '=', 'm.idx')
            ->where('t.mq_status', 1)
            ->where('m.mq_status', 1);
    }
}
