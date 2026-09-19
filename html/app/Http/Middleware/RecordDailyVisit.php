<?php

namespace App\Http\Middleware;

use App\Services\ExpService;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * 오늘 첫 접속을 기록해 경험치와 연속 접속일을 쌓는다.
 *
 * 로그인 이벤트에 걸지 않는 이유: 세션이 길면 매일 로그인하지 않는다.
 * 실제로 들어온 날을 세려면 요청 단계에서 봐야 한다.
 *
 * 매 요청마다 DB 를 건드리지 않도록 세션에 오늘 날짜를 남겨 하루 첫 요청에만 동작한다.
 * 세션이 날아가 다시 들어와도 mq_exp_history 의 유니크 키 때문에 두 번 지급되지 않는다.
 */
class RecordDailyVisit
{
    /** 오늘 이미 기록했는지 표시하는 세션 키 */
    const SESSION_KEY = 'exp_visit_date';

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->record($request);

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function record($request)
    {
        // 화면을 여는 요청에서만 센다. 폼 전송이나 AJAX 로 접속일이 찍히면
        // "들어온 날" 의 의미가 흐려진다.
        if (!$request->isMethod('GET') || $request->ajax() || !Auth::check()) {
            return;
        }

        $today = Carbon::now()->toDateString();

        if ($request->session()->get(self::SESSION_KEY) === $today) {
            return;
        }

        // 기록에 실패하더라도 매 요청마다 다시 시도하지 않도록 먼저 표시한다.
        // 하루치 접속 경험치를 놓치는 것보다 사이트가 느려지는 쪽이 더 나쁘다.
        $request->session()->put(self::SESSION_KEY, $today);

        app(ExpService::class)->recordVisit(Auth::user()->mq_user_id);
    }
}
