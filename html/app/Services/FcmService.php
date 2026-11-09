<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Firebase Cloud Messaging HTTP v1 API 를 직접 호출한다.
 *
 * 왜 라이브러리(kreait / laravel-notification-channels/fcm)를 쓰지 않는가
 *
 * - 서버가 PHP 7.2 라서 쓸 수 있는 버전이 2021년 무렵에 멈춰 있고, 그 버전의 여러 명 동시
 *   발송은 이미 종료된 batch 엔드포인트(fcm.googleapis.com/batch)를 쓴다.
 * - 우리가 쓰는 것은 '기기 토큰 하나에 메시지 하나 보내기' 뿐이라 REST 를 직접 부르는 편이
 *   단순하다. 서비스 계정 OAuth 토큰만 google/auth 로 받는다.
 *
 * FCM v1 에는 여러 기기에 한 번에 보내는 API 가 없어서 기기마다 요청을 하나씩 보낸다.
 * 대상이 많을 때는 sendEach() 가 curl_multi 로 여러 요청을 동시에 보낸다.
 *
 * 모든 메서드는 예외를 던지지 않고 결과 배열을 돌려준다.
 * {success: bool, status: int, data: array|null, error: string|null, error_code: string|null}
 */
class FcmService
{
    const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    const API_BASE = 'https://fcm.googleapis.com/v1/projects/';

    /** OAuth 접근 토큰 캐시 키 */
    const ACCESS_TOKEN_CACHE_KEY = 'fcm_access_token';

    /** 요청 하나의 제한 시간(초) */
    const REQUEST_TIMEOUT = 10;

    /** sendEach() 가 동시에 보내는 요청 수 */
    const CONCURRENCY = 20;

    /** @var array|null 서비스 계정 키 내용 */
    protected $credentials;

    /**
     * 서비스 계정 키가 준비되어 있는지.
     *
     * @return bool
     */
    public function isConfigured()
    {
        $credentials = $this->credentials();

        return $credentials !== null;
    }

    /**
     * Firebase 프로젝트 ID (서비스 계정 키에 들어 있는 값)
     *
     * @return string|null
     */
    public function projectId()
    {
        $credentials = $this->credentials();

        return $credentials ? $credentials['project_id'] : null;
    }

    /**
     * 메시지 한 건을 보낸다. 대상 토큰은 $message['token'] 에 들어 있어야 한다.
     *
     * @param  array $message FCM v1 Message 객체 (https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages)
     * @param  bool  $validateOnly true 면 실제로 보내지 않고 요청 형식만 검사한다
     * @return array
     */
    public function send(array $message, $validateOnly = false)
    {
        $results = $this->sendEach([$message], $validateOnly);

        return $results[0];
    }

    /**
     * 메시지 여러 건을 동시에 CONCURRENCY 개씩 보낸다.
     *
     * @param  array $messages 키 => FCM v1 Message. 돌려주는 결과도 같은 키를 쓴다.
     * @param  bool  $validateOnly
     * @return array 키 => 결과 배열
     */
    public function sendEach(array $messages, $validateOnly = false)
    {
        if (!$messages) {
            return [];
        }

        if (!$this->isConfigured()) {
            return $this->failAll($messages, $this->failure(0, 'FCM 서비스 계정 키가 설정되지 않았습니다.', 'NOT_CONFIGURED'));
        }

        $accessToken = $this->accessToken();
        if (!$accessToken) {
            return $this->failAll($messages, $this->failure(0, 'FCM 인증에 실패했습니다.', 'AUTH_FAILED'));
        }

        $url = self::API_BASE . $this->projectId() . '/messages:send';
        $queue = array_keys($messages);
        $results = [];
        $running = [];

        $multi = curl_multi_init();

        do {
            // 빈 자리만큼 새 요청을 넣는다.
            while ($queue && count($running) < self::CONCURRENCY) {
                $key = array_shift($queue);
                $body = ['message' => $messages[$key]];
                if ($validateOnly) {
                    $body['validate_only'] = true;
                }
                $ch = $this->createHandle('POST', $url, $body, $accessToken);
                curl_multi_add_handle($multi, $ch);
                $running[(int) $ch] = [$key, $ch];
            }

            curl_multi_exec($multi, $active);
            if ($active) {
                curl_multi_select($multi, 1.0);
            }

            // 끝난 요청을 꺼내 결과로 바꾼다.
            while ($info = curl_multi_info_read($multi)) {
                $ch = $info['handle'];
                list($key) = $running[(int) $ch];
                unset($running[(int) $ch]);

                $results[$key] = $this->parseResponse(
                    'POST',
                    $url,
                    curl_multi_getcontent($ch),
                    (int) curl_getinfo($ch, CURLINFO_HTTP_CODE),
                    $info['result'] === CURLE_OK ? '' : curl_error($ch)
                );

                curl_multi_remove_handle($multi, $ch);
                curl_close($ch);
            }
        } while ($queue || $running);

        curl_multi_close($multi);

        // 넣은 순서대로 돌려준다.
        $ordered = [];
        foreach (array_keys($messages) as $key) {
            $ordered[$key] = $results[$key];
        }

        return $ordered;
    }

    /**
     * 응답이 '이 토큰은 더 이상 쓸 수 없다' 는 뜻인지.
     * 브라우저에서 알림을 끄거나 사이트 데이터를 지우면 토큰이 무효가 된다.
     *
     * INVALID_ARGUMENT 는 넣지 않는다. 토큰 문제가 아니라 메시지 형식 문제일 때도 같은 코드가
     * 와서, 이것까지 무효로 보면 메시지 하나 잘못 만들었을 때 모든 토큰이 꺼진다.
     *
     * @param  array $result
     * @return bool
     */
    public function isInvalidToken(array $result)
    {
        if ($result['success'] || $result['status'] === 0) {
            return false;
        }

        // UNREGISTERED: 해지된 토큰 / SENDER_ID_MISMATCH: 다른 Firebase 프로젝트의 토큰 / 404: 없는 토큰
        return in_array($result['error_code'], ['UNREGISTERED', 'SENDER_ID_MISMATCH'], true)
            || $result['status'] === 404;
    }

    /**
     * 서비스 계정 OAuth 접근 토큰. 유효기간(1시간)보다 조금 짧게 캐시한다.
     *
     * @return string|null
     */
    protected function accessToken()
    {
        $cached = Cache::get(self::ACCESS_TOKEN_CACHE_KEY);
        if ($cached) {
            return $cached;
        }

        $credentials = $this->credentials();
        if (!$credentials) {
            return null;
        }

        try {
            $auth = new ServiceAccountCredentials(self::SCOPE, $credentials);
            $token = $auth->fetchAuthToken();
        } catch (\Throwable $e) {
            Log::error('FCM 접근 토큰 발급 실패', ['error' => $e->getMessage()]);
            return null;
        }

        if (empty($token['access_token'])) {
            Log::error('FCM 접근 토큰 발급 실패', ['error' => 'access_token 없음']);
            return null;
        }

        $ttl = isset($token['expires_in']) ? (int) $token['expires_in'] - 300 : 3000;
        Cache::put(self::ACCESS_TOKEN_CACHE_KEY, $token['access_token'], max(60, $ttl));

        return $token['access_token'];
    }

    /**
     * 서비스 계정 키 파일을 읽는다. 파일이 없거나 형식이 맞지 않으면 null.
     *
     * @return array|null
     */
    protected function credentials()
    {
        if ($this->credentials !== null) {
            return $this->credentials ?: null;
        }

        $path = (string) config('services.firebase.credentials');
        if ($path !== '' && !preg_match('#^([a-zA-Z]:)?[\\\\/]#', $path)) {
            $path = base_path($path);
        }

        $json = ($path !== '' && is_readable($path)) ? json_decode(file_get_contents($path), true) : null;

        if (!is_array($json) || empty($json['project_id']) || empty($json['private_key']) || empty($json['client_email'])) {
            // 없다는 사실도 기억해 두어 요청마다 파일을 다시 읽지 않는다.
            $this->credentials = [];
            return null;
        }

        $this->credentials = $json;

        return $json;
    }

    /**
     * @param  string            $method
     * @param  string            $url
     * @param  array|object|null $body
     * @param  string            $accessToken
     * @return resource
     */
    protected function createHandle($method, $url, $body, $accessToken)
    {
        $ch = curl_init();
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => self::REQUEST_TIMEOUT,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ],
        ];
        if ($body !== null) {
            $options[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        curl_setopt_array($ch, $options);

        return $ch;
    }

    /**
     * FCM 응답을 결과 배열로 바꾼다.
     *
     * @param  string      $method
     * @param  string      $url
     * @param  string|null $response
     * @param  int         $httpCode
     * @param  string      $curlError
     * @return array
     */
    protected function parseResponse($method, $url, $response, $httpCode, $curlError)
    {
        if ($curlError !== '') {
            Log::error('FCM 요청 실패', ['method' => $method, 'url' => $url, 'error' => $curlError]);
            return $this->failure(0, 'FCM 서버에 연결하지 못했습니다.', 'CONNECTION_ERROR');
        }

        $data = json_decode((string) $response, true);

        // 접근 토큰이 만료됐다면 캐시를 버려 다음 요청에서 새로 받게 한다.
        if ($httpCode === 401) {
            Cache::forget(self::ACCESS_TOKEN_CACHE_KEY);
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            $message = isset($data['error']['message']) ? $data['error']['message'] : ('HTTP ' . $httpCode);
            $result = $this->failure($httpCode, $message, $this->extractErrorCode($data), is_array($data) ? $data : null);

            // 만료된 토큰은 흔한 일이고 호출한 쪽이 정리하므로 경고 로그를 남기지 않는다.
            if (!$this->isInvalidToken($result)) {
                Log::warning('FCM 응답 오류', [
                    'method' => $method,
                    'url'    => $url,
                    'status' => $httpCode,
                    'code'   => $result['error_code'],
                    'body'   => mb_substr((string) $response, 0, 500, 'UTF-8'),
                ]);
            }

            return $result;
        }

        return [
            'success'    => true,
            'status'     => $httpCode,
            'data'       => is_array($data) ? $data : null,
            'error'      => null,
            'error_code' => null,
        ];
    }

    /**
     * FCM 오류 응답에서 원인 코드를 꺼낸다.
     * details 의 FcmError.errorCode(UNREGISTERED 등)가 있으면 그것을, 없으면 error.status 를 쓴다.
     *
     * @param  mixed $data
     * @return string|null
     */
    protected function extractErrorCode($data)
    {
        if (!is_array($data) || !isset($data['error'])) {
            return null;
        }

        if (!empty($data['error']['details']) && is_array($data['error']['details'])) {
            foreach ($data['error']['details'] as $detail) {
                if (!empty($detail['errorCode'])) {
                    return $detail['errorCode'];
                }
            }
        }

        return isset($data['error']['status']) ? $data['error']['status'] : null;
    }

    /**
     * @param  array $messages
     * @param  array $failure
     * @return array
     */
    protected function failAll(array $messages, array $failure)
    {
        return array_fill_keys(array_keys($messages), $failure);
    }

    /**
     * @param  int         $status
     * @param  string      $error
     * @param  string|null $errorCode
     * @param  array|null  $data
     * @return array
     */
    protected function failure($status, $error, $errorCode = null, $data = null)
    {
        return [
            'success'    => false,
            'status'     => $status,
            'data'       => $data,
            'error'      => $error,
            'error_code' => $errorCode,
        ];
    }
}
