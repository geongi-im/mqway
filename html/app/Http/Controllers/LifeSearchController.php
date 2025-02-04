<?php

namespace App\Http\Controllers;

use App\Models\LifeSearch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LifeSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');  // 로그인 필요
    }

    public function index()
    {
        $lifeSearches = LifeSearch::where('mq_user_id', auth()->user()->mq_user_id)
            ->orderBy('mq_reg_date', 'desc')
            ->get();

        return view('guidebook.life-search', [
            'lifeSearches' => $lifeSearches
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mq_category' => 'required|string|max:50',
            'mq_content' => 'required|string',
            'mq_price' => 'required|integer',
            'mq_expected_time' => 'required|string|max:50'
        ]);

        $validated['mq_user_id'] = auth()->user()->mq_user_id;
        $validated['mq_reg_date'] = Carbon::now();
        
        LifeSearch::create($validated);

        return response()->json([
            'message' => '성공적으로 추가되었습니다.',
            'success' => true
        ]);
    }

    public function update(Request $request, $idx)
    {
        $lifeSearch = LifeSearch::where('idx', $idx)
            ->where('mq_user_id', auth()->user()->mq_user_id)
            ->firstOrFail();

        $validated = $request->validate([
            'mq_category' => 'required|string|max:50',
            'mq_content' => 'required|string',
            'mq_price' => 'required|integer',
            'mq_expected_time' => 'required|string|max:50'
        ]);

        $validated['mq_update_date'] = Carbon::now();

        $lifeSearch->update($validated);

        return response()->json([
            'message' => '성공적으로 수정되었습니다.',
            'success' => true
        ]);
    }

    public function destroy($idx)
    {
        $lifeSearch = LifeSearch::where('idx', $idx)
            ->where('mq_user_id', auth()->user()->mq_user_id)
            ->firstOrFail();

        $lifeSearch->delete();

        return response()->json([
            'message' => '성공적으로 삭제되었습니다.',
            'success' => true
        ]);
    }
} 