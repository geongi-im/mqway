<?php

namespace App\Models;

use App\Services\ExpService;
use Illuminate\Database\Eloquent\Model;

/**
 * 경험치 지급 원장 (append only)
 *
 * 한 줄이 한 번의 지급이다. 수정하거나 지우지 않는다.
 * 회원의 누적 경험치는 항상 이 표의 합이며, mq_member_exp 는 그 합을 복사해 둔 캐시일 뿐이다.
 */
class ExpHistory extends Model
{
    protected $table = 'mq_exp_history';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_user_id',
        'mq_action_code',
        'mq_ref_key',
        'mq_exp',
        'mq_reg_date',
    ];

    protected $dates = [
        'mq_reg_date',
    ];

    /**
     * 지급 사유 문구 (config/exp.php 의 label)
     *
     * @return string
     */
    public function getLabel()
    {
        $action = ExpService::action($this->mq_action_code);

        return $action && isset($action['label']) ? $action['label'] : $this->mq_action_code;
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('mq_user_id', $userId);
    }
}
