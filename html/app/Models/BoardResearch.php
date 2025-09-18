<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;

class BoardResearch extends Model
{
    protected $table = 'mq_board_research';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_title',
        'mq_content',
        'mq_category',
        'mq_user_id',
        'mq_image',
        'mq_original_image',
        'mq_thumbnail_image',
        'mq_thumbnail_original',
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
     * 속성 캐스팅
     */
    protected $casts = [
        'mq_image' => 'array',
        'mq_original_image' => 'array',
        'mq_thumbnail_image' => 'array',
        'mq_thumbnail_original' => 'array'
    ];

    /**
     * 썸네일 이미지가 있는지 확인
     */
    public function hasThumbnail()
    {
        return !empty($this->mq_thumbnail_image) && is_array($this->mq_thumbnail_image);
    }

    /**
     * 첫 번째 썸네일 이미지 URL 반환
     */
    public function getThumbnailUrl()
    {
        if ($this->hasThumbnail()) {
            $filename = $this->mq_thumbnail_image[0];
            return !filter_var($filename, FILTER_VALIDATE_URL)
                ? asset('storage/uploads/board_research/' . $filename)
                : $filename;
        }
        return null;
    }

    /**
     * 썸네일 이미지 파일명 반환
     */
    public function getThumbnailFilename()
    {
        return $this->hasThumbnail() ? $this->mq_thumbnail_image[0] : null;
    }

    /**
     * 썸네일 원본 파일명 반환
     */
    public function getThumbnailOriginalName()
    {
        return !empty($this->mq_thumbnail_original) && is_array($this->mq_thumbnail_original)
            ? $this->mq_thumbnail_original[0]
            : null;
    }

    /**
     * 첨부 이미지 원본 파일명 반환
     */
    public function getImageOriginalName($index)
    {
        return !empty($this->mq_original_image) && is_array($this->mq_original_image) && isset($this->mq_original_image[$index])
            ? $this->mq_original_image[$index]
            : null;
    }

    /**
     * 사용자와의 관계 정의
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'mq_user_id', 'mq_user_id');
    }
} 