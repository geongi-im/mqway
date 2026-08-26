<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * 뉴스 스크랩 [AI분석] 의 본체.
 *
 * 1) NewsArticleExtractor 로 서버에서 본문을 긁는다.
 * 2) 본문 확보에 실패하면 Gemini 의 url_context 도구로 모델이 직접 URL 을 읽게 폴백한다.
 * 3) 한 번의 호출로 4개 파트(해석 / 용어 / 전망 / 질문)를 구조화 JSON 으로 받는다.
 *
 * 4개 파트를 각각 호출하지 않고 한 번에 받는 이유:
 *  - 같은 기사에 대한 4개 결과가 서로 모순되지 않는다 (용어 정의와 해석의 톤이 어긋나지 않음).
 *  - 버튼 한 번에 4배의 지연·비용이 붙지 않는다.
 * 파트별 지침은 프롬프트 안에서 [파트 1]~[파트 4] 블록으로 분리해 각각 관리한다.
 */
class NewsAiAnalyzer
{
    /** 파트5 용어 최대 개수 */
    const MAX_TERMS = 10;

    /**
     * Gemini 응답 대기 상한(초).
     *
     * url_context 폴백은 모델이 URL 을 직접 열기 때문에 20초 가까이 걸린다.
     * 넉넉하게 잡아두되 웹 요청이 무한정 매달리지는 않게 한다.
     */
    const REQUEST_TIMEOUT = 120;

    /** @var NewsArticleExtractor */
    protected $extractor;

    public function __construct(NewsArticleExtractor $extractor = null)
    {
        $this->extractor = $extractor ?: new NewsArticleExtractor();
    }

    /**
     * 뉴스 URL 을 분석한다.
     *
     * @param string $url    뉴스 원문 URL
     * @param array  $context 개인화 맥락
     *                        - reason    : 사용자가 쓴 "이 뉴스를 선택한 이유" 평문
     *                        - ageGroup  : "30대" 같은 연령대 문자열 (없으면 null)
     * @return array {success: bool, message: string|null, data: array|null, meta: array}
     */
    public function analyze($url, array $context = [])
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            return $this->fail('AI 분석 설정이 완료되지 않았습니다. 관리자에게 문의해주세요.', 'missing_api_key');
        }

        $article = $this->extractor->extract($url);
        $useUrlContext = !$article['success'];

        $payload = $this->buildPayload($url, $article, $context, $useUrlContext);
        $response = $this->callGemini($apiKey, $payload);

        if (!$response['success']) {
            return $this->fail($response['message'], $response['reason']);
        }

        $parsed = $this->parseResult($response['text']);

        if ($parsed === null) {
            Log::warning('뉴스 AI 분석 응답 파싱 실패', ['url' => $url, 'raw' => mb_substr($response['text'], 0, 500, 'UTF-8')]);
            return $this->fail('AI 응답을 해석하지 못했습니다. 잠시 후 다시 시도해주세요.', 'parse_failed');
        }

        if (empty($parsed['articleReadable'])) {
            return $this->fail(
                '뉴스 본문을 읽을 수 없었습니다. 로그인이 필요한 기사이거나 삭제된 페이지일 수 있습니다. 원문 링크를 확인해주세요.',
                'article_unreadable'
            );
        }

        return [
            'success' => true,
            'message' => null,
            'data'    => [
                'articleTitle'   => $article['title'],
                'interpretation' => $parsed['interpretation'],
                'terms'          => $parsed['terms'],
                'outlook'        => $parsed['outlook'],
                'questions'      => $parsed['questions'],
            ],
            'meta'    => [
                'model'      => config('services.gemini.model'),
                'source'     => $useUrlContext ? 'url_context' : 'crawl',
                'bodyLength' => $article['length'],
            ],
        ];
    }

    /**
     * Gemini 요청 본문을 만든다.
     *
     * @param string $url
     * @param array  $article
     * @param array  $context
     * @param bool   $useUrlContext
     * @return array
     */
    protected function buildPayload($url, array $article, array $context, $useUrlContext)
    {
        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $this->systemPrompt()],
                ],
            ],
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => $this->userPrompt($url, $article, $context, $useUrlContext)],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature'      => 0.4, // 사실 기반 정리이므로 낮게 잡는다
                'responseMimeType' => 'application/json',
                'responseSchema'   => $this->responseSchema(),
            ],
        ];

        // 서버 크롤링이 실패한 경우에만 모델이 직접 URL 을 열도록 한다.
        if ($useUrlContext) {
            $payload['tools'] = [
                ['url_context' => new \stdClass()],
            ];
        }

        return $payload;
    }

    /**
     * 역할 / 독자 / 금지사항 — 4개 파트 전체에 공통 적용되는 지침.
     *
     * @return string
     */
    protected function systemPrompt()
    {
        return <<<'PROMPT'
<role>
당신은 한국의 경제 뉴스를 경제 초보자에게 풀어주는 경제 교육 코치입니다.
MQWAY 라는 금융·경제 교육 서비스의 "뉴스 스크랩" 기능에서, 사용자가 스크랩한 기사 한 건을 분석해
학습용 정리를 만들어 줍니다.
</role>

<audience>
- 독자는 경제 기사를 이제 꾸준히 읽기 시작한 일반인입니다. 금융 전공자가 아닙니다.
- 중학생도 이해할 수 있는 문장으로 쓰되, 내용을 유아적으로 단순화하지는 않습니다.
- 전문 용어를 쓸 때는 처음 등장할 때 괄호로 짧게 풀어 씁니다.
</audience>

<absolute_rules>
1. 기사 본문에 없는 사실·수치·인용·날짜를 만들어내지 마십시오. 근거가 없으면 해당 항목을 비웁니다.
2. 개별 종목·펀드·코인·부동산 등 특정 투자 상품을 추천하거나 매수/매도를 권하지 마십시오.
3. "반드시 오른다 / 폭락한다" 같은 단정적 예측을 쓰지 마십시오. 전망은 조건과 가능성으로 서술합니다.
4. 독자를 훈계하거나 평가하지 마십시오. ("~해야 합니다" 대신 "~를 살펴볼 수 있습니다")
5. 모든 출력은 한국어입니다. 문장은 '~입니다 / ~합니다' 체로 통일합니다.
6. 마크다운 기호(**, ##, -, * 등)를 쓰지 마십시오. 모든 값은 순수 텍스트로 채웁니다.
7. 기사 문장을 그대로 베끼지 말고 반드시 자신의 문장으로 재구성합니다.
8. 주어진 JSON 스키마를 정확히 지키고, 스키마에 없는 키를 추가하지 마십시오.
</absolute_rules>

<readability_guard>
주어진 페이지가 기사 본문이 아니라 로그인 화면, 오류·삭제 안내, 기사 목록, 광고 페이지이거나
본문을 확인할 수 없다면 articleReadable 을 false 로 두고 나머지 항목은 빈 값으로 두십시오.
이 경우 추측으로 내용을 채우는 것은 명백한 실패입니다.
본문을 정상적으로 확인했다면 articleReadable 을 true 로 둡니다.
</readability_guard>
PROMPT;
    }

    /**
     * 기사 / 사용자 맥락 / 4개 파트 작업 지시.
     *
     * @param string $url
     * @param array  $article
     * @param array  $context
     * @param bool   $useUrlContext
     * @return string
     */
    protected function userPrompt($url, array $article, array $context, $useUrlContext)
    {
        $reason = isset($context['reason']) ? trim((string) $context['reason']) : '';
        $reason = $reason === '' ? '(없음)' : mb_substr($reason, 0, 1500, 'UTF-8');

        $ageGroup = isset($context['ageGroup']) && $context['ageGroup'] !== null
            ? $context['ageGroup']
            : '(알 수 없음)';

        $articleBlock = "<url>{$url}</url>";

        if ($useUrlContext) {
            $articleBlock .= "\n<note>서버에서 본문을 내려받지 못했습니다. 위 URL 을 직접 열어 기사 본문을 확인한 뒤 분석하십시오.</note>";
        } else {
            if (!empty($article['title'])) {
                $articleBlock .= "\n<title>{$article['title']}</title>";
            }
            $articleBlock .= "\n<body>\n{$article['body']}\n</body>";
        }

        $tasks = $this->taskPrompt();

        return <<<PROMPT
<article>
{$articleBlock}
</article>

<user_context>
<age_group>{$ageGroup}</age_group>
<selected_reason>
{$reason}
</selected_reason>
</user_context>

{$tasks}
PROMPT;
    }

    /**
     * 4개 파트별 작성 지침.
     *
     * 파트를 추가하거나 문구를 손볼 때는 이 메서드만 수정하면 된다.
     *
     * @return string
     */
    protected function taskPrompt()
    {
        $maxTerms = self::MAX_TERMS;

        return <<<PROMPT
<tasks>
아래 네 개 파트를 각각의 규칙에 따라 작성하십시오.

[파트 1] interpretation — 뉴스에 대한 짧은 해석
목적: 기사를 읽지 않은 사람이 이 문단만 읽고도 "무슨 일이고 왜 중요한지" 알 수 있게 합니다.
분량: 3~5문장, 250~400자.
아래 순서를 지켜 한 문단으로 씁니다.
 (1) 무슨 일이 있었는가 — 핵심 사실을 한 문장으로. 주체와 행동, 기사에 있다면 핵심 수치를 포함합니다.
 (2) 왜 이런 일이 생겼는가 — 기사에 나온 배경이나 원인.
 (3) 무엇을 의미하는가 — 시장·업계·정책 차원의 함의.
 (4) 보통 사람의 생활·지갑과 어떤 접점이 있는가 — 물가, 금리, 일자리, 세금, 주거, 요금 중 기사와 실제로 닿는 것 하나.
금지: 기사에 없는 배경 지식을 사실처럼 덧붙이기, 기사 문장 그대로 옮기기.

[파트 2] terms — 기사 본문에 등장한 경제 용어 정의 (최대 {$maxTerms}개)
선정 기준:
 - 기사 본문에 실제로 등장한 표현만 고릅니다. 기사에 없는 용어를 끌어오지 않습니다.
 - 이 기사를 이해하는 데 꼭 필요한 것부터 중요도 순으로 배열합니다.
 - "경제", "정부", "회사", "가격" 처럼 설명이 필요 없는 일상어는 제외합니다.
 - 같은 개념의 다른 표기(예: 기준금리 / 정책금리)는 하나로 합칩니다.
 - 기관명·법령명·제도명·지표명·약어도 설명이 필요하면 포함합니다. (예: 공정거래위원회, 대규모유통업법, CPI)
 - 설명이 필요한 용어가 기사에 5개 있으면 5개, 8개 있으면 8개를 담습니다.
   기사에 있는 것을 빠뜨리지도, 기사에 없는 것으로 채우지도 마십시오. 상한은 {$maxTerms}개입니다.
 - 기관명, 법령명, 절차명, 제도명은 특히 빠뜨리기 쉽습니다. 본문을 처음부터 끝까지 훑어 빠진 것이 없는지 확인하십시오.
항목별 작성 규칙:
 - term: 기사에 쓰인 표기 그대로. 영문 약어나 원어가 이해를 도우면 괄호로 덧붙입니다.
         예) "기준금리(Base Rate)", "CPI(소비자물가지수)"
 - definition: 1~2문장, 50~120자. 그 말을 처음 듣는 사람에게 설명하듯 씁니다.
         용어를 정의 안에서 되풀이하지 않습니다. (나쁜 예: "기준금리는 기준이 되는 금리입니다")
         가능하면 "무엇을 재거나 정하는 것인지 + 오르내리면 무슨 뜻인지" 를 함께 담습니다.
 - context: 이 기사에서 그 용어가 어떤 역할로 등장했는지 한 문장(40~80자). 기사 내용에 근거해서 씁니다.

[파트 3] outlook — 향후 전망
outlook.shortTerm: 앞으로 3~6개월 사이에 나타날 수 있는 흐름. 2~4개 항목.
outlook.longTerm: 앞으로 1~3년에 걸친 구조적 변화 가능성. 2~4개 항목.
항목별 작성 규칙:
 - 한 항목은 1~2문장(60~150자)의 완결된 문장입니다. 앞에 기호(-, ·)를 붙이지 않습니다.
 - "조건 → 결과" 구조로 씁니다.
   예) "본안 소송 결과가 나오면, 플랫폼 대상 현장조사의 기준이 다시 정리될 수 있습니다."
 - 반드시 가능성의 언어로 씁니다. "~할 가능성이 있습니다", "~하면 ~할 수 있습니다", "~인지가 갈림길입니다".
 - 근거는 기사에 나온 변수에서 가져옵니다. 진행 중인 절차, 예정된 일정, 언급된 리스크 등.
 - shortTerm 과 longTerm 이 같은 말을 반복하지 않게 나눕니다.
   단기는 이미 예정된 일정·절차 중심, 중장기는 제도·산업구조·소비자 영향 중심으로 씁니다.
금지: 확정적 예측, 목표가·수익률 제시, 개별 투자 상품 언급.

[파트 4] questions — 독자가 자기 경제 상황에 대입해볼 질문 정확히 2개
개인화 근거는 <user_context> 뿐입니다.
 - selected_reason 이 있으면 거기 드러난 관심사·고민·상황(직업, 투자 여부, 가족, 목표 등)을 우선 반영합니다.
 - selected_reason 이 "(없음)" 이거나 관심사가 드러나지 않으면 age_group 의 일반적인 재무 과제를 근거로 씁니다.
   20대: 소득의 시작, 저축 습관, 학자금 / 30대: 주거, 결혼, 자녀, 본격적인 투자
   40대: 자녀 교육비, 소득 정점, 노후 준비 시작 / 50대 이상: 은퇴 설계, 의료비, 소득 구조 전환
 - age_group 도 알 수 없으면 연령을 특정하지 않는 표현으로 씁니다.
 - <user_context> 에 없는 개인 정보(연봉, 자산, 결혼 여부 등)를 있다고 가정하지 마십시오.
두 질문의 역할을 나눕니다.
 - 첫 번째 질문: 이 뉴스와 관련해 "지금 당장 확인해볼 수 있는 것" 을 묻습니다.
   통장, 계약서, 요금제, 청구서처럼 손에 잡히는 대상으로 이어지게 합니다.
 - 두 번째 질문: 이 뉴스가 시사하는 흐름을 "내 1~3년 계획" 과 연결해 묻습니다.
질문별 작성 규칙:
 - 60~140자이며, 반드시 물음표(?) 로 끝나는 한 개의 의문문입니다. 서술문으로 쓰면 실패입니다.
   나쁜 예: "내 지출 내역에 변화가 있었는지 살펴볼 수 있습니다."  (평서문이라 실패)
   나쁜 예: "향후 영향을 예상해 볼 수 있습니다."                  (평서문이라 실패)
   좋은 예: "이번 달 카드 명세서에서 배달앱 결제액이 지난달보다 얼마나 늘었는지 확인해 보시겠습니까?"
   좋은 예: "구독료가 지금보다 20퍼센트 오른다면 어떤 항목을 먼저 줄이시겠습니까?"
 - 예/아니오로 답이 끝나지 않는 열린 질문으로 씁니다. 숫자나 구체적인 행동을 떠올리게 만듭니다.
 - 기사의 핵심 이슈를 질문 안에 반드시 반영합니다. 아무 뉴스에나 붙일 수 있는 일반론은 실패입니다.
 - 정답을 암시하거나 훈계하지 않습니다. 판단은 독자가 합니다.
</tasks>
PROMPT;
    }

    /**
     * 구조화 출력 스키마. 파싱 실패 자체를 없애기 위해 모델에 강제한다.
     *
     * @return array
     */
    protected function responseSchema()
    {
        return [
            'type'       => 'OBJECT',
            'properties' => [
                'articleReadable' => ['type' => 'BOOLEAN'],
                'interpretation'  => ['type' => 'STRING'],
                'terms'           => [
                    'type'     => 'ARRAY',
                    'maxItems' => self::MAX_TERMS,
                    'items'    => [
                        'type'       => 'OBJECT',
                        'properties' => [
                            'term'       => ['type' => 'STRING'],
                            'definition' => ['type' => 'STRING'],
                            'context'    => ['type' => 'STRING'],
                        ],
                        'required'         => ['term', 'definition', 'context'],
                        'propertyOrdering' => ['term', 'definition', 'context'],
                    ],
                ],
                'outlook' => [
                    'type'       => 'OBJECT',
                    'properties' => [
                        'shortTerm' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                        'longTerm'  => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                    ],
                    'required'         => ['shortTerm', 'longTerm'],
                    'propertyOrdering' => ['shortTerm', 'longTerm'],
                ],
                'questions' => [
                    'type'     => 'ARRAY',
                    'maxItems' => 2,
                    'items'    => ['type' => 'STRING'],
                ],
            ],
            'required'         => ['articleReadable', 'interpretation', 'terms', 'outlook', 'questions'],
            'propertyOrdering' => ['articleReadable', 'interpretation', 'terms', 'outlook', 'questions'],
        ];
    }

    /**
     * Gemini generateContent 호출.
     *
     * @param string $apiKey
     * @param array  $payload
     * @return array {success: bool, text: string|null, message: string|null, reason: string|null}
     */
    protected function callGemini($apiKey, array $payload)
    {
        $model = config('services.gemini.model');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => self::REQUEST_TIMEOUT,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error !== '') {
            Log::error('Gemini 뉴스 분석 요청 실패', ['error' => $error]);
            return [
                'success' => false,
                'text'    => null,
                'message' => 'AI 서버에 연결하지 못했습니다. 잠시 후 다시 시도해주세요.',
                'reason'  => 'curl_error',
            ];
        }

        if ($httpCode !== 200) {
            Log::error('Gemini 뉴스 분석 응답 오류', [
                'status' => $httpCode,
                'body'   => mb_substr((string) $response, 0, 1000, 'UTF-8'),
            ]);

            $message = $httpCode === 429
                ? 'AI 분석 요청이 많아 잠시 제한되었습니다. 1~2분 뒤에 다시 시도해주세요.'
                : 'AI 분석에 실패했습니다. 잠시 후 다시 시도해주세요.';

            return ['success' => false, 'text' => null, 'message' => $message, 'reason' => 'http_' . $httpCode];
        }

        $data = json_decode($response, true);

        if (!is_array($data)) {
            return [
                'success' => false,
                'text'    => null,
                'message' => 'AI 응답을 해석하지 못했습니다. 잠시 후 다시 시도해주세요.',
                'reason'  => 'invalid_json',
            ];
        }

        // 안전 필터 등으로 후보가 비어 돌아오는 경우
        if (empty($data['candidates'][0]['content']['parts'])) {
            $finishReason = isset($data['candidates'][0]['finishReason']) ? $data['candidates'][0]['finishReason'] : 'unknown';
            Log::warning('Gemini 뉴스 분석 결과 없음', ['finishReason' => $finishReason]);

            return [
                'success' => false,
                'text'    => null,
                'message' => 'AI가 이 기사를 분석하지 못했습니다. 다른 기사로 시도해주세요.',
                'reason'  => 'no_candidate_' . $finishReason,
            ];
        }

        $text = '';
        foreach ($data['candidates'][0]['content']['parts'] as $part) {
            if (isset($part['text'])) {
                $text .= $part['text'];
            }
        }

        return ['success' => true, 'text' => $text, 'message' => null, 'reason' => null];
    }

    /**
     * 모델 응답 JSON 을 화면에서 바로 쓸 수 있는 형태로 정규화한다.
     *
     * @param string $text
     * @return array|null
     */
    protected function parseResult($text)
    {
        $decoded = json_decode($text, true);

        // responseMimeType 을 줬어도 앞뒤에 군더더기가 붙는 경우를 대비한다.
        if (!is_array($decoded)) {
            $start = strpos($text, '{');
            $end   = strrpos($text, '}');
            if ($start === false || $end === false || $end <= $start) {
                return null;
            }
            $decoded = json_decode(substr($text, $start, $end - $start + 1), true);
        }

        if (!is_array($decoded)) {
            return null;
        }

        return [
            'articleReadable' => !isset($decoded['articleReadable']) || (bool) $decoded['articleReadable'],
            'interpretation'  => $this->cleanText(isset($decoded['interpretation']) ? $decoded['interpretation'] : ''),
            'terms'           => $this->normalizeTerms(isset($decoded['terms']) ? $decoded['terms'] : []),
            'outlook'         => [
                'shortTerm' => $this->normalizeLines(isset($decoded['outlook']['shortTerm']) ? $decoded['outlook']['shortTerm'] : []),
                'longTerm'  => $this->normalizeLines(isset($decoded['outlook']['longTerm']) ? $decoded['outlook']['longTerm'] : []),
            ],
            'questions'       => array_slice($this->normalizeLines(isset($decoded['questions']) ? $decoded['questions'] : []), 0, 2),
        ];
    }

    /**
     * @param mixed $terms
     * @return array
     */
    protected function normalizeTerms($terms)
    {
        if (!is_array($terms)) {
            return [];
        }

        $result = [];

        foreach ($terms as $term) {
            if (!is_array($term)) {
                continue;
            }

            $name = $this->cleanText(isset($term['term']) ? $term['term'] : '');
            $definition = $this->cleanText(isset($term['definition']) ? $term['definition'] : '');

            if ($name === '' || $definition === '') {
                continue;
            }

            $result[] = [
                'term'       => mb_substr($name, 0, 100, 'UTF-8'),
                'definition' => mb_substr($definition, 0, 500, 'UTF-8'),
                'context'    => mb_substr($this->cleanText(isset($term['context']) ? $term['context'] : ''), 0, 300, 'UTF-8'),
                'checked'    => false, // 사용자가 체크하기 전 기본값
            ];

            if (count($result) >= self::MAX_TERMS) {
                break;
            }
        }

        return $result;
    }

    /**
     * 문자열 배열에서 빈 값과 앞머리 기호를 걷어낸다.
     *
     * @param mixed $lines
     * @return array
     */
    protected function normalizeLines($lines)
    {
        if (is_string($lines)) {
            $lines = preg_split('/\r\n|\r|\n/', $lines);
        }

        if (!is_array($lines)) {
            return [];
        }

        $result = [];

        foreach ($lines as $line) {
            if (!is_string($line)) {
                continue;
            }

            $clean = $this->cleanText($line);
            if ($clean !== '') {
                $result[] = mb_substr($clean, 0, 500, 'UTF-8');
            }
        }

        return $result;
    }

    /**
     * 마크다운 잔여물과 불릿 기호를 제거한다. (프롬프트로 금지했지만 방어적으로 한 번 더)
     *
     * @param mixed $text
     * @return string
     */
    protected function cleanText($text)
    {
        if (!is_string($text)) {
            return '';
        }

        $text = preg_replace('/^\s*(?:[-*\x{2022}\x{00B7}\x{25CF}]|\d+[.)])\s+/u', '', $text);
        $text = preg_replace('/\*{1,3}([^*]+)\*{1,3}/u', '$1', $text);
        $text = preg_replace('/^#{1,6}\s*/u', '', $text);
        $text = preg_replace('/[ \t]+/u', ' ', $text);

        return trim($text);
    }

    /**
     * @param string $message
     * @param string $reason
     * @return array
     */
    protected function fail($message, $reason)
    {
        return [
            'success' => false,
            'message' => $message,
            'data'    => null,
            'meta'    => ['reason' => $reason],
        ];
    }
}
