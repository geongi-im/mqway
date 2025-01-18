<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pick extends Model
{
    protected $fillable = ['user_id', 'status'];

    public function results()
    {
        return $this->hasMany(PickResult::class);
    }
} 