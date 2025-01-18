<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $news = [
            [
                'mq_category' => '테크',
                'mq_title' => '삼성전자, TSMC 따돌리고 미국서 3년째 1위',
                'mq_content' => '삼성전자가 2024년 미국 특허청에 가장 많은 특허를 등록한 기업인 것으로 나타났다. 3년 연속 1위를 차지했다. 2위는 TSMC로 삼성전자를 바짝 추격하는 양상이다.',
                'mq_company' => '매일경제',
                'mq_reg_date' => Carbon::parse('2025-01-15'),
                'mq_source_url' => 'https://www.mk.co.kr/news/it/11219062',
                'mq_status' => 1
            ],
            [
                'mq_category' => '테크',
                'mq_title' => '올 투자만 540조…빅테크 데이터센터 올인',
                'mq_content' => '인공지능(AI) 기술 발전에 따라 필수 인프라스트럭처인 데이터센터 수요가 급증하면서 아마존웹서비스(AWS), 마이크로소프트(MS) 등 글로벌 빅테크 기업을 중심으로 데이터센터 구축에 뭉칫돈을 쏟아붓고 있다. 올해 예상되는 투자 규모는 사상 최초로 500조원을 돌파할 전망이다.',
                'mq_company' => '매일경제',
                'mq_reg_date' => Carbon::parse('2025-01-15'),
                'mq_source_url' => 'https://www.mk.co.kr/news/it/11218963',
                'mq_status' => 1
            ],
            [
                'mq_category' => '경제',
                'mq_title' => '2025년 코스피 전망 상저하고 섹터별 차별화 가속',
                'mq_content' => '국내 주요 증권사들은 2025년 코스피가 상반기 저점, 하반기 고점의 흐름을 보일 것으로 전망하며, 섹터별 차별화가 가속화될 것으로 예상하고 있습니다.',
                'mq_company' => '매일경제',
                'mq_reg_date' => Carbon::parse('2024-12-19'),
                'mq_source_url' => 'https://www.mk.co.kr/news/economy/11199797',
                'mq_status' => 1
            ],
            [
                'mq_category' => '사회',
                'mq_title' => '2025년, 재테크 성공하려면 어디에 투자해야 할까?',
                'mq_content' => '경제의 불확실성이 높아지는 2025년, 청년들은 ESG 투자, 디지털 자산, 테마형 ETF 등 다양한 투자 전략에 주목해야 한다는 분석이 나왔습니다.',
                'mq_company' => '대한청년일보',
                'mq_reg_date' => Carbon::parse('2024-10-01'),
                'mq_source_url' => 'https://www.koreayouthdaily.com/news/articleView.html?idxno=63',
                'mq_status' => 1
            ],
            [
                'mq_category' => '사회',
                'mq_title' => '지표로 본 2025 한국경제 위기 가능성',
                'mq_content' => '2025년 한국경제의 위기 가능성을 환율, 외환보유고, 경상수지 등 경제 지표를 통해 분석한 결과, 외환시장 불안과 정치적 리더십 부재가 주요 위험 요인으로 지적되었습니다.',
                'mq_company' => '매일경제',
                'mq_reg_date' => Carbon::parse('2025-01-01'),
                'mq_source_url' => 'https://www.mk.co.kr/news/economy/11207143',
                'mq_status' => 1
            ],
            [
                'mq_category' => '문화',
                'mq_title' => '12월 증시, 지루한 시장 분위기 속 2025년 준비',
                'mq_content' => '12월 국내 증시는 지루한 분위기가 예상되지만, 2025년을 대비한 전략적 관점에서 바이오, 엔터, 2차전지, 의료용 AI 섹터에 주목해야 한다는 의견이 제시되었습니다.',
                'mq_company' => '한국경제',
                'mq_reg_date' => Carbon::parse('2024-12-06'),
                'mq_source_url' => 'https://www.hankyung.com/article/2024120691565',
                'mq_status' => 1
            ],
            [
                'mq_category' => '문화',
                'mq_title' => '베스트 파트너에게 듣는다 2025년 유망섹터와 종목',
                'mq_content' => '주식 전문가들은 2025년 유망 섹터로 바이오, 엔터, 게임, 2차전지 등을 꼽으며, 엔씨소프트와 에스엠을 유망 종목으로 추천했습니다.',
                'mq_company' => '한국경제',
                'mq_reg_date' => Carbon::parse('2024-11-29'),
                'mq_source_url' => 'https://www.hankyung.com/article/2024112933125',
                'mq_status' => 1
            ]
        ];

        foreach ($news as $item) {
            News::create($item);
        }
    }
} 