<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    protected $table = 'mq_member';
    protected $primaryKey = 'idx';
    
    // 타임스탬프 사용 안함
    public $timestamps = false;
    
    protected $fillable = [
        'mq_user_id',
        'mq_user_name',
        'mq_user_email',
        'mq_user_password',
        'mq_provider',
        'mq_provider_id',
        'mq_last_login_date',
        'mq_status',
        'mq_level',
        'mq_profile_image'
    ];
} 