<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InvestorPortfolio extends Model
{
    protected $table = 'investor_portfolio';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'investor_code',
        'investor_name',
        'portfolio_date',
        'portfolio_period',
        'portfolio_value',
        'number_of_stocks',
    ];

    // 날짜 필드를 Carbon 인스턴스로 변환
    protected $dates = [
        'record_created_at'
    ];

    /**
     * 이 포트폴리오에 속한 상세 종목 정보
     */
    public function details()
    {
        return $this->hasMany(InvestorPortfolioDetail::class, 'p_idx', 'idx');
    }
} 