<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsRss extends Model
{
    protected $table = 'mq_news_rss';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_category',
        'mq_company',
        'mq_rss',
        'mq_status',
        'mq_reg_date',
        'mq_update_date'
    ];

    protected $casts = [
        'mq_reg_date' => 'datetime',
        'mq_update_date' => 'datetime',
        'mq_status' => 'integer'
    ];
} 