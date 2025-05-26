<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class EconomyTermGameHistory extends Model
{
    /**
     * 모델과 연결된 테이블 이름
     *
     * @var string
     */
    protected $table = 'mq_economy_term_game_history';

    /**
     * 테이블의 기본 키
     *
     * @var string
     */
    protected $primaryKey = 'idx';

    /**
     * 모델 타임스탬프 사용 여부
     * mq_reg_date를 직접 관리하므로 false로 설정합니다.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 대량 할당 가능한 속성
     *
     * @var array
     */
    protected $fillable = [
        'mq_user_id',
        'mq_total_count',
        'mq_correct_count',
        'mq_duration_time',
        'mq_reg_date',
    ];

    /**
     * 날짜로 취급되어야 하는 속성들.
     * mq_reg_date를 Carbon 인스턴스로 다루기 위해 추가합니다.
     *
     * @var array
     */
    protected $dates = [
        'mq_reg_date',
    ];

    /**
     * 이 게임 기록에 해당하는 사용자를 가져옵니다.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'mq_user_id', 'idx');
    }
}