<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsertDataCommand extends Command
{
    protected $signature = 'insert:data';
    protected $description = 'Insert data into the users table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::table('mq_life_search')->insert([
            [
                'mq_category' => '여행',
                'mq_type' => 'want_to_do',
                'mq_content' => '유럽 여행: 파리, 로마, 바르셀로나 탐방',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '여행',
                'mq_type' => 'want_to_go',
                'mq_content' => '북극 탐험 : 스발바르 제도에서 오로라 관찰',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '여행',
                'mq_type' => 'want_to_share',
                'mq_content' => '자신 기부: 지역 사회의 자원봉사 프로젝트에 참여',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '여행',
                'mq_type' => 'want_to_do',
                'mq_content' => '일본 온천: 일본의 유명 온천 지역 방문',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '여행',
                'mq_type' => 'want_to_go',
                'mq_content' => '일본 온천: 일본의 온천 지역 방문',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '여행',
                'mq_type' => 'want_to_share',
                'mq_content' => '기부금 모금: 자선 단체를 위한 기부금 모금 캠페인 계획',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '여행',
                'mq_type' => 'want_to_do',
                'mq_content' => '사하라 사막: 사막 캠핑 및 낙타 타기 경험',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '여행',
                'mq_type' => 'want_to_go',
                'mq_content' => '사하라 사막',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '여행',
                'mq_type' => 'want_to_share',
                'mq_content' => '장학금 설립: 경제적으로 어려운 학생들을 위한 장학금 설립',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '자아 실현',
                'mq_type' => 'want_to_do',
                'mq_content' => '최고의 요리사 되기: 다양한 국제 요리 기술 배우기',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '자아 실현',
                'mq_type' => 'want_to_go',
                'mq_content' => '대륙 기로지르는 자동차 여행',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '자아 실현',
                'mq_type' => 'want_to_share',
                'mq_content' => '자원봉사: 지역 사회의 자원봉사 프로젝트에 참여',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '자아 실현',
                'mq_type' => 'want_to_do',
                'mq_content' => '책 출간하기: 자신의 경험에 관한 책 출간',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '자아 실현',
                'mq_type' => 'want_to_go',
                'mq_content' => '지역 속에서 캠핑: 산 속 또는 해변에서 캠핑',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '자아 실현',
                'mq_type' => 'want_to_share',
                'mq_content' => '구호 물품 기부: 재난 지역에 구호 물품 기부하기',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '자아 실현',
                'mq_type' => 'want_to_do',
                'mq_content' => '미술 전시회 열기: 자신의 작품으로 갤러리 전시',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '자아 실현',
                'mq_type' => 'want_to_go',
                'mq_content' => '세계적으로 유명한 박물관 및 역사 유적지 방문',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '자아 실현',
                'mq_type' => 'want_to_share',
                'mq_content' => '멘토링: 신진 전문가를 멘토링하여 지식 전수',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '직업 및 경력',
                'mq_type' => 'want_to_do',
                'mq_content' => '스타트업 창업: 자신의 비즈니스 시작 및 운영',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '직업 및 경력',
                'mq_type' => 'want_to_go',
                'mq_content' => '유럽 주요 도시 탐방',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '직업 및 경력',
                'mq_type' => 'want_to_share',
                'mq_content' => '전문 지식 전수: 자신이 가진 전문 지식을 나누기',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '직업 및 경력',
                'mq_type' => 'want_to_do',
                'mq_content' => '전문 분야에서 인지도 높이기: 업계에서 인정받는 전문가',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '직업 및 경력',
                'mq_type' => 'want_to_go',
                'mq_content' => '스카이 다이빙: 자유 낙하 경험',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ],
            [
                'mq_category' => '직업 및 경력',
                'mq_type' => 'want_to_share',
                'mq_content' => '전구들과의 정기 모임: 정기적으로 모임 가지기',
                'mq_price' => 0,
                'mq_expected_time' => 0,
                'mq_reg_date' => '2024-11-29',
                'mq_update_date' => null,
            ]
        ]);
        
        $this->info('Data inserted successfully!');
    }
}
