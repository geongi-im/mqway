<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * L1 소개 페이지
     */
    public function l1Intro()
    {
        return view('course.l1.intro');
    }

    /**
     * L2 소개 페이지
     */
    public function l2Intro()
    {
        return view('course.l1.intro');
    }
}