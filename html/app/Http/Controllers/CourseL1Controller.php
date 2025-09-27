<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseL1Controller extends Controller
{
    /**
     * L1 소개 페이지
     */
    public function intro()
    {
        return view('course-l1.intro');
    }

    /**
     * L1 인생의 지도 그리기 페이지
     */
    public function lifeMap()
    {
        return view('course-l1.life-map');
    }
}