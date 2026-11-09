<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PushService;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 외부 프로그램(cron 수집기 등)이 앱 푸시를 보낼 때 부르는 API.
 *
 * 알림을 허용한 회원 중 마케팅 정보 수신에 동의한 회원의 기기 전체에 보낸다.
 * 인증은 라우트에 붙은 VerifyPushApiKey 미들웨어(X-Push-Key 헤더)가 한다.
 *
 * 요청 예시
 *   POST /api/push/send
 *   X-Push-Key: <PUSH_API_KEY>
 *   {
 *     "title": "새 뉴스가 도착했어요",
 *     "body": "오늘의 주요 뉴스를 확인해 보세요.",
 *     "link": "/board-news",                 // 우리 사이트 경로 또는 절대 주소
 *     "dedupe_key": "news:2026-11-09:am"     // 같은 키는 한 번만 발송 (재시도 대비)
 *   }
 *
 * 발송은 응답을 보낸 뒤에 진행된다. 기기별 결과는 mq_push_logs 의 log_idx 행에서 확인한다.
 */
class PushApiController extends Controller
{
    /**
     * 1분 동안 받는 발송 요청 수 (호출한 곳과 관계없이 전체 합계).
     *
     * throttle 미들웨어를 쓰지 않는 이유: Laravel 6 의 throttle 은 IP 하나에 카운터 하나를 두고
     * 모든 라우트가 같이 쓴다. 수집 프로그램이 같은 IP 로 /api/news 를 여러 번 부르면 그 횟수가
     * 여기까지 합쳐져 발송이 막힌다. 그래서 발송 API 전용 키로 따로 센다.
     */
    const RATE_LIMIT_PER_MINUTE = 10;

    const RATE_LIMIT_KEY = 'push-api-send';

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
     * 응답 코드
     *   202 발송 접수 (응답 뒤에 발송) / 200 받을 기기가 없어 보내지 않음
     *   409 같은 dedupe_key 로 이미 발송 / 422 입력값 오류
     *   429 발송 요청이 너무 많음 / 503 서버에 FCM 키가 없음
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        if ($this->limiter->tooManyAttempts(self::RATE_LIMIT_KEY, self::RATE_LIMIT_PER_MINUTE)) {
            return response()->json([
                'success' => false,
                'message' => '발송 요청이 너무 많습니다. ' . $this->limiter->availableIn(self::RATE_LIMIT_KEY) . '초 뒤에 다시 시도해주세요.',
            ], 429);
        }
        $this->limiter->hit(self::RATE_LIMIT_KEY, 60);

        $validator = Validator::make($request->all(), [
            'title'      => 'required|string|max:' . PushService::TITLE_MAX,
            'body'       => 'nullable|string|max:' . PushService::BODY_MAX,
            'link'       => 'nullable|string|max:500',
            'dedupe_key' => 'nullable|string|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '입력값 검증 실패',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $link = $this->push->normalizeLink($request->input('link'));
        if ($link === null) {
            return response()->json([
                'success' => false,
                'message' => '입력값 검증 실패',
                'errors'  => ['link' => ['link 는 우리 사이트 경로(/로 시작) 또는 우리 사이트 주소여야 합니다.']],
            ], 422);
        }

        $prepared = $this->push->prepareBroadcast(
            $request->input('title'),
            $request->input('body'),
            $link,
            PushService::SOURCE_API,
            $request->input('dedupe_key')
        );

        // 큐가 없으므로 응답을 먼저 보내고 같은 프로세스에서 이어서 보낸다.
        // php-fpm 에서는 응답 전송 때 연결이 닫히고(fastcgi_finish_request) 그 뒤에 terminating 콜백이 돈다.
        if ($prepared['result'] === 'accepted') {
            $log = $prepared['log'];
            app()->terminating(function () use ($log) {
                app(PushService::class)->deliver($log);
            });
        }

        $responses = [
            'accepted'       => [202, true, '알림 발송을 시작했습니다.'],
            'no_target'      => [200, true, '알림을 받을 회원이 없어 보내지 않았습니다.'],
            'duplicate'      => [409, false, '이미 같은 알림을 보냈습니다.'],
            'not_configured' => [503, false, 'FCM 서비스 계정 키가 설정되지 않았습니다.'],
        ];
        list($status, $success, $message) = $responses[$prepared['result']];

        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'    => [
                'result'       => $prepared['result'],
                'log_idx'      => $prepared['log'] ? $prepared['log']->idx : null,
                'target_count' => $prepared['target_count'],
            ],
        ], $status);
    }
}
