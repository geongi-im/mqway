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
        'mq_target_date',
        'mq_reg_date',
        'mq_update_date'
    ];

    protected $casts = [
        'mq_price' => 'integer',
        'mq_target_date' => 'date',
        'mq_reg_date' => 'datetime',
        'mq_update_date' => 'datetime'
    ];

    // 카테고리별 색상 매핑
    public static $categoryColors = [
        '여행' => 'bg-blue-100 text-blue-800',
        '취미' => 'bg-green-100 text-green-800',
        '음식' => 'bg-orange-100 text-orange-800',
        '문화' => 'bg-purple-100 text-purple-800',
        '학습' => 'bg-yellow-100 text-yellow-800',
        '건강' => 'bg-red-100 text-red-800',
        '생활' => 'bg-gray-100 text-gray-800',
        '기타' => 'bg-indigo-100 text-indigo-800'
    ];

    // 카테고리 색상 클래스를 가져오는 메서드
    public function getCategoryColorClass()
    {
        return self::$categoryColors[$this->mq_category] ?? 'bg-gray-100 text-gray-800';
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'mq_user_id', 'mq_user_id');
    }
} 