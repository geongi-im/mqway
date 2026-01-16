<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;

class BoardInsights extends Model
{
    protected $table = 'mq_board_insights';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_title',
        'mq_content',
        'mq_category',
        'mq_user_id',
        'mq_image',
        'mq_original_image',
        'mq_view_cnt',
        'mq_like_cnt',
        'mq_status',
        'mq_reg_date',
        'mq_update_date'
    ];

    protected $dates = [
        'mq_reg_date',
        'mq_update_date'
    ];

    public function getMqRegDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getMqUpdateDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    protected $casts = [
        'mq_image' => 'array',
        'mq_original_image' => 'array'
    ];

    public function getImageOriginalName($index)
    {
        return !empty($this->mq_original_image) && is_array($this->mq_original_image) && isset($this->mq_original_image[$index])
            ? $this->mq_original_image[$index]
            : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'mq_user_id', 'mq_user_id');
    }
}
