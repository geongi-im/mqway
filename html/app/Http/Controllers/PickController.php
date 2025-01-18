<?php

namespace App\Http\Controllers;

use App\Models\Pick;
use App\Models\PickResult;
use App\Models\QuestionPair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickController extends Controller
{
    public function index()
    {
        $hasStarted = false;
        $currentPage = 0;
        $progress = ['total' => 0];
        $currentItem = null;

        if (auth()->check()) {
            $pick = Pick::where('user_id', auth()->id())
                       ->latest()
                       ->first();

            if ($pick) {
                $progress = [
                    'total' => $pick->results()->count(),
                    'completed' => $pick->results()->where('status', 1)->count()
                ];

                $hasStarted = true;
                $currentPage = $progress['completed'];

                if ($hasStarted) {
                    $currentItem = $pick->results()
                        ->with(['questionPair.questionA', 'questionPair.questionB'])
                        ->where('sequence', $currentPage + 1)
                        ->where('status', 0)
                        ->first();
                }
            }
        }

        return view('pick.index', compact('hasStarted', 'currentPage', 'progress', 'currentItem'));
    }

    public function start()
    {
        try {
            DB::beginTransaction();

            // 새로운 Pick 생성
            $pick = Pick::create([
                'user_id' => auth()->id(),
                'status' => 0
            ]);

            // 질문 쌍 가져오기
            $questionPairs = QuestionPair::all();
            
            // Pick Result 생성
            foreach ($questionPairs as $index => $pair) {
                PickResult::create([
                    'pick_id' => $pick->id,
                    'question_pair_id' => $pair->id,
                    'sequence' => $index + 1,
                    'status' => 0
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $result = PickResult::findOrFail($request->result_idx);
            
            $result->update([
                'selected_question_id' => $request->selected_id,
                'status' => 1
            ]);

            $completed = $result->pick->results()->where('status', 0)->count() === 0;

            return response()->json([
                'success' => true,
                'completed' => $completed
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function prev(Request $request)
    {
        try {
            $result = PickResult::findOrFail($request->result_idx);
            
            // 이전 결과 찾기
            $prevResult = PickResult::where('pick_id', $result->pick_id)
                                  ->where('sequence', $result->sequence - 1)
                                  ->first();

            if ($prevResult) {
                $prevResult->update(['status' => 0, 'selected_question_id' => null]);
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => '이전 결과를 찾을 수 없습니다.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 