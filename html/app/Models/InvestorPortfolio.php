<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\BoardPortfolio;

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
        'portfolio_avg_return',
    ];

    // 날짜 필드를 Carbon 인스턴스로 변환
    protected $dates = [
        'record_created_at',
        'record_updated_at'
    ];

    /**
     * 이 포트폴리오에 속한 상세 종목 정보
     */
    public function details()
    {
        return $this->hasMany(InvestorPortfolioDetail::class, 'p_idx', 'idx');
    }

    /**
     * 이 포트폴리오에 연결된 게시판 포트폴리오 정보
     */
    public function boardPortfolio()
    {
        return $this->hasOne(BoardPortfolio::class, 'mq_portfolio_idx', 'idx');
    }
} 