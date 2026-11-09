<?php

namespace App\Http\Controllers;

use App\Models\PushToken;
use App\Services\PushService;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * 브라우저(또는 앱 웹뷰)가 받은 FCM 토큰을 등록/해지한다.
 *
 * 등록은 회원만 할 수 있다. 실제로 알림을 받는지는 발송 시점의 마케팅 정보 수신 동의로 정해진다.
 */
class PushTokenController extends Controller
{
    /**
     * 세션 하나가 1분 동안 보낼 수 있는 등록/해지 요청 수.
     * IP 기준이 아니라 세션 기준인 이유는 routes/web.php 의 /push/token 주석 참고.
     */
    const RATE_LIMIT_PER_MINUTE = 10;

    /** @var PushService */
    protected $push;

    /** @var RateLimiter */
    protected $limiter;

    public function __construct(PushService $push, RateLimiter $limiter)
    {
        $this->push = $push;
        $this->limiter = $limiter;
    }

    /**
     * 요청 횟수를 세고, 넘었으면 429 응답을 돌려준다.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    protected function rateLimited(Request $request)
    {
        $key = 'push-token:' . $request->session()->getId();

        if ($this->limiter->tooManyAttempts($key, self::RATE_LIMIT_PER_MINUTE)) {
            return response()->json(['success' => false, 'message' => '요청이 너무 많습니다. 잠시 후 다시 시도해주세요.'], 429);
        }
        $this->limiter->hit($key, 60);

        return null;
    }

    /**
     * 토큰 등록 (알림 켜기)
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => '로그인한 회원만 알림을 받을 수 있습니다.'], 401);
        }

        if ($limited = $this->rateLimited($request)) {
            return $limited;
        }

        $validator = Validator::make($request->all(), [
            'token'    => 'required|string|max:4096',
            'platform' => 'nullable|in:' . implode(',', [PushToken::PLATFORM_WEB, PushToken::PLATFORM_ANDROID, PushToken::PLATFORM_IOS]),
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => '알림 등록 정보가 올바르지 않습니다.'], 422);
        }

        $this->push->registerToken(
            $request->input('token'),
            Auth::id(),
            $request->input('platform') ?: PushToken::PLATFORM_WEB,
            $request->userAgent()
        );

        return response()->json(['success' => true, 'message' => '알림을 받도록 설정했습니다.']);
    }

    /**
     * 토큰 해지 (알림 끄기). 토큰을 가진 기기라면 로그아웃 상태여도 끌 수 있다.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        if ($limited = $this->rateLimited($request)) {
            return $limited;
        }

        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => '알림 해지 정보가 올바르지 않습니다.'], 422);
        }

        $this->push->unregisterToken($request->input('token'));

        return response()->json(['success' => true, 'message' => '알림을 껐습니다.']);
    }
}
