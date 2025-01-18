<?php

namespace App\Http\Controllers;

use App\Models\LifeSearch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LifeSearchController extends Controller
{
    public function index()
    {
        $types = [
            LifeSearch::TYPE_DO,
            LifeSearch::TYPE_GO,
            LifeSearch::TYPE_SHARE
        ];

        $lifeSearches = [];
        foreach ($types as $type) {
            $lifeSearches[$type] = LifeSearch::where('mq_type', $type)
                ->orderBy('mq_reg_date', 'desc')
                ->get();
        }

        return view('guidebook.life-search', [
            'lifeSearches' => $lifeSearches,
            'types' => $types
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mq_type' => 'required|string',
            'mq_category' => 'required|string|max:50',
            'mq_content' => 'required|string',
            'mq_price' => 'required|integer',
            'mq_expected_time' => 'required|string|max:50'
        ]);

        $validated['mq_reg_date'] = Carbon::now();
        
        LifeSearch::create($validated);

        return response()->json([
            'message' => '성공적으로 추가되었습니다.',
            'success' => true
        ]);
    }

    public function update(Request $request, $idx)
    {
        $validated = $request->validate([
            'mq_type' => 'required|string',
            'mq_category' => 'required|string|max:50',
            'mq_content' => 'required|string',
            'mq_price' => 'required|integer',
            'mq_expected_time' => 'required|string|max:50'
        ]);

        $validated['mq_update_date'] = Carbon::now();

        $lifeSearch = LifeSearch::findOrFail($idx);
        $lifeSearch->update($validated);

        return response()->json([
            'message' => '성공적으로 수정되었습니다.',
            'success' => true
        ]);
    }

    public function destroy($idx)
    {
        $lifeSearch = LifeSearch::findOrFail($idx);
        $lifeSearch->delete();

        return response()->json([
            'message' => '성공적으로 삭제되었습니다.',
            'success' => true
        ]);
    }
} 