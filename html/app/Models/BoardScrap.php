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

    /**
     * URL 정규화 시 버리는 추적용 쿼리 파라미터
     *
     * utm_ 으로 시작하는 것은 목록과 별개로 접두사 검사로 걸러낸다.
     * 기사 식별에 쓰이는 파라미터(네이버의 oid/aid, 일부 언론사의 idxno 등)는
     * 지우면 서로 다른 기사가 같은 글로 뭉쳐지므로 절대 목록에 넣지 않는다.
     */
    const URL_DROP_PARAMS = [
        'fbclid', 'gclid', 'dclid', 'msclkid', 'igshid', 'yclid', 'ttclid',
        'mc_cid', 'mc_eid', 'spm', 'cmpid', 'ncid', '_ga', '_gl',
    ];

    protected $fillable = [
        'mq_user_id',
        'mq_title',
        'mq_url',
        'mq_url_hash',
        'mq_reason',
        'mq_new_terms',
        'mq_ai_interpretation',
        'mq_news_term',
        'mq_ai_outlook_short',
        'mq_ai_outlook_long',
        'mq_ai_questions',
        'mq_ai_answers',
        'mq_ai_model',
        'mq_ai_source',
        'mq_ai_date',
        'mq_thumbnail_url',
        'mq_is_public',
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

    public function getMqAiDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    /**
     * 파트5 경제 용어 리스트
     *
     * mq_news_term 은 [{term, definition, checked}] 형태의 JSON 이다.
     * 예전 글에는 값이 없으므로 항상 배열로 정규화해서 돌려준다.
     * 예전 글이 남긴 context(이 기사에서의 쓰임) 키는 더 이상 쓰지 않으므로 읽을 때 버린다.
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
                'checked'    => !empty($row['checked']),
            ];
        }

        return $terms;
    }

    /**
     * 사용자가 "저장" 으로 표시한 용어만 (몰랐던 용어 즐겨찾기)
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
     * 파트7 질문에 사용자가 쓴 답변
     *
     * 질문과 같은 순서의 배열이며, 답을 쓰지 않은 칸은 빈 문자열로 채워 돌려준다.
     * 답변은 선택 항목이라 질문 수보다 짧게 저장돼 있을 수 있다.
     *
     * @param int|null $size 맞출 칸 수 (기본: 질문 개수)
     * @return array
     */
    public function getAiAnswers($size = null)
    {
        $size = $size === null ? count($this->getAiQuestions()) : (int) $size;
        $answers = [];

        if (!empty($this->mq_ai_answers)) {
            $decoded = json_decode($this->mq_ai_answers, true);

            if (is_array($decoded)) {
                foreach ($decoded as $answer) {
                    $answers[] = is_string($answer) ? trim($answer) : '';
                }
            }
        }

        if ($size <= 0) {
            return $answers;
        }

        return array_pad(array_slice($answers, 0, $size), $size, '');
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
     * 같은 기사로 볼 수 있게 URL 을 정규화한다.
     *
     * 같은 기사라도 아래 차이 때문에 문자열이 달라져서 중복 검사가 새어 나간다.
     * - http / https
     * - www. / m. / mobile. 서브도메인
     * - 끝의 슬래시, 중복 슬래시
     * - utm_* 같은 추적 파라미터, 파라미터 순서
     * - #앵커
     *
     * 파싱이 안 되는 문자열은 소문자로 다듬어 그대로 돌려준다. 정규화에 실패했다고
     * 중복 검사를 포기하는 것보다 원문끼리라도 비교하는 편이 낫다.
     *
     * @param string|null $url
     * @return string
     */
    public static function normalizeUrl($url)
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        $parts = parse_url($url);

        if ($parts === false || empty($parts['host'])) {
            return rtrim(strtolower($url), '/');
        }

        // http 와 https 는 같은 기사로 본다
        $scheme = 'https';

        $host = strtolower($parts['host']);
        $host = preg_replace('/^(www|m|mobile)\./', '', $host);

        $path = isset($parts['path']) ? $parts['path'] : '/';
        $path = preg_replace('#/+#', '/', $path);
        $path = $path === '/' ? '/' : rtrim($path, '/');

        if ($path === '') {
            $path = '/';
        }

        $query = '';

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $params);

            foreach (array_keys($params) as $key) {
                $lower = strtolower((string) $key);

                if (strpos($lower, 'utm_') === 0 || in_array($lower, self::URL_DROP_PARAMS, true)) {
                    unset($params[$key]);
                }
            }

            ksort($params);
            $query = http_build_query($params);
        }

        return $scheme . '://' . $host . $path . ($query !== '' ? '?' . $query : '');
    }

    /**
     * 중복 검사용 URL 해시
     *
     * mq_url 이 TEXT 라 인덱스를 못 걸기 때문에 정규화 결과의 sha256 을 따로 저장한다.
     *
     * @param string|null $url
     * @return string|null 빈 URL 이면 null
     */
    public static function urlHash($url)
    {
        $normalized = self::normalizeUrl($url);

        return $normalized === '' ? null : hash('sha256', $normalized);
    }

    /**
     * 같은 회원이 이미 올린 기사인지 확인한다.
     *
     * 삭제(mq_status=0)한 글은 중복으로 보지 않는다. 지운 기사는 다시 올릴 수 있어야 한다.
     *
     * @param string $userId
     * @param string $url
     * @param int|null $ignoreIdx 수정 화면에서 자기 자신을 제외할 때
     * @return \App\Models\BoardScrap|null
     */
    public static function findDuplicate($userId, $url, $ignoreIdx = null)
    {
        $hash = self::urlHash($url);

        if ($hash === null) {
            return null;
        }

        return self::active()
            ->ownedBy($userId)
            ->where('mq_url_hash', $hash)
            ->when($ignoreIdx, function ($query) use ($ignoreIdx) {
                return $query->where('idx', '!=', $ignoreIdx);
            })
            ->first();
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

    /**
     * 원문 링크의 도메인
     *
     * 출처 표기에 쓴다. www. 는 떼고 보여준다.
     *
     * @return string|null
     */
    public function getSourceDomain()
    {
        if (empty($this->mq_url)) {
            return null;
        }

        $host = parse_url($this->mq_url, PHP_URL_HOST);

        if (empty($host)) {
            return null;
        }

        return preg_replace('/^www\./i', '', $host);
    }
}
