<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerCheck extends Model
{
    /**
     * 테이블 이름
     *
     * @var string
     */
    protected $table = 'server_check';
    
    /**
     * 기본 키 비활성화 (sc_market과 sc_user_id의 복합키 사용)
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * 타임스탬프 사용 여부
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * 대량 할당 가능한 속성
     *
     * @var array
     */
    protected $fillable = [
        'sc_server',
        'sc_user_id',
        'sc_watch',
        'sc_datetime'
    ];
} 