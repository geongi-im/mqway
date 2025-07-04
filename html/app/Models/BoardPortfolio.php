<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;

class BoardPortfolio extends Model
{
    protected $table = 'mq_board_portfolio';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_title',
        'mq_portfolio_idx',
        'mq_investor_code',
        'mq_user_id',
        'mq_view_cnt',
        'mq_like_cnt',
        'mq_status',
        'mq_reg_date',
        'mq_update_date'
    ];

    // 날짜 필드를 Carbon 인스턴스로 변환
    protected $dates = [
        'mq_reg_date',
        'mq_update_date'
    ];

    // 날짜를 항상 Carbon 인스턴스로 변환
    public function getMqRegDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getMqUpdateDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }
    
    /**
     * 사용자와의 관계 정의
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'mq_user_id', 'mq_user_id');
    }
} 