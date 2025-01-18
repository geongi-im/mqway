<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickResult extends Model
{
    protected $fillable = [
        'pick_id',
        'question_pair_id',
        'sequence',
        'selected_question_id',
        'status'
    ];

    public function pick()
    {
        return $this->belongsTo(Pick::class);
    }

    public function questionPair()
    {
        return $this->belongsTo(QuestionPair::class);
    }

    public function selectedQuestion()
    {
        return $this->belongsTo(Question::class, 'selected_question_id');
    }
} 