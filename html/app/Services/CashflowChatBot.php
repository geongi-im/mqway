<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * 캐시플로우 챗봇 본체.
 *
 * 이전 구현(GeminiBotController)을 통째로 대체한다. 바뀐 핵심은 세 가지다.
 *
 * 1) alt=sse 로 호출한다.
 *    예전에는 :streamGenerateContent 를 그냥 불러서 응답이 JSON '배열' 로 왔고,
 *    서버와 브라우저가 각자 정규식으로 쉼표·대괄호를 잘라내며 조립했다. 청크가 객체 중간을
 *    가르면 그대로 깨졌다. alt=sse 를 붙이면 표준 SSE 로 한 줄에 완결된 JSON 이 온다.
 *
 * 2) 이스케이프를 두 번 풀지 않는다.
 *    json_decode 가 이미 \uXXXX 와 \n 을 처리한다. 예전 코드는 그 위에 다시 언이스케이프를
 *    돌려서 본문에 백슬래시가 있으면 손상됐다.
 *
 * 3) 프롬프트와 참고 자료를 코드 밖으로 뺐다.
 *    resources/prompts/cashflow/system.md 와 resources/knowledge/cashflow/*.md 만 고치면
 *    배포 없이 챗봇의 지식과 성격이 바뀐다.
 *
 * 인증은 NewsAiAnalyzer 와 같이 x-goog-api-key 헤더로 넘긴다. 쿼리스트링(?key=)은
 * 액세스 로그와 프록시에 키가 남는다.
 */
class CashflowChatBot
{
    /** 사용자 한 발화의 최대 길이 */
    const MAX_MESSAGE_CHARS = 2000;

    /** 이미지 원본(디코딩 후) 최대 바이트. Gemini inline_data 제한에 맞춘다. */
    const MAX_IMAGE_BYTES = 4194304;

    /** 허용하는 이미지 형식. 프런트가 JPEG 로 정규화하지만 서버도 독립적으로 검증한다. */
    const ALLOWED_IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif'];

    /** 답변 길이 상한. 규칙 안내라 길 필요가 없고, 길수록 세션과 지연이 함께 커진다. */
    const MAX_OUTPUT_TOKENS = 2048;

    /**
     * 규칙 안내이므로 낮게 잡는다. 다만 대화체라 뉴스 분석(0.4)보다는 조금 높인다.
     */
    const TEMPERATURE = 0.6;

    const CONNECT_TIMEOUT = 10;

    /** 스트리밍 전체 대기 상한(초) */
    const REQUEST_TIMEOUT = 120;

    /** @var CashflowKnowledgeBase */
    protected $knowledge;

    public function __construct(CashflowKnowledgeBase $knowledge = null)
    {
        $this->knowledge = $knowledge ?: new CashflowKnowledgeBase();
    }

    /**
     * 사용자가 보낸 data URI 이미지를 검증해 Gemini inline_data 형태로 바꾼다.
     *
     * mime 을 하드코딩하지 않고 data URI 에 적힌 값을 쓴다. 예전 구현은 무엇을 받든
     * image/jpeg 로 보내서, 2MB 이하 PNG 는 형식을 속인 채 전달됐다.
     *
     * @param  mixed $raw "data:image/png;base64,...."
     * @return array {success: bool, image: array|null, message: string|null}
     */
    public function parseImage($raw)
    {
        if ($raw === null || $raw === '') {
            return ['success' => true, 'image' => null, 'message' => null];
        }

        if (!is_string($raw) || !preg_match('#^data:(image/[a-z0-9.+-]+);base64,(.+)$#is', trim($raw), $matches)) {
            return ['success' => false, 'image' => null, 'message' => '이미지를 읽을 수 없습니다. 다시 첨부해주세요.'];
        }

        $mime = strtolower($matches[1]);

        if (!in_array($mime, self::ALLOWED_IMAGE_MIMES, true)) {
            return ['success' => false, 'image' => null, 'message' => 'JPG, PNG, WEBP 이미지만 첨부할 수 있습니다.'];
        }

        $binary = base64_decode($matches[2], true);

        if ($binary === false) {
            return ['success' => false, 'image' => null, 'message' => '이미지를 읽을 수 없습니다. 다시 첨부해주세요.'];
        }

        if (strlen($binary) > self::MAX_IMAGE_BYTES) {
            return ['success' => false, 'image' => null, 'message' => '이미지가 너무 큽니다. 4MB 이하로 첨부해주세요.'];
        }

        // 확장자만 바꾼 파일을 걸러낸다. getimagesizefromstring 은 실제 바이트를 본다.
        $info = @getimagesizefromstring($binary);

        if ($info === false) {
            return ['success' => false, 'image' => null, 'message' => '이미지 파일이 아닌 것 같습니다. 다시 첨부해주세요.'];
        }

        return [
            'success' => true,
            'image'   => ['mime' => $mime, 'data' => base64_encode($binary)],
            'message' => null,
        ];
    }

    /**
     * 한 번의 발화를 모델에 보내고, 도착하는 텍스트 조각을 $onDelta 로 흘려보낸다.
     *
     * @param string        $message  사용자 텍스트 (이미지만 보낸 경우 빈 문자열)
     * @param array|null    $image    parseImage() 결과의 image
     * @param array         $history  CashflowChatHistory::all()
     * @param array         $context  게임 상태 등 개인화 맥락. 지금은 항상 비어 있다.
     *                                (연결부만 열어둔 상태 — buildContextBlock 주석 참고)
     * @param callable      $onDelta  function (string $chunk) : void
     * @return array {success: bool, text: string, message: string|null, reason: string|null}
     */
    public function stream($message, $image, array $history, array $context, callable $onDelta)
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            return $this->fail('챗봇 설정이 완료되지 않았습니다. 관리자에게 문의해주세요.', 'missing_api_key');
        }

        $model = config('services.gemini.model');

        if (empty($model)) {
            return $this->fail('챗봇 설정이 완료되지 않았습니다. 관리자에게 문의해주세요.', 'missing_model');
        }

        $payload = $this->buildPayload($message, $image, $history, $context);

        return $this->callGemini($apiKey, $model, $payload, $onDelta);
    }

    /**
     * Gemini 요청 본문.
     *
     * @param string     $message
     * @param array|null $image
     * @param array      $history
     * @param array      $context
     * @return array
     */
    protected function buildPayload($message, $image, array $history, array $context)
    {
        $contents = [];

        foreach ($history as $entry) {
            $contents[] = [
                'role'  => $entry['role'],
                'parts' => [['text' => $entry['text']]],
            ];
        }

        $contents[] = [
            'role'  => 'user',
            'parts' => $this->buildUserParts($message, $image),
        ];

        return [
            'systemInstruction' => [
                'parts' => [['text' => $this->systemPrompt($context)]],
            ],
            'contents'         => $contents,
            'generationConfig' => [
                'temperature'     => self::TEMPERATURE,
                'maxOutputTokens' => self::MAX_OUTPUT_TOKENS,
            ],
        ];
    }

    /**
     * 이번 발화의 parts. 이미지가 있으면 이미지를 먼저 둔다.
     *
     * @param string     $message
     * @param array|null $image
     * @return array
     */
    protected function buildUserParts($message, $image)
    {
        $parts = [];

        if (!empty($image)) {
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $image['mime'],
                    'data'      => $image['data'],
                ],
            ];
        }

        $text = trim((string) $message);

        if ($text === '' && !empty($image)) {
            $text = '이 이미지가 캐시플로우 게임의 무엇인지 설명하고, 지금 제가 고를 수 있는 선택지를 알려주세요.';
        }

        $parts[] = ['text' => $text];

        return $parts;
    }

    /**
     * 시스템 지시문 = 프롬프트 파일 + 참고 자료 + 개인화 맥락.
     *
     * @param array $context
     * @return string
     */
    protected function systemPrompt(array $context)
    {
        $blocks = [$this->basePrompt()];

        $reference = $this->knowledge->toPromptBlock();

        if ($reference !== '') {
            $blocks[] = $reference;
        } else {
            // 자료가 아직 없는 상태를 모델에게 숨기지 않는다. 근거가 없으면 없다고 말하게 한다.
            $blocks[] = "<reference>\n(참고 자료가 아직 등록되지 않았습니다. 일반적인 캐시플로우 규칙 지식으로 답하되,"
                . " 세부 수치나 카드별 내용은 확실하지 않다는 점을 밝히십시오.)\n</reference>";
        }

        $contextBlock = $this->buildContextBlock($context);

        if ($contextBlock !== '') {
            $blocks[] = $contextBlock;
        }

        return implode("\n\n", $blocks);
    }

    /**
     * resources/prompts/cashflow/system.md 를 읽는다.
     *
     * 파일이 없거나 비면 내장 기본값으로 떨어진다. 배포 중 파일이 빠져도 챗봇이
     * 지시문 없이 응답하는 상황은 만들지 않는다.
     *
     * @return string
     */
    protected function basePrompt()
    {
        $path = resource_path('prompts/cashflow/system.md');

        if (!is_file($path)) {
            Log::warning('캐시플로우 시스템 프롬프트 파일이 없어 기본값을 사용합니다.', ['path' => $path]);
            return $this->fallbackSystemPrompt();
        }

        $body = (string) file_get_contents($path);

        // 편집용 HTML 주석은 모델에 보내지 않는다.
        $body = trim(preg_replace('/<!--.*?-->/s', '', $body));

        if ($body === '') {
            Log::warning('캐시플로우 시스템 프롬프트가 비어 있어 기본값을 사용합니다.', ['path' => $path]);
            return $this->fallbackSystemPrompt();
        }

        return $body;
    }

    /**
     * 프롬프트 파일을 읽지 못했을 때만 쓰는 최소 지시문.
     *
     * @return string
     */
    protected function fallbackSystemPrompt()
    {
        return <<<'PROMPT'
<role>
당신은 보드게임 "캐시플로우(CASHFLOW)"의 규칙과 진행을 한국어로 안내하는 도우미입니다.
</role>

<rules>
- 캐시플로우 게임과 관련된 내용만 답합니다. 그 밖의 질문에는
  "죄송합니다. 캐시플로우 게임과 관련된 내용만 도와드릴 수 있습니다." 라고만 답합니다.
- 확실하지 않은 세부 규칙이나 수치는 지어내지 말고 모른다고 밝힙니다.
- 게임 밖의 실제 투자 상품을 추천하지 않습니다.
- 한국어 '~입니다 / ~합니다' 체로, 마크다운을 써서 짧고 읽기 쉽게 정리합니다.
</rules>
PROMPT;
    }

    /**
     * 개인화 맥락 블록.
     *
     * 지금은 $context 가 항상 비어 있어 빈 문자열을 돌려준다. 게임 상태 연동은 이 메서드와
     * CashflowChatController::buildContext() 두 곳만 채우면 된다.
     *
     * 나중에 mq_cashflow_games / assets / liabilities 를 물릴 때 넣을 키(예상):
     *  - profession   : 직업 카드 이름
     *  - salary       : 월 급여
     *  - passiveIncome: 불로소득 합계
     *  - expenses     : 총 지출
     *  - cash         : 보유 현금
     *  - assets       : [['name' => ..., 'cashflow' => ...], ...]
     *  - liabilities  : [['name' => ..., 'balance' => ...], ...]
     * 값을 넣기 시작하면 프롬프트 쪽에도 "이 수치는 사용자의 실제 게임 상태다" 는 지침을
     * 함께 추가해야 한다. 맥락만 주고 쓰는 법을 안 주면 모델이 무시한다.
     *
     * @param array $context
     * @return string
     */
    protected function buildContextBlock(array $context)
    {
        if (empty($context)) {
            return '';
        }

        $lines = [];

        foreach ($context as $key => $value) {
            if ($value === null || $value === '' || is_array($value)) {
                continue;
            }

            $lines[] = "<{$key}>{$value}</{$key}>";
        }

        if (empty($lines)) {
            return '';
        }

        return "<user_context>\n" . implode("\n", $lines) . "\n</user_context>";
    }

    /**
     * streamGenerateContent?alt=sse 호출.
     *
     * @param string   $apiKey
     * @param string   $model
     * @param array    $payload
     * @param callable $onDelta
     * @return array
     */
    protected function callGemini($apiKey, $model, array $payload, callable $onDelta)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:streamGenerateContent?alt=sse";

        $status       = 0;
        $buffer       = '';
        $errorBody    = '';
        $text         = '';
        $finishReason = null;
        $blockReason  = null;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT        => self::REQUEST_TIMEOUT,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: text/event-stream',
                'x-goog-api-key: ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        // 본문보다 헤더가 먼저 온다. 200 이 아니면 아래 write 콜백이 SSE 대신 에러 본문을 모은다.
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$status) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $header, $matches)) {
                $status = (int) $matches[1];
            }

            return strlen($header);
        });

        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $chunk) use (
            &$status, &$buffer, &$errorBody, &$text, &$finishReason, &$blockReason, $onDelta
        ) {
            $length = strlen($chunk);

            if ($status !== 200) {
                $errorBody .= $chunk;
                return $length;
            }

            // 사용자가 창을 닫았거나 중단을 눌렀다. 0 을 돌려주면 curl 이 전송을 끊는다.
            if (connection_aborted()) {
                return 0;
            }

            $buffer .= $chunk;

            // SSE 는 줄 단위다. 청크가 줄 중간을 가를 수 있으므로 완결된 줄만 처리하고 나머지는 남긴다.
            while (($position = strpos($buffer, "\n")) !== false) {
                $line   = rtrim(substr($buffer, 0, $position), "\r");
                $buffer = substr($buffer, $position + 1);

                $this->handleSseLine($line, $text, $finishReason, $blockReason, $onDelta);
            }

            return $length;
        });

        curl_exec($ch);

        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        // 클라이언트가 끊어서 우리가 중단시킨 경우다. 오류로 볼 필요가 없다.
        if ($curlErrno === CURLE_WRITE_ERROR && connection_aborted()) {
            return ['success' => false, 'text' => $text, 'message' => null, 'reason' => 'client_aborted'];
        }

        if ($curlErrno !== 0) {
            Log::error('캐시플로우 챗봇 요청 실패', ['errno' => $curlErrno, 'error' => $curlError]);
            return $this->fail('AI 서버에 연결하지 못했습니다. 잠시 후 다시 시도해주세요.', 'curl_' . $curlErrno, $text);
        }

        if ($status !== 200) {
            Log::error('캐시플로우 챗봇 응답 오류', [
                'status' => $status,
                'body'   => mb_substr($errorBody, 0, 1000, 'UTF-8'),
            ]);

            $message = $status === 429
                ? '요청이 몰려 잠시 제한되었습니다. 1~2분 뒤에 다시 시도해주세요.'
                : '챗봇 응답에 실패했습니다. 잠시 후 다시 시도해주세요.';

            return $this->fail($message, 'http_' . $status, $text);
        }

        if ($blockReason !== null) {
            Log::warning('캐시플로우 챗봇 입력이 차단되었습니다.', ['blockReason' => $blockReason]);
            return $this->fail('이 질문에는 답변할 수 없습니다. 다른 방식으로 물어봐 주세요.', 'blocked_' . $blockReason, $text);
        }

        if (trim($text) === '') {
            Log::warning('캐시플로우 챗봇 응답이 비었습니다.', ['finishReason' => $finishReason]);
            return $this->fail('답변을 만들지 못했습니다. 질문을 조금 바꿔서 다시 시도해주세요.', 'empty_' . ($finishReason ?: 'unknown'));
        }

        return ['success' => true, 'text' => $text, 'message' => null, 'reason' => $finishReason];
    }

    /**
     * SSE 한 줄을 처리한다.
     *
     * alt=sse 에서는 "data: {완결된 JSON}" 이 한 줄로 온다. 이벤트 구분용 빈 줄과 주석(:)은 무시한다.
     *
     * @param string      $line
     * @param string      $text         누적 응답 (참조)
     * @param string|null $finishReason (참조)
     * @param string|null $blockReason  (참조)
     * @param callable    $onDelta
     * @return void
     */
    protected function handleSseLine($line, &$text, &$finishReason, &$blockReason, callable $onDelta)
    {
        if (strpos($line, 'data:') !== 0) {
            return;
        }

        $json = trim(substr($line, 5));

        if ($json === '' || $json === '[DONE]') {
            return;
        }

        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            Log::warning('캐시플로우 챗봇 SSE 파싱 실패', ['line' => mb_substr($json, 0, 300, 'UTF-8')]);
            return;
        }

        if (isset($decoded['promptFeedback']['blockReason'])) {
            $blockReason = $decoded['promptFeedback']['blockReason'];
            return;
        }

        if (isset($decoded['candidates'][0]['finishReason'])) {
            $finishReason = $decoded['candidates'][0]['finishReason'];
        }

        if (!isset($decoded['candidates'][0]['content']['parts'])) {
            return;
        }

        foreach ($decoded['candidates'][0]['content']['parts'] as $part) {
            // json_decode 가 \uXXXX 와 \n 을 이미 풀었다. 여기서 다시 손대면 본문이 망가진다.
            if (!isset($part['text']) || $part['text'] === '') {
                continue;
            }

            $text .= $part['text'];
            $onDelta($part['text']);
        }
    }

    /**
     * @param string $message
     * @param string $reason
     * @param string $text
     * @return array
     */
    protected function fail($message, $reason, $text = '')
    {
        return ['success' => false, 'text' => $text, 'message' => $message, 'reason' => $reason];
    }
}
