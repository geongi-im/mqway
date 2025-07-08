<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 캐시플로우 게임 메인 모델
 * 
 * 테이블: mq_cashflow_games
 * 용도: 게임 세션 및 플레이어의 전체 재무 상태 관리
 */
class CashflowGame extends Model
{
    protected $table = 'mq_cashflow_games';
    protected $primaryKey = 'idx';
    protected $guarded = [];
    
    const CREATED_AT = 'mq_reg_date';
    const UPDATED_AT = 'mq_update_date';
    
    /**
     * 필드별 설명:
     * 
     * === 기본 정보 ===
     * idx: 게임 고유 식별자
     * mq_user_id: 게임을 플레이하는 사용자 ID
     * mq_session_key: 게임 세션 고유 키 (UUID)
     * mq_player_name: 플레이어 이름
     * mq_profession: 선택한 직업 (의사, 엔지니어, 교사 등)
     * mq_dream: 플레이어의 꿈/목표
     * mq_dream_cost: 꿈 달성에 필요한 금액
     * mq_game_started: 게임 시작 여부
     * 
     * === 재무 상태 정보 ===
     * mq_cash: 현재 보유 현금
     * mq_salary: 월 급여 (근로 소득)
     * mq_passive_income: 월 패시브 인컴 (임대료, 배당금 등)
     * mq_total_income: 월 총 수입 (급여 + 패시브 인컴)
     * mq_total_expenses: 월 총 지출
     * mq_monthly_cash_flow: 월 현금 흐름 (수입 - 지출)
     * mq_has_child: 자녀 보유 여부
     * mq_children_count: 자녀 수
     * mq_per_child_expense: 자녀 1명당 월 지출액
     * 
     * === 지출 내역 상세 ===
     * mq_expenses_taxes: 월 세금
     * mq_expenses_home_payment: 월 주택 대출 상환액
     * mq_expenses_school_loan: 월 학자금 대출 상환액
     * mq_expenses_car_loan: 월 자동차 대출 상환액
     * mq_expenses_credit_card: 월 신용카드 결제액
     * mq_expenses_retail: 월 소매 지출 (생활비)
     * mq_expenses_other: 월 기타 지출 (모기지 상환액 등)
     * mq_expenses_children: 월 자녀 관련 지출
     * 
     * === 시스템 필드 ===
     * mq_reg_date: 게임 생성일시
     * mq_update_date: 게임 최종 수정일시
     */
    
    protected $casts = [
        'mq_dream_cost' => 'decimal:2',
        'mq_cash' => 'decimal:2',
        'mq_salary' => 'decimal:2',
        'mq_passive_income' => 'decimal:2',
        'mq_total_income' => 'decimal:2',
        'mq_total_expenses' => 'decimal:2',
        'mq_monthly_cash_flow' => 'decimal:2',
        'mq_per_child_expense' => 'decimal:2',
        'mq_expenses_taxes' => 'decimal:2',
        'mq_expenses_home_payment' => 'decimal:2',
        'mq_expenses_school_loan' => 'decimal:2',
        'mq_expenses_car_loan' => 'decimal:2',
        'mq_expenses_credit_card' => 'decimal:2',
        'mq_expenses_retail' => 'decimal:2',
        'mq_expenses_other' => 'decimal:2',
        'mq_expenses_children' => 'decimal:2',
        'mq_game_started' => 'boolean',
        'mq_has_child' => 'boolean',
        'mq_children_count' => 'integer',
        'mq_reg_date' => 'datetime',
        'mq_update_date' => 'datetime',
    ];
    
    /**
     * 게임에 속한 자산들
     */
    public function assets()
    {
        return $this->hasMany(CashflowAsset::class, 'mq_game_idx', 'idx');
    }
    
    /**
     * 게임에 속한 부채들
     */
    public function liabilities()
    {
        return $this->hasMany(CashflowLiability::class, 'mq_game_idx', 'idx');
    }
    
    /**
     * 게임 로그들
     */
    public function logs()
    {
        return $this->hasMany(CashflowLog::class, 'mq_game_idx', 'idx');
    }
    
    /**
     * 세션 키로 게임 찾기
     */
    public static function findBySessionKey($sessionKey)
    {
        return static::where('mq_session_key', $sessionKey)->first();
    }
    
    /**
     * 사용자 ID로 게임들 찾기
     */
    public static function findByUserId($userId)
    {
        return static::where('mq_user_id', $userId)->orderBy('mq_update_date', 'desc')->get();
    }
    
    /**
     * 게임 상태를 JavaScript 객체 형태로 변환
     */
    public function toGameState()
    {
        return [
            'player' => [
                'name' => $this->mq_player_name,
                'profession' => $this->mq_profession,
                'dream' => $this->mq_dream,
                'dreamCost' => (float) $this->mq_dream_cost,
                'cash' => (float) $this->mq_cash,
                'monthlyCashFlow' => (float) $this->mq_monthly_cash_flow,
                'totalIncome' => (float) $this->mq_total_income,
                'totalExpenses' => (float) $this->mq_total_expenses,
                'salary' => (float) $this->mq_salary,
                'passiveIncome' => (float) $this->mq_passive_income,
                'hasChild' => $this->mq_has_child,
                'assets' => $this->assets->map(function($asset) {
                    return $asset->toAssetObject();
                })->toArray(),
                'liabilities' => $this->liabilities->map(function($liability) {
                    return $liability->toLiabilityObject();
                })->toArray(),
                'expenses' => [
                    'taxes' => (float) $this->mq_expenses_taxes,
                    'homePayment' => (float) $this->mq_expenses_home_payment,
                    'schoolLoan' => (float) $this->mq_expenses_school_loan,
                    'carLoan' => (float) $this->mq_expenses_car_loan,
                    'creditCard' => (float) $this->mq_expenses_credit_card,
                    'retail' => (float) $this->mq_expenses_retail,
                    'other' => (float) $this->mq_expenses_other,
                    'children' => (float) $this->mq_expenses_children,
                    'totalExpenses' => (float) $this->mq_total_expenses,
                    'childrenCount' => (int) $this->mq_children_count,
                    'perChildExpense' => (float) $this->mq_per_child_expense,
                ],
                'stocks' => $this->getStocksData(),
                'funds' => $this->getFundsData(),
                'emergencyLoans' => $this->getEmergencyLoansData(),
            ],
            'gameStarted' => $this->mq_game_started,
            'gameLog' => $this->logs->map(function($log) {
                return [
                    'timestamp' => $log->mq_reg_date->toISOString(),
                    'message' => $log->mq_log_message,
                    'type' => $log->mq_log_type,
                    'details' => null
                ];
            })->toArray(),
            'currentSellingAssetId' => null,
        ];
    }
    
    /**
     * 주식 데이터 가져오기
     */
    private function getStocksData()
    {
        $stocks = [];
        $stockAssets = $this->assets()->where('mq_asset_type', 'Stock')->get();
        
        foreach ($stockAssets as $asset) {
            if ($asset->mq_symbol) {
                $stocks[$asset->mq_symbol] = [
                    'shares' => (int) $asset->mq_shares,
                    'averagePrice' => (float) $asset->mq_average_price,
                    'totalInvested' => (float) $asset->mq_total_invested,
                    'monthlyDividend' => (float) $asset->mq_monthly_dividend,
                ];
            }
        }
        
        return $stocks;
    }
    
    /**
     * 펀드 데이터 가져오기
     */
    private function getFundsData()
    {
        $funds = [];
        $fundAssets = $this->assets()->where('mq_asset_type', 'Fund')->get();
        
        foreach ($fundAssets as $asset) {
            if ($asset->mq_symbol) {
                $funds[$asset->mq_symbol] = [
                    'shares' => (int) $asset->mq_shares,
                    'averagePrice' => (float) $asset->mq_average_price,
                    'totalInvested' => (float) $asset->mq_total_invested,
                    'monthlyDividend' => (float) $asset->mq_monthly_dividend,
                ];
            }
        }
        
        return $funds;
    }
    
    /**
     * 응급 대출 데이터 가져오기
     */
    private function getEmergencyLoansData()
    {
        return $this->liabilities()
            ->where('mq_liability_type', 'Emergency')
            ->get()
            ->map(function($liability) {
                return [
                    'loanAmount' => (float) $liability->mq_amount,
                    'interestRate' => (float) $liability->mq_interest_rate,
                    'monthlyPayment' => (float) $liability->mq_monthly_payment,
                    'remainingBalance' => (float) $liability->mq_remaining_balance,
                ];
            })
            ->toArray();
    }
}