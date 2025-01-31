<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RealityCheck;
use Exception;

class RealityCheckController extends Controller
{
    /**
     * 현실 점검 페이지 표시
     */
    public function index()
    {
        $expenses = DB::table('mq_reality_check')
            ->orderBy('mq_reg_date', 'desc')
            ->get();

        return view('guidebook.reality-check', [
            'expenses' => $expenses
        ]);
    }

    /**
     * 지출 항목 생성
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'mq_category' => 'required|string|max:50',
                'mq_expected_amount' => 'required|integer|min:0',
                'mq_actual_amount' => 'required|integer|min:0'
            ]);

            $result = DB::table('mq_reality_check')->insert([
                'mq_category' => $validated['mq_category'],
                'mq_expected_amount' => $validated['mq_expected_amount'],
                'mq_actual_amount' => $validated['mq_actual_amount'],
                'mq_reg_date' => now()
            ]);

            if (!$result) {
                throw new Exception('Failed to create expense');
            }

            return response()->json([
                'message' => '지출 항목이 추가되었습니다.',
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => '지출 항목 추가 중 오류가 발생했습니다.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * 지출 항목 수정
     */
    public function update(Request $request, $idx)
    {
        try {
            $validated = $request->validate([
                'mq_category' => 'required|string|max:50',
                'mq_expected_amount' => 'required|integer|min:0',
                'mq_actual_amount' => 'required|integer|min:0'
            ]);

            $result = DB::table('mq_reality_check')
                ->where('idx', $idx)
                ->update([
                    'mq_category' => $validated['mq_category'],
                    'mq_expected_amount' => $validated['mq_expected_amount'],
                    'mq_actual_amount' => $validated['mq_actual_amount'],
                    'mq_update_date' => now()
                ]);

            if (!$result) {
                throw new Exception('Failed to update expense');
            }

            return response()->json([
                'message' => '지출 항목이 수정되었습니다.',
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => '지출 항목 수정 중 오류가 발생했습니다.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * 지출 항목 삭제
     */
    public function destroy($idx)
    {
        try {
            $result = DB::table('mq_reality_check')
                ->where('idx', $idx)
                ->delete();

            if (!$result) {
                throw new Exception('Failed to delete expense');
            }

            return response()->json([
                'message' => '지출 항목이 삭제되었습니다.',
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => '지출 항목 삭제 중 오류가 발생했습니다.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
} 