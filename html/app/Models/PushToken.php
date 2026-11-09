<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 회원이 앱 푸시(FCM) 알림을 허용한 기기 토큰 하나.
 *
 * 등록/해지 규칙은 App\Services\PushService 가 갖고 있고, 이 모델은 테이블 매핑과
 * 자주 쓰는 조회 조건만 맡는다.
 */
class PushToken extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const PLATFORM_WEB = 'web';
    const PLATFORM_ANDROID = 'android';
    const PLATFORM_IOS = 'ios';

    protected $table = 'mq_push_tokens';
    protected $primaryKey = 'idx';

    // mq_reg_date / mq_update_date 를 직접 관리한다.
    public $timestamps = false;

    protected $fillable = [
        'mq_token',
        'mq_token_hash',
        'member_idx',
        'mq_platform',
        'mq_user_agent',
        'mq_status',
        'mq_reg_date',
        'mq_update_date',
    ];

    protected $casts = [
        'member_idx'     => 'integer',
        'mq_status'      => 'integer',
        'mq_reg_date'    => 'datetime',
        'mq_update_date' => 'datetime',
    ];

    /**
     * 토큰 원문으로 중복 판별용 해시를 만든다.
     *
     * @param  string $token
     * @return string
     */
    public static function hashToken($token)
    {
        return hash('sha256', $token);
    }

    /**
     * 회원 관계.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_idx', 'idx');
    }

    /**
     * 알림이 켜져 있는 토큰만.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('mq_status', self::STATUS_ACTIVE);
    }
}
