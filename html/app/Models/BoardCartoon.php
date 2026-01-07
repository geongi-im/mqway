<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;

class BoardCartoon extends Model
{
    protected $table = 'mq_board_cartoon';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_title',
        'mq_content',
        'mq_category',
        'mq_user_id',
        'mq_image',
        'mq_original_image',
        'mq_thumbnail_image',
        'mq_thumbnail_original',
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
        'mq_thumbnail_image' => 'array',
        'mq_thumbnail_original' => 'array'
    ];

    public function hasThumbnail()
    {
        return !empty($this->mq_thumbnail_image) && is_array($this->mq_thumbnail_image);
    }

    public function getThumbnailUrl()
    {
        if ($this->hasThumbnail()) {
            $filename = $this->mq_thumbnail_image[0];
            return !filter_var($filename, FILTER_VALIDATE_URL)
                ? asset('storage/uploads/board_cartoon/' . $filename)
                : $filename;
        }

        return null;
    }

    public function getThumbnailFilename()
    {
        return $this->hasThumbnail() ? $this->mq_thumbnail_image[0] : null;
    }

    public function getThumbnailOriginalName()
    {
        return !empty($this->mq_thumbnail_original) && is_array($this->mq_thumbnail_original)
            ? $this->mq_thumbnail_original[0]
            : null;
    }

    public function getImageOriginalName($index)
    {
        return !empty($this->mq_original_image) && is_array($this->mq_original_image) && isset($this->mq_original_image[$index])
            ? $this->mq_original_image[$index]
            : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'mq_user_id', 'mq_user_id');
    }
}
