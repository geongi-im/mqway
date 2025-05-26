<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EconomyTermGameHistory;
use App\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class EconomyTermGameController extends Controller
{
    public function index()
    {
        $economyTerms = [
            [
                "term" => "저축",
                "description" => "돈을 미래에 사용하기 위해 모아두는 것"
            ],
            [
                "term" => "소비",
                "description" => "필요한 물건이나 서비스를 사기 위해 돈을 사용하는 것"
            ],
            [
                "term" => "예산",
                "description" => "계획을 세워서 돈을 어떻게 쓸지 정해놓는 것"
            ],
            [
                "term" => "시장",
                "description" => "물건을 사고파는 장소나 활동"
            ],
            [
                "term" => "수요",
                "description" => "사람들이 어떤 물건을 사고 싶어하는 마음"
            ],
            [
                "term" => "공급",
                "description" => "가게에서 물건을 만들어서 팔 수 있는 양"
            ],
            [
                "term" => "이자",
                "description" => "은행에 돈을 맡기면 받는 추가 돈"
            ],
            [
                "term" => "세금",
                "description" => "국가가 좋은 일을 하기 위해 국민들이 내는 돈"
            ],
            [
                "term" => "직업",
                "description" => "돈을 벌기 위해 하는 일"
            ],
            [
                "term" => "은행",
                "description" => "돈을 안전하게 맡기고 빌려주는 곳"
            ],
            [
                "term" => "투자",
                "description" => "미래에 더 많은 돈을 벌기 위해 지금 돈을 사용하는 것"
            ],
            [
                "term" => "가격",
                "description" => "어떤 물건을 사기 위해 내야 하는 돈의 양"
            ],
            [
                "term" => "상품",
                "description" => "사고파는 물건들"
            ],
            [
                "term" => "서비스",
                "description" => "다른 사람이 해주는 도움이나 일"
            ],
            [
                "term" => "용돈",
                "description" => "부모님이 아이에게 주는 작은 돈"
            ],
            [
                "term" => "할인",
                "description" => "원래 가격보다 더 싸게 파는 것"
            ],
            [
                "term" => "거스름돈",
                "description" => "물건 값보다 더 많이 낸 돈 중에서 돌려받는 돈"
            ],
            [
                "term" => "신용카드",
                "description" => "지금 돈이 없어도 나중에 갚기로 하고 물건을 살 수 있는 카드"
            ],
            [
                "term" => "경제",
                "description" => "사람들이 돈을 벌고 쓰고 저축하는 모든 활동"
            ],
            [
                "term" => "물가",
                "description" => "여러 가지 물건들의 평균적인 가격"
            ]
        ];

        $randomTerms = collect($economyTerms)->shuffle()->take(5)->values()->all();
        return view('economy_term_game', ['economyTerms' => $randomTerms]);
    }

    /**
     * 게임 결과를 DB에 저장합니다.
     */
    public function storeGameResult(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $validatedData = $request->validate([
            'total_questions' => 'required|integer',
            'score' => 'required|integer',
            'time' => 'required|integer',
        ]);

        try {
            EconomyTermGameHistory::create([
                'mq_user_id' => Auth::id(),
                'mq_total_count' => $validatedData['total_questions'],
                'mq_correct_count' => $validatedData['score'],
                'mq_duration_time' => $validatedData['time'],
                'mq_reg_date' => now(),
            ]);

            return response()->json(['success' => 'Game result saved successfully.']);

        } catch (Exception $e) {
            Log::error('Error saving game result: '.$e->getMessage(). ' --- Trace: '.$e->getTraceAsString());
            return response()->json(['error' => 'Failed to save game result.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 상위 랭킹을 DB에서 조회합니다.
     */
    public function getRanking()
    {
        try {
            $rankings = EconomyTermGameHistory::with(['user' => function ($query) {
                    $query->select('idx', 'mq_user_name');
                }])
                ->select('mq_user_id', 'mq_correct_count as score', 'mq_duration_time as time', 'mq_reg_date as date')
                ->orderBy('mq_correct_count', 'desc')
                ->orderBy('mq_duration_time', 'asc')
                ->take(5)
                ->get();

            $rankings->transform(function ($item) {
                $minutes = floor($item->time / 60);
                $seconds = $item->time % 60;
                $item->time_formatted = sprintf('%02d:%02d', $minutes, $seconds);
                $item->userName = $item->user ? $item->user->mq_user_name : '익명';
                unset($item->user);
                return $item;
            });
            
            return response()->json($rankings);

        } catch (Exception $e) {
            Log::error('Error fetching ranking: '.$e->getMessage(). ' --- Trace: '.$e->getTraceAsString());
            return response()->json(['error' => 'Failed to fetch ranking.', 'message' => $e->getMessage()], 500);
        }
    }
} 