<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RealityCheckController extends Controller
{
    public function index()
    {
        // 샘플 데이터
        $expenses = [
            [
                'category' => '주거비',
                'expected_amount' => 500000,
                'actual_amount' => 520000,
                'difference' => -20000
            ],
            [
                'category' => '식비',
                'expected_amount' => 400000,
                'actual_amount' => 450000,
                'difference' => -50000
            ],
            [
                'category' => '교통비',
                'expected_amount' => 150000,
                'actual_amount' => 130000,
                'difference' => 20000
            ],
            [
                'category' => '통신비',
                'expected_amount' => 100000,
                'actual_amount' => 100000,
                'difference' => 0
            ],
            [
                'category' => '문화생활',
                'expected_amount' => 200000,
                'actual_amount' => 300000,
                'difference' => -100000
            ]
        ];

        return view('guidebook.reality-check', [
            'expenses' => $expenses
        ]);
    }
} 