<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class News extends Model
{
    protected $table = 'mq_news';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_category',
        'mq_title',
        'mq_content',
        'mq_company',
        'mq_source_url',
        'mq_status',
        'mq_reg_date',
        'mq_update_date',
        'mq_published_date',
        'mq_step1_score'
    ];

    protected $casts = [
        'mq_reg_date' => 'datetime',
        'mq_update_date' => 'datetime',
        'mq_published_date' => 'datetime',
        'mq_status' => 'integer',
        'mq_step1_score' => 'integer'
    ];
} 