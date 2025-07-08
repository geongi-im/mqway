<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 캐시플로우 게임 부채 모델
 * 
 * 테이블: mq_cashflow_liabilities
 * 용도: 플레이어가 보유한 모든 부채 관리 (모기지, 대출, 신용카드, 응급대출 등)
 */
class CashflowLiability extends Model
{
    protected $table = 'mq_cashflow_liabilities';
    protected $primaryKey = 'idx';
    protected $guarded = [];
    
    const CREATED_AT = 'mq_reg_date';
    const UPDATED_AT = 'mq_update_date';
    
    /**
     * 필드별 설명:
     * 
     * === 기본 정보 ===
     * idx: 부채 고유 식별자
     * mq_game_idx: 게임 테이블 외래키
     * mq_liability_type: 부채 유형 (Mortgage:모기지, Loan:일반대출, CreditCard:신용카드, 
     *                               HomeLoan:주택대출, SchoolLoan:학자금대출, CarLoan:자동차대출, Emergency:응급대출)
     * mq_name: 부채명 (은행명, 카드사명 등)
     * mq_amount: 부채 총액/대출 원금
     * mq_monthly_payment: 월 상환액
     * mq_property_id: 연결된 부동산 ID (모기지 전용)
     * 
     * === 응급 대출 추가 정보 ===
     * mq_interest_rate: 연 이자율 (%) - 응급대출용
     * mq_remaining_balance: 잔여 대출 잔액 - 응급대출용
     * 
     * === 시스템 필드 ===
     * mq_reg_date: 부채 등록일시
     * mq_update_date: 부채 최종 수정일시
     */
    
    protected $casts = [
        'mq_amount' => 'decimal:2',
        'mq_monthly_payment' => 'decimal:2',
        'mq_interest_rate' => 'decimal:2',
        'mq_remaining_balance' => 'decimal:2',
        'mq_reg_date' => 'datetime',
        'mq_update_date' => 'datetime',
    ];
    
    /**
     * 부채가 속한 게임
     */
    public function game()
    {
        return $this->belongsTo(CashflowGame::class, 'mq_game_idx', 'idx');
    }
    
    /**
     * 모기지와 연결된 부동산 자산
     */
    public function property()
    {
        return $this->belongsTo(CashflowAsset::class, 'mq_property_id', 'mq_property_id')
                    ->where('mq_asset_type', 'RealEstate');
    }
    
    /**
     * 부채를 JavaScript 객체 형태로 변환
     */
    public function toLiabilityObject()
    {
        
        $baseData = [
            'id' => $this->generateLiabilityId(),
            'name' => $this->mq_name,
            'type' => $this->mq_liability_type,
            'amount' => (float) ($this->mq_amount ?: 0),
            'totalAmount' => (float) ($this->mq_amount ?: 0), // JavaScript 호환성
            'remainingAmount' => (float) ($this->mq_remaining_balance ?: $this->mq_amount ?: 0), // UI 표시용
            'monthlyPayment' => (float) ($this->mq_monthly_payment ?: 0),
        ];
        
        switch ($this->mq_liability_type) {
            case 'Mortgage':
                $result = array_merge($baseData, [
                    'propertyId' => $this->mq_property_id,
                ]);
                break;
                
            case 'Emergency':
                $result = array_merge($baseData, [
                    'interestRate' => (float) ($this->mq_interest_rate ?: 0),
                    'remainingBalance' => (float) ($this->mq_remaining_balance ?: $this->mq_amount ?: 0),
                ]);
                break;
                
            default:
                $result = $baseData;
                break;
        }
        
        return $result;
    }
    
    /**
     * 부채 ID 생성 (JavaScript와 호환)
     */
    public function generateLiabilityId()
    {
        switch ($this->mq_liability_type) {
            case 'Mortgage':
                return 'mortgage_' . $this->mq_property_id;
            case 'Emergency':
                return 'emergency_loan_' . $this->idx;
            default:
                return strtolower($this->mq_liability_type) . '_' . $this->idx;
        }
    }
    
    /**
     * 모기지 부채 생성
     */
    public static function createMortgage($gameIdx, $propertyName, $amount, $monthlyPayment, $propertyId)
    {
        return static::create([
            'mq_game_idx' => $gameIdx,
            'mq_liability_type' => 'Mortgage',
            'mq_name' => $propertyName . ' 모기지',
            'mq_amount' => $amount,
            'mq_monthly_payment' => $monthlyPayment,
            'mq_property_id' => $propertyId,
        ]);
    }
    
    /**
     * 응급 대출 생성
     */
    public static function createEmergencyLoan($gameIdx, $amount, $interestRate, $monthlyPayment)
    {
        return static::create([
            'mq_game_idx' => $gameIdx,
            'mq_liability_type' => 'Emergency',
            'mq_name' => '응급 대출',
            'mq_amount' => $amount,
            'mq_monthly_payment' => $monthlyPayment,
            'mq_interest_rate' => $interestRate,
            'mq_remaining_balance' => $amount,
        ]);
    }
    
    /**
     * 일반 부채 생성
     */
    public static function createLiability($gameIdx, $type, $name, $amount, $monthlyPayment)
    {
        return static::create([
            'mq_game_idx' => $gameIdx,
            'mq_liability_type' => $type,
            'mq_name' => $name,
            'mq_amount' => $amount,
            'mq_monthly_payment' => $monthlyPayment,
        ]);
    }
    
    /**
     * 속성 ID로 모기지 찾기
     */
    public static function findMortgageByPropertyId($gameIdx, $propertyId)
    {
        return static::where('mq_game_idx', $gameIdx)
                    ->where('mq_liability_type', 'Mortgage')
                    ->where('mq_property_id', $propertyId)
                    ->first();
    }
    
    /**
     * 응급 대출들 가져오기
     */
    public static function getEmergencyLoans($gameIdx)
    {
        return static::where('mq_game_idx', $gameIdx)
                    ->where('mq_liability_type', 'Emergency')
                    ->get();
    }
}