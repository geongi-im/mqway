<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['question_text'];

    public function pairsAsA()
    {
        return $this->hasMany(QuestionPair::class, 'question_a_id');
    }

    public function pairsAsB()
    {
        return $this->hasMany(QuestionPair::class, 'question_b_id');
    }
} 