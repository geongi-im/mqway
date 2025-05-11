<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class BoardVideo extends Model
{
    
    /**
     * 테이블 이름 설정
     */
    protected $table = 'mq_board_video';
    
    /**
     * 기본 키 설정
     */
    protected $primaryKey = 'idx';
    
    /**
     * 자동 타임스탬프 비활성화
     */
    public $timestamps = false;
    
    /**
     * 대량 할당 가능한 속성
     */
    protected $fillable = [
        'mq_title',
        'mq_content',
        'mq_category',
        'mq_user_id',
        'mq_view_cnt',
        'mq_like_cnt',
        'mq_reg_date',
        'mq_update_date',
        'mq_status',
        'mq_video_url'
    ];
    
    /**
     * 날짜 형식으로 다루어야할 속성
     */
    protected $dates = [
        'mq_reg_date',
        'mq_update_date',
    ];
    
    /**
     * JSON으로 변환되어야 하는 속성
     */
    protected $casts = [
        'mq_image' => 'array',
        'mq_original_image' => 'array',
    ];
    
    /**
     * 사용자 정보 관계 설정
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'mq_user_id', 'mq_user_id');
    }
} 