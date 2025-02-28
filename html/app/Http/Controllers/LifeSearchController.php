<?php

namespace App\Http\Controllers;

use App\Models\LifeSearch;
use App\Models\LifeSearchSample;
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
            'mq_price' => 'required|integer'
        ]);

        $validated['mq_user_id'] = auth()->user()->mq_user_id;
        $validated['mq_reg_date'] = Carbon::now();
        $validated['mq_target_date'] = Carbon::now();  // 현재 날짜로 설정
        
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
            'mq_price' => 'required|integer'
        ]);

        $validated['mq_update_date'] = Carbon::now();
        $validated['mq_target_date'] = Carbon::now();  // 현재 날짜로 설정

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

    /**
     * 카테고리 정보를 가져오는 API
     */
    public function getCategories()
    {
        $categories = LifeSearchSample::select('mq_s_category')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('mq_s_category')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'categories' => $categories->map(function($category) {
                return [
                    'name' => $category->mq_s_category,
                    'count' => $category->count
                ];
            })
        ]);
    }

    /**
     * 샘플 데이터를 가져오는 API
     */
    public function getSamples(Request $request)
    {
        // 페이지네이션 처리
        $page = $request->input('page', 1);
        $perPage = 10;
        $category = $request->input('category');
        
        $query = LifeSearchSample::query();
        
        // 카테고리 필터링
        if ($category) {
            $query->where('mq_s_category', $category);
        }
        
        $samples = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'cards' => $samples->map(function($sample) {
                return [
                    'id' => $sample->idx,
                    'category' => $sample->mq_s_category,
                    'content' => $sample->mq_s_content,
                    'price' => $sample->mq_s_price
                ];
            }),
            'has_more' => $samples->hasMorePages()
        ]);
    }

    /**
     * 선택한 샘플을 사용자의 목록에 추가
     */
    public function applySamples(Request $request)
    {
        $validated = $request->validate([
            'selectedCards' => 'required|array',
            'selectedCards.*' => 'required|integer'
        ]);

        try {
            $samples = LifeSearchSample::whereIn('idx', $validated['selectedCards'])->get();

            foreach ($samples as $sample) {
                LifeSearch::create([
                    'mq_user_id' => auth()->user()->mq_user_id,
                    'mq_category' => $sample->mq_s_category,
                    'mq_content' => $sample->mq_s_content,
                    'mq_price' => $sample->mq_s_price,
                    'mq_target_date' => Carbon::now(),  // 현재 날짜로 설정
                    'mq_reg_date' => now()
                ]);
            }

            return response()->json([
                'message' => '샘플이 성공적으로 추가되었습니다.',
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => '샘플 추가 중 오류가 발생했습니다.',
                'success' => false
            ], 500);
        }
    }
} 