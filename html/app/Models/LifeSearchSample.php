<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LifeSearchSample extends Model
{
    protected $table = 'mq_life_search_sample';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_s_category',
        'mq_s_content',
        'mq_s_price',
        'mq_s_target_date',
        'mq_reg_date'
    ];

    protected $casts = [
        'mq_s_price' => 'integer',
        'mq_s_target_date' => 'date',
        'mq_reg_date' => 'datetime'
    ];

    // 카테고리별 색상 매핑 - LifeSearch 모델과 동일하게 사용
    public static $categoryColors = [
        '여행' => 'bg-blue-100 text-blue-800',
        '취미' => 'bg-green-100 text-green-800',
        '음식' => 'bg-orange-100 text-orange-800',
        '문화' => 'bg-purple-100 text-purple-800',
        '학습' => 'bg-yellow-100 text-yellow-800',
        '건강' => 'bg-red-100 text-red-800',
        '생활' => 'bg-gray-100 text-gray-800'
    ];

    public function getCategoryColorClass()
    {
        return self::$categoryColors[$this->mq_s_category] ?? 'bg-gray-100 text-gray-800';
    }
} 