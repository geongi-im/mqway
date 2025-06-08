<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InvestorPortfolioDetail extends Model
{
    protected $table = 'investor_portfolio_detail';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'p_idx',
        'ticker',
        'stk_name',
        'portfolio_rate',
        'recent_activity_type',
        'recent_activity_value',
        'shares',
        'reported_price',
        'reported_value_amount',
        'current_price',
        'reported_price_rate',
        'low_52_week',
        'high_52_week'
    ];

    /**
     * 이 상세 항목이 속한 포트폴리오
     */
    public function portfolio()
    {
        return $this->belongsTo(InvestorPortfolio::class, 'p_idx', 'idx');
    }
} 