<?php

/*
|--------------------------------------------------------------------------
| 회원 등급 / 경험치 설정
|--------------------------------------------------------------------------
|
| 화면에 보이는 성장 지표는 "등급" 하나다. mq_member.mq_level 은 권한 값이며
| 여기와 아무 관계가 없다. 절대 섞어 쓰지 않는다.
|
| 지급 대상은 우선 기본 활동(접속)과 뉴스 스크랩으로만 좁혀 둔다. 다른 컨텐츠는
| actions 에 한 줄 추가하고 해당 컨트롤러에서 ExpService::grant 를 부르면 붙는다.
|
| 소급 지급은 구조적으로 일어나지 않는다. 경험치는 행동이 일어난 그 순간에만
| 기록되고 과거 데이터를 훑는 코드가 없어서, 배포한 시점부터 자연히 쌓인다.
|
*/

return [

    /*
    | 행동별 지급량
    |
    | exp         한 번 지급량
    | daily_limit 하루에 이 행동으로 받을 수 있는 최대 횟수 (어뷰징 방어)
    | label       획득 내역 화면에 보여줄 문구
    */
    'actions' => [
        'visit.daily'    => ['exp' => 5,   'daily_limit' => 1,  'label' => '오늘 첫 접속'],
        'visit.streak7'  => ['exp' => 50,  'daily_limit' => 1,  'label' => '7일 연속 접속'],
        'visit.streak30' => ['exp' => 300, 'daily_limit' => 1,  'label' => '30일 연속 접속'],

        'scrap.create'   => ['exp' => 50,  'daily_limit' => 2,  'label' => '뉴스 스크랩 작성'],
        'scrap.ai'       => ['exp' => 10,  'daily_limit' => 2,  'label' => 'AI 분석을 붙여 저장'],
        'scrap.public'   => ['exp' => 10,  'daily_limit' => 3,  'label' => '스크랩을 공개로 공유'],
        'scrap.liked'    => ['exp' => 5,   'daily_limit' => 20, 'label' => '내 스크랩이 좋아요를 받음'],
    ],

    /*
    | 등급 구간 (누적 경험치 기준, 반드시 오름차순)
    |
    | 등급은 내려가지 않는다. 글을 지워도 이미 받은 경험치는 회수하지 않는다.
    |
    | 체감 속도 기준:
    | - 가볍게 쓰는 회원(접속 위주, 주 1회 스크랩): 실버까지 약 한 달
    | - 활발한 회원(매일 접속 + 스크랩 1개): 골드까지 약 3주, 다이아까지 약 두 달
    */
    'grades' => [
        ['code' => 'bronze',  'name' => '브론즈',  'exp' => 0,    'color' => '#B08D57', 'text' => '#FFFFFF'],
        ['code' => 'silver',  'name' => '실버',    'exp' => 300,  'color' => '#8E99A8', 'text' => '#FFFFFF'],
        ['code' => 'gold',    'name' => '골드',    'exp' => 1000, 'color' => '#E0A32E', 'text' => '#FFFFFF'],
        ['code' => 'diamond', 'name' => '다이아',  'exp' => 3000, 'color' => '#3BB8AF', 'text' => '#FFFFFF'],
        ['code' => 'master',  'name' => '마스터',  'exp' => 8000, 'color' => '#8B4DFF', 'text' => '#FFFFFF'],
    ],

];
