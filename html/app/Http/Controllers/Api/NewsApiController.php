<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NewsController;
use App\Models\News;
use App\Models\NewsRss;
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
} 