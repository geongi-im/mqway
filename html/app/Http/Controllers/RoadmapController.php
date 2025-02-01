<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');  // 로그인 필요
    }

    public function index()
    {
        // 데모 데이터
        $data = [
            'targetAmount' => 300000000, // 목표 금액 (3억)
            'currentAmount' => 50000000, // 현재 모은 금액 (5천만원)
            'remainingMonths' => 60,     // 남은 기간 (5년)
            'monthlyExpenses' => [
                ['name' => '식비', 'value' => 600000],
                ['name' => '주거·통신', 'value' => 750000],
                ['name' => '카페·간식', 'value' => 150000],
                ['name' => '편의점·마트·잡화', 'value' => 200000],
                ['name' => '취미·여가', 'value' => 300000],
                ['name' => '교통·자동차', 'value' => 200000],
                ['name' => '의료·건강·피트니스', 'value' => 150000],
                ['name' => '교육', 'value' => 200000],
            ]
        ];

        return view('guidebook.roadmap', compact('data'));
    }
} 