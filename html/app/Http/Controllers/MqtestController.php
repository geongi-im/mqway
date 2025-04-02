<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MqtestController extends Controller
{
    /**
     * 경제 상식 퀴즈 데이터를 반환하는 메소드
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuizData()
    {
        // 난이도별 문제 배열
        $easyQuestions = [
            [
                'question' => '물건이나 서비스를 구매할 때 주로 사용하는 교환 수단을 무엇이라고 할까요?',
                'options' => [
                    '신용',
                    '돈 (화폐)',
                    '쿠폰',
                    '약속'
                ],
                'correctAnswer' => 1,
                'explanation' => '돈(화폐)은 상품과 서비스를 교환하는 기본적인 매개체입니다.'
            ],
            [
                'question' => '사고 싶은 것은 많지만 가진 돈이나 자원이 한정되어 있어 모든 것을 가질 수 없는 상태를 무엇이라고 할까요?',
                'options' => [
                    '풍요',
                    '희소성',
                    '낭비',
                    '절약'
                ],
                'correctAnswer' => 1,
                'explanation' => '희소성은 인간의 욕구에 비해 이를 충족시켜 줄 자원이 부족한 상태를 의미하며, 경제 문제의 근본적인 원인입니다.'
            ],
            [
                'question' => '물건을 사려는 사람(수요)과 팔려는 사람(공급)이 만나 가격이 결정되는 곳을 일반적으로 무엇이라고 할까요?',
                'options' => [
                    '공장',
                    '학교',
                    '시장',
                    '은행'
                ],
                'correctAnswer' => 2,
                'explanation' => '시장은 수요와 공급이 만나 가격이 결정되고 거래가 이루어지는 장소나 시스템을 의미합니다.'
            ],
            [
                'question' => '국가가 공공 서비스(도로, 학교, 국방 등)를 제공하기 위해 국민이나 기업으로부터 법에 따라 거두는 돈은 무엇일까요?',
                'options' => [
                    '세금',
                    '벌금',
                    '기부금',
                    '이용료'
                ],
                'correctAnswer' => 0,
                'explanation' => '세금은 국가 운영에 필요한 재원을 마련하기 위한 기본적인 수단입니다.'
            ]
        ];

        $mediumQuestions = [
            [
                'question' => '물가가 전반적으로 지속해서 하락하여 돈의 가치가 오히려 상승하는 현상을 무엇이라고 할까요?',
                'options' => [
                    '디플레이션 (Deflation)',
                    '인플레이션 (Inflation)',
                    '스태그플레이션 (Stagflation)',
                    '하이퍼인플레이션 (Hyperinflation)'
                ],
                'correctAnswer' => 0,
                'explanation' => '디플레이션은 인플레이션과 반대되는 현상으로, 물가 하락이 소비와 투자를 위축시켜 경제 침체를 유발할 수 있습니다.'
            ],
            [
                'question' => '한 나라의 중앙은행(한국의 경우 한국은행)이 물가 안정 등을 위해 시중 금리에 영향을 미치는 기준이 되는 금리는 무엇일까요?',
                'options' => [
                    '예금금리',
                    '대출금리',
                    '기준금리',
                    '콜금리'
                ],
                'correctAnswer' => 2,
                'explanation' => '기준금리는 중앙은행이 금융기관과 거래할 때 기준으로 삼는 금리로, 통화 정책의 중요한 수단입니다.'
            ],
            [
                'question' => '투자할 때, 기대수익률이 높을수록 일반적으로 함께 높아지는 것은 무엇일까요?',
                'options' => [
                    '안정성',
                    '위험 (리스크)',
                    '환금성 (현금화 용이성)',
                    '원금 보장 가능성'
                ],
                'correctAnswer' => 1,
                'explanation' => '일반적으로 투자에서는 \'High Risk, High Return\'의 관계가 성립합니다. 높은 수익을 기대하려면 그만큼 높은 위험을 감수해야 할 가능성이 큽니다.'
            ]
        ];

        $hardQuestions = [
            [
                'question' => '경기 침체와 물가 상승(인플레이션)이 동시에 나타나는 어려운 경제 상황을 무엇이라고 할까요?',
                'options' => [
                    '디플레이션 (Deflation)',
                    '스태그플레이션 (Stagflation)',
                    '애그플레이션 (Agflation)',
                    '리플레이션 (Reflation)'
                ],
                'correctAnswer' => 1,
                'explanation' => '스태그플레이션(Stagflation = Stagnation + Inflation)은 경기 부양책과 물가 안정책이 상충되어 정책 대응이 매우 어려운 상황입니다.'
            ],
            [
                'question' => '소득이 증가할수록 세율이 높아지는 세금 제도를 무엇이라고 할까요? (예: 소득세)',
                'options' => [
                    '비례세',
                    '누진세',
                    '역진세',
                    '정액세'
                ],
                'correctAnswer' => 1,
                'explanation' => '누진세는 조세 부담의 공평성(수직적 공평)을 높이기 위한 제도로, 소득 재분배 효과를 가집니다.'
            ],
            [
                'question' => '기업이 생산량을 한 단위 늘릴 때 추가적으로 드는 비용을 무엇이라고 할까요?',
                'options' => [
                    '평균 비용 (Average Cost)',
                    '고정 비용 (Fixed Cost)',
                    '한계 비용 (Marginal Cost)',
                    '총 비용 (Total Cost)'
                ],
                'correctAnswer' => 2,
                'explanation' => '한계 비용은 기업이 생산량을 결정하는 데 중요한 기준으로, 이윤 극대화는 일반적으로 한계 수입(MR)과 한계 비용(MC)이 일치하는 지점에서 이루어집니다.'
            ]
        ];

        // 각 난이도에서 필요한 만큼의 문제를 랜덤하게 선택
        $selectedEasy = array_rand($easyQuestions, 4);
        $selectedMedium = array_rand($mediumQuestions, 3);
        $selectedHard = array_rand($hardQuestions, 3);

        // 선택된 문제들을 하나의 배열로 합치기
        $quizData = [];
        
        // Easy 문제 4개 추가
        foreach ((array)$selectedEasy as $index) {
            $quizData[] = $easyQuestions[$index];
        }
        
        // Medium 문제 3개 추가
        foreach ((array)$selectedMedium as $index) {
            $quizData[] = $mediumQuestions[$index];
        }
        
        // Hard 문제 3개 추가
        foreach ((array)$selectedHard as $index) {
            $quizData[] = $hardQuestions[$index];
        }

        // 최종 문제 순서를 랜덤하게 섞기
        shuffle($quizData);

        return response()->json($quizData);
    }
} 