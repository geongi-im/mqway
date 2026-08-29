<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

/**
 * 캐시플로우 챗봇 대화 이력. 세션에만 보관한다.
 *
 * 저장 형태를 Gemini 규격(role = 'user' | 'model')과 똑같이 맞춰 둔다.
 * 이전 구현은 세션에 'assistant' 로 담고 API 로 보낼 때 다시 변환하다가 역할값을 틀렸고,
 * 그 결과 두 번째 메시지부터 400 이 났다. 변환 단계를 없애서 그 실수를 구조적으로 막는다.
 *
 * 이미지는 이력에 담지 않는다. base64 한 장이 수 MB 라 세션 파일이 감당하지 못하고,
 * 직전 이미지를 다시 보낼 이유도 없다. 대신 텍스트 표식만 남긴다.
 */
class CashflowChatHistory
{
    const SESSION_KEY = 'cashflow_chat_history';

    /** 유지할 대화 턴 수 (한 턴 = 사용자 1 + 모델 1) */
    const MAX_TURNS = 8;

    /** 이력에 담는 한 발화의 최대 길이. 긴 답변으로 세션이 붓는 것을 막는다. */
    const MAX_ENTRY_CHARS = 4000;

    /** 이미지만 보낸 턴을 이력에 남길 때 쓰는 표식 */
    const IMAGE_PLACEHOLDER = '(이미지 첨부)';

    /**
     * 모델에 그대로 넘길 수 있는 형태로 이력을 돌려준다.
     *
     * @return array [['role' => 'user'|'model', 'text' => string], ...]
     */
    public function all()
    {
        $history = Session::get(self::SESSION_KEY, []);

        if (!is_array($history)) {
            return [];
        }

        return $this->normalize($history);
    }

    /**
     * 한 턴(사용자 발화 + 모델 답변)을 통째로 덧붙인다.
     *
     * 턴 단위로만 쓰는 이유는 이력이 항상 user/model 짝을 유지하게 하기 위해서다.
     * 모델 답변이 비면(스트리밍 실패 등) 사용자 발화도 남기지 않는다. 짝이 깨진 이력은
     * 다음 요청에서 모델을 혼란스럽게 만든다.
     *
     * @param string $userText
     * @param string $modelText
     * @return void
     */
    public function appendTurn($userText, $modelText)
    {
        $userText  = $this->clip($userText);
        $modelText = $this->clip($modelText);

        if ($userText === '' || $modelText === '') {
            return;
        }

        $history = Session::get(self::SESSION_KEY, []);

        if (!is_array($history)) {
            $history = [];
        }

        $history[] = ['role' => 'user',  'text' => $userText];
        $history[] = ['role' => 'model', 'text' => $modelText];

        Session::put(self::SESSION_KEY, $this->normalize($history));

        // 스트리밍 응답 본문은 StartSession 미들웨어가 세션을 저장한 뒤에 실행된다.
        // 여기서 직접 저장하지 않으면 이번 턴이 통째로 유실된다.
        Session::save();
    }

    /**
     * 대화를 비운다.
     *
     * @return void
     */
    public function reset()
    {
        Session::forget(self::SESSION_KEY);
        Session::save();
    }

    /**
     * 이력이 비어 있는지.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->all()) === 0;
    }

    /**
     * 길이 제한과 정렬 규칙을 적용한다.
     *
     * Gemini 는 contents 가 user 로 시작하기를 기대한다. 오래된 항목을 잘라내다 보면
     * 맨 앞이 model 로 시작할 수 있어, 그런 경우 한 칸 더 버린다.
     *
     * @param array $history
     * @return array
     */
    protected function normalize(array $history)
    {
        $clean = [];

        foreach ($history as $entry) {
            if (!is_array($entry) || !isset($entry['role']) || !isset($entry['text'])) {
                continue;
            }

            $role = $entry['role'] === 'user' ? 'user' : 'model';
            $text = $this->clip($entry['text']);

            if ($text === '') {
                continue;
            }

            $clean[] = ['role' => $role, 'text' => $text];
        }

        $max = self::MAX_TURNS * 2;

        if (count($clean) > $max) {
            $clean = array_slice($clean, -$max);
        }

        while (!empty($clean) && $clean[0]['role'] !== 'user') {
            array_shift($clean);
        }

        return array_values($clean);
    }

    /**
     * @param mixed $text
     * @return string
     */
    protected function clip($text)
    {
        $text = trim((string) $text);

        if ($text === '') {
            return '';
        }

        if (mb_strlen($text, 'UTF-8') > self::MAX_ENTRY_CHARS) {
            $text = mb_substr($text, 0, self::MAX_ENTRY_CHARS, 'UTF-8');
        }

        return $text;
    }
}
