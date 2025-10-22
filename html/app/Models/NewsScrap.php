<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NewsScrap extends Model
{
    protected $table = 'mq_news_scrap';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_user_id',
        'mq_title',
        'mq_url',
        'mq_reason',
        'mq_new_terms',
        'mq_thumbnail_url',
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
     * 썸네일 이미지가 있는지 확인
     */
    public function hasThumbnail()
    {
        return !empty($this->mq_thumbnail_url);
    }

    /**
     * 썸네일 이미지 URL 반환
     */
    public function getThumbnailUrl()
    {
        return $this->mq_thumbnail_url ?? asset('images/default-news-thumbnail.png');
    }

    /**
     * 사용자와의 관계 정의
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Member::class, 'mq_user_id', 'mq_user_id');
    }

    /**
     * 본인의 스크랩인지 확인
     */
    public function isOwner($userId)
    {
        return $this->mq_user_id === $userId;
    }
}
