<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertMqMappingItemNew25 extends Migration
{
    /**
     * Run the migrations.
     * 각 카테고리별 5개씩, 총 25개의 새로운 MQ 맵핑 항목 추가
     */
    public function up(): void
    {
        $items = [
            // ========================================
            // Creation (창작) - 10~14번 항목
            // ========================================
            [
                'mq_category'   => 'creation',
                'mq_image'      => 'mapping_creation_7_010.png',
                'mq_description'=> '클레이로 우리 가족 얼굴 만들어서 액자에 넣기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'creation',
                'mq_image'      => 'mapping_creation_7_011.png',
                'mq_description'=> '재활용 종이박스와 우유갑으로 거북선 만들기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'creation',
                'mq_image'      => 'mapping_creation_10_012.png',
                'mq_description'=> '다이소 스티커로 나만의 다이어리(다꾸) 1페이지 완성하기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'creation',
                'mq_image'      => 'mapping_creation_14_013.png',
                'mq_description'=> '학교 학예회 장기자랑 쓸 배경 음악 직접 믹싱해보기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'creation',
                'mq_image'      => 'mapping_creation_14_014.png',
                'mq_description'=> '스마트폰 앱으로 릴스/쇼츠 댄스 챌린지 영상 편집하기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],

            // ========================================
            // Adventure (탐험) - 10~14번 항목
            // ========================================
            [
                'mq_category'   => 'adventure',
                'mq_image'      => 'mapping_adventure_7_010.png',
                'mq_description'=> '주말 아침 아빠랑 동네 뒷산 약수터까지 걸어 다녀오기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'adventure',
                'mq_image'      => 'mapping_adventure_7_011.png',
                'mq_description'=> '교통카드 직접 찍고 엄마랑 시내버스 뒷자리 타보기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'adventure',
                'mq_image'      => 'mapping_adventure_10_012.png',
                'mq_description'=> '지하철 노선도 보고 혼자서 문구점 다녀오기 심부름',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'adventure',
                'mq_image'      => 'mapping_adventure_14_013.png',
                'mq_description'=> '친구들이랑 기차(KTX) 타고 1박 2일 우정 여행 가보기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'adventure',
                'mq_image'      => 'mapping_adventure_14_014.png',
                'mq_description'=> '따릉이 빌려서 친구들과 한강 공원 자전거 라이딩하기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],

            // ========================================
            // Challenge (도전) - 10~14번 항목
            // ========================================
            [
                'mq_category'   => 'challenge',
                'mq_image'      => 'mapping_challenge_7_010.png',
                'mq_description'=> '태권도 승급 심사 당당하게 합격해서 색깔 띠 매기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'challenge',
                'mq_image'      => 'mapping_challenge_7_011.png',
                'mq_description'=> '훌라후프 땅에 안 떨어뜨리고 연속 30번 돌리기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'challenge',
                'mq_image'      => 'mapping_challenge_10_012.png',
                'mq_description'=> '학교 쌩쌩이(이단뛰기) 10번 연속 성공하기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'challenge',
                'mq_image'      => 'mapping_challenge_14_013.png',
                'mq_description'=> '체육대회 반 대항전 피구/계주 경기에서 대활약하기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'challenge',
                'mq_image'      => 'mapping_challenge_14_014.png',
                'mq_description'=> '컴퓨터활용능력이나 워드프로세서 자격증 시험 합격하기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],

            // ========================================
            // Growth (성장) - 10~14번 항목
            // ========================================
            [
                'mq_category'   => 'growth',
                'mq_image'      => 'mapping_growth_7_010.png',
                'mq_description'=> '어른용 젓가락으로 흘리지 않고 밥 끝까지 먹기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'growth',
                'mq_image'      => 'mapping_growth_7_011.png',
                'mq_description'=> '혼자서 머리 감고 깨끗하게 샤워 마친 후 스스로 옷 입기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'growth',
                'mq_image'      => 'mapping_growth_10_012.png',
                'mq_description'=> '어린이 통장 만들어서 세뱃돈과 용돈 꾸준히 저축하기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'growth',
                'mq_image'      => 'mapping_growth_10_013.png',
                'mq_description'=> '하루 30분씩 밀리지 않고 매일 눈높이/구몬 학습지 풀기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'growth',
                'mq_image'      => 'mapping_growth_14_014.png',
                'mq_description'=> '내가 가고 싶은 목표 대학교 캠퍼스 탐방 다녀오기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],

            // ========================================
            // Experience (경험) - 10~14번 항목
            // ========================================
            [
                'mq_category'   => 'experience',
                'mq_image'      => 'mapping_experience_7_010.png',
                'mq_description'=> '주말농장이나 체험농장에 가서 싱싱한 딸기나 고구마 캐기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'experience',
                'mq_image'      => 'mapping_experience_7_011.png',
                'mq_description'=> '분리수거 날 엄마 아빠 도와서 페트병과 종이 분리하기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'experience',
                'mq_image'      => 'mapping_experience_10_012.png',
                'mq_description'=> '직업체험관(키자니아 등)에 가서 꿈꾸는 직업 3가지 체험하기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'experience',
                'mq_image'      => 'mapping_experience_14_013.png',
                'mq_description'=> '하교 길 교복 입고 단짝 친구들과 인생네컷(셀프 사진) 찍기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
            [
                'mq_category'   => 'experience',
                'mq_image'      => 'mapping_experience_14_014.png',
                'mq_description'=> '학원 끝나는 밤, 친구들이랑 편의점 삼각김밥, 컵라면 야식 먹기',
                'mq_status'     => 1,
                'mq_reg_date'   => now(),
            ],
        ];

        DB::table('mq_mapping_item')->insert($items);
    }

    /**
     * Reverse the migrations.
     * 추가된 25개 항목 롤백
     */
    public function down(): void
    {
        $descriptions = [
            // Creation
            '클레이로 우리 가족 얼굴 만들어서 액자에 넣기',
            '재활용 종이박스와 우유갑으로 거북선 만들기',
            '다이소 스티커로 나만의 다이어리(다꾸) 1페이지 완성하기',
            '학교 학예회 장기자랑 쓸 배경 음악 직접 믹싱해보기',
            '스마트폰 앱으로 릴스/쇼츠 댄스 챌린지 영상 편집하기',
            // Adventure
            '주말 아침 아빠랑 동네 뒷산 약수터까지 걸어 다녀오기',
            '교통카드 직접 찍고 엄마랑 시내버스 뒷자리 타보기',
            '지하철 노선도 보고 혼자서 문구점 다녀오기 심부름',
            '친구들이랑 기차(KTX) 타고 1박 2일 우정 여행 가보기',
            '따릉이 빌려서 친구들과 한강 공원 자전거 라이딩하기',
            // Challenge
            '태권도 승급 심사 당당하게 합격해서 색깔 띠 매기',
            '훌라후프 땅에 안 떨어뜨리고 연속 30번 돌리기',
            '학교 쌩쌩이(이단뛰기) 10번 연속 성공하기',
            '체육대회 반 대항전 피구/계주 경기에서 대활약하기',
            '컴퓨터활용능력이나 워드프로세서 자격증 시험 합격하기',
            // Growth
            '어른용 젓가락으로 흘리지 않고 밥 끝까지 먹기',
            '혼자서 머리 감고 깨끗하게 샤워 마친 후 스스로 옷 입기',
            '어린이 통장 만들어서 세뱃돈과 용돈 꾸준히 저축하기',
            '하루 30분씩 밀리지 않고 매일 눈높이/구몬 학습지 풀기',
            '내가 가고 싶은 목표 대학교 캠퍼스 탐방 다녀오기',
            // Experience
            '주말농장이나 체험농장에 가서 싱싱한 딸기나 고구마 캐기',
            '분리수거 날 엄마 아빠 도와서 페트병과 종이 분리하기',
            '직업체험관(키자니아 등)에 가서 꿈꾸는 직업 3가지 체험하기',
            '하교 길 교복 입고 단짝 친구들과 인생네컷(셀프 사진) 찍기',
            '학원 끝나는 밤, 친구들이랑 편의점 삼각김밥, 컵라면 야식 먹기',
        ];

        DB::table('mq_mapping_item')
            ->whereIn('mq_description', $descriptions)
            ->delete();
    }
}
