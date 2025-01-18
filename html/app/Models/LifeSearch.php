<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LifeSearch extends Model
{
    protected $table = 'mq_life_search';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_type',
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

    const TYPE_DO = 'want_to_do';
    const TYPE_GO = 'want_to_go';
    const TYPE_SHARE = 'want_to_share';

    public static function getTypeLabel($type)
    {
        return [
            self::TYPE_DO => '하고 싶은 것',
            self::TYPE_GO => '가고 싶은 곳',
            self::TYPE_SHARE => '나누고 싶은 것'
        ][$type] ?? '';
    }
} 