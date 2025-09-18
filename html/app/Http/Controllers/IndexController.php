<?php

namespace App\Http\Controllers;

use App\Models\BoardContent;
use App\Models\BoardResearch;
use App\Models\BoardVideo;
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

        // 쉽게 보는 경제 콘텐츠 가져오기
        $videoContents = BoardVideo::where('mq_status', 1)
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
                // 썸네일 이미지가 있으면 우선 사용
                if (is_array($post->mq_thumbnail_image) && !empty($post->mq_thumbnail_image)) {
                    $filename = $post->mq_thumbnail_image[0];
                    $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL)
                        ? asset('storage/uploads/board_research/' . $filename)
                        : $filename;
                } else if (is_array($post->mq_image) && !empty($post->mq_image)) {
                    $filename = $post->mq_image[0];
                    $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL)
                        ? asset('storage/uploads/board_research/' . $filename)
                        : $filename;
                } else {
                    // 본문에 이미지가 있는지 확인 (원본 콘텐츠 사용)
                    $firstImageSrc = extractFirstImageSrc($post->mq_original_content);
                    if ($firstImageSrc) {
                        $post->mq_image = $firstImageSrc;
                    } else {
                        $post->mq_image = null;
                    }
                }
            }
        }

        // 이미지 경로 처리 - 추천 콘텐츠
        foreach ($recommendedContents as $post) {
            // 썸네일 이미지가 있으면 우선 사용
            if (is_array($post->mq_thumbnail_image) && !empty($post->mq_thumbnail_image)) {
                $filename = $post->mq_thumbnail_image[0];
                $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL)
                    ? asset('storage/uploads/board_content/' . $filename)
                    : $filename;
            } else if (is_array($post->mq_image) && !empty($post->mq_image)) {
                $filename = $post->mq_image[0];
                $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL)
                    ? asset('storage/uploads/board_content/' . $filename)
                    : $filename;
            } else {
                // 본문에 이미지가 있는지 확인 (원본 콘텐츠 사용)
                $firstImageSrc = extractFirstImageSrc($post->mq_original_content);
                if ($firstImageSrc) {
                    $post->mq_image = $firstImageSrc;
                } else {
                    $post->mq_image = null;
                }
            }
        }
        
        // 이미지 경로 처리 - 쉽게 보는 경제
        foreach ($videoContents as $post) {
            // 썸네일 이미지가 있으면 우선 사용
            if (is_array($post->mq_thumbnail_image) && !empty($post->mq_thumbnail_image)) {
                $filename = $post->mq_thumbnail_image[0];
                $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL)
                    ? asset('storage/uploads/board_video/' . $filename)
                    : $filename;
            } else if (is_array($post->mq_image) && !empty($post->mq_image)) {
                // 업로드된 이미지가 있으면 그걸 사용
                $filename = $post->mq_image[0];
                $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL)
                    ? asset('storage/uploads/board_video/' . $filename)
                    : $filename;
            } else if (isset($post->mq_video_url) && !empty($post->mq_video_url)) {
                // 비디오 URL이 있으면 YouTube 썸네일 추출
                $thumbnailUrl = $this->getVideoThumbnail($post->mq_video_url);
                if ($thumbnailUrl) {
                    $post->mq_image = $thumbnailUrl;
                } else {
                    // 본문에서 이미지 추출
                    $firstImageSrc = extractFirstImageSrc($post->mq_original_content);
                    if ($firstImageSrc) {
                        $post->mq_image = $firstImageSrc;
                    } else {
                        // 이미지가 없으면 null 설정
                        $post->mq_image = null;
                    }
                }
            } else {
                // 본문에서 이미지 추출
                $firstImageSrc = extractFirstImageSrc($post->mq_original_content);
                if ($firstImageSrc) {
                    $post->mq_image = $firstImageSrc;
                } else {
                    // 이미지가 없으면 null 설정
                    $post->mq_image = null;
                }
            }
        }
        
        $latestNews = News::orderBy('mq_published_date', 'desc')
                         ->take(4)
                         ->get();

        // 각 게시판별 카테고리 색상 설정
        $boardContentColors = $this->getCategoryColors('board_content');
        $boardResearchColors = $this->getCategoryColors('board_research');
        $boardVideoColors = $this->getCategoryColors('board_video');

        return view('index', [
            'recommendedContents' => $recommendedContents,
            'videoContents' => $videoContents,
            'researchContents' => $researchContents,
            'latestNews' => $latestNews,
            'isLoggedIn' => $isLoggedIn,
            'newsCategoryColors' => $this->newsCategoryColors,
            'boardContentColors' => $boardContentColors,
            'boardResearchColors' => $boardResearchColors,
            'boardVideoColors' => $boardVideoColors,
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