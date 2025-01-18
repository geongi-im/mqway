<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beecube extends Model
{
    protected $fillable = ['user_id', 'content', 'status'];

    protected $casts = [
        'content' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 