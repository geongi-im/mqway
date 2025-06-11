<?php

namespace App\Traits;

use App\Models\Board;

trait BoardCategoryColorTrait
{
    protected $predefinedColors = [
        'bg-yellow-100 text-yellow-800',
        'bg-blue-100 text-blue-800',
        'bg-green-100 text-green-800',
        'bg-red-100 text-red-800',
        'bg-purple-100 text-purple-800',
        'bg-indigo-100 text-indigo-800',
        'bg-pink-100 text-pink-800',
        'bg-orange-100 text-orange-800',
        'bg-teal-100 text-teal-800',
        'bg-gray-100 text-gray-800'
    ];
    
    // 고정된 카테고리 목록 정의
    protected $fixedCategories = [
        'board_content' => ['투자명언', '경제용어', '기타'],
        'board_research' => ['거래량', '순매수대금', 'RS랭킹', '증권사리포트', '기관순매수', '52주신고가','기타'],
        'board_video' => ['경제기초', '경제뉴스', '기타'],
        'board_portfolio' => ['포트폴리오', '기타']
    ];

    protected function getCategoryColors($board = null)
    {
        $categories = $this->getCategories($board);
        $categoryColors = [];
        foreach ($categories as $index => $category) {
            $categoryColors[$category] = $this->predefinedColors[$index % count($this->predefinedColors)];
        }

        return $categoryColors;
    }

    protected function getCategories($board = null)
    {
        // board 파라미터에 따라 해당 게시판의 고정 카테고리 반환
        if ($board && isset($this->fixedCategories[$board])) {
            return $this->fixedCategories[$board];
        }
        
        // board가 지정되지 않은 경우 모든 카테고리 반환
        $allCategories = [];
        foreach ($this->fixedCategories as $categories) {
            $allCategories = array_merge($allCategories, $categories);
        }
        
        return array_unique($allCategories);
    }
    
    // 특정 게시판의 카테고리만 가져오는 메소드
    protected function getBoardContentCategories()
    {
        return $this->fixedCategories['board_content'];
    }
    
    // 투자 리서치 게시판의 카테고리만 가져오는 메소드
    protected function getBoardResearchCategories() 
    {
        return $this->fixedCategories['board_research'];
    }
    
    // 비디오 게시판의 카테고리만 가져오는 메소드
    protected function getBoardVideoCategories() 
    {
        return $this->fixedCategories['board_video'];
    }
    
    // 포트폴리오 게시판의 카테고리만 가져오는 메소드
    protected function getBoardPortfolioCategories()
    {
        return $this->fixedCategories['board_portfolio'];
    }
} 