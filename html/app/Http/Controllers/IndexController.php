<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\News;

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
        '사회' => 'bg-green-100 text-green-800',
        '문화' => 'bg-red-100 text-red-800'
    ];

    public function index()
    {
        $query = Board::query();
        $query->orderBy('mq_reg_date', 'desc');
        $posts = $query->paginate(10);
        
        $latestNews = News::orderBy('mq_reg_date', 'desc')
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