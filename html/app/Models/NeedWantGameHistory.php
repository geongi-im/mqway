<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class NeedWantGameHistory extends Model
{
    protected $table = 'mq_need_want_game_history';

    protected $primaryKey = 'idx';

    public $timestamps = false;

    protected $fillable = [
        'mq_user_id',
        'mq_answers',
        'mq_reg_date',
    ];

    protected $casts = [
        'mq_answers' => 'array',
    ];

    protected $dates = [
        'mq_reg_date',
    ];

    /**
     * 이 게임 기록에 해당하는 사용자를 가져옵니다.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'mq_user_id', 'mq_user_id');
    }
}
