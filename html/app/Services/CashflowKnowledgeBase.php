<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * 캐시플로우 챗봇이 답변 근거로 삼는 참고 자료 로더.
 *
 * resources/knowledge/cashflow/*.md 를 파일명 오름차순으로 읽어 하나의 <reference> 블록으로
 * 묶는다. CashflowChatBot 이 이 블록을 시스템 프롬프트 뒤에 붙인다.
 *
 * 자료를 파일로 분리해 둔 이유는 규칙·카드 데이터를 채워 넣을 때 PHP 를 건드리지 않기 위해서다.
 * 파일을 추가하면 다음 요청부터 바로 반영된다.
 *
 * 향후 자료가 수만 자 규모로 커지면:
 *  - 매 요청마다 전체를 다시 보내므로 입력 토큰이 선형으로 늘어난다.
 *  - 그 시점에는 Gemini context caching (cachedContents) 으로 옮겨 캐시 이름만 넘기는 편이 싸다.
 *  - 지금은 자료가 작고 파일 교체가 잦을 것으로 보아 단순 주입을 택했다.
 */
class CashflowKnowledgeBase
{
    /** 자료가 놓이는 디렉터리 (resource_path 기준 상대 경로) */
    const DIR = 'knowledge/cashflow';

    /**
     * 주입 총량 상한(문자).
     *
     * 넘으면 뒤쪽 파일부터 통째로 제외한다. 파일 중간에서 자르면 모델이 잘린 문장을
     * 사실로 받아들일 수 있어, 자르지 않고 파일 단위로 버린다.
     */
    const MAX_CHARS = 60000;

    /**
     * 같은 요청 안에서 두 번 읽지 않기 위한 메모이제이션.
     *
     * @var string|null
     */
    protected $cached;

    /**
     * 모델에 넣을 <reference> 블록을 만든다. 자료가 없으면 빈 문자열.
     *
     * @return string
     */
    public function toPromptBlock()
    {
        if ($this->cached !== null) {
            return $this->cached;
        }

        $documents = $this->documents();

        if (empty($documents)) {
            $this->cached = '';
            return $this->cached;
        }

        $blocks = [];

        foreach ($documents as $name => $body) {
            $blocks[] = "<document name=\"{$name}\">\n{$body}\n</document>";
        }

        $this->cached = "<reference>\n" . implode("\n\n", $blocks) . "\n</reference>";

        return $this->cached;
    }

    /**
     * 주입 대상 자료를 [파일명 => 본문] 으로 읽는다.
     *
     * README.md 와 '_' 로 시작하는 파일(작업 중 초안)은 제외한다.
     *
     * @return array
     */
    public function documents()
    {
        $dir = resource_path(self::DIR);

        if (!is_dir($dir)) {
            return [];
        }

        $paths = glob($dir . DIRECTORY_SEPARATOR . '*.md');

        if ($paths === false) {
            return [];
        }

        sort($paths, SORT_STRING);

        $documents = [];
        $total = 0;
        $skipped = [];

        foreach ($paths as $path) {
            $name = basename($path);

            if ($name === 'README.md' || strpos($name, '_') === 0) {
                continue;
            }

            $body = trim((string) file_get_contents($path));

            if ($body === '') {
                continue;
            }

            $length = mb_strlen($body, 'UTF-8');

            // 상한을 넘기는 파일은 통째로 건너뛴다. 잘린 자료를 주는 것보다 없는 편이 안전하다.
            if ($total + $length > self::MAX_CHARS) {
                $skipped[] = $name;
                continue;
            }

            $documents[$name] = $body;
            $total += $length;
        }

        if (!empty($skipped)) {
            Log::warning('캐시플로우 참고 자료가 상한을 넘어 일부를 제외했습니다.', [
                'limit'   => self::MAX_CHARS,
                'used'    => $total,
                'skipped' => $skipped,
            ]);
        }

        return $documents;
    }

    /**
     * 주입할 자료가 하나도 없는 상태인지.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->toPromptBlock() === '';
    }
}
