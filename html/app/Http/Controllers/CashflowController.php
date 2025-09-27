<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CashflowController extends Controller
{
    /**
     * Cashflow 소개 페이지
     */
    public function intro()
    {
        return view('cashflow.intro');
    }

    /**
     * Cashflow 진행 페이지
     */
    public function process()
    {
        return redirect()->route('cashflow.intro')->with('alert', '준비중입니다.');
    }

    /**
     * Cashflow 도우미 페이지
     */
    public function helper()
    {
        return view('cashflow.helper');
    }
}