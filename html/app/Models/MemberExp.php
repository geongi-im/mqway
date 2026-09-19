<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 회원 등급 / 경험치 캐시 (회원당 1행)
 *
 * mq_exp_history 에서 파생된 값만 담는다. 값이 틀어져도 ExpService::syncMember 로
 * 언제든 다시 만들 수 있다.
 *
 * mq_member.mq_level 은 권한 값이라 이 모델과 무관하다.
 */
class MemberExp extends Model
{
    protected $table = 'mq_member_exp';
    protected $primaryKey = 'mq_user_id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'mq_user_id',
        'mq_total_exp',
        'mq_grade_code',
        'mq_grade_date',
        'mq_current_streak',
        'mq_best_streak',
        'mq_last_visit_date',
        'mq_reg_date',
        'mq_update_date',
    ];

    protected $dates = [
        'mq_grade_date',
        'mq_last_visit_date',
        'mq_reg_date',
        'mq_update_date',
    ];

    public function user()
    {
        return $this->belongsTo(Member::class, 'mq_user_id', 'mq_user_id');
    }
}
