<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NewsTop extends Model
{
    protected $table = 'mq_news_top';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    protected $fillable = [
        'mq_news_date',
        'mq_company',
        'mq_title',
        'mq_source_url',
        'mq_status',
        'mq_reg_date',
        'mq_update_date'
    ];

    protected $casts = [
        'mq_news_date' => 'date',
        'mq_reg_date' => 'datetime',
        'mq_update_date' => 'datetime',
        'mq_status' => 'integer'
    ];

    /**
     * 신문사 로고 매핑
     */
    protected static $logoMap = [
        '머니투데이' => '/images/logo/mt.png',
        '파이낸셜뉴스' => '/images/logo/fnnews.png',
        '한국경제' => '/images/logo/hankyung.png',
        '매일경제' => '/images/logo/mk.png',
        '헤럴드경제' => '/images/logo/heraldbiz.png',
    ];

    /**
     * 특정 날짜의 뉴스만 조회
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDate($query, $date)
    {
        if ($date instanceof Carbon) {
            $date = $date->format('Y-m-d');
        }

        return $query->where('mq_news_date', $date);
    }

    /**
     * 활성화된 뉴스만 조회
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('mq_status', 1);
    }

    /**
     * 신문사 로고 경로 반환
     *
     * @return string
     */
    public function getCompanyLogo()
    {
        return self::$logoMap[$this->mq_company] ?? '/images/logo/company/default.png';
    }

    /**
     * 로고 매핑 목록 반환 (API 문서용)
     *
     * @return array
     */
    public static function getLogoMap()
    {
        return self::$logoMap;
    }
}
