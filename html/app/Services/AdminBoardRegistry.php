<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * 관리자 페이지 게시판관리에서 다루는 게시판 목록.
 *
 * 게시판마다 테이블이 따로 있지만 제목(mq_title), 작성자(mq_user_id), 조회수(mq_view_cnt),
 * 상태(mq_status: 1 노출, 0 숨김/삭제), 작성일(mq_reg_date) 컬럼은 모두 같다.
 * 그래서 UNION ALL 로 묶어 한 목록으로 보여준다.
 *
 * 뉴스 게시판(mq_news)은 수집기가 올리는 글이라 회원 글 관리 대상에서 뺐다.
 */
class AdminBoardRegistry
{
    /** key => [이름, 테이블, 글 보기 라우트] (메뉴 순서) */
    const BOARDS = [
        'board-scrap'     => ['뉴스 스크랩',           'mq_board_scrap',     'board-scrap.show'],
        'board-research'  => ['투자 리서치',           'mq_board_research',  'board-research.show'],
        'board-insights'  => ['투자 인사이트',         'mq_board_insights',  'board-insights.show'],
        'board-portfolio' => ['투자대가의 포트폴리오', 'mq_board_portfolio', 'board-portfolio.show'],
        'board-content'   => ['추천 콘텐츠',           'mq_board_content',   'board-content.show'],
        'board-video'     => ['쉽게 보는 경제',        'mq_board_video',     'board-video.show'],
        'board-cartoon'   => ['인사이트 만화',         'mq_board_cartoon',   'board-cartoon.show'],
        'board-mission'   => ['미션',                  'mq_board_mission',   'board-mission.show'],
        'board'           => ['자유게시판',            'mq_board',           'board.show'],
    ];

    public static function exists($key)
    {
        return isset(self::BOARDS[$key]);
    }

    public static function name($key)
    {
        return self::BOARDS[$key][0] ?? $key;
    }

    public static function table($key)
    {
        return self::BOARDS[$key][1];
    }

    public static function showUrl($key, $idx)
    {
        return route(self::BOARDS[$key][2], $idx);
    }

    /**
     * 게시판들의 글을 한 목록으로 묶은 쿼리. 바깥에서 fromSub 로 감싸 정렬/페이지를 건다.
     *
     * @param  string[] $keys 비우면 전체
     * @return \Illuminate\Database\Query\Builder
     */
    public static function unionQuery(array $keys = [])
    {
        $keys = $keys ?: array_keys(self::BOARDS);
        $union = null;

        foreach ($keys as $key) {
            // mq_board_portfolio.mq_status 는 char 라서 숫자로 맞춘다.
            $q = DB::table(self::table($key))->select([
                DB::raw("'" . $key . "' as board"),
                'idx',
                'mq_title',
                'mq_user_id',
                'mq_view_cnt',
                DB::raw('CAST(mq_status AS SIGNED) as mq_status'),
                'mq_reg_date',
            ]);
            $union = $union === null ? $q : $union->unionAll($q);
        }

        return $union;
    }
}
