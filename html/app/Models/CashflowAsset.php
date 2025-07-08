<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 캐시플로우 게임 자산 모델
 * 
 * 테이블: mq_cashflow_assets
 * 용도: 플레이어가 보유한 모든 자산 관리 (주식, 펀드, 부동산, 수집품, 대출, 투자)
 */
class CashflowAsset extends Model
{
    protected $table = 'mq_cashflow_assets';
    protected $primaryKey = 'idx';
    protected $guarded = [];
    
    const CREATED_AT = 'mq_reg_date';
    const UPDATED_AT = 'mq_update_date';
    
    /**
     * 필드별 설명:
     * 
     * === 기본 정보 ===
     * idx: 자산 고유 식별자
     * mq_game_idx: 게임 테이블 외래키
     * mq_asset_type: 자산 유형 (Stock:주식, Fund:펀드, RealEstate:부동산, Collectible:수집품, Loan:대출, Investment:투자)
     * mq_name: 자산명 (회사명, 부동산명 등)
     * mq_symbol: 주식/펀드 심볼 (AAPL, MSFT 등) - 주식/펀드만 사용
     * 
     * === 가격 정보 ===
     * mq_purchase_price: 구매 가격 (부동산의 경우 총 구매가)
     * mq_current_value: 현재 가치/시가
     * mq_total_value: 총 자산 가치
     * mq_down_payment: 계약금/초기 투자금 (부동산용)
     * 
     * === 수익 정보 ===
     * mq_monthly_income: 월 수익 (임대료, 이자 등)
     * mq_monthly_dividend: 월 배당금 (주식/펀드용)
     * 
     * === 주식/펀드 정보 ===
     * mq_shares: 보유 주식/펀드 수량
     * mq_average_price: 평균 매입가
     * mq_total_invested: 총 투자 금액
     * 
     * === 부동산 정보 ===
     * mq_property_id: 부동산 고유 ID (모기지 연결용)
     * 
     * === 시스템 필드 ===
     * mq_reg_date: 자산 등록일시
     * mq_update_date: 자산 최종 수정일시
     */
    
    protected $casts = [
        'mq_purchase_price' => 'decimal:2',
        'mq_current_value' => 'decimal:2',
        'mq_total_value' => 'decimal:2',
        'mq_down_payment' => 'decimal:2',
        'mq_monthly_income' => 'decimal:2',
        'mq_monthly_dividend' => 'decimal:2',
        'mq_average_price' => 'decimal:2',
        'mq_total_invested' => 'decimal:2',
        'mq_shares' => 'integer',
        'mq_reg_date' => 'datetime',
        'mq_update_date' => 'datetime',
    ];
    
    /**
     * 자산이 속한 게임
     */
    public function game()
    {
        return $this->belongsTo(CashflowGame::class, 'mq_game_idx', 'idx');
    }
    
    /**
     * 부동산 자산과 연결된 모기지 부채
     */
    public function mortgage()
    {
        return $this->hasOne(CashflowLiability::class, 'mq_property_id', 'mq_property_id')
                    ->where('mq_liability_type', 'Mortgage');
    }
    
    /**
     * 자산을 JavaScript 객체 형태로 변환
     */
    public function toAssetObject()
    {
        $baseData = [
            'id' => $this->generateAssetId(),
            'name' => $this->mq_name,
            'type' => $this->mq_asset_type,
            'assetType' => $this->mq_asset_type,
            'monthlyIncome' => (float) $this->mq_monthly_income,
        ];
        
        switch ($this->mq_asset_type) {
            case 'Stock':
            case 'Fund':
                return array_merge($baseData, [
                    'symbol' => $this->mq_symbol,
                    'shares' => (int) $this->mq_shares,
                    'averagePrice' => (float) $this->mq_average_price,
                    'totalInvested' => (float) $this->mq_total_invested,
                    'monthlyDividend' => (float) $this->mq_monthly_dividend,
                    'totalValue' => (float) $this->mq_total_value,
                    'currentValue' => (float) $this->mq_current_value,
                ]);
                
            case 'RealEstate':
                return array_merge($baseData, [
                    'purchasePrice' => (float) $this->mq_purchase_price,
                    'totalValue' => (float) $this->mq_total_value,
                    'downPayment' => (float) $this->mq_down_payment,
                    'propertyId' => $this->mq_property_id,
                ]);
                
            default:
                return array_merge($baseData, [
                    'totalValue' => (float) ($this->mq_total_value ?: $this->mq_current_value),
                    'currentValue' => (float) $this->mq_current_value,
                ]);
        }
    }
    
    /**
     * 자산 ID 생성 (JavaScript와 호환)
     */
    public function generateAssetId()
    {
        switch ($this->mq_asset_type) {
            case 'Stock':
                return 'stock_' . strtolower($this->mq_symbol ?: $this->mq_name);
            case 'Fund':
                return 'fund_' . strtolower($this->mq_symbol ?: $this->mq_name);
            case 'RealEstate':
                return 'realestate_' . $this->idx;
            default:
                return strtolower($this->mq_asset_type) . '_' . $this->idx;
        }
    }
    
    /**
     * 주식 자산 생성
     */
    public static function createStock($gameIdx, $symbol, $shares, $price, $dividend = 0)
    {
        return static::create([
            'mq_game_idx' => $gameIdx,
            'mq_asset_type' => 'Stock',
            'mq_name' => $symbol,
            'mq_symbol' => $symbol,
            'mq_shares' => $shares,
            'mq_average_price' => $price,
            'mq_total_invested' => $shares * $price,
            'mq_monthly_dividend' => $dividend,
            'mq_current_value' => $shares * $price,
            'mq_total_value' => $shares * $price,
        ]);
    }
    
    /**
     * 펀드 자산 생성
     */
    public static function createFund($gameIdx, $symbol, $shares, $price, $dividend = 0)
    {
        return static::create([
            'mq_game_idx' => $gameIdx,
            'mq_asset_type' => 'Fund',
            'mq_name' => $symbol,
            'mq_symbol' => $symbol,
            'mq_shares' => $shares,
            'mq_average_price' => $price,
            'mq_total_invested' => $shares * $price,
            'mq_monthly_dividend' => $dividend,
            'mq_current_value' => $shares * $price,
            'mq_total_value' => $shares * $price,
        ]);
    }
    
    /**
     * 부동산 자산 생성
     */
    public static function createRealEstate($gameIdx, $name, $purchasePrice, $downPayment, $monthlyIncome, $propertyId = null)
    {
        return static::create([
            'mq_game_idx' => $gameIdx,
            'mq_asset_type' => 'RealEstate',
            'mq_name' => $name,
            'mq_purchase_price' => $purchasePrice,
            'mq_total_value' => $purchasePrice,
            'mq_current_value' => $purchasePrice,
            'mq_down_payment' => $downPayment,
            'mq_monthly_income' => $monthlyIncome,
            'mq_property_id' => $propertyId ?: uniqid('property_'),
        ]);
    }
    
    /**
     * 일반 투자 자산 생성
     */
    public static function createInvestment($gameIdx, $name, $value, $monthlyIncome = 0, $type = 'Investment')
    {
        return static::create([
            'mq_game_idx' => $gameIdx,
            'mq_asset_type' => $type,
            'mq_name' => $name,
            'mq_total_value' => $value,
            'mq_current_value' => $value,
            'mq_monthly_income' => $monthlyIncome,
        ]);
    }
}