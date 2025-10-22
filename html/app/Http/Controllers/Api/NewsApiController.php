<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NewsController;
use App\Models\News;
use App\Models\NewsRss;
use App\Models\NewsTop;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NewsApiController extends Controller
{
    /**
     * RSS 피드 목록을 조회합니다.
     */
    public function rssList()
    {
        try {
            $rssList = NewsRss::where('mq_status', 1)
                ->select('idx', 'mq_category', 'mq_company', 'mq_rss')
                ->get()
                ->map(function ($rss) {
                    return [
                        'idx' => $rss->idx,
                        'mq_category' => $rss->mq_category,
                        'mq_company' => $rss->mq_company,
                        'mq_rss' => $rss->mq_rss
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $rssList
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'RSS 목록 조회 중 오류가 발생했습니다.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 뉴스 URL의 중복 여부를 체크합니다.
     */
    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $exists = News::where('mq_source_url', $request->url)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    /**
     * 새로운 뉴스를 저장합니다.
     */
    public function store(Request $request)
    {
        return app(NewsController::class)->store($request);
    }

    /**
     * 오늘의 뉴스 1면 저장 (외부 API용)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeTopNews(Request $request)
    {
        try {
            $validated = $request->validate([
                'news_date' => 'required|date',
                'company' => 'required|string|max:100',
                'title' => 'required|string|max:500',
                'source_url' => 'required|url'
            ]);

            // 중복 체크 (같은 날짜 + 같은 URL)
            $exists = NewsTop::where('mq_news_date', $validated['news_date'])
                             ->where('mq_source_url', $validated['source_url'])
                             ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => '이미 등록된 뉴스입니다.'
                ], 409);
            }

            // 저장
            $newsTop = NewsTop::create([
                'mq_news_date' => $validated['news_date'],
                'mq_company' => $validated['company'],
                'mq_title' => $validated['title'],
                'mq_source_url' => $validated['source_url'],
                'mq_status' => 1,
                'mq_reg_date' => Carbon::now(),
                'mq_update_date' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => '뉴스가 저장되었습니다.',
                'data' => $newsTop
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '입력값 검증 실패',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '뉴스 저장 중 오류가 발생했습니다.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

} 