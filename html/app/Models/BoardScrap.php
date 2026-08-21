<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BoardScrap extends Model
{
    protected $table = 'mq_board_scrap';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    /**
     * mq_like_history 테이블에서 이 게시판을 식별하는 이름
     */
    const BOARD_NAME = 'board_scrap';

    protected $fillable = [
        'mq_user_id',
        'mq_title',
        'mq_url',
        'mq_reason',
        'mq_new_terms',
        'mq_thumbnail_url',
        'mq_is_public',
        'mq_public_date',
        'mq_view_cnt',
        'mq_like_cnt',
        'mq_status',
        'mq_reg_date',
        'mq_update_date'
    ];

    // 날짜 필드를 Carbon 인스턴스로 변환
    protected $dates = [
        'mq_reg_date',
        'mq_update_date',
        'mq_public_date'
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

    public function getMqPublicDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    /**
     * 삭제되지 않은 스크랩
     */
    public function scopeActive($query)
    {
        return $query->where('mq_status', 1);
    }

    /**
     * 공개된 스크랩만
     */
    public function scopePublicOnly($query)
    {
        return $query->where('mq_is_public', 1);
    }

    /**
     * 특정 회원이 작성한 스크랩만
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('mq_user_id', $userId);
    }

    /**
     * 해당 회원이 볼 수 있는 스크랩 (공개글 + 본인 글)
     *
     * @param string|null $userId 비로그인은 null
     */
    public function scopeVisibleTo($query, $userId = null)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('mq_is_public', 1);

            if ($userId) {
                $q->orWhere('mq_user_id', $userId);
            }
        });
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

    /**
     * 공개 상태인지 확인
     */
    public function isPublic()
    {
        return (int) $this->mq_is_public === 1;
    }

    /**
     * 목록에 노출할 작성자 이름
     */
    public function getAuthorName()
    {
        return $this->user->mq_user_name ?? '알 수 없음';
    }
}
