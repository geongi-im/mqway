<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    protected $categoryColors = [
        '테크' => 'bg-yellow-100 text-yellow-800',
        '경제' => 'bg-blue-100 text-blue-800',
        '사회' => 'bg-green-100 text-green-800',
        '문화' => 'bg-red-100 text-red-800'
    ];

    public function index(Request $request)
    {
        $query = News::query();

        // 카테고리 필터링
        if ($request->has('category') && $request->category !== '전체') {
            $query->where('mq_category', $request->category);
        }

        // 검색어 필터링
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('mq_title', 'like', "%{$search}%")
                  ->orWhere('mq_content', 'like', "%{$search}%");
            });
        }

        // 정렬 (기본값: 최신순)
        $query->orderBy('mq_reg_date', 'desc');

        // 페이지네이션
        $news = $query->paginate(10);

        // 카테고리 목록
        $categories = ['전체'];
        $categories = array_merge($categories, News::distinct()->pluck('mq_category')->toArray());

        return view('news.index', [
            'news' => $news,
            'categories' => $categories,
            'categoryColors' => $this->categoryColors
        ]);
    }

    public function show($idx)
    {
        $news = News::findOrFail($idx);
        return view('news.show', compact('news'));
    }
} 