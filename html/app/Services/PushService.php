<?php

namespace App\Services;

use App\Models\PushLog;
use App\Models\PushToken;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

/**
 * 앱 푸시 기기 등록과 발송 규칙.
 *
 * FCM 과의 통신은 FcmService 가 하고, 이 클래스는 우리 쪽 장부(mq_push_tokens, mq_push_logs)와
 * 발송 규칙(받는 사람, 링크 검사, 중복 방지)을 맡는다.
 *
 * 받는 사람
 * - 회원이 헤더의 종 버튼으로 알림을 허용하면 그 기기 토큰이 mq_push_tokens 에 회원과 함께 저장된다.
 * - 발송할 때는 그중 '활성 회원 + 마케팅 정보 수신 동의' 인 회원의 기기에만 보낸다.
 *   동의 여부는 발송 시점의 mq_member 값을 보므로, 마이페이지에서 동의를 철회하면 바로 빠진다.
 *
 * 발송 흐름 (외부 프로그램이 /api/push/send 호출)
 * 1. prepareBroadcast(): 입력을 정리하고 발송 이력을 만든 뒤 대상 수를 센다.
 * 2. 호출한 쪽에 먼저 응답한다.
 * 3. deliver(): 대상 기기마다 FCM 요청을 보내고 결과를 이력에 남긴다. (큐가 없어서 응답 뒤에 같은 프로세스에서 보낸다)
 */
class PushService
{
    const SOURCE_API = 'api';

    /** mq_push_logs.mq_target 값: 마케팅 수신 동의 회원 전체 */
    const TARGET_MARKETING_MEMBERS = 'marketing_members';

    const TITLE_MAX = 100;
    const BODY_MAX = 500;

    /** 알림이 기기에 도착하지 못했을 때 FCM 이 보관하는 시간(초). 하루 지난 소식은 의미가 없다. */
    const TTL_SECONDS = 86400;

    /** 대상 토큰을 DB 에서 한 번에 읽는 개수 */
    const CHUNK_SIZE = 500;

    /** pending 인 채로 이 시간(분)이 지난 이력은 발송 중 프로세스가 죽은 것으로 보고 다시 보낼 수 있게 한다. */
    const STALE_PENDING_MINUTES = 30;

    /** @var FcmService */
    protected $fcm;

    public function __construct(FcmService $fcm)
    {
        $this->fcm = $fcm;
    }

    /**
     * 회원의 기기 토큰을 등록한다. 이미 있는 토큰이면 회원/상태/마지막 확인 시각을 갱신한다.
     * 한 브라우저에서 다른 회원이 로그인해 다시 등록하면 그 회원의 기기가 된다.
     *
     * @param  string      $token
     * @param  int         $memberIdx
     * @param  string      $platform
     * @param  string|null $userAgent
     * @return void
     */
    public function registerToken($token, $memberIdx, $platform = PushToken::PLATFORM_WEB, $userAgent = null)
    {
        $now = Carbon::now();
        $hash = PushToken::hashToken($token);
        $userAgent = $userAgent !== null ? mb_substr($userAgent, 0, 255, 'UTF-8') : null;

        $row = PushToken::where('mq_token_hash', $hash)->first();

        if ($row) {
            $row->member_idx = $memberIdx;
            $row->mq_status = PushToken::STATUS_ACTIVE;
            $row->mq_platform = $platform;
            $row->mq_update_date = $now;
            if ($userAgent !== null) {
                $row->mq_user_agent = $userAgent;
            }
            $row->save();
            return;
        }

        try {
            PushToken::create([
                'mq_token'       => $token,
                'mq_token_hash'  => $hash,
                'member_idx'     => $memberIdx,
                'mq_platform'    => $platform,
                'mq_user_agent'  => $userAgent,
                'mq_status'      => PushToken::STATUS_ACTIVE,
                'mq_reg_date'    => $now,
                'mq_update_date' => $now,
            ]);
        } catch (QueryException $e) {
            // 같은 토큰이 동시에 두 번 들어온 경우. 먼저 들어온 쪽이 이미 저장했다.
            if (!$this->isDuplicateKey($e)) {
                throw $e;
            }
        }
    }

    /**
     * 기기 토큰을 끈다. (알림 끄기)
     *
     * @param  string $token
     * @return void
     */
    public function unregisterToken($token)
    {
        $this->deactivate([PushToken::hashToken($token)]);
    }

    /**
     * 지금 보내면 받는 기기 수.
     *
     * @return int
     */
    public function countTargets()
    {
        return $this->targetQuery()->count();
    }

    /**
     * 발송을 준비한다. 이력을 만들고 대상 수를 센다. 실제 발송은 deliver() 가 한다.
     *
     * @param  string      $title
     * @param  string|null $body
     * @param  string|null $link      정규화된 절대 주소 (normalizeLink() 결과)
     * @param  string      $source    self::SOURCE_*
     * @param  string|null $dedupeKey 같은 키로 이미 보냈으면 다시 보내지 않는다
     * @return array {result: accepted|no_target|duplicate|not_configured, log: PushLog|null, target_count: int}
     */
    public function prepareBroadcast($title, $body, $link, $source, $dedupeKey = null)
    {
        if (!$this->fcm->isConfigured()) {
            return ['result' => 'not_configured', 'log' => null, 'target_count' => 0];
        }

        $title = mb_substr($title, 0, self::TITLE_MAX, 'UTF-8');
        $body = $body !== null ? mb_substr($body, 0, self::BODY_MAX, 'UTF-8') : null;
        $targetCount = $this->countTargets();

        $log = $this->openLog($dedupeKey, [
            'mq_source'       => $source,
            'mq_target'       => self::TARGET_MARKETING_MEMBERS,
            'mq_title'        => $title,
            'mq_body'         => $body,
            'mq_link'         => $link,
            'mq_target_count' => $targetCount,
        ]);

        if ($log === null) {
            return ['result' => 'duplicate', 'log' => null, 'target_count' => $targetCount];
        }

        // 받을 사람이 없으면 보낼 것도 없다. 처리 끝난 것으로 기록한다.
        if ($targetCount === 0) {
            $log->update(['mq_status' => PushLog::STATUS_SENT, 'mq_send_date' => Carbon::now()]);
            return ['result' => 'no_target', 'log' => $log, 'target_count' => 0];
        }

        return ['result' => 'accepted', 'log' => $log, 'target_count' => $targetCount];
    }

    /**
     * 준비된 발송 이력의 알림을 대상 기기 모두에게 보낸다.
     *
     * 한 기기라도 받았으면 sent, 전부 실패하면 failed. failed 는 같은 dedupe_key 로 다시 보낼 수 있다.
     * 해지된 토큰(UNREGISTERED 등)은 이 자리에서 꺼서 다음 발송부터 빠지게 한다.
     *
     * @param  PushLog $log
     * @return void
     */
    public function deliver(PushLog $log)
    {
        // 응답을 보낸 뒤에 도는 작업이라 호출한 쪽 연결이 끊겨도 끝까지 보내게 한다.
        ignore_user_abort(true);
        @set_time_limit(0);

        $success = 0;
        $failure = 0;
        $firstError = null;
        $title = $log->mq_title;
        $body = $log->mq_body;
        $link = $log->mq_link;

        try {
            $this->targetQuery()
                ->select('mq_push_tokens.idx', 'mq_push_tokens.mq_token', 'mq_push_tokens.mq_token_hash')
                ->chunkById(self::CHUNK_SIZE, function ($tokens) use ($title, $body, $link, &$success, &$failure, &$firstError) {
                    $messages = [];
                    foreach ($tokens as $token) {
                        $messages[$token->mq_token_hash] = $this->buildMessage($token->mq_token, $title, $body, $link);
                    }

                    $invalid = [];
                    foreach ($this->fcm->sendEach($messages) as $hash => $result) {
                        if ($result['success']) {
                            $success++;
                            continue;
                        }
                        $failure++;
                        if ($this->fcm->isInvalidToken($result)) {
                            $invalid[] = $hash;
                        } elseif ($firstError === null) {
                            $firstError = $result['error'];
                        }
                    }

                    $this->deactivate($invalid);
                }, 'mq_push_tokens.idx', 'idx');
        } catch (\Throwable $e) {
            Log::error('앱 푸시 발송 중 오류', ['log_idx' => $log->idx, 'error' => $e->getMessage()]);
            $firstError = $e->getMessage();
        }

        $log->update([
            'mq_status'        => $success > 0 ? PushLog::STATUS_SENT : PushLog::STATUS_FAILED,
            'mq_success_count' => $success,
            'mq_failure_count' => $failure,
            'mq_error'         => $firstError !== null ? mb_substr($firstError, 0, 1000, 'UTF-8') : null,
            'mq_send_date'     => Carbon::now(),
        ]);
    }

    /**
     * 알림을 눌렀을 때 열 주소를 우리 사이트의 절대 주소로 바꾼다.
     * 사이트 밖 주소는 받지 않는다. (공유 키가 새더라도 피싱 링크를 뿌릴 수 없게)
     *
     * @param  string|null $link '/board-news' 같은 경로 또는 우리 사이트 절대 주소
     * @return string|null 허용되지 않는 주소면 null
     */
    public function normalizeLink($link)
    {
        $base = rtrim((string) config('app.url'), '/');

        if ($link === null || $link === '') {
            return $base . '/';
        }

        // '//evil.com' 처럼 프로토콜 상대 주소는 경로가 아니다.
        if ($link[0] === '/' && (strlen($link) === 1 || $link[1] !== '/')) {
            return $base . $link;
        }

        $host = parse_url($link, PHP_URL_HOST);
        $scheme = parse_url($link, PHP_URL_SCHEME);
        if (in_array($scheme, ['http', 'https'], true) && $host !== null && $host === parse_url($base, PHP_URL_HOST)) {
            return $link;
        }

        return null;
    }

    /**
     * 알림을 받을 기기: 켜져 있는 토큰 중 활성 회원이면서 마케팅 정보 수신에 동의한 회원의 것.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function targetQuery()
    {
        return PushToken::query()
            ->join('mq_member', 'mq_member.idx', '=', 'mq_push_tokens.member_idx')
            ->where('mq_push_tokens.mq_status', PushToken::STATUS_ACTIVE)
            ->where('mq_member.mq_status', 1)
            ->where('mq_member.mq_marketing_agree', 1);
    }

    /**
     * FCM v1 메시지를 만든다.
     *
     * notification 을 넣어 두면 브라우저(Firebase SDK)와 나중의 앱이 알아서 알림을 띄운다.
     * 알림을 눌렀을 때 열 주소는 data.link 로 보내고, 서비스 워커가 이 값을 읽어 연다.
     * (webpush.fcm_options.link 는 https 만 받아서 로컬 http 환경에서 쓸 수 없다)
     *
     * @param  string      $token
     * @param  string      $title
     * @param  string|null $body
     * @param  string|null $link
     * @return array
     */
    protected function buildMessage($token, $title, $body, $link)
    {
        $notification = ['title' => $title];
        if ($body !== null && $body !== '') {
            $notification['body'] = $body;
        }

        $base = rtrim((string) config('app.url'), '/');

        return [
            'token'        => $token,
            'notification' => $notification,
            'data'         => ['link' => (string) $link],
            'webpush'      => [
                'headers'      => ['TTL' => (string) self::TTL_SECONDS],
                'notification' => ['icon' => $base . '/images/pwa/icon-192.png'],
            ],
        ];
    }

    /**
     * 발송 이력을 연다. 같은 중복 방지 키로 이미 보냈거나 보내는 중이면 null.
     * 실패했던 키와, pending 인 채로 오래 멈춘 키는 같은 행을 다시 써서 재발송을 허용한다.
     *
     * @param  string|null $dedupeKey
     * @param  array       $fields
     * @return PushLog|null
     */
    protected function openLog($dedupeKey, array $fields)
    {
        $fields += [
            'mq_status'        => PushLog::STATUS_PENDING,
            'mq_success_count' => 0,
            'mq_failure_count' => 0,
            'mq_error'         => null,
            'mq_send_date'     => null,
        ];

        if ($dedupeKey !== null) {
            $existing = PushLog::where('mq_dedupe_key', $dedupeKey)->first();

            if ($existing) {
                $stale = Carbon::now()->subMinutes(self::STALE_PENDING_MINUTES);

                // 두 요청이 동시에 재시도하면 한쪽만 바꾸도록 조건을 걸어 갱신한다.
                $updated = PushLog::where('idx', $existing->idx)
                    ->where(function ($query) use ($stale) {
                        $query->where('mq_status', PushLog::STATUS_FAILED)
                            ->orWhere(function ($query) use ($stale) {
                                $query->where('mq_status', PushLog::STATUS_PENDING)
                                    ->where('mq_reg_date', '<', $stale);
                            });
                    })
                    ->update($fields + ['mq_reg_date' => Carbon::now()]);

                return $updated ? PushLog::find($existing->idx) : null;
            }
        }

        try {
            return PushLog::create($fields + [
                'mq_dedupe_key' => $dedupeKey,
                'mq_reg_date'   => Carbon::now(),
            ]);
        } catch (QueryException $e) {
            if ($dedupeKey !== null && $this->isDuplicateKey($e)) {
                return null;
            }
            throw $e;
        }
    }

    /**
     * @param  array $hashes mq_token_hash 목록
     * @return void
     */
    protected function deactivate(array $hashes)
    {
        if (!$hashes) {
            return;
        }

        PushToken::whereIn('mq_token_hash', $hashes)->update([
            'mq_status'      => PushToken::STATUS_INACTIVE,
            'mq_update_date' => Carbon::now(),
        ]);
    }

    /**
     * MySQL 유니크 키 충돌(1062)인지.
     *
     * @param  QueryException $e
     * @return bool
     */
    protected function isDuplicateKey(QueryException $e)
    {
        return isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
    }
}
