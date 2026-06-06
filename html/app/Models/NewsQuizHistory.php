<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class NewsQuizHistory extends Model
{
    protected $table = 'mq_news_quiz_history';

    protected $primaryKey = 'idx';

    public $timestamps = false;

    protected $fillable = [
        'mq_user_id',
        'mq_news_quiz_idx',
        'mq_news_date',
        'mq_selected_answer',
        'mq_is_correct',
        'mq_streak_days',
        'mq_status',
        'mq_reg_date',
        'mq_update_date',
    ];

    protected $dates = [
        'mq_news_date',
        'mq_reg_date',
        'mq_update_date',
    ];

    public function quiz()
    {
        return $this->belongsTo(NewsQuiz::class, 'mq_news_quiz_idx', 'idx');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'mq_user_id', 'mq_user_id');
    }
}
