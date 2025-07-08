<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 캐시플로우 게임 로그 모델
 * 
 * 테이블: mq_cashflow_logs
 * 용도: 게임 진행 중 발생하는 모든 이벤트 로그 저장 (카드 구매, 자산 판매, 수입/지출 변화 등)
 */
class CashflowLog extends Model
{
    protected $table = 'mq_cashflow_logs';
    protected $primaryKey = 'idx';
    protected $guarded = [];
    
    const CREATED_AT = 'mq_reg_date';
    const UPDATED_AT = null; // 로그는 업데이트하지 않음
    
    /**
     * 필드별 설명:
     * 
     * === 기본 정보 ===
     * idx: 로그 고유 식별자
     * mq_game_idx: 게임 테이블 외래키
     * mq_log_message: 게임 진행 로그 메시지 (카드 구매, 자산 판매 등)
     * mq_log_type: 로그 유형 (info:일반정보, success:성공, warning:경고, error:오류)
     * 
     * === 시스템 필드 ===
     * mq_reg_date: 로그 생성일시
     * 
     * === 로그 예시 ===
     * - "김민수님이 의사로 게임을 시작했습니다!"
     * - "McDonald's 주식 100주를 $50에 구매했습니다."
     * - "Duplex 부동산을 $50,000에 판매했습니다."
     * - "자녀가 생겨 월 지출이 $200 증가했습니다."
     */
    
    protected $casts = [
        'mq_reg_date' => 'datetime',
    ];
    
    /**
     * 로그가 속한 게임
     */
    public function game()
    {
        return $this->belongsTo(CashflowGame::class, 'mq_game_idx', 'idx');
    }
    
    /**
     * 게임 로그 추가
     */
    public static function addLog($gameIdx, $message, $type = 'info', $timestamp = null)
    {
        // 로그 타입을 데이터베이스 enum 값에 맞게 매핑
        $mappedType = static::mapLogType($type);
        
        // 타임스탬프 처리
        $regDate = null;
        if ($timestamp) {
            try {
                $regDate = date('Y-m-d H:i:s', strtotime($timestamp));
            } catch (\Exception $e) {
                \Log::warning("타임스탬프 파싱 실패: {$timestamp}, 현재 시간 사용");
                $regDate = now();
            }
        } else {
            $regDate = now();
        }
        
        return static::create([
            'mq_game_idx' => $gameIdx,
            'mq_log_message' => $message,
            'mq_log_type' => $mappedType,
            'mq_reg_date' => $regDate,
        ]);
    }
    
    /**
     * 로그 타입을 데이터베이스 enum 값에 매핑
     */
    private static function mapLogType($type)
    {
        $typeMapping = [
            // 양수 이벤트 → success
            'event-positive' => 'success',
            'income' => 'success',
            'success' => 'success',
            
            // 음수 이벤트 → warning  
            'event-negative' => 'warning',
            'warning' => 'warning',
            
            // 지출 관련 → expense
            'expense' => 'expense',
            'expense-detail' => 'expense',
            
            // 오류 → error
            'error' => 'error',
            
            // 기본 → info
            'info' => 'info',
        ];
        
        return $typeMapping[$type] ?? 'info';
    }
    
    /**
     * 게임의 모든 로그 가져오기
     */
    public static function getGameLogs($gameIdx, $limit = null)
    {
        $query = static::where('mq_game_idx', $gameIdx)
                       ->orderBy('mq_reg_date', 'asc');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }
    
    /**
     * 게임의 최근 로그 가져오기
     */
    public static function getRecentLogs($gameIdx, $limit = 10)
    {
        return static::where('mq_game_idx', $gameIdx)
                    ->orderBy('mq_reg_date', 'desc')
                    ->limit($limit)
                    ->get()
                    ->reverse()
                    ->values();
    }
    
    /**
     * 특정 타입의 로그들 가져오기
     */
    public static function getLogsByType($gameIdx, $type)
    {
        return static::where('mq_game_idx', $gameIdx)
                    ->where('mq_log_type', $type)
                    ->orderBy('mq_reg_date', 'asc')
                    ->get();
    }
    
    /**
     * 게임 로그 개수 가져오기
     */
    public static function getLogCount($gameIdx)
    {
        return static::where('mq_game_idx', $gameIdx)->count();
    }
    
    /**
     * 오래된 로그 정리 (선택적)
     */
    public static function cleanOldLogs($gameIdx, $keepDays = 30)
    {
        $cutoffDate = now()->subDays($keepDays);
        
        return static::where('mq_game_idx', $gameIdx)
                    ->where('mq_reg_date', '<', $cutoffDate)
                    ->delete();
    }
}