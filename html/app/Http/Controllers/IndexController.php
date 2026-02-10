<?php

namespace App\Http\Controllers;

use App\Models\BoardCartoon;
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
                // 원본 콘텐츠 보존
                $post->mq_original_content = $post->mq_content;
                // 표시용 콘텐츠만 제한
                $post->mq_content = Str::limit(strip_tags($post->mq_content), 50);
                return $post;
            });

        // 인사이트 만화 콘텐츠 가져오기
        $cartoonContents = BoardCartoon::where('mq_status', 1)
            ->orderBy('mq_reg_date', 'desc')
            ->take(8)
            ->get()
            ->map(function ($post) {
                // 원본 콘텐츠 보존
                $post->mq_original_content = $post->mq_content;
                // 표시용 콘텐츠만 제한
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
                    // 원본 콘텐츠 보존
                    $post->mq_original_content = $post->mq_content;
                    // 표시용 콘텐츠만 제한
                    $post->mq_content = Str::limit(strip_tags($post->mq_content), 50);
                    return $post;
                });
                
            // 이미지 경로 처리 - 투자 리서치
            foreach ($researchContents as $post) {
                // 썸네일 이미지가 있으면 사용, 없으면 null
                if (is_array($post->mq_thumbnail_image) && !empty($post->mq_thumbnail_image)) {
                    $filename = $post->mq_thumbnail_image[0];
                    $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL)
                        ? asset('storage/uploads/board_research/' . $filename)
                        : $filename;
                } else {
                    $post->mq_image = null;
                }
            }
        }

        // 이미지 경로 처리 - 추천 콘텐츠
        foreach ($recommendedContents as $post) {
            // 썸네일 이미지가 있으면 사용, 없으면 null
            if (is_array($post->mq_thumbnail_image) && !empty($post->mq_thumbnail_image)) {
                $filename = $post->mq_thumbnail_image[0];
                $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL)
                    ? asset('storage/uploads/board_content/' . $filename)
                    : $filename;
            } else {
                $post->mq_image = null;
            }
        }

        // 이미지 경로 처리 - 인사이트 만화
        foreach ($cartoonContents as $post) {
            // 썸네일 이미지가 있으면 사용, 없으면 null
            if (is_array($post->mq_thumbnail_image) && !empty($post->mq_thumbnail_image)) {
                $filename = $post->mq_thumbnail_image[0];
                $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL)
                    ? asset('storage/uploads/board_cartoon/' . $filename)
                    : $filename;
            } else {
                $post->mq_image = null;
            }
        }
        

        
        $latestNews = News::orderBy('mq_published_date', 'desc')
                         ->take(4)
                         ->get();

        // 각 게시판별 카테고리 색상 설정
        $boardContentColors = $this->getCategoryColors('board_content');
        $boardCartoonColors = $this->getCategoryColors('board_cartoon');
        $boardResearchColors = $this->getCategoryColors('board_research');

        // Features 데이터
        $features = [
            ['emoji' => '🎓', 'title' => '레벨별 맞춤 학습', 'desc' => '돈의 개념, 소비와 저축, 자산과 가치까지 단계별로 차근차근 배워요'],
            ['emoji' => '🎮', 'title' => '게임으로 배우는 경제', 'desc' => '경제 보드게임, 상식 퀴즈 등 참여형 콘텐츠로 흥미와 자기주도 학습을 이끌어요'],
            ['emoji' => '📖', 'title' => '만화로 보는 경제 이야기', 'desc' => '어려운 경제 개념도 재미있는 만화로 쉽게 이해할 수 있어요'],
            ['emoji' => '📰', 'title' => '매일 어린이 경제뉴스', 'desc' => '세상 돌아가는 이야기를 아이 눈높이에서 매일 전해드려요'],
            ['emoji' => '✅', 'title' => '실천 미션', 'desc' => '배운 내용을 실생활에서 직접 실천하며 습관으로 만들어요'],
            ['emoji' => '👨‍👩‍👧', 'title' => '부모님과 함께', 'desc' => '아이와 부모가 함께 배우고 대화하며 성장하는 가족 교육'],
        ];

        return view('index', [
            'recommendedContents' => $recommendedContents,
            'cartoonContents' => $cartoonContents,
            'researchContents' => $researchContents,
            'latestNews' => $latestNews,
            'isLoggedIn' => $isLoggedIn,
            'newsCategoryColors' => $this->newsCategoryColors,
            'boardContentColors' => $boardContentColors,
            'boardCartoonColors' => $boardCartoonColors,
            'boardResearchColors' => $boardResearchColors,
            'features' => $features,
        ]);
    }
    
    /**
     * 비디오 URL에서 썸네일 추출
     */
    protected function getVideoThumbnail($videoUrl)
    {
        // YouTube URL 패턴 인식
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $videoUrl, $matches) || 
            preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
            $videoId = $matches[1];
            // YouTube 고품질 썸네일 URL 반환
            return "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
        }
        
        // Vimeo URL 패턴 인식 (Vimeo는 API 호출 필요로 복잡하므로 생략)
        // 다른 비디오 플랫폼은 필요에 따라 추가
        
        return null;
    }
} 