<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealityCheck extends Model
{
    protected $table = 'mq_reality_check';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_user_id',
        'mq_category',
        'mq_expected_amount',
        'mq_actual_amount',
        'mq_reg_date',
        'mq_update_date'
    ];

    // Member 모델과의 관계 설정
    public function member()
    {
        return $this->belongsTo(Member::class, 'mq_user_id', 'mq_user_id');
    }
} 