<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\News;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    protected $boardCategoryColors = [
        '경제용어' => 'bg-yellow-100 text-yellow-800',
        'RS랭킹' => 'bg-blue-100 text-blue-800',
        '순매수대금' => 'bg-green-100 text-green-800',
        '거래량' => 'bg-red-100 text-red-800',
    ];

    protected $newsCategoryColors = [
        '테크' => 'bg-yellow-100 text-yellow-800',
        '경제' => 'bg-blue-100 text-blue-800',
        '산업' => 'bg-green-100 text-green-800',
        '증권' => 'bg-red-100 text-red-800'
    ];

    public function index()
    {
        $query = Board::where('mq_status', 1);
        $query->orderBy('mq_reg_date', 'desc');
        $posts = $query->paginate(10)->map(function ($post) {
            $post->mq_content = Str::limit(strip_tags($post->mq_content), 50);
            return $post;
        });

        // 이미지 경로 처리
        foreach ($posts as $post) {
            $post->mq_image = $post->mq_image 
                ? asset('storage/' . $post->mq_image)
                : asset('images/content/no_image.jpeg');
        }
        
        $latestNews = News::orderBy('mq_published_date', 'desc')
                         ->take(4)
                         ->get();

        return view('index', [
            'posts' => $posts,
            'latestNews' => $latestNews,
            'newsCategoryColors' => $this->newsCategoryColors,
            'boardCategoryColors' => $this->boardCategoryColors,
        ]);
    }

} 