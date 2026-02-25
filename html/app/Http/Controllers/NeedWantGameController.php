<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NeedWantItem;
use App\Models\NeedWantGameHistory;
use Exception;
use Illuminate\Support\Facades\Log;

class NeedWantGameController extends Controller
{
    /**
     * 게임 페이지를 표시합니다.
     * DB에 활성화된 아이템이 있으면 DB에서, 없으면 기본 데이터를 사용합니다.
     */
    public function index()
    {
        $dbItems = NeedWantItem::active()
            ->orderBy('idx')
            ->get();

        if ($dbItems->isNotEmpty()) {
            $items = $dbItems->map(function ($item) {
                return [
                    'id' => $item->idx,
                    'image' => $item->mq_image ? asset('images/need_want/' . $item->mq_image) : null,
                    'name' => $item->mq_name,
                    'description' => $item->mq_description,
                ];
            })->values()->all();
        } else {
            // DB에 아이템이 없을 경우 기본 데이터 사용
            $items = [
                ['id' => 1, 'image' => null, 'name' => '건강한 음식', 'description' => '우리 몸이 튼튼하게 자라기 위해 필요한 과일과 채소예요.'],
                ['id' => 2, 'image' => null, 'name' => '게임기', 'description' => '재미있는 비디오 게임을 할 수 있는 최신 게임 콘솔이에요.'],
                ['id' => 3, 'image' => null, 'name' => '교과서', 'description' => '학교에서 공부할 때 꼭 필요한 교과서와 학습 교재예요.'],
                ['id' => 4, 'image' => null, 'name' => '운동화', 'description' => '체육 시간이나 운동할 때 신는 편안한 신발이에요.'],
                ['id' => 5, 'image' => null, 'name' => '인형', 'description' => '귀여운 캐릭터가 그려진 부드러운 봉제 인형이에요.'],
                ['id' => 6, 'image' => null, 'name' => '감기약', 'description' => '감기에 걸렸을 때 빨리 낫도록 도와주는 약이에요.'],
                ['id' => 7, 'image' => null, 'name' => '사탕', 'description' => '달콤하고 맛있는 간식거리예요.'],
                ['id' => 8, 'image' => null, 'name' => '따뜻한 집', 'description' => '비바람을 막아주고 가족이 함께 생활하는 안전한 공간이에요.'],
                ['id' => 9, 'image' => null, 'name' => '스마트폰', 'description' => '사진도 찍고 게임도 할 수 있는 최신 휴대전화예요.'],
                ['id' => 10, 'image' => null, 'name' => '겨울 외투', 'description' => '추운 겨울에 몸을 따뜻하게 감싸주는 두꺼운 옷이에요.'],
            ];
        }

        return view('tools.need-want-game', ['items' => $items]);
    }

    /**
     * 게임 결과를 DB에 저장합니다.
     * 비회원도 저장 가능 (mq_user_id = null)
     */
    public function storeGameResult(Request $request)
    {
        $validatedData = $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.item_idx' => 'required|integer',
            'answers.*.item_name' => 'required|string|max:100',
            'answers.*.choice' => 'required|in:need,want',
            'answers.*.reason' => 'required|string|max:200',
        ]);

        try {
            NeedWantGameHistory::create([
                'mq_user_id' => Auth::check() ? Auth::user()->mq_user_id : null,
                'mq_answers' => $validatedData['answers'],
                'mq_reg_date' => now(),
            ]);

            return response()->json(['success' => '게임 결과가 저장되었습니다.']);

        } catch (Exception $e) {
            Log::error('Need/Want 게임 결과 저장 실패: ' . $e->getMessage() . ' --- Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => '게임 결과 저장에 실패했습니다.', 'message' => $e->getMessage()], 500);
        }
    }
}
