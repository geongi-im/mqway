<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionPair extends Model
{
    protected $fillable = ['question_a_id', 'question_b_id'];

    public function questionA()
    {
        return $this->belongsTo(Question::class, 'question_a_id');
    }

    public function questionB()
    {
        return $this->belongsTo(Question::class, 'question_b_id');
    }
} 