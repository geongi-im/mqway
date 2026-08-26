<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BoardScrap extends Model
{
    protected $table = 'mq_board_scrap';
    protected $primaryKey = 'idx';
    public $timestamps = false;

    /**
     * mq_like_history 테이블에서 이 게시판을 식별하는 이름
     */
    const BOARD_NAME = 'board_scrap';

    /** 파트5 경제 용어 최대 개수 (AI 응답 / 사용자 편집 공통 상한) */
    const MAX_TERMS = 10;

    protected $fillable = [
        'mq_user_id',
        'mq_title',
        'mq_url',
        'mq_reason',
        'mq_new_terms',
        'mq_ai_interpretation',
        'mq_news_term',
        'mq_ai_outlook_short',
        'mq_ai_outlook_long',
        'mq_ai_questions',
        'mq_ai_model',
        'mq_ai_source',
        'mq_ai_date',
        'mq_thumbnail_url',
        'mq_is_public',
        'mq_public_date',
        'mq_view_cnt',
        'mq_like_cnt',
        'mq_status',
        'mq_reg_date',
        'mq_update_date'
    ];

    // 날짜 필드를 Carbon 인스턴스로 변환
    protected $dates = [
        'mq_reg_date',
        'mq_update_date',
        'mq_public_date',
        'mq_ai_date'
    ];

    // 날짜를 항상 Carbon 인스턴스로 변환
    public function getMqRegDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getMqUpdateDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getMqPublicDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getMqAiDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    /**
     * 파트5 경제 용어 리스트
     *
     * mq_news_term 은 [{term, definition, context, checked}] 형태의 JSON 이다.
     * 예전 글에는 값이 없으므로 항상 배열로 정규화해서 돌려준다.
     *
     * @return array
     */
    public function getAiTerms()
    {
        if (empty($this->mq_news_term)) {
            return [];
        }

        $decoded = json_decode($this->mq_news_term, true);

        if (!is_array($decoded)) {
            return [];
        }

        $terms = [];

        foreach ($decoded as $row) {
            if (!is_array($row) || empty($row['term'])) {
                continue;
            }

            $terms[] = [
                'term'       => (string) $row['term'],
                'definition' => isset($row['definition']) ? (string) $row['definition'] : '',
                'context'    => isset($row['context']) ? (string) $row['context'] : '',
                'checked'    => !empty($row['checked']),
            ];
        }

        return $terms;
    }

    /**
     * 사용자가 "알게 된 용어" 로 체크한 항목만
     *
     * @return array
     */
    public function getCheckedTerms()
    {
        $checked = [];

        foreach ($this->getAiTerms() as $term) {
            if ($term['checked']) {
                $checked[] = $term;
            }
        }

        return $checked;
    }

    /**
     * 파트7 질문 리스트
     *
     * @return array
     */
    public function getAiQuestions()
    {
        if (empty($this->mq_ai_questions)) {
            return [];
        }

        $decoded = json_decode($this->mq_ai_questions, true);

        if (!is_array($decoded)) {
            return [];
        }

        $questions = [];

        foreach ($decoded as $question) {
            if (is_string($question) && trim($question) !== '') {
                $questions[] = trim($question);
            }
        }

        return $questions;
    }

    /**
     * 파트6 전망을 줄 단위 배열로 반환
     *
     * @param string $term 'short' | 'long'
     * @return array
     */
    public function getOutlookLines($term = 'short')
    {
        $raw = $term === 'long' ? $this->mq_ai_outlook_long : $this->mq_ai_outlook_short;

        if (empty($raw)) {
            return [];
        }

        $lines = [];

        foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    /**
     * AI 분석 결과가 하나라도 있는지 (상세 화면 섹션 노출 판단용)
     *
     * @return bool
     */
    public function hasAiAnalysis()
    {
        return !empty($this->mq_ai_interpretation)
            || !empty($this->mq_news_term)
            || !empty($this->mq_ai_outlook_short)
            || !empty($this->mq_ai_outlook_long)
            || !empty($this->mq_ai_questions);
    }

    /**
     * 삭제되지 않은 스크랩
     */
    public function scopeActive($query)
    {
        return $query->where('mq_status', 1);
    }

    /**
     * 공개된 스크랩만
     */
    public function scopePublicOnly($query)
    {
        return $query->where('mq_is_public', 1);
    }

    /**
     * 특정 회원이 작성한 스크랩만
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('mq_user_id', $userId);
    }

    /**
     * 해당 회원이 볼 수 있는 스크랩 (공개글 + 본인 글)
     *
     * @param string|null $userId 비로그인은 null
     */
    public function scopeVisibleTo($query, $userId = null)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('mq_is_public', 1);

            if ($userId) {
                $q->orWhere('mq_user_id', $userId);
            }
        });
    }

    /**
     * 썸네일 이미지가 있는지 확인
     */
    public function hasThumbnail()
    {
        return !empty($this->mq_thumbnail_url);
    }

    /**
     * 썸네일 이미지 URL 반환
     */
    public function getThumbnailUrl()
    {
        return $this->mq_thumbnail_url ?? asset('images/default-news-thumbnail.png');
    }

    /**
     * 사용자와의 관계 정의
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Member::class, 'mq_user_id', 'mq_user_id');
    }

    /**
     * 본인의 스크랩인지 확인
     */
    public function isOwner($userId)
    {
        return $this->mq_user_id === $userId;
    }

    /**
     * 공개 상태인지 확인
     */
    public function isPublic()
    {
        return (int) $this->mq_is_public === 1;
    }

    /**
     * 목록에 노출할 작성자 이름
     */
    public function getAuthorName()
    {
        return $this->user->mq_user_name ?? '알 수 없음';
    }
}
