<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsQuiz extends Model
{
    protected $table = 'mq_news_quiz';

    protected $primaryKey = 'idx';

    public $timestamps = false;

    protected $casts = [
        'mq_quiz_content' => 'array',
    ];

    protected $dates = [
        'mq_news_date',
        'mq_reg_date',
        'mq_update_date',
    ];

    public function scopeActive($query)
    {
        return $query->where('mq_status', 1);
    }

    public function histories()
    {
        return $this->hasMany(NewsQuizHistory::class, 'mq_news_quiz_idx', 'idx');
    }
}
