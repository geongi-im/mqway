<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * 관리자 페이지(/admin)는 최고관리자(mq_level 10)만 연다.
 *
 * 로그인 확인은 앞의 auth 미들웨어가 한다. 여기서는 권한만 본다.
 * 로그인 뒤에 비활성/해지된 계정은 세션이 남아 있어도 막는다.
 */
class EnsureSuperAdmin
{
    const LEVEL = 10;

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || (int) $user->mq_level < self::LEVEL || (int) $user->mq_status !== 1) {
            abort(403, '관리자만 볼 수 있는 페이지입니다.');
        }

        return $next($request);
    }
}
