<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsTop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewsController extends Controller
{
    protected $categoryColors = [
        '테크' => 'bg-yellow-100 text-yellow-800',
        '경제' => 'bg-blue-100 text-blue-800',
        '산업' => 'bg-green-100 text-green-800',
        '증권' => 'bg-red-100 text-red-800'
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
        $query->orderBy('mq_published_date', 'desc');

        // 페이지네이션
        $news = $query->paginate(10);

        // 카테고리 목록
        $categories = ['전체'];
        $categories = array_merge($categories, News::distinct()->pluck('mq_category')->toArray());

        // 오늘의 뉴스 1면 조회
        $topNews = NewsTop::byDate(today())
                          ->active()
                          ->orderBy('idx', 'asc')
                          ->get()
                          ->map(function($news) {
                              return [
                                  'idx' => $news->idx,
                                  'company' => $news->mq_company,
                                  'company_logo' => $news->getCompanyLogo(),
                                  'title' => $news->mq_title,
                                  'source_url' => $news->mq_source_url
                              ];
                          });

        return view('news.index', [
            'news' => $news,
            'categories' => $categories,
            'categoryColors' => $this->categoryColors,
            'topNews' => $topNews
        ]);
    }

    public function show($idx)
    {
        $news = News::findOrFail($idx);
        return view('news.show', compact('news'));
    }

    /**
     * 새로운 뉴스를 저장합니다.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'category' => 'required|string|max:50',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'company' => 'required|string|max:100',
                'source_url' => 'required|url',
                'published_date' => 'required|date',
                'step1_score' => 'required|integer|min:0|max:10'
            ]);

            // 뉴스 데이터 준비
            $newsData = [
                'mq_category' => $request->category,
                'mq_title' => $request->title,
                'mq_content' => $request->content,
                'mq_company' => $request->company,
                'mq_source_url' => $request->source_url,
                'mq_published_date' => $request->published_date,
                'mq_step1_score' => $request->step1_score,
                'mq_status' => 1,
                'mq_reg_date' => Carbon::now(),
                'mq_update_date' => Carbon::now()
            ];

            // 새로운 뉴스 생성
            $news = new News();
            foreach ($newsData as $key => $value) {
                $news->$key = $value;
            }
            $news->save();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '뉴스가 성공적으로 저장되었습니다.',
                    'data' => $news
                ], 201);
            }

            return redirect()->route('news.index')->with('success', '뉴스가 성공적으로 저장되었습니다.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '입력값 검증 실패',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '뉴스 저장 중 오류가 발생했습니다.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', '뉴스 저장 중 오류가 발생했습니다.')->withInput();
        }
    }

    /**
     * 특정 날짜의 1면 뉴스 조회 (AJAX용)
     *
     * @param string $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopNewsByDate($date)
    {
        try {
            $newsDate = Carbon::parse($date);

            $topNews = NewsTop::byDate($newsDate)
                              ->active()
                              ->orderBy('idx', 'asc')
                              ->get()
                              ->map(function($news) {
                                  return [
                                      'idx' => $news->idx,
                                      'company' => $news->mq_company,
                                      'company_logo' => $news->getCompanyLogo(),
                                      'title' => $news->mq_title,
                                      'source_url' => $news->mq_source_url
                                  ];
                              });

            // 요일 계산 (한글)
            $days = ['일', '월', '화', '수', '목', '금', '토'];
            $dayOfWeek = $days[$newsDate->dayOfWeek];

            return response()->json([
                'success' => true,
                'date' => $newsDate->format('Y-m-d'),
                'dayOfWeek' => $dayOfWeek,
                'news' => $topNews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '뉴스 조회 중 오류가 발생했습니다.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 