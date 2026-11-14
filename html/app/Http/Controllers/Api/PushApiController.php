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

    /**
     * 회원 지정 발송은 회원별 소식(관심 종목, 내 글 반응 등)이라 전체 발송보다 자주 불린다.
     * 그래서 전체 발송과 따로 세고 한도를 넉넉히 둔다. 한 번에 최대 500명이므로 분당 3만 명까지다.
     */
    const USERS_RATE_LIMIT_PER_MINUTE = 60;

    const USERS_RATE_LIMIT_KEY = 'push-api-send-users';

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
        if ($limited = $this->rateLimited(self::RATE_LIMIT_KEY, self::RATE_LIMIT_PER_MINUTE)) {
            return $limited;
        }

        $validator = Validator::make($request->all(), $this->messageRules());

        if ($validator->fails()) {
            return $this->invalid($validator->errors());
        }

        $link = $this->push->normalizeLink($request->input('link'));
        if ($link === null) {
            return $this->invalidLink();
        }

        $prepared = $this->push->prepareBroadcast(
            $request->input('title'),
            $request->input('body'),
            $link,
            PushService::SOURCE_API,
            $request->input('dedupe_key')
        );

        return $this->respond($prepared);
    }

    /**
     * 지정한 회원에게 보낸다.
     *
     * 요청 예시
     *   POST /api/push/send-users
     *   X-Push-Key: <PUSH_API_KEY>
     *   {
     *     "user_ids": ["hong123", "kim456"],     // mq_member.mq_user_id, 최대 500개
     *     "title": "관심 종목이 급등했어요",
     *     "body": "삼성전자 +5.2%",
     *     "link": "/board-research/123",
     *     "dedupe_key": "surge:2026-11-11:005930" // 받는 회원이 다르면 키도 다르게 만든다
     *   }
     *
     * 받는 사람 규칙은 전체 발송과 같다. (활성 회원 + 마케팅 정보 수신 동의 + 알림을 켠 기기)
     * 지정했지만 이 규칙에 걸려 받지 못하는 아이디는 data.skipped_user_ids 로 돌려준다.
     * 응답 코드는 send() 와 같다.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendToUsers(Request $request)
    {
        if ($limited = $this->rateLimited(self::USERS_RATE_LIMIT_KEY, self::USERS_RATE_LIMIT_PER_MINUTE)) {
            return $limited;
        }

        $validator = Validator::make($request->all(), $this->messageRules() + [
            'user_ids'   => 'required|array|min:1|max:' . PushService::USER_IDS_MAX,
            'user_ids.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->invalid($validator->errors());
        }

        $link = $this->push->normalizeLink($request->input('link'));
        if ($link === null) {
            return $this->invalidLink();
        }

        $userIds = array_values(array_unique(array_map('trim', $request->input('user_ids'))));

        $prepared = $this->push->prepareForUsers(
            $userIds,
            $request->input('title'),
            $request->input('body'),
            $link,
            PushService::SOURCE_API,
            $request->input('dedupe_key')
        );

        return $this->respond($prepared, [
            'user_count'       => count($userIds),
            'skipped_user_ids' => $prepared['skipped_user_ids'],
        ]);
    }

    /**
     * 알림 내용 검증 규칙 (전체 발송과 회원 지정 발송 공통)
     *
     * @return array
     */
    protected function messageRules()
    {
        return [
            'title'      => 'required|string|max:' . PushService::TITLE_MAX,
            'body'       => 'nullable|string|max:' . PushService::BODY_MAX,
            'link'       => 'nullable|string|max:500',
            'dedupe_key' => 'nullable|string|max:191',
        ];
    }

    /**
     * 요청 횟수를 세고, 넘었으면 429 응답을 돌려준다.
     *
     * @param  string $key
     * @param  int    $perMinute
     * @return \Illuminate\Http\JsonResponse|null
     */
    protected function rateLimited($key, $perMinute)
    {
        if ($this->limiter->tooManyAttempts($key, $perMinute)) {
            return response()->json([
                'success' => false,
                'message' => '발송 요청이 너무 많습니다. ' . $this->limiter->availableIn($key) . '초 뒤에 다시 시도해주세요.',
            ], 429);
        }
        $this->limiter->hit($key, 60);

        return null;
    }

    /**
     * @param  mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalid($errors)
    {
        return response()->json([
            'success' => false,
            'message' => '입력값 검증 실패',
            'errors'  => $errors,
        ], 422);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidLink()
    {
        return $this->invalid(['link' => ['link 는 우리 사이트 경로(/로 시작) 또는 우리 사이트 주소여야 합니다.']]);
    }

    /**
     * 준비 결과를 응답으로 바꾸고, 접수됐으면 응답 뒤에 발송을 건다.
     *
     * @param  array $prepared PushService::prepare*() 결과
     * @param  array $extra    data 에 덧붙일 값
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond(array $prepared, array $extra = [])
    {
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
            ] + $extra,
        ], $status);
    }
}
