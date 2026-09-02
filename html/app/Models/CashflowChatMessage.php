<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 캐시플로우 챗봇의 활성 대화 한 발화.
 *
 * 회원 한 명당 활성 대화 하나를 이 테이블에 그대로 쌓는다. 저장·정리 규칙은
 * App\Services\CashflowChatHistory 가 갖고 있고, 이 모델은 테이블 매핑과
 * 자주 쓰는 조회 조건만 맡는다.
 */
class CashflowChatMessage extends Model
{
    /** 사용자 발화 */
    const ROLE_USER = 'user';

    /** 챗봇 발화. Gemini contents 의 role 값과 같은 표기를 쓴다. */
    const ROLE_MODEL = 'model';

    protected $table = 'mq_cashflow_chat_messages';
    protected $primaryKey = 'idx';

    // 테이블이 mq_reg_date 한 칸만 두므로 Eloquent 의 created_at/updated_at 은 쓰지 않는다.
    public $timestamps = false;

    protected $fillable = [
        'member_idx',
        'mq_role',
        'mq_message',
        'mq_has_image',
        'mq_reg_date',
    ];

    protected $casts = [
        'member_idx'   => 'integer',
        'mq_has_image' => 'boolean',
        'mq_reg_date'  => 'datetime',
    ];

    /**
     * 회원 관계.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_idx', 'idx');
    }

    /**
     * 특정 회원의 발화만.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $memberIdx
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfMember($query, $memberIdx)
    {
        return $query->where('member_idx', $memberIdx);
    }
}
