<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class GeminiBotController extends Controller
{
    // 대화 기록을 위한 세션 키
    const CONVERSATION_KEY = 'chatbot_conversation';
    // 대화 기록 최대 개수
    const MAX_CONVERSATION_HISTORY = 5;

    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        $imageData = $request->input('image'); // 이미지 데이터 (Base64 인코딩)

        // 현재 대화 기록 가져오기
        $conversation = Session::get(self::CONVERSATION_KEY, []);
        
        // 디버깅을 위해 현재 세션에 저장된 대화 내용 로깅
        Log::info('현재 세션에 저장된 대화 내용: ', [
            'conversation_count' => count($conversation),
            'conversation_data' => $conversation
        ]);

        return new StreamedResponse(function () use ($message, $imageData, $conversation) {
            try {
                $apiKey = env('GEMINI_API_KEY');
                $modelName = 'models/gemini-2.0-flash-lite'; 
                $apiUrl = "https://generativelanguage.googleapis.com/v1beta/{$modelName}:streamGenerateContent?key={$apiKey}";
                
                $parts = [];
                
                // 텍스트 메시지 추가
                if (!empty($message)) {
                    $parts[] = ['text' => $message];
                } else if (!empty($imageData)) {
                    // 메시지가 없고 이미지만 있는 경우 기본 프롬프트 설정
                    $parts[] = ['text' => '이 이미지에 대해 설명해주세요.'];
                }
                
                // 이미지 데이터 추가
                if (!empty($imageData)) {
                    // Base64 데이터에서 prefix 제거 (data:image/jpeg;base64, 등)
                    $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                    
                    // 이미지 데이터 사이즈 확인 및 로깅
                    $imageSize = strlen(base64_decode($imageData));
                    $imageSizeKB = round($imageSize / 1024, 2);
                    Log::info('Gemini API에 전송되는 이미지 크기: ' . $imageSizeKB . 'KB');
                    
                    // 최대 허용 크기 (4MB) - Gemini API 제한
                    $maxSizeBytes = 4 * 1024 * 1024;
                    
                    if ($imageSize > $maxSizeBytes) {
                        Log::warning('이미지 크기 초과: ' . $imageSizeKB . 'KB (최대 4MB)');
                        throw new \Exception('이미지 크기가 너무 큽니다. 최대 4MB까지 허용됩니다.');
                    }
                    
                    $parts[] = [
                        'inline_data' => [
                            'mime_type' => 'image/jpeg', // 또는 이미지 타입에 맞게 조정
                            'data' => $imageData
                        ]
                    ];
                }
                
                // 시스템 지시문 설정
                $systemInstruction = '<role>
You are reading the game walkthrough guide for "Cashflow", a financial board game created by Robert Kiyosaki. Please kindly answer the user\'s questions.
</role>

<example>
<user>
방금 보낸 초록색 카드 이미지는 무슨 카드인가요?
</user>
<bad_responses>
무슨 카드인지 설명해드릴까요?
</bad_responses>
<bad_responses>
무슨 카드인지 잘모르겠어요.
</bad_responses>
<good_responses>
이미지에 보이는 카드는 캐시플로우 게임의 "기회 카드" 중 하나입니다. 구체적으로는 "4-PLEX FOR SALE" 이라고 적혀있는 부동산 투자 기회 카드입니다. 

**카드 내용 요약:**

*   **매물:** 4세대 주택 (4-PLEX)
*   **특징:** 재개발 지역에 위치, 소유주가 세금 문제로 매각
*   **수익률 (ROI):** 24%
*   **매각 가능 금액:** $100,000 - $140,000
*   **구입 비용 (Cost):** $80,000
*   **계약금 (Down Payment):** $20,000
*   **모기지 (Mortgage):** $60,000
*   **현금 흐름 (Cash Flow):** +$400

**이 카드를 얻었을 때 플레이어가 할 수 있는 선택:**

1.  **직접 구입:** $20,000의 계약금을 지불하고 4-PLEX를 구입합니다. 그러면 매달 $400의 현금 흐름이 발생하고, 자산 목록에 4-PLEX가 추가됩니다.
2.  **다른 플레이어에게 판매:** 다른 플레이어에게 $100,000에서 $140,000 사이의 금액으로 판매할 수 있습니다. 판매 금액에서 원래 구입 비용 $80,000을 제외한 차액이 플레이어의 이익이 됩니다.
3.  **포기:** 투자를 포기하고 카드를 버릴 수 있습니다.

**투자 결정 시 고려 사항:**

*   **자신의 재정 상황:** $20,000의 계약금을 지불할 여유가 있는지 확인합니다.
*   **현금 흐름:** 매달 $400의 현금 흐름이 자신의 재정 상황에 얼마나 도움이 되는지 고려합니다.
*   **판매 가능성:** 다른 플레이어가 이 카드를 얼마에 구입할 의향이 있는지 파악합니다.
*   **ROI (Return on Investment):** 24%의 수익률은 괜찮은 투자 기회인지 판단합니다.

이 카드는 부동산 투자를 통해 자산 수입을 늘리고 쥐 경주에서 탈출하는 데 도움이 될 수 있는 기회를 제공합니다.
</good_responses>
</example>

IMPORTANT REMINDERS:
 - You can only explain the rules for cashflow games.
 - You MUST respond to questions that are not related to Cashflow games with "죄송합니다. 캐시플로우 게임과 관련된 답변만 가능합니다." to non-Cashflow game-related questions.
 - Please be kind and mix your answers with emojis.
 - REMEMBER PREVIOUS CONVERSATION CONTEXT to provide coherent responses that build on what was already discussed.';

                // 이전 대화 컨텍스트 추가
                $contents = [];
                
                // 이전 대화 컨텍스트 추가 (Gemini API에서는 assistant 역할 대신 assistant 사용)
                foreach ($conversation as $entry) {
                    // 'assistant' 역할을 'assistant'로 매핑 (이전에는 'model'로 잘못 매핑됨)
                    $role = ($entry['role'] === 'user') ? 'user' : 'assistant';
                    $contents[] = [
                        'role' => $role,
                        'parts' => [
                            ['text' => $entry['content']]
                        ]
                    ];
                }
                
                // 디버깅을 위해 API에 보내는 대화 컨텍스트 로깅
                Log::info('Gemini API에 보내는 대화 컨텍스트: ', [
                    'contents_count' => count($contents),
                    'contents_data' => $contents
                ]);
                
                // 현재 사용자 메시지 추가
                $contents[] = [
                    'role' => 'user',
                    'parts' => $parts
                ];
                
                $postData = json_encode([
                    'contents' => $contents,
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ],
                ]);

                $ch = curl_init($apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // 응답을 직접 출력 (스트리밍)
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                ]);
                
                // 응답 텍스트를 저장할 변수
                $responseText = '';
                
                // 각 데이터 청크를 SSE 형식으로 출력하는 콜백
                curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) use (&$responseText) {
                    // 데이터를 SSE 형식으로 출력
                    echo "data: " . $data . "\n\n";
                    
                    // 응답 내용을 추출하여 저장
                    $this->extractAndSaveResponse($data, $responseText);
                    
                    flush();
                    ob_flush();
                    return strlen($data); // 반드시 수신된 데이터 길이 반환
                });
                
                curl_setopt($ch, CURLOPT_BUFFERSIZE, 8192);
                curl_setopt($ch, CURLOPT_TIMEOUT, 0);

                curl_exec($ch);

                if (curl_errno($ch)) {
                    throw new \Exception('CURL error: ' . curl_error($ch));
                }

                curl_close($ch);
                
                // 대화 기록에 사용자 메시지 및 봇 응답 추가
                if (!empty($message) || !empty($imageData)) {
                    // 사용자 메시지 저장
                    $userMessage = !empty($message) ? $message : "이미지 첨부";
                    $this->addToConversationHistory('user', $userMessage);
                    
                    // 봇 응답 저장
                    if (!empty($responseText)) {
                        $this->addToConversationHistory('assistant', $responseText);
                    }
                }
                
                // 세션에 저장된 대화 내용 로깅 (응답 후)
                $currentConversation = Session::get(self::CONVERSATION_KEY, []);
                Log::info('응답 후 세션에 저장된 대화 내용: ', [
                    'conversation_count' => count($currentConversation),
                    'conversation_data' => $currentConversation
                ]);

                // 스트림 종료 알림
                echo "data: [DONE]\n\n";
                flush();
                ob_flush();

            } catch (\Exception $e) {
                \Log::error('Gemini API CURL Streaming Error: ' . $e->getMessage());
                echo "data: [ERROR] 챗봇 API 스트리밍 중 오류 발생: " . $e->getMessage() . "\n\n";
                flush();
                ob_flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // Nginx 버퍼링 비활성화 (선택 사항)
        ]);
    }
    
    /**
     * 응답 데이터에서 텍스트 내용을 추출하고 저장
     */
    private function extractAndSaveResponse($data, &$responseText) {
        // 디버깅: 응답 데이터 로깅
        Log::debug('API 응답 데이터 청크: ' . $data);
        
        // JSON 데이터 체크
        if (strpos($data, '"text"') !== false) {
            // JSON 데이터에서 text 필드 추출 시도
            if (preg_match('/"text"\s*:\s*"([^"]*)"/s', $data, $matches)) {
                $extractedText = $matches[1];
                $responseText .= $extractedText;
                
                // 디버깅: 추출된 텍스트 로깅
                Log::debug('추출된 텍스트: ' . $extractedText);
                Log::debug('누적된 전체 텍스트: ' . $responseText);
            }
        }
        
        // 추가: finishReason이 포함되어 있는 경우 로깅 (마지막 응답인지 확인)
        if (strpos($data, '"finishReason"') !== false) {
            Log::debug('최종 응답 감지 (finishReason 포함)');
        }
    }
    
    /**
     * 대화 내용을 세션에 저장
     */
    private function addToConversationHistory($role, $content) {
        // 현재 대화 내용 가져오기
        $conversation = Session::get(self::CONVERSATION_KEY, []);
        
        // 디버깅: 저장 전 대화 내용 로깅
        Log::debug('저장 전 대화 내용: ', ['count' => count($conversation)]);
        
        // 새 대화 항목 추가
        $conversation[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->timestamp
        ];
        
        // 대화 기록이 최대 개수를 초과하면 가장 오래된 항목 제거
        while (count($conversation) > self::MAX_CONVERSATION_HISTORY * 2) { // 사용자+봇 메시지 쌍으로 계산
            array_shift($conversation);
        }
        
        // 세션에 대화 내용 저장
        Session::put(self::CONVERSATION_KEY, $conversation);
        Session::save(); // 세션 즉시 저장 (중요)
        
        // 디버깅: 저장 후 대화 내용 로깅
        $currentConversation = Session::get(self::CONVERSATION_KEY, []);
        $lastMessage = !empty($currentConversation) ? end($currentConversation)['content'] : 'none';
        
        Log::debug('저장 후 대화 내용: ', [
            'count' => count($currentConversation),
            'last_message' => $lastMessage
        ]);
    }
    
    /**
     * 대화 내용 초기화
     */
    public function resetConversation(Request $request)
    {
        // 세션에서 대화 내용 삭제
        Session::forget(self::CONVERSATION_KEY);
        Session::save();
        
        Log::info('대화 내용 초기화됨');
        
        return response()->json([
            'success' => true,
            'message' => '대화 내용이 초기화되었습니다.'
        ]);
    }
} 