<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

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
        'mq_profile_image',
        'mq_birthday'
    ];

    protected $hidden = [
        'mq_user_password',
        'remember_token',
    ];

    protected $casts = [
        'mq_birthday' => 'date'
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->mq_user_password;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Automatically hash password when setting
     */
    public function setMqUserPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['mq_user_password'] = Hash::make($value);
        }
    }
} 