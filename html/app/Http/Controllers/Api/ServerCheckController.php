<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServerCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServerCheckController extends Controller
{
    /**
     * 서버 상태 확인 API - 상태 업데이트 및 비활성 서버 체크 통합
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function serverCheck(Request $request)
    {
        // 파라미터 검증
        $validated = $request->validate([
            'user_id' => 'required|string',
            'server' => 'required|string',
        ]);

        // 파라미터 가져오기
        $user_id = $request->input('user_id');
        $server = $request->input('server');

        try {
            // 1. 현재 서버 상태 업데이트
            DB::statement("
                INSERT INTO server_check (sc_server, sc_user_id, sc_watch, sc_datetime)
                VALUES (?, ?, 1, NOW())
                ON DUPLICATE KEY 
                UPDATE sc_datetime = NOW()
            ", [$server, $user_id]);

            // 2. 비활성 서버 체크 - 5분 이상 응답이 없는 서버 찾기
            $inactiveServers = DB::select("
                SELECT * FROM server_check 
                WHERE sc_watch = 1 AND sc_datetime < NOW() - INTERVAL 5 MINUTE
            ");
            
            // 비활성 서버가 있으면 알림 발송
            if (!empty($inactiveServers)) {
                foreach ($inactiveServers as $inactiveServer) {
                    // 텔레그램 알림 메시지 생성
                    $message = "🚨 서버 응답 없음 알림\n";
                    $message .= "서버: {$inactiveServer->sc_server}\n";
                    $message .= "아이디: {$inactiveServer->sc_user_id}\n";
                    $message .= "마지막 응답: " . Carbon::parse($inactiveServer->sc_datetime)->format('Y-m-d H:i:s') . "\n";
                    $message .= "경과 시간: " . Carbon::parse($inactiveServer->sc_datetime)->diffForHumans();
                    
                    // 여기서는 실제 알림을 보내지 않고 로그만 남깁니다
                    Log::info('서버 알림 메시지: ' . $message);
                    
                    // 필요한 경우 아래 샘플 함수를 호출하여 텔레그램으로 알림을 보낼 수 있습니다
                    $this->sendTelegramNotification($message);
                }
            }

            // 성공 응답 (HTTP 상태 코드 200)
            return response()->json([
                'status' => '200'
            ], 200);
        } catch (\Exception $e) {
            Log::error('서버 체크 오류: ' . $e->getMessage());
            
            // 실패 응답 (HTTP 상태 코드 500)
            return response()->json([
                'status' => '500',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * 텔레그램으로 알림 발송 (샘플 함수)
     * 
     * @param string $message
     * @return bool
     */
    private function sendTelegramNotification($message)
    {
        try {
            // 텔레그램 봇 토큰과 채팅 ID 설정
            $botToken = env('TELEGRAM_BOT_TOKEN', '');
            $chatId = env('TELEGRAM_CHAT_ID', '');
            
            if (empty($botToken) || empty($chatId)) {
                Log::error('텔레그램 봇 토큰 또는 채팅 ID가 설정되지 않았습니다.');
                return false;
            }
            
            // 텔레그램 API URL
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            
            // curl 초기화
            $ch = curl_init();
            
            // curl 옵션 설정
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // API 요청 실행
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // curl 종료
            curl_close($ch);
            
            // 응답 확인
            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info('텔레그램 알림 발송 성공');
                return true;
            } else {
                Log::error('텔레그램 알림 발송 실패: ' . $response);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('텔레그램 알림 발송 중 오류 발생: ' . $e->getMessage());
            return false;
        }
    }
} 