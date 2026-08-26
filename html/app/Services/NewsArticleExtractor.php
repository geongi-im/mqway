<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * 뉴스 URL에서 제목 / 본문 텍스트를 추출한다.
 *
 * AI 분석의 1차 입력원이다. 여기서 본문을 충분히 확보하지 못하면
 * NewsAiAnalyzer 가 Gemini 의 url_context 도구로 폴백한다.
 */
class NewsArticleExtractor
{
    /** 본문으로 인정할 최소 길이 (이보다 짧으면 추출 실패로 간주) */
    const MIN_BODY_LENGTH = 300;

    /** 프롬프트에 실어보낼 본문 최대 길이 */
    const MAX_BODY_LENGTH = 12000;

    /** 내려받을 HTML 최대 바이트 (과도한 응답 방어) */
    const MAX_HTML_BYTES = 3145728; // 3MB

    /**
     * 국내 주요 언론사 본문 컨테이너. 위에 있는 것부터 우선 적용한다.
     *
     * XPath 로 평가하므로 CSS 선택자가 아니라 XPath 표현식으로 적는다.
     */
    protected $bodyXPaths = [
        "//*[@id='dic_area']",                     // 네이버 뉴스 (신)
        "//*[@id='newsct_article']",               // 네이버 뉴스 (신, 래퍼)
        "//*[@id='articleBodyContents']",          // 네이버 뉴스 (구)
        "//*[@id='articeBody']",                   // 네이버 스포츠/엔터
        "//*[@id='article-view-content-div']",     // 다수의 지역지 (에디터 기반 CMS)
        "//*[@itemprop='articleBody']",
        "//*[@id='articleBody']",
        "//*[@id='article_body']",
        "//*[@id='newsView']",
        "//*[@id='CmAdContent']",                  // 조선비즈 계열
        "//*[contains(concat(' ', normalize-space(@class), ' '), ' article-body ')]",
        "//*[contains(concat(' ', normalize-space(@class), ' '), ' article_body ')]",
        "//*[contains(concat(' ', normalize-space(@class), ' '), ' news_body ')]",
        "//*[contains(concat(' ', normalize-space(@class), ' '), ' art_txt ')]",
        "//*[contains(concat(' ', normalize-space(@class), ' '), ' story-news ')]",
        "//*[contains(concat(' ', normalize-space(@class), ' '), ' articleView ')]",
        "//article",
    ];

    /** 본문 후보에서 무조건 제거할 노드 */
    protected $stripTags = [
        'script', 'style', 'noscript', 'iframe', 'form', 'button', 'select',
        'nav', 'header', 'footer', 'aside', 'figcaption', 'svg', 'template',
    ];

    /**
     * URL 에서 기사 정보를 추출한다.
     *
     * 실패해도 예외를 던지지 않는다. success=false 로 알리고 호출측이 폴백을 결정한다.
     *
     * @param string $url
     * @return array {success: bool, title: string|null, body: string, length: int, reason: string|null}
     */
    public function extract($url)
    {
        $empty = [
            'success' => false,
            'title'   => null,
            'body'    => '',
            'length'  => 0,
            'reason'  => null,
        ];

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $empty['reason'] = 'invalid_url';
            return $empty;
        }

        $html = $this->fetchHtml($url);

        if ($html === null || $html === '') {
            $empty['reason'] = 'fetch_failed';
            return $empty;
        }

        try {
            $dom = $this->toDom($html);
        } catch (\Exception $e) {
            Log::warning('뉴스 본문 파싱 실패', ['url' => $url, 'error' => $e->getMessage()]);
            $empty['reason'] = 'parse_failed';
            return $empty;
        }

        $xpath = new \DOMXPath($dom);

        $title = $this->extractTitle($xpath);
        $body  = $this->extractBody($xpath);

        $length = mb_strlen($body, 'UTF-8');

        if ($length > self::MAX_BODY_LENGTH) {
            $body   = mb_substr($body, 0, self::MAX_BODY_LENGTH, 'UTF-8');
            $length = self::MAX_BODY_LENGTH;
        }

        return [
            'success' => $length >= self::MIN_BODY_LENGTH,
            'title'   => $title,
            'body'    => $body,
            'length'  => $length,
            'reason'  => $length >= self::MIN_BODY_LENGTH ? null : 'body_too_short',
        ];
    }

    /**
     * HTML 을 내려받아 UTF-8 문자열로 반환한다.
     *
     * @param string $url
     * @return string|null
     */
    protected function fetchHtml($url)
    {
        if (!function_exists('curl_init')) {
            return null;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_ENCODING       => '',
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER     => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: ko-KR,ko;q=0.9,en;q=0.8',
            ],
        ]);

        // 응답이 지나치게 크면 중단한다.
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $downloadSize, $downloaded) {
            return $downloaded > self::MAX_HTML_BYTES ? 1 : 0;
        });

        $body       = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error      = curl_error($ch);
        curl_close($ch);

        if ($error !== '' || $body === false || $statusCode < 200 || $statusCode >= 400) {
            Log::info('뉴스 HTML 요청 실패', [
                'url'    => $url,
                'status' => $statusCode,
                'error'  => $error,
            ]);
            return null;
        }

        return $this->toUtf8($body);
    }

    /**
     * meta charset 을 보고 UTF-8 로 변환한다. (국내 언론사에 EUC-KR 이 아직 남아있다)
     *
     * @param string $html
     * @return string
     */
    protected function toUtf8($html)
    {
        $charset = null;

        if (preg_match('/<meta[^>]+charset\s*=\s*["\']?\s*([a-zA-Z0-9_\-]+)/i', $html, $m)) {
            $charset = strtoupper($m[1]);
        }

        if ($charset === null || $charset === 'UTF-8' || $charset === 'UTF8') {
            return $html;
        }

        // EUC-KR / KS_C_5601-1987 등은 CP949 로 읽으면 확장 문자까지 처리된다.
        if (strpos($charset, 'EUC-KR') !== false || strpos($charset, '5601') !== false || $charset === 'CP949') {
            $charset = 'CP949';
        }

        $converted = @mb_convert_encoding($html, 'UTF-8', $charset);

        return $converted !== false && $converted !== '' ? $converted : $html;
    }

    /**
     * @param string $html
     * @return \DOMDocument
     */
    protected function toDom($html)
    {
        // loadHTML 은 meta charset 을 믿기 때문에 UTF-8 임을 명시해준다.
        $html = preg_replace('/<meta[^>]+charset[^>]*>/i', '', $html, 1);
        $html = '<?xml encoding="UTF-8">' . $html;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();

        return $dom;
    }

    /**
     * og:title > twitter:title > <title> 순으로 제목을 찾는다.
     *
     * @param \DOMXPath $xpath
     * @return string|null
     */
    protected function extractTitle(\DOMXPath $xpath)
    {
        $queries = [
            "//meta[@property='og:title']/@content",
            "//meta[@name='twitter:title']/@content",
            "//title",
            "//h1",
        ];

        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes !== false && $nodes->length > 0) {
                $title = $this->normalize($nodes->item(0)->nodeValue);
                if ($title !== '') {
                    // "기사 제목 | 언론사명" 형태에서 뒤쪽 꼬리를 떼어낸다.
                    $title = preg_replace('/\s*[|\x{FF5C}]\s*[^|\x{FF5C}]{1,20}$/u', '', $title);
                    return mb_substr($title, 0, 500, 'UTF-8');
                }
            }
        }

        return null;
    }

    /**
     * 알려진 컨테이너 → 텍스트가 가장 많은 블록 순으로 본문을 찾는다.
     *
     * @param \DOMXPath $xpath
     * @return string
     */
    protected function extractBody(\DOMXPath $xpath)
    {
        $this->removeNoise($xpath);

        foreach ($this->bodyXPaths as $query) {
            $nodes = $xpath->query($query);
            if ($nodes === false || $nodes->length === 0) {
                continue;
            }

            $best = '';
            foreach ($nodes as $node) {
                $text = $this->nodeText($node);
                if (mb_strlen($text, 'UTF-8') > mb_strlen($best, 'UTF-8')) {
                    $best = $text;
                }
            }

            if (mb_strlen($best, 'UTF-8') >= self::MIN_BODY_LENGTH) {
                return $best;
            }
        }

        // 폴백: <p> 가 가장 많이 모여있는 블록을 본문으로 본다.
        $fallback = $this->densestTextBlock($xpath);
        if (mb_strlen($fallback, 'UTF-8') >= self::MIN_BODY_LENGTH) {
            return $fallback;
        }

        // 최후 폴백: og:description 이라도 넘긴다.
        $desc = $xpath->query("//meta[@property='og:description']/@content");
        if ($desc !== false && $desc->length > 0) {
            return $this->normalize($desc->item(0)->nodeValue);
        }

        return '';
    }

    /**
     * 스크립트 / 내비게이션 / 관련기사 등 본문이 아닌 노드를 DOM 에서 제거한다.
     *
     * @param \DOMXPath $xpath
     * @return void
     */
    protected function removeNoise(\DOMXPath $xpath)
    {
        $queries = [];

        foreach ($this->stripTags as $tag) {
            $queries[] = '//' . $tag;
        }

        // 광고 / 관련기사 / 기자 프로필 같은 상투적인 클래스명
        $lower = "translate(%s,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')";
        $noiseKeywords = ['banner', 'related', 'reporter', 'copyright', 'comment', 'promotion', 'recommend', 'advertis'];
        foreach ($noiseKeywords as $keyword) {
            $queries[] = "//*[contains(" . sprintf($lower, '@class') . ",'" . $keyword . "')]";
            $queries[] = "//*[contains(" . sprintf($lower, '@id') . ",'" . $keyword . "')]";
        }

        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes === false) {
                continue;
            }

            // 순회 중 DOM 을 바꾸면 NodeList 가 흔들리므로 먼저 모아둔다.
            $targets = [];
            foreach ($nodes as $node) {
                $targets[] = $node;
            }

            foreach ($targets as $node) {
                if ($node->parentNode) {
                    $node->parentNode->removeChild($node);
                }
            }
        }
    }

    /**
     * <p> 텍스트가 가장 많이 모인 부모 노드를 찾는다. (본문 컨테이너 추정)
     *
     * @param \DOMXPath $xpath
     * @return string
     */
    protected function densestTextBlock(\DOMXPath $xpath)
    {
        $paragraphs = $xpath->query('//p');
        if ($paragraphs === false || $paragraphs->length === 0) {
            return '';
        }

        $scores = [];
        $nodes  = [];

        foreach ($paragraphs as $p) {
            $text = $this->normalize($p->textContent);
            if (mb_strlen($text, 'UTF-8') < 30) {
                continue; // 캡션 / 메뉴 같은 짧은 문단은 점수에 넣지 않는다
            }

            $parent = $p->parentNode;
            if (!$parent instanceof \DOMElement) {
                continue;
            }

            $key = $parent->getNodePath();
            if (!isset($scores[$key])) {
                $scores[$key] = 0;
                $nodes[$key]  = $parent;
            }
            $scores[$key] += mb_strlen($text, 'UTF-8');
        }

        if (empty($scores)) {
            return '';
        }

        arsort($scores);
        $bestKey = key($scores);

        return $this->nodeText($nodes[$bestKey]);
    }

    /**
     * 노드의 텍스트를 문단 구분을 살려 추출한다.
     *
     * @param \DOMNode $node
     * @return string
     */
    protected function nodeText(\DOMNode $node)
    {
        $html = $node->ownerDocument->saveHTML($node);

        if ($html === false) {
            return $this->normalize($node->textContent);
        }

        // <br>, </p>, </div> 를 줄바꿈으로 바꿔 문단 경계를 남긴다.
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('#</(p|div|li|h[1-6]|tr|blockquote)>#i', "\n", $html);

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $this->normalize($text, true);
    }

    /**
     * 공백 정리. $keepNewlines 면 문단 줄바꿈은 유지한다.
     *
     * @param string $text
     * @param bool $keepNewlines
     * @return string
     */
    protected function normalize($text, $keepNewlines = false)
    {
        $text = str_replace(["\xC2\xA0", "\xE2\x80\x8B"], ' ', (string) $text); // NBSP, zero-width space

        if ($keepNewlines) {
            $text = preg_replace('/[ \t\x{3000}]+/u', ' ', $text);
            $text = preg_replace('/\s*\n\s*/u', "\n", $text);
            $text = preg_replace('/\n{2,}/u', "\n", $text);
        } else {
            $text = preg_replace('/\s+/u', ' ', $text);
        }

        return trim($text);
    }
}
