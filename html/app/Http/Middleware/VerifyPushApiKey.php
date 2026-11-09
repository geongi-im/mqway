<?php

namespace App\Http\Middleware;

use Closure;

/**
 * 앱 푸시 발송 API(/api/push/send) 호출을 공유 키로 확인한다.
 *
 * 이 API 가 열려 있으면 누구나 알림을 허용한 회원 전체에게 알림을 보낼 수 있으므로,
 * 외부 수집 프로그램이 .env 의 PUSH_API_KEY 와 같은 값을 X-Push-Key 헤더로 보내야 통과한다.
 * 서버에 키가 설정되어 있지 않으면 항상 거절한다.
 */
class VerifyPushApiKey
{
    const HEADER = 'X-Push-Key';

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $expected = (string) config('services.firebase.push_api_key');

        if ($expected === '') {
            return response()->json([
                'success' => false,
                'message' => '발송 API 가 설정되지 않았습니다.',
            ], 503);
        }

        $given = (string) $request->header(self::HEADER, '');

        if ($given === '' || !hash_equals($expected, $given)) {
            return response()->json([
                'success' => false,
                'message' => '인증에 실패했습니다.',
            ], 401);
        }

        return $next($request);
    }
}
