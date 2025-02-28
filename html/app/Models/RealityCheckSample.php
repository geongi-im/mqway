<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealityCheckSample extends Model
{
    protected $table = 'mq_reality_check_sample';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_s_age',
        'mq_s_gender',
        'mq_s_name',
        'mq_s_description',
        'mq_s_content'
    ];

    /**
     * JSON 데이터를 자동으로 배열로 변환
     */
    protected $casts = [
        'mq_s_content' => 'array',
    ];
} 