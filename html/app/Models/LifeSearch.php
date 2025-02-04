<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LifeSearch extends Model
{
    protected $table = 'mq_life_search';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_user_id',
        'mq_category',
        'mq_content',
        'mq_price',
        'mq_expected_time',
        'mq_reg_date',
        'mq_update_date'
    ];

    protected $casts = [
        'mq_price' => 'integer',
        'mq_reg_date' => 'datetime',
        'mq_update_date' => 'datetime'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'mq_user_id', 'mq_user_id');
    }
} 