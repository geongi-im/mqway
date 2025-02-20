<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RealityCheck;
use App\Models\LifeSearch;
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

        // 기본 데이터 설정
        $data = [
            'targetAmount' => $totalTargetAmount, // DB에서 가져온 목표금액
            'currentAmount' => 0, // 현재 모은 금액 (5천만원)
            'remainingMonths' => 60,     // 남은 기간 (5년)
            'monthlyExpenses' => []
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
} 