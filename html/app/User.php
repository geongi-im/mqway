<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * 테이블 이름 설정
     *
     * @var string
     */
    protected $table = 'mq_member';

    /**
     * 기본 키 설정
     *
     * @var string
     */
    protected $primaryKey = 'idx';

    /**
     * timestamps 사용 여부
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mq_user_id', 'mq_password', 'mq_email', 'mq_user_name', 'mq_level', 'mq_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'mq_password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 사용자가 관리자인지 확인
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->mq_level === 10;
    }

    /**
     * 사용자가 매니저인지 확인
     *
     * @return bool
     */
    public function isManager()
    {
        return $this->mq_level === 9;
    }

    /**
     * 이름 접근자
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->mq_user_name;
    }

    /**
     * 이메일 접근자
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->mq_email;
    }

    /**
     * 비밀번호 설정 뮤테이터
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['mq_password'] = $value;
    }

    /**
     * 비밀번호 가져오기 접근자
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->mq_password;
    }
}
