<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RealityCheck;
use App\Models\LifeSearch;
use App\Models\InvestorPortfolio;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;

class RoadmapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');  // 로그인 필요
    }

    public function index()
    {
        // 사용자의 모든 목표금액 합계 계산
        $totalTargetAmount = LifeSearch::where('mq_user_id', Auth::user()->mq_user_id)
            ->sum('mq_price');

        // 목표금액이 없거나 0원인 경우
        if ($totalTargetAmount <= 0) {
            return view('guidebook.roadmap', [
                'hasLifeGoal' => false
            ]);
        }

        // 실제 사용자의 지출 데이터 가져오기
        $expenses = RealityCheck::where('mq_user_id', Auth::user()->mq_user_id)
            ->orderBy('idx', 'desc')
            ->get();
            
        // 총 수입과 총 지출 계산
        $totalIncome = RealityCheck::where('mq_user_id', Auth::user()->mq_user_id)
            ->where('mq_type', 1) // 수입
            ->sum('mq_price');
            
        $totalExpense = RealityCheck::where('mq_user_id', Auth::user()->mq_user_id)
            ->where('mq_type', 0) // 지출
            ->sum('mq_price');
            
        // 수입과 지출의 차이 계산
        $difference = $totalIncome - $totalExpense;

        // 추천 포트폴리오 가져오기
        $portfolios = $this->getRecommendedPortfolios();

        // 기본 데이터 설정
        $data = [
            'targetAmount' => $totalTargetAmount, // DB에서 가져온 목표금액
            'currentAmount' => 0, // 현재 모은 금액 (5천만원)
            'remainingMonths' => 60,     // 남은 기간 (5년)
            'monthlyExpenses' => [],
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'difference' => $difference,
            'recommendedPortfolios' => $portfolios // 추천 포트폴리오 데이터 추가
        ];

        // 지출 데이터가 없는 경우
        if ($expenses->isEmpty()) {
            return view('guidebook.roadmap', [
                'data' => $data,
                'hasExpenses' => false,
                'hasLifeGoal' => true
            ]);
        }

        // 지출 데이터 처리
        $expensesData = $expenses->map(function ($expense) {
            return [
                'name' => $expense->mq_category,
                'value' => $expense->mq_actual_amount
            ];
        });

        $data['monthlyExpenses'] = $expensesData;

        return view('guidebook.roadmap', [
            'data' => $data,
            'hasExpenses' => true,
            'hasLifeGoal' => true
        ]);
    }

    private function fetchPortfoliosByReturnRange($min, $max, $limit)
    {
        return InvestorPortfolio::with('boardPortfolio')
            ->whereBetween('portfolio_avg_return', [$min, $max])
            ->orderByDesc('portfolio_avg_return')
            ->limit($limit)
            ->get(['idx', 'investor_name', 'portfolio_value', 'portfolio_avg_return'])
            ->map(function ($portfolio) {
                return [
                    'idx' => $portfolio->idx,
                    'investor_name' => $portfolio->investor_name,
                    'portfolio_value' => $portfolio->portfolio_value,
                    'portfolio_avg_return' => $portfolio->portfolio_avg_return,
                    'board_portfolio_idx' => $portfolio->boardPortfolio->idx ?? null,
                ];
            });
    }


    private function getRecommendedPortfolios()
    {
        return [
            'stable' => ($stable = $this->fetchPortfoliosByReturnRange(4, 6, 3))->isEmpty()
                ? ['message' => '추천 포트폴리오를 준비중입니다'] : $stable,

            'growth' => ($growth = $this->fetchPortfoliosByReturnRange(8, 10, 4))->isEmpty()
                ? ['message' => '추천 포트폴리오를 준비중입니다'] : $growth,

            'aggressive' => ($aggressive = $this->fetchPortfoliosByReturnRange(13, 100, 3))->isEmpty()
                ? ['message' => '추천 포트폴리오를 준비중입니다'] : $aggressive,
        ];
    }
} 