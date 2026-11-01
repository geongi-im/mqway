<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\User;

class BoardInsights extends Model
{
    // 첨부파일 저장 경로 (local 디스크, 웹에서 직접 접근 불가)
    const ATTACH_PATH = 'private/board_insights_files';

    protected $table = 'mq_board_insights';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_title',
        'mq_content',
        'mq_category',
        'mq_user_id',
        'mq_image',
        'mq_original_image',
        'mq_attach_file',
        'mq_attach_original',
        'mq_view_cnt',
        'mq_like_cnt',
        'mq_status',
        'mq_reg_date',
        'mq_update_date'
    ];

    protected $dates = [
        'mq_reg_date',
        'mq_update_date'
    ];

    public function getMqRegDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getMqUpdateDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    protected $casts = [
        'mq_image' => 'array',
        'mq_original_image' => 'array',
        'mq_attach_file' => 'array',
        'mq_attach_original' => 'array'
    ];

    public function getImageOriginalName($index)
    {
        return !empty($this->mq_original_image) && is_array($this->mq_original_image) && isset($this->mq_original_image[$index])
            ? $this->mq_original_image[$index]
            : null;
    }

    /**
     * 첨부파일 목록 (저장명/원본명/확장자/용량)
     * 파일이 디스크에 없으면 목록에서 뺀다.
     */
    public function getAttachments()
    {
        $files = is_array($this->mq_attach_file) ? $this->mq_attach_file : [];
        $originals = is_array($this->mq_attach_original) ? $this->mq_attach_original : [];
        $disk = Storage::disk('local');
        $attachments = [];

        foreach ($files as $index => $filename) {
            $path = self::ATTACH_PATH . '/' . $filename;
            if (!$disk->exists($path)) {
                continue;
            }

            $attachments[] = [
                'file' => $filename,
                'name' => $originals[$index] ?? $filename,
                'ext' => strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
                'size' => $disk->size($path),
            ];
        }

        return $attachments;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'mq_user_id', 'mq_user_id');
    }
}
