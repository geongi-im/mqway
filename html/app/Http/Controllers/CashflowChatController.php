<?php

namespace App\Http\Controllers;

use App\Services\CashflowChatBot;
use App\Services\CashflowChatHistory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * 캐시플로우 챗봇 엔드포인트.
 *
 * 컨트롤러는 검증과 SSE 중계만 한다. 모델 호출·프롬프트·이력은 각각
 * CashflowChatBot / CashflowChatHistory 가 맡는다.
 *
 * 브라우저에는 Gemini 응답을 그대로 흘리지 않고 자체 이벤트 봉투로 감싸 보낸다.
 *
 *   data: {"type":"delta","text":"..."}
 *   data: {"type":"done"}
 *   data: {"type":"error","message":"..."}
 *
 * 이렇게 두면 프런트가 Gemini 응답 구조를 몰라도 되고, 모델이나 API 버전이 바뀌어도
 * 화면 코드를 건드릴 일이 없다. 예전 구현은 Gemini 의 원본 JSON 을 그대로 내보내서
 * 브라우저가 candidates[0].content.parts[0].text 를 직접 파고들었다.
 */
class CashflowChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 사용자 발화 한 건을 처리하고 응답을 스트리밍한다.
     *
     * @param  Request             $request
     * @param  CashflowChatBot     $bot
     * @param  CashflowChatHistory $history
     * @return StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function send(Request $request, CashflowChatBot $bot, CashflowChatHistory $history)
    {
        $validator = validator($request->all(), [
            'message' => 'nullable|string|max:' . CashflowChatBot::MAX_MESSAGE_CHARS,
            'image'   => 'nullable|string',
        ], [
            'message.max' => '메시지가 너무 깁니다. ' . CashflowChatBot::MAX_MESSAGE_CHARS . '자 이내로 입력해주세요.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $message = trim((string) $request->input('message'));
        $parsed  = $bot->parseImage($request->input('image'));

        if (!$parsed['success']) {
            return response()->json(['success' => false, 'message' => $parsed['message']], 422);
        }

        $image = $parsed['image'];

        if ($message === '' && $image === null) {
            return response()->json(['success' => false, 'message' => '메시지를 입력하거나 이미지를 첨부해주세요.'], 422);
        }

        // 스트림 콜백은 미들웨어가 끝난 뒤에 실행된다. 세션 의존값은 여기서 미리 읽어둔다.
        $previous = $history->all();
        $context  = $this->buildContext($request);

        return $this->stream(function () use ($bot, $history, $message, $image, $previous, $context) {
            $result = $bot->stream($message, $image, $previous, $context, function ($chunk) {
                $this->emit(['type' => 'delta', 'text' => $chunk]);
            });

            if ($result['success']) {
                $history->appendTurn(
                    $message !== '' ? $message : CashflowChatHistory::IMAGE_PLACEHOLDER,
                    $result['text']
                );

                $this->emit(['type' => 'done']);
                return;
            }

            // 사용자가 중단했거나 창을 닫았다. 보낼 곳이 없으므로 조용히 끝낸다.
            if ($result['reason'] === 'client_aborted') {
                return;
            }

            $this->emit(['type' => 'error', 'message' => $result['message']]);
        });
    }

    /**
     * 대화를 비운다.
     *
     * @param  CashflowChatHistory $history
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(CashflowChatHistory $history)
    {
        $history->reset();

        return response()->json(['success' => true]);
    }

    /**
     * 개인화 맥락.
     *
     * 지금은 비워 둔다. 게임 상태를 물릴 때 여기서 mq_cashflow_games / assets / liabilities 를
     * 읽어 배열로 돌려주면 되고, 프롬프트 쪽은 CashflowChatBot::buildContextBlock() 이 받는다.
     * 두 메서드 외에 손댈 곳은 없다.
     *
     * @param  Request $request
     * @return array
     */
    protected function buildContext(Request $request)
    {
        return [];
    }

    /**
     * SSE 응답 껍데기.
     *
     * @param  callable $callback
     * @return StreamedResponse
     */
    protected function stream(callable $callback)
    {
        return new StreamedResponse(function () use ($callback) {
            // php-fpm 기본 max_execution_time(30초)보다 스트리밍이 길어질 수 있다.
            @set_time_limit(CashflowChatBot::REQUEST_TIMEOUT + 30);

            // 클라이언트가 끊기면 스크립트도 멈춰야 한다. 그래야 CashflowChatBot 이
            // connection_aborted() 를 보고 Gemini 전송을 중단한다.
            ignore_user_abort(false);

            $callback();
        }, 200, [
            'Content-Type'      => 'text/event-stream; charset=UTF-8',
            'Cache-Control'     => 'no-cache, no-store, must-revalidate',
            // nginx fastcgi 버퍼링을 끄지 않으면 응답이 모였다가 한 번에 온다.
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * 이벤트 한 건을 내보낸다.
     *
     * json_encode 가 개행을 \n 으로 이스케이프하므로 항상 한 줄에 담긴다.
     * SSE 의 줄 단위 규약이 깨지지 않는다.
     *
     * @param  array $payload
     * @return void
     */
    protected function emit(array $payload)
    {
        echo 'data: ' . json_encode($payload, JSON_UNESCAPED_UNICODE) . "\n\n";

        if (ob_get_level() > 0) {
            @ob_flush();
        }

        flush();
    }
}
