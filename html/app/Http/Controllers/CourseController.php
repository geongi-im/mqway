<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseProgress;
use Illuminate\Support\Facades\Auth;

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
        return redirect()->back()->with('error', '코스 준비중입니다.');
    }

    /**
     * 사용자의 코스 진행 상태 조회
     */
    public function getProgress(Request $request, $courseCode)
    {
        // 비로그인 사용자는 빈 배열 반환
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'progress' => [],
                'completion_rate' => 0
            ]);
        }

        $memberIdx = Auth::user()->idx;
        $progress = CourseProgress::getUserCourseProgress($memberIdx, $courseCode);
        $completionRate = CourseProgress::getCompletionRate($memberIdx, $courseCode);

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'completion_rate' => $completionRate
        ]);
    }

    /**
     * 단계 완료 상태 토글
     */
    public function toggleProgress(Request $request)
    {
        // 비로그인 사용자는 에러 반환
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => '로그인이 필요합니다.'
            ], 401);
        }

        $request->validate([
            'course_code' => 'required|string|max:50',
            'step_number' => 'required|integer|min:1|max:8',
        ]);

        $memberIdx = Auth::user()->idx;
        $courseCode = $request->input('course_code');
        $stepNumber = $request->input('step_number');

        try {
            $progress = CourseProgress::toggleStep($memberIdx, $courseCode, $stepNumber);
            $completionRate = CourseProgress::getCompletionRate($memberIdx, $courseCode);

            return response()->json([
                'success' => true,
                'is_completed' => $progress->is_completed,
                'completion_rate' => $completionRate,
                'message' => $progress->is_completed ? '단계를 완료했습니다.' : '진행중으로 변경했습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '저장 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}