<?php

namespace App\Http\Controllers;

use App\Models\BoardContent;
use App\Models\BoardResearch;
use App\Models\News;
use App\Traits\BoardCategoryColorTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    use BoardCategoryColorTrait;

    protected $newsCategoryColors = [
        '테크' => 'bg-yellow-100 text-yellow-800',
        '경제' => 'bg-blue-100 text-blue-800',
        '산업' => 'bg-green-100 text-green-800',
        '증권' => 'bg-red-100 text-red-800'
    ];

    public function index()
    {
        // 로그인 상태 확인
        $isLoggedIn = Auth::check();

        // 추천 콘텐츠 가져오기
        $recommendedContents = BoardContent::where('mq_status', 1)
            ->orderBy('mq_reg_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($post) {
                $post->mq_content = Str::limit(strip_tags($post->mq_content), 50);
                return $post;
            });

        // 투자 리서치 콘텐츠 (로그인 한 사용자만)
        $researchContents = collect([]);
        if ($isLoggedIn) {
            $researchContents = BoardResearch::where('mq_status', 1)
                ->orderBy('mq_reg_date', 'desc')
                ->take(8)
                ->get()
                ->map(function ($post) {
                    $post->mq_content = Str::limit(strip_tags($post->mq_content), 50);
                    return $post;
                });
                
            // 이미지 경로 처리 - 투자 리서치
            foreach ($researchContents as $post) {
                if (is_array($post->mq_image) && !empty($post->mq_image)) {
                    $filename = $post->mq_image[0];
                    $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL) 
                        ? asset('storage/uploads/board_research/' . $filename)
                        : $filename;
                } else {
                    $post->mq_image = asset('images/content/no_image.jpeg');
                }
            }
        }

        // 이미지 경로 처리 - 추천 콘텐츠
        foreach ($recommendedContents as $post) {
            if (is_array($post->mq_image) && !empty($post->mq_image)) {
                $filename = $post->mq_image[0];
                $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL) 
                    ? asset('storage/uploads/board_content/' . $filename)
                    : $filename;
            } else {
                $post->mq_image = asset('images/content/no_image.jpeg');
            }
        }
        
        $latestNews = News::orderBy('mq_published_date', 'desc')
                         ->take(4)
                         ->get();

        return view('index', [
            'recommendedContents' => $recommendedContents,
            'researchContents' => $researchContents,
            'latestNews' => $latestNews,
            'isLoggedIn' => $isLoggedIn,
            'newsCategoryColors' => $this->newsCategoryColors,
            'boardCategoryColors' => $this->getCategoryColors(),
        ]);
    }
} 