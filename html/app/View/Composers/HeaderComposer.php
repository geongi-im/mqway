<?php

namespace App\View\Composers;

use Illuminate\View\View;

class HeaderComposer
{
    /**
     * 헤더 뷰에 메뉴 데이터를 바인딩합니다.
     */
    public function compose(View $view): void
    {
        $request = request();

        $headerMenus = [
            [
                'label' => 'MQWAY 소개',
                'url' => route('introduce'),
                'active' => $request->routeIs('introduce'),
                'children' => [],
                'target' => 'introduce',
            ],
            [
                'label' => '코스 소개',
                'url' => route('course.l1.intro'),
                'active' => $request->is('course*'),
                'target' => 'course',
                'children' => [
                    ['label' => 'L1 코스', 'url' => route('course.l1.intro'), 'active' => $request->routeIs('course.l1.intro'), 'onclick' => ''],
                    ['label' => 'L2 코스', 'url' => '#', 'active' => false, 'onclick' => "event.preventDefault(); alert('코스 준비중입니다.');"],
                ],
            ],
            [
                'label' => '학습 도구',
                'url' => route('tools.economy-term-game'),
                'active' => $request->is('tools*'),
                'target' => 'tools',
                'children' => [
                    ['label' => '경제 용어 카드 맞추기', 'url' => route('tools.economy-term-game'), 'active' => $request->routeIs('tools.economy-term-game'), 'onclick' => ''],
                    ['label' => '경제 상식 퀴즈', 'url' => route('tools.financial-quiz'), 'active' => $request->routeIs('tools.financial-quiz'), 'onclick' => ''],
                    ['label' => '노후 자금 계산기', 'url' => route('tools.retirement-calculator'), 'active' => $request->routeIs('tools.retirement-calculator'), 'onclick' => ''],
                    ['label' => 'Need or Want?', 'url' => route('tools.need-want-game'), 'active' => $request->routeIs('tools.need-want-game'), 'onclick' => ''],
                ],
            ],
            [
                'label' => '학습 자료',
                'url' => route('board-content.index'),
                'active' => $request->is('board-content*') || $request->is('board-video*') || $request->is('board-cartoon*'),
                'target' => 'learning',
                'children' => [
                    ['label' => '추천 콘텐츠', 'url' => route('board-content.index'), 'active' => $request->routeIs('board-content.*'), 'onclick' => ''],
                    ['label' => '쉽게 보는 경제', 'url' => route('board-video.index'), 'active' => $request->routeIs('board-video.*'), 'onclick' => ''],
                    ['label' => '인사이트 만화', 'url' => route('board-cartoon.index'), 'active' => $request->routeIs('board-cartoon.*'), 'onclick' => ''],
                ],
            ],
            [
                'label' => '투자 정보',
                'url' => route('board-research.index'),
                'active' => $request->is('board-research*') || $request->is('board-news*') || $request->is('board-portfolio*') || $request->is('board-insights*'),
                'target' => 'investment',
                'children' => [
                    ['label' => '투자 리서치', 'url' => route('board-research.index'), 'active' => $request->routeIs('board-research.*'), 'onclick' => ''],
                    ['label' => '투자 인사이트', 'url' => route('board-insights.index'), 'active' => $request->routeIs('board-insights.*'), 'onclick' => ''],
                    ['label' => '투자대가의 포트폴리오', 'url' => route('board-portfolio.index'), 'active' => $request->routeIs('board-portfolio.*'), 'onclick' => ''],
                    ['label' => '뉴스 게시판', 'url' => route('board-news.index'), 'active' => $request->routeIs('board-news.*'), 'onclick' => ''],
                ],
            ],
        ];

        $view->with('headerMenus', $headerMenus);
    }
}
