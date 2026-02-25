<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeedWantItem extends Model
{
    protected $table = 'mq_need_want_item';

    protected $primaryKey = 'idx';

    public $timestamps = false;

    protected $fillable = [
        'mq_name',
        'mq_description',
        'mq_image',
        'mq_is_active',
        'mq_reg_date',
        'mq_update_date',
    ];

    protected $dates = [
        'mq_reg_date',
        'mq_update_date',
    ];

    /**
     * 활성화된 아이템만 조회하는 스코프
     */
    public function scopeActive($query)
    {
        return $query->where('mq_is_active', 1);
    }
}
