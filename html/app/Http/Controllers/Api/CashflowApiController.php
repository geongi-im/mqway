<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashflowGame;
use App\Models\CashflowAsset;
use App\Models\CashflowLiability;
use App\Models\CashflowLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CashflowApiController extends Controller
{
    /**
     * 사용자 ID 변환 헬퍼 메소드
     */
    private function getUserId()
    {
        $userIdRaw = Auth::user()->mq_user_id;
        
        // 문자열 ID를 숫자로 변환 (해시 사용)
        if (is_string($userIdRaw)) {
            return abs(crc32($userIdRaw));
        }
        
        return $userIdRaw;
    }
    
    /**
     * 숫자 값 정리 헬퍼 메소드
     */
    private function cleanNumericValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        // 문자열을 숫자로 변환 시도
        if (is_string($value)) {
            // NaN, Infinity 등의 문자열 제거
            $value = preg_replace('/[^0-9.-]/', '', $value);
            
            // 빈 문자열이면 0 반환
            if ($value === '') {
                return 0;
            }
        }
        
        // 숫자로 변환
        $numericValue = floatval($value);
        
        // NaN이나 Infinity 체크
        if (!is_finite($numericValue)) {
            return 0;
        }
        
        return $numericValue;
    }
    
    /**
     * 테스트용 API 엔드포인트
     */
    public function test(Request $request)
    {
        \Log::info('테스트 API 호출됨');
        \Log::info('Auth::check(): ' . (Auth::check() ? 'true' : 'false'));
        \Log::info('현재 사용자', ['user' => Auth::user()]);
        
        $userId = Auth::check() ? $this->getUserId() : null;
        
        return response()->json([
            'success' => true,
            'message' => 'API 연결 정상',
            'auth_check' => Auth::check(),
            'user' => Auth::user(),
            'converted_user_id' => $userId,
            'timestamp' => now()
        ]);
    }
    /**
     * 게임 상태 저장
     */
    public function saveGameState(Request $request)
    {
        \Log::info('=== 캐시플로우 게임 저장 시작 ===');
        \Log::info('요청 데이터', ['request' => $request->all()]);
        
        // 데드락 재시도 로직
        $maxRetries = 3;
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                DB::beginTransaction();
            
                $gameData = $request->input('gameState');
                $logOnly = $request->input('logOnly', false); // 로그만 저장하는지 확인
                
                \Log::info('게임 데이터', ['gameData' => $gameData, 'logOnly' => $logOnly]);
                
                // 로그만 저장하는 경우 약간의 지연으로 충돌 방지
                if ($logOnly && $retryCount > 0) {
                    usleep(rand(5000, 15000)); // 5-15ms 지연
                }
            
            // 현재 로그인한 사용자 확인
            \Log::info('Auth::check() 결과: ' . (Auth::check() ? 'true' : 'false'));
            \Log::info('현재 사용자', ['user' => Auth::user()]);
            
            if (!Auth::check()) {
                \Log::error('사용자 인증 실패');
                return response()->json(['error' => '사용자 인증이 필요합니다.'], 401);
            }
            
            $userId = $this->getUserId();
            \Log::info('사용자 ID: ' . $userId);
            
            // 세션 키 생성 또는 기존 세션 키 사용
            $sessionKey = $request->input('sessionKey') ?: Str::uuid();
            \Log::info("세션 키: {$sessionKey}");
            
            // 기존 게임 찾기 또는 새 게임 생성
            $game = CashflowGame::where('mq_session_key', $sessionKey)->first();
            
            if (!$game) {
                if ($logOnly) {
                    \Log::error("로그만 저장 시도했으나 기존 게임이 없습니다. SessionKey: {$sessionKey}");
                    return response()->json(['error' => '게임을 찾을 수 없습니다.'], 404);
                }
                
                \Log::info("기존 게임을 찾을 수 없음. 새 게임 생성. SessionKey: {$sessionKey}");
                $game = new CashflowGame();
                $game->mq_session_key = $sessionKey;
                $game->mq_user_id = $userId;
            } else {
                \Log::info("기존 게임 발견. Game ID: {$game->idx}, SessionKey: {$sessionKey}");
            }
            
            // 로그만 저장하는 경우 player 데이터 처리 건너뛰기
            if (!$logOnly) {
                // 플레이어 데이터 검증
                if (!isset($gameData['player'])) {
                    \Log::error('게임 데이터에 player 정보가 없습니다');
                    return response()->json(['error' => '플레이어 정보가 필요합니다.'], 400);
                }
                
                // 플레이어 데이터 저장
                $player = $gameData['player'];
                $game->mq_player_name = $player['name'] ?? $game->mq_player_name;
                $game->mq_profession = $player['profession'] ?? $game->mq_profession;
                $game->mq_dream = $player['dream'] ?? $game->mq_dream;
                $game->mq_dream_cost = $this->cleanNumericValue($player['dreamCost'] ?? $game->mq_dream_cost);
                $game->mq_game_started = $gameData['gameStarted'] ?? $game->mq_game_started;
                
                // 재무 정보 저장
                $game->mq_cash = $this->cleanNumericValue($player['cash'] ?? 0);
                $game->mq_salary = $this->cleanNumericValue($player['salary'] ?? 0);
                $game->mq_passive_income = $this->cleanNumericValue($player['passiveIncome'] ?? 0);
                $game->mq_total_income = $this->cleanNumericValue($player['totalIncome'] ?? 0);
                $game->mq_total_expenses = $this->cleanNumericValue($player['totalExpenses'] ?? 0);
                $game->mq_monthly_cash_flow = $this->cleanNumericValue($player['monthlyCashFlow'] ?? 0);
                $game->mq_has_child = $player['hasChild'] ?? false;
                $game->mq_children_count = $this->cleanNumericValue($player['expenses']['childrenCount'] ?? 0);
                $game->mq_per_child_expense = $this->cleanNumericValue($player['expenses']['perChildExpense'] ?? 200);
                
                // 지출 정보 저장
                $expenses = $player['expenses'] ?? [];
                $game->mq_expenses_taxes = $this->cleanNumericValue($expenses['taxes'] ?? 0);
                $game->mq_expenses_home_payment = $this->cleanNumericValue($expenses['homePayment'] ?? 0);
                $game->mq_expenses_school_loan = $this->cleanNumericValue($expenses['schoolLoan'] ?? 0);
                $game->mq_expenses_car_loan = $this->cleanNumericValue($expenses['carLoan'] ?? 0);
                $game->mq_expenses_credit_card = $this->cleanNumericValue($expenses['creditCard'] ?? 0);
                $game->mq_expenses_retail = $this->cleanNumericValue($expenses['retail'] ?? 0);
                $game->mq_expenses_other = $this->cleanNumericValue($expenses['other'] ?? 0);
                $game->mq_expenses_children = $this->cleanNumericValue($expenses['children'] ?? 0);
                
            }
            
            // 게임 기본 정보는 항상 저장 (로그만 저장일 때도 업데이트 날짜는 갱신)
            $game->save();
            
            // 자산, 주식, 부채 저장 (전체 저장 모드에서만)
            if (!$logOnly && isset($gameData['player'])) {
                \Log::info("전체 저장 모드: 자산/주식/부채 저장 시작");
            } else {
                \Log::info("로그 전용 모드: 자산/주식/부채 저장 건너뛰기 (기존 데이터 보존)");
            }
            
            if (!$logOnly && isset($gameData['player'])) {
                $player = $gameData['player'];
                
                // 기존 자산/부채 삭제 후 새로 저장 (전체 저장 모드에서만)
                // 데드락 방지를 위해 더 안전한 방식으로 삭제
                \Log::info("전체 저장 모드: 기존 자산/부채 삭제 후 재저장");
                DB::statement('SET SESSION innodb_lock_wait_timeout = 10');
                try {
                    $game->assets()->delete();
                    $game->liabilities()->delete();
                } catch (\Exception $e) {
                    \Log::warning("자산/부채 삭제 중 오류 (무시하고 계속): " . $e->getMessage());
                    // 삭제 실패해도 계속 진행 (덮어쓰기 방식으로 처리)
                }
                
                // 자산 저장
                if (isset($player['assets']) && is_array($player['assets'])) {
                    foreach ($player['assets'] as $asset) {
                        $this->saveAsset($game->idx, $asset);
                    }
                }
                
                // 주식 저장
                \Log::info('=== 주식 저장 시작 ===');
                \Log::info('player.stocks 존재 여부: ' . (isset($player['stocks']) ? 'true' : 'false'));
                \Log::info('player.stocks 타입: ' . gettype($player['stocks'] ?? null));
                \Log::info('player.stocks 내용: ', ['stocks' => $player['stocks'] ?? 'NOT_SET']);
                
                if (isset($player['stocks']) && is_array($player['stocks'])) {
                    \Log::info('주식 개수: ' . count($player['stocks']));
                    foreach ($player['stocks'] as $symbol => $stockData) {
                        \Log::info("주식 저장 시도 - 심볼: {$symbol}", [
                            'stockData' => $stockData,
                            'shares' => $stockData['shares'] ?? 'NOT_SET',
                            'averagePrice' => $stockData['averagePrice'] ?? 'NOT_SET',
                            'monthlyDividend' => $stockData['monthlyDividend'] ?? 'NOT_SET'
                        ]);
                        
                        try {
                            $result = CashflowAsset::createStock(
                                $game->idx,
                                $symbol,
                                $this->cleanNumericValue($stockData['shares']),
                                $this->cleanNumericValue($stockData['averagePrice']),
                                $this->cleanNumericValue($stockData['monthlyDividend'] ?? 0)
                            );
                            \Log::info("주식 저장 성공 - {$symbol}: ", ['result' => $result]);
                        } catch (\Exception $e) {
                            \Log::error("주식 저장 실패 - {$symbol}: " . $e->getMessage());
                            \Log::error("주식 저장 스택: " . $e->getTraceAsString());
                        }
                    }
                } else {
                    \Log::warning('주식 데이터가 없거나 배열이 아닙니다');
                }
                
                // 펀드 저장
                if (isset($player['funds']) && is_array($player['funds'])) {
                    foreach ($player['funds'] as $symbol => $fundData) {
                        CashflowAsset::createFund(
                            $game->idx,
                            $symbol,
                            $this->cleanNumericValue($fundData['shares']),
                            $this->cleanNumericValue($fundData['averagePrice']),
                            $this->cleanNumericValue($fundData['monthlyDividend'] ?? 0)
                        );
                    }
                }
                
                // 부채 저장
                if (isset($player['liabilities']) && is_array($player['liabilities'])) {
                    foreach ($player['liabilities'] as $liability) {
                        $this->saveLiability($game->idx, $liability);
                    }
                }
                
                // 응급 대출 저장
                if (isset($player['emergencyLoans']) && is_array($player['emergencyLoans'])) {
                    foreach ($player['emergencyLoans'] as $loan) {
                        \Log::info('긴급 대출 저장 시도', ['loan' => $loan]);
                        
                        // JavaScript 필드명에 맞춰 수정
                        $loanAmount = $this->cleanNumericValue($loan['amount'] ?? $loan['loanAmount'] ?? 0);
                        $remainingAmount = $this->cleanNumericValue($loan['remainingAmount'] ?? $loanAmount);
                        $monthlyPayment = $this->cleanNumericValue($loan['monthlyPayment'] ?? 0);
                        
                        // 이자율이 없으면 월 지급액으로부터 역산 (10% 월 이자)
                        $interestRate = ($loanAmount > 0) ? ($monthlyPayment / $loanAmount) : 0.1;
                        
                        CashflowLiability::createEmergencyLoan(
                            $game->idx,
                            $loanAmount,
                            $interestRate,
                            $monthlyPayment
                        );
                        
                        \Log::info('긴급 대출 저장 성공', [
                            'loanAmount' => $loanAmount,
                            'interestRate' => $interestRate,
                            'monthlyPayment' => $monthlyPayment
                        ]);
                    }
                }
            }
            
            // 게임 로그 저장 (모든 로그를 확실히 저장하도록 개선)
            \Log::info('=== 게임 로그 저장 시작 ===');
            
            if (isset($gameData['gameLog']) && is_array($gameData['gameLog'])) {
                \Log::info('받은 gameLog 배열 길이: ' . count($gameData['gameLog']));
                
                // 기존 저장된 로그들을 메시지+타임스탬프 조합으로 조회 (중복 방지용)
                $existingLogEntries = $game->logs()
                    ->select('mq_log_message', 'mq_reg_date')
                    ->get()
                    ->map(function($log) {
                        return $log->mq_log_message . '|' . $log->mq_reg_date->format('Y-m-d H:i:s');
                    })
                    ->toArray();
                
                \Log::info("기존 DB 로그 개수: " . count($existingLogEntries));
                
                $totalLogCount = count($gameData['gameLog']);
                $newLogsToSave = [];
                
                // 모든 게임로그를 확인하여 DB에 없는 로그만 저장 대상으로 선정
                foreach ($gameData['gameLog'] as $index => $logEntry) {
                    $message = null;
                    $type = 'info';
                    $timestamp = null;
                    
                    // gameLog가 객체 배열인 경우 처리
                    if (is_array($logEntry) && isset($logEntry['message'])) {
                        $message = $logEntry['message'];
                        $type = $logEntry['type'] ?? 'info';
                        $timestamp = $logEntry['timestamp'] ?? null;
                    }
                    // gameLog가 문자열 배열인 경우 처리 (이전 호환성)
                    else if (is_string($logEntry) && !empty(trim($logEntry))) {
                        $message = $logEntry;
                        $type = 'info';
                        $timestamp = null;
                    }
                    
                    if ($message && !empty(trim($message))) {
                        // 타임스탬프가 있으면 정확한 중복 체크
                        if ($timestamp) {
                            $parsedTimestamp = date('Y-m-d H:i:s', strtotime($timestamp));
                            $logKey = $message . '|' . $parsedTimestamp;
                            
                            // 메시지+타임스탬프 조합이 DB에 없으면 저장 대상에 추가
                            if (!in_array($logKey, $existingLogEntries)) {
                                $newLogsToSave[] = [
                                    'message' => $message,
                                    'type' => $type,
                                    'index' => $index,
                                    'timestamp' => $timestamp
                                ];
                            } else {
                                \Log::info("중복 로그 스킵: {$message} (시간: {$parsedTimestamp})");
                            }
                        } else {
                            // 타임스탬프가 없으면 메시지만으로 중복 체크 (기존 방식)
                            $existingMessages = array_map(function($entry) {
                                return explode('|', $entry)[0];
                            }, $existingLogEntries);
                            
                            if (!in_array($message, $existingMessages)) {
                                $newLogsToSave[] = [
                                    'message' => $message,
                                    'type' => $type,
                                    'index' => $index,
                                    'timestamp' => null
                                ];
                            } else {
                                \Log::info("중복 로그 스킵 (타임스탬프 없음): {$message}");
                            }
                        }
                    }
                }
                
                \Log::info("새로 저장할 로그 개수: " . count($newLogsToSave));
                
                // 새 로그들을 역순으로 저장 (가장 오래된 로그부터 저장)
                $reversedNewLogs = array_reverse($newLogsToSave);
                
                foreach ($reversedNewLogs as $logData) {
                    $timestampInfo = isset($logData['timestamp']) ? " (시간: {$logData['timestamp']})" : "";
                    \Log::info("새 로그 저장 시도 [{$logData['index']}]: {$logData['message']} (타입: {$logData['type']}){$timestampInfo}");
                    
                    try {
                        $result = CashflowLog::addLog(
                            $game->idx, 
                            $logData['message'], 
                            $logData['type'],
                            $logData['timestamp'] ?? null
                        );
                        \Log::info("로그 저장 성공: {$logData['message']}");
                    } catch (\Exception $e) {
                        \Log::error("로그 저장 실패: " . $e->getMessage());
                        \Log::error("로그 저장 스택: " . $e->getTraceAsString());
                    }
                }
                
                \Log::info("로그 저장 완료. 새로 저장된 로그: " . count($newLogsToSave) . "개");
                
                // 추가 디버깅: 저장 후 DB 로그 개수 재확인
                $finalLogCount = $game->logs()->count();
                \Log::info("저장 완료 후 DB 로그 개수: {$finalLogCount}");
                
            } else {
                \Log::warning('gameLog가 존재하지 않거나 배열이 아닙니다');
                if (isset($gameData['gameLog'])) {
                    \Log::warning('gameLog 타입: ' . gettype($gameData['gameLog']));
                }
            }
            
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'sessionKey' => $sessionKey,
                    'gameIdx' => $game->idx
                ]);
                
            } catch (\Exception $e) {
                DB::rollback();
                
                // 데드락 오류인 경우 재시도
                if ($this->isDeadlockError($e) && $retryCount < $maxRetries - 1) {
                    $retryCount++;
                    \Log::warning("데드락 발생, 재시도 {$retryCount}/{$maxRetries}: " . $e->getMessage());
                    
                    // 랜덤 지연 (10-100ms)
                    usleep(rand(10000, 100000));
                    continue;
                }
                
                // 최대 재시도 횟수 초과 또는 다른 오류
                \Log::error("게임 저장 실패 (재시도 {$retryCount}회): " . $e->getMessage());
                return response()->json([
                    'error' => '게임 상태 저장 실패',
                    'message' => $e->getMessage()
                ], 500);
            }
        }
    }
    
    /**
     * 데드락 오류 확인
     */
    private function isDeadlockError(\Exception $e)
    {
        return strpos($e->getMessage(), '1213') !== false || 
               strpos($e->getMessage(), 'Deadlock') !== false ||
               strpos($e->getMessage(), 'deadlock') !== false;
    }
    
    /**
     * 게임 상태 로드
     */
    public function loadGameState(Request $request)
    {
        try {
            $sessionKey = $request->input('sessionKey');
            
            // 현재 로그인한 사용자 확인
            if (!Auth::check()) {
                return response()->json(['error' => '사용자 인증이 필요합니다.'], 401);
            }
            
            $userId = $this->getUserId();
            
            if (!$sessionKey) {
                return response()->json(['error' => '세션 키가 필요합니다.'], 400);
            }
            
            $game = CashflowGame::with(['assets', 'liabilities', 'logs'])
                              ->where('mq_session_key', $sessionKey)
                              ->where('mq_user_id', $userId)
                              ->first();
            
            if (!$game) {
                return response()->json(['error' => '게임을 찾을 수 없습니다.'], 404);
            }
            
            return response()->json([
                'success' => true,
                'gameState' => $game->toGameState()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => '게임 상태 로드 실패',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 사용자의 게임 목록 조회
     */
    public function getUserGames(Request $request)
    {
        try {
            // 현재 로그인한 사용자 확인
            if (!Auth::check()) {
                return response()->json(['error' => '사용자 인증이 필요합니다.'], 401);
            }
            
            $userId = $this->getUserId();
            
            $games = CashflowGame::where('mq_user_id', $userId)
                               ->orderBy('mq_update_date', 'desc')
                               ->get()
                               ->map(function($game) {
                                   return [
                                       'sessionKey' => $game->mq_session_key,
                                       'playerName' => $game->mq_player_name,
                                       'profession' => $game->mq_profession,
                                       'dream' => $game->mq_dream,
                                       'gameStarted' => $game->mq_game_started,
                                       'cash' => $game->mq_cash,
                                       'monthlyCashFlow' => $game->mq_monthly_cash_flow,
                                       'updatedAt' => $game->mq_update_date,
                                   ];
                               });
            
            return response()->json([
                'success' => true,
                'games' => $games
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => '게임 목록 조회 실패',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 게임 삭제
     */
    public function deleteGame(Request $request)
    {
        try {
            $sessionKey = $request->input('sessionKey');
            
            // 현재 로그인한 사용자 확인
            if (!Auth::check()) {
                return response()->json(['error' => '사용자 인증이 필요합니다.'], 401);
            }
            
            $userId = $this->getUserId();
            
            if (!$sessionKey) {
                return response()->json(['error' => '세션 키가 필요합니다.'], 400);
            }
            
            $game = CashflowGame::where('mq_session_key', $sessionKey)
                              ->where('mq_user_id', $userId)
                              ->first();
            
            if (!$game) {
                return response()->json(['error' => '게임을 찾을 수 없습니다.'], 404);
            }
            
            $game->delete(); // 연관된 데이터는 cascade로 자동 삭제
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => '게임 삭제 실패',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 자산 저장 헬퍼 메소드
     */
    private function saveAsset($gameIdx, $asset)
    {
        if (!isset($asset['type'])) {
            return;
        }
        
        switch ($asset['type']) {
            case 'RealEstate':
                CashflowAsset::createRealEstate(
                    $gameIdx,
                    $asset['name'],
                    $this->cleanNumericValue($asset['purchasePrice'] ?? $asset['totalValue'] ?? 0),
                    $this->cleanNumericValue($asset['downPayment'] ?? 0),
                    $this->cleanNumericValue($asset['monthlyIncome'] ?? 0),
                    $asset['propertyId'] ?? null
                );
                break;
                
            default:
                CashflowAsset::createInvestment(
                    $gameIdx,
                    $asset['name'],
                    $this->cleanNumericValue($asset['totalValue'] ?? $asset['currentValue'] ?? 0),
                    $this->cleanNumericValue($asset['monthlyIncome'] ?? 0),
                    $asset['type']
                );
                break;
        }
    }
    
    /**
     * 부채 저장 헬퍼 메소드
     */
    private function saveLiability($gameIdx, $liability)
    {
        if (!isset($liability['type'])) {
            return;
        }
        
        // 안전한 배열 접근을 위한 기본값 설정
        $name = $liability['name'] ?? 'Unknown';
        
        // amount 필드가 없으면 totalAmount 또는 remainingAmount 사용
        $amountValue = $liability['amount'] ?? $liability['totalAmount'] ?? $liability['remainingAmount'] ?? 0;
        $amount = $this->cleanNumericValue($amountValue);
        
        $monthlyPayment = $this->cleanNumericValue($liability['monthlyPayment'] ?? 0);
        
        if ($liability['type'] === 'Mortgage' && isset($liability['propertyId'])) {
            CashflowLiability::createMortgage(
                $gameIdx,
                $name,
                $amount,
                $monthlyPayment,
                $liability['propertyId']
            );
        } else {
            CashflowLiability::createLiability(
                $gameIdx,
                $liability['type'],
                $name,
                $amount,
                $monthlyPayment
            );
        }
    }
}