<?php

namespace App\Services;

use App\Models\CashflowChatMessage;
use Illuminate\Support\Facades\DB;

/**
 * 캐시플로우 챗봇 대화 이력. 회원별로 mq_cashflow_chat_messages 에 보관한다.
 *
 * 예전에는 세션에만 담았다. 그래서 브라우저를 닫거나 세션이 만료되면 대화가 통째로
 * 사라졌고, 파일 세션이라 긴 답변이 몇 개만 쌓여도 세션 파일이 부풀었다. 지금은 DB 에
 * 두므로 다시 접속해도 열려 있던 대화를 그대로 이어서 볼 수 있다.
 *
 * 회원 식별자를 필드로 들고 있지 않고 메서드 인자로 받는 이유가 있다. 저장 시점이
 * SSE 스트림 콜백 안이라 미들웨어가 끝난 뒤이고, 그때 Auth 세션에 다시 기대고 싶지 않다.
 * 컨트롤러가 요청 초반에 읽어 둔 값을 그대로 넘기는 편이 안전하다.
 *
 * 역할값은 Gemini 규격(role = 'user' | 'model')과 똑같이 저장한다. 예전 구현은 세션에
 * 'assistant' 로 담고 API 로 보낼 때 변환하다가 역할값을 틀려 두 번째 메시지부터 400 이
 * 났다. 변환 단계를 없애서 그 실수를 구조적으로 막는다.
 *
 * 이미지 자체는 담지 않는다. base64 한 장이 수 MB 라 테이블이 감당하지 못하고, 직전
 * 이미지를 다시 보낼 이유도 없다. 대신 첨부 사실만 표식으로 남긴다.
 */
class CashflowChatHistory
{
    /** 모델에 함께 실어 보내는 대화 턴 수 (한 턴 = 사용자 1 + 모델 1) */
    const MAX_TURNS = 8;

    /**
     * 회원 한 명이 보관하는 최대 발화 수.
     *
     * 화면 복원은 이 범위 전체를 쓰고, 모델에는 그중 최근 MAX_TURNS 턴만 보낸다.
     * 화면에서는 지난 대화를 길게 훑을 수 있어야 하지만, 모델에 매번 전부 실어 보내면
     * 토큰만 늘고 답변이 나아지지는 않기 때문에 두 값을 나눠 뒀다.
     */
    const MAX_STORED_MESSAGES = 100;

    /** 한 발화의 최대 길이. 긴 답변으로 테이블이 붓는 것을 막는다. */
    const MAX_ENTRY_CHARS = 4000;

    /** 이미지만 보낸 턴을 이력에 남길 때 쓰는 표식 */
    const IMAGE_PLACEHOLDER = '(이미지 첨부)';

    /**
     * 모델에 그대로 넘길 수 있는 형태로 최근 이력을 돌려준다.
     *
     * @param  int|null $memberIdx
     * @return array [['role' => 'user'|'model', 'text' => string], ...]
     */
    public function all($memberIdx)
    {
        $rows = $this->recent($memberIdx, self::MAX_TURNS * 2);

        $history = [];

        foreach ($rows as $row) {
            $history[] = [
                'role' => $row->mq_role === CashflowChatMessage::ROLE_USER ? 'user' : 'model',
                'text' => $this->clip($row->mq_message),
            ];
        }

        return $this->normalize($history);
    }

    /**
     * 화면 복원용 이력.
     *
     * 브라우저를 새로 연 뒤 지난 대화를 다시 그리기 위한 것이라 시각과 이미지 첨부 여부까지
     * 함께 준다. 모델에 보내는 all() 과 달리 맨 앞을 user 로 맞추려고 잘라내지 않는다.
     * 화면은 남아 있는 그대로 보여주는 편이 자연스럽다.
     *
     * @param  int|null $memberIdx
     * @return array [['role' => ..., 'text' => ..., 'has_image' => bool, 'time' => 'HH:MM'], ...]
     */
    public function transcript($memberIdx)
    {
        $rows = $this->recent($memberIdx, self::MAX_STORED_MESSAGES);

        $messages = [];

        foreach ($rows as $row) {
            $text = $this->clip($row->mq_message);

            if ($text === '') {
                continue;
            }

            $messages[] = [
                'role'      => $row->mq_role === CashflowChatMessage::ROLE_USER ? 'user' : 'model',
                'text'      => $text,
                'has_image' => (bool) $row->mq_has_image,
                'time'      => $this->timeLabel($row->mq_reg_date),
            ];
        }

        return $messages;
    }

    /**
     * 한 턴(사용자 발화 + 모델 답변)을 통째로 덧붙인다.
     *
     * 턴 단위로만 쓰는 이유는 이력이 항상 user/model 짝을 유지하게 하기 위해서다.
     * 모델 답변이 비면(스트리밍 실패 등) 사용자 발화도 남기지 않는다. 짝이 깨진 이력은
     * 다음 요청에서 모델을 혼란스럽게 만든다.
     *
     * @param  int|null $memberIdx
     * @param  string   $userText
     * @param  string   $modelText
     * @param  bool     $hasImage  사용자가 이미지를 첨부한 턴인지
     * @return void
     */
    public function appendTurn($memberIdx, $userText, $modelText, $hasImage = false)
    {
        $memberIdx = $this->memberIdx($memberIdx);

        if ($memberIdx === null) {
            return;
        }

        $userText  = $this->clip($userText);
        $modelText = $this->clip($modelText);

        if ($userText === '' || $modelText === '') {
            return;
        }

        // 두 행을 한 트랜잭션으로 묶는다. 사용자 발화만 남고 답변이 빠진 상태가 생기면
        // 다음 요청의 프롬프트에서 짝이 깨진다.
        DB::transaction(function () use ($memberIdx, $userText, $modelText, $hasImage) {
            CashflowChatMessage::create([
                'member_idx'   => $memberIdx,
                'mq_role'      => CashflowChatMessage::ROLE_USER,
                'mq_message'   => $userText,
                'mq_has_image' => $hasImage ? 1 : 0,
            ]);

            CashflowChatMessage::create([
                'member_idx'   => $memberIdx,
                'mq_role'      => CashflowChatMessage::ROLE_MODEL,
                'mq_message'   => $modelText,
                'mq_has_image' => 0,
            ]);

            $this->trim($memberIdx);
        });
    }

    /**
     * 대화를 비운다. 화면의 '새 대화' 가 이 메서드를 탄다.
     *
     * @param  int|null $memberIdx
     * @return void
     */
    public function reset($memberIdx)
    {
        $memberIdx = $this->memberIdx($memberIdx);

        if ($memberIdx === null) {
            return;
        }

        CashflowChatMessage::ofMember($memberIdx)->delete();
    }

    /**
     * 말풍선에 붙일 시각 라벨.
     *
     * 며칠 전 대화도 그대로 이어 보게 되었으므로 시:분만 보여주면 방금 나눈 말처럼 읽힌다.
     * 오늘이 아닌 발화에는 날짜를 붙인다.
     *
     * @param  \Carbon\Carbon|null $date
     * @return string
     */
    protected function timeLabel($date)
    {
        if (empty($date)) {
            return '';
        }

        return $date->isToday() ? $date->format('H:i') : $date->format('n/j H:i');
    }

    /**
     * 최근 발화 $limit 개를 오래된 순으로 돌려준다.
     *
     * 최신부터 잘라낸 뒤 뒤집는다. 앞에서부터 읽으면 대화가 길어졌을 때 필요 없는 구간까지
     * 훑게 된다.
     *
     * @param  int|null $memberIdx
     * @param  int      $limit
     * @return \Illuminate\Support\Collection
     */
    protected function recent($memberIdx, $limit)
    {
        $memberIdx = $this->memberIdx($memberIdx);

        if ($memberIdx === null) {
            return collect();
        }

        return CashflowChatMessage::ofMember($memberIdx)
            ->orderBy('idx', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * 보관 한도를 넘은 오래된 발화를 지운다.
     *
     * 한 턴이 두 행이라 지우는 개수도 짝수로 맞춘다. 홀수 개를 지우면 짝을 잃은 model
     * 발화가 맨 앞에 남는다.
     *
     * @param  int $memberIdx
     * @return void
     */
    protected function trim($memberIdx)
    {
        $total = CashflowChatMessage::ofMember($memberIdx)->count();

        if ($total <= self::MAX_STORED_MESSAGES) {
            return;
        }

        $excess = $total - self::MAX_STORED_MESSAGES;

        if ($excess % 2 === 1) {
            $excess++;
        }

        $oldest = CashflowChatMessage::ofMember($memberIdx)
            ->orderBy('idx', 'asc')
            ->limit($excess)
            ->pluck('idx');

        if ($oldest->isEmpty()) {
            return;
        }

        CashflowChatMessage::whereIn('idx', $oldest)->delete();
    }

    /**
     * 길이 제한과 정렬 규칙을 적용한다.
     *
     * Gemini 는 contents 가 user 로 시작하기를 기대한다. 오래된 항목을 잘라내다 보면
     * 맨 앞이 model 로 시작할 수 있어, 그런 경우 한 칸 더 버린다.
     *
     * @param  array $history
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
     * 회원 식별자를 정수로 확정한다. 값이 없으면 null 을 돌려 호출부가 조용히 지나가게 한다.
     *
     * 챗봇 엔드포인트에는 auth 미들웨어가 걸려 있어 실제로는 항상 값이 들어온다. 다만
     * 스트림 콜백처럼 요청 흐름이 끝난 뒤에 불리는 자리가 있어 방어해 둔다.
     *
     * @param  mixed $memberIdx
     * @return int|null
     */
    protected function memberIdx($memberIdx)
    {
        if ($memberIdx === null || $memberIdx === '') {
            return null;
        }

        $memberIdx = (int) $memberIdx;

        return $memberIdx > 0 ? $memberIdx : null;
    }

    /**
     * @param  mixed $text
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
