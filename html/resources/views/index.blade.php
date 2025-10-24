@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Facades\Auth;
@endphp

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<!-- ECharts 라이브러리 -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<style>
/* 메인 배너 스타일 */
.mainBanner {
    position: relative;
    width: 100%;
    height: 65vh; /* 화면 높이의 65%로 확장 */
    background: #34383d;
    overflow: hidden;
}

.mainBanner .swiper-wrapper {
    width: 100%;
    height: 100%;
}

.mainBanner .swiper-slide {
    position: relative;
    width: 100%;
    height: 100%;
    background-color: #f8f8f8; /* 배경색 추가 */
}

.mainBanner .banner-image {
    position: relative;
    width: 100%;
    height: 100%;
}

.mainBanner .banner-image img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* cover에서 contain으로 변경하여 이미지가 짤리지 않도록 함 */
    object-position: center;
}

.mainBanner .banner-content {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7)); /* 그라데이션 강화 */
    display: flex;
    align-items: flex-end; /* 중앙에서 아래쪽으로 정렬 변경 */
    padding: 0 20px;
    padding-bottom: 120px; /* 하단 여백 추가 */
}

.mainBanner .banner-content .text-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 60px;
    text-align: center;
}

.mainBanner .banner-content h2 {
    font-size: 3.0rem; /* 글자 크기 증가 */
    line-height: 1.3;
    font-weight: 700;
    color: #fff;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.6); /* 텍스트 그림자 강화 */
    text-align: center;
    margin-bottom: 1.5rem; /* 버튼과의 간격 */
}

/* CTA 버튼 추가 */
.mainBanner .banner-content .cta-button {
    display: inline-block;
    background-color: #eba568; /* 포인트 색상 */
    color: #333;
    font-weight: 600;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-size: 1.125rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.mainBanner .banner-content .cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.4);
}

/* 네비게이션 버튼 */
.mainBanner .swiper-button-next,
.mainBanner .swiper-button-prev {
    color: #fff;
    background: rgba(0, 0, 0, 0.3);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.mainBanner .swiper-button-next {
    right: 30px;
}

.mainBanner .swiper-button-prev {
    left: 30px;
}

.mainBanner .swiper-button-next:hover,
.mainBanner .swiper-button-prev:hover {
    background: rgba(0, 0, 0, 0.6);
    transform: scale(1.1);
}

.mainBanner .swiper-button-next:after,
.mainBanner .swiper-button-prev:after {
    font-size: 24px;
}

/* 페이지네이션 */
.mainBanner .swiper-pagination {
    bottom: 30px !important;
}

.mainBanner .swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: rgba(255, 255, 255, 0.7);
    margin: 0 6px !important;
}

.mainBanner .swiper-pagination-bullet-active {
    background: #fff;
    transform: scale(1.2);
}

/* 콘텐츠 슬라이더 스타일 */
.contentSlider, .researchSlider, .videoSlider {
    position: relative;
    padding-bottom: 50px; /* 페이지네이션을 위한 하단 여백 */
}

.contentSlider .swiper-button-next,
.contentSlider .swiper-button-prev,
.researchSlider .swiper-button-next,
.researchSlider .swiper-button-prev,
.videoSlider .swiper-button-next,
.videoSlider .swiper-button-prev {
    color: #34383d;
    background: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.contentSlider .swiper-button-next,
.researchSlider .swiper-button-next,
.videoSlider .swiper-button-next {
    right: -5px;
}

.contentSlider .swiper-button-prev,
.researchSlider .swiper-button-prev,
.videoSlider .swiper-button-prev {
    left: -5px;
}

.contentSlider .swiper-button-next:hover,
.contentSlider .swiper-button-prev:hover,
.researchSlider .swiper-button-next:hover,
.researchSlider .swiper-button-prev:hover,
.videoSlider .swiper-button-next:hover,
.videoSlider .swiper-button-prev:hover {
    background: #f8f8f8;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.contentSlider .swiper-button-next:after,
.contentSlider .swiper-button-prev:after,
.researchSlider .swiper-button-next:after,
.researchSlider .swiper-button-prev:after,
.videoSlider .swiper-button-next:after,
.videoSlider .swiper-button-prev:after {
    font-size: 18px;
}

.contentSlider .swiper-pagination,
.researchSlider .swiper-pagination,
.videoSlider .swiper-pagination {
    bottom: 0 !important;
    position: absolute;
}

.contentSlider .swiper-pagination-bullet,
.researchSlider .swiper-pagination-bullet,
.videoSlider .swiper-pagination-bullet {
    width: 8px;
    height: 8px;
    background: rgba(0, 0, 0, 0.2);
    margin: 0 4px !important;
}

.contentSlider .swiper-pagination-bullet-active,
.researchSlider .swiper-pagination-bullet-active,
.videoSlider .swiper-pagination-bullet-active {
    background: #34383d;
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .mainBanner {
        height: 50vh; /* 모바일에서도 높이 확장 */
        padding: 0;
    }

    .mainBanner .banner-image {
        height: 100%;
    }

    .mainBanner .banner-content {
        background: linear-gradient(to bottom, 
            rgba(0,0,0,0.2) 0%,
            rgba(0,0,0,0.6) 50%,
            rgba(0,0,0,0.8) 100%
        );
        padding-bottom: 80px; /* 모바일 하단 여백 조정 */
    }

    .mainBanner .banner-content .text-container {
        padding: 0 20px;
        max-width: 100%;
    }

    .mainBanner .banner-content h2 {
        font-size: 1.5rem; /* 모바일 글자 크기 조정 */
        line-height: 1.5;
        letter-spacing: -0.02em;
        text-align: center;
        word-break: keep-all;
        margin-bottom: 1rem;
    }

    .mainBanner .banner-content .cta-button {
        padding: 0.5rem 1.5rem;
        font-size: 1rem;
    }

    /* 네비게이션 버튼 크기 조정 */
    .mainBanner .swiper-button-next,
    .mainBanner .swiper-button-prev {
        width: 36px;
        height: 36px;
    }

    .mainBanner .swiper-button-next {
        right: 10px;
    }

    .mainBanner .swiper-button-prev {
        left: 10px;
    }

    .mainBanner .swiper-button-next:after,
    .mainBanner .swiper-button-prev:after {
        font-size: 18px;
    }

    /* 페이지네이션 위치 및 크기 조정 */
    .mainBanner .swiper-pagination {
        bottom: 15px !important;
    }

    .mainBanner .swiper-pagination-bullet {
        width: 8px;
        height: 8px;
        margin: 0 4px !important;
    }

    .contentSlider, .researchSlider, .videoSlider {
        padding: 0 15px 40px 15px;
    }

    .contentSlider .swiper-button-next,
    .contentSlider .swiper-button-prev,
    .researchSlider .swiper-button-next,
    .researchSlider .swiper-button-prev,
    .videoSlider .swiper-button-next,
    .videoSlider .swiper-button-prev {
        display: none;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .mainBanner {
        height: 55vh; /* 태블릿 높이 설정 */
        padding: 0;
    }

    .mainBanner .banner-content {
        padding-bottom: 100px; /* 태블릿 하단 여백 조정 */
    }

    .mainBanner .banner-content h2 {
        font-size: 2.0rem; /* 태블릿 글자 크기 조정 */
        line-height: 1.4;
        text-align: center;
    }

    .mainBanner .banner-content .text-container {
        max-width: 600px;
        margin: 0 auto;
    }
}

/* 로딩 애니메이션 */
.dot-animation {
    animation: dots 1.5s infinite;
}

@keyframes dots {
    0%, 20% {
        content: ".";
    }
    40% {
        content: "..";
    }
    60%, 100% {
        content: "...";
    }
}

/* 챗봇 메시지 스타일 */
.bot-response {
    white-space: pre-wrap;
    line-height: 1.5;
}

</style>

<!-- 메인 배너 슬라이더 -->
<div class="swiper mainBanner mb-0">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <div class="banner-image">
                <img src="{{ asset('images/banner/main_banner_10.png') }}" alt="배너1">
                <div class="banner-content">
                    <div class="text-container">
                        <h2>경제는 지식이 아닌 습관입니다.<br>지금 바로 시작하세요.</h2>
                        <a href="{{ route('tools.financial-quiz') }}" class="cta-button">경제 상식 테스트</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="banner-image">
                <img src="{{ asset('images/banner/main_banner_11.png') }}" alt="배너2">
                <div class="banner-content">
                    <div class="text-container">
                        <h2>용돈이 아니라,<br>인생을 가르치는 시간입니다.</h2>
                        <a href="{{ url('/board-news') }}" class="cta-button">주요 뉴스 보기</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="banner-image">
                <img src="{{ asset('images/banner/main_banner_12.png') }}" alt="배너3">
                <div class="banner-content">
                    <div class="text-container">
                    <h2>부모의 한마디가<br>아이의 금융감각을 만듭니다.</h2>
                        <a href="{{ url('/board-content') }}" class="cta-button">추천 콘텐츠 보기</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="banner-image">
                <img src="{{ asset('images/banner/main_banner_13.png') }}" alt="배너4">
                <div class="banner-content">
                    <div class="text-container">
                        <h2>미래 준비의 시작,<br>가족의 내일을 설계하세요.</h2>
                        <a href="{{ route('tools.retirement-calculator') }}" class="cta-button">노후 자금 계산기</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 네비게이션 버튼 -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <!-- 페이지네이션 -->
    <div class="swiper-pagination"></div>
</div>

<!-- Hero Section -->
<section class="bg-primary py-12 md:py-16">
    <div class="container mx-auto px-4">
        <!-- 타이틀 -->
        <div class="text-center mb-10 md:mb-12">
            <h2 class="text-2xl md:text-4xl font-bold text-gray-800 mb-4">
                자녀와 함께하는<br class="md:hidden">
                선진국형 금융교육 커뮤니티
            </h2>
            <p class="text-gray-600 text-base md:text-lg max-w-2xl mx-auto">
                자녀의 금융 습관을 만들고, 부모님은 재테크를 배우는<br class="hidden md:block">
                온/오프라인 통합 커뮤니티입니다.
            </p>
        </div>

        <!-- 3개 핵심 가치 카드 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto mb-8 md:mb-10">
            <!-- 카드 1: 함께 -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                <div class="text-4xl md:text-5xl mb-4">👨‍👩‍👧‍👦</div>
                <h3 class="font-bold text-lg md:text-xl mb-2">학부모와 자녀가 함께</h3>
                <p class="text-gray-600 text-sm">
                    부모와 아이가 함께 배우고 성장하는 가족 단위 교육
                </p>
            </div>

            <!-- 카드 2: 체계적 -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                <div class="text-4xl md:text-5xl mb-4">📚</div>
                <h3 class="font-bold text-lg md:text-xl mb-2">체계적인 교육 프로그램</h3>
                <p class="text-gray-600 text-sm">
                    레벨별 맞춤 커리큘럼과 실전형 미션 활동
                </p>
            </div>

            <!-- 카드 3: 성장 -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                <div class="text-4xl md:text-5xl mb-4">🌱</div>
                <h3 class="font-bold text-lg md:text-xl mb-2">실천하며 성장</h3>
                <p class="text-gray-600 text-sm">
                    온라인 학습 + 오프라인 활동으로 실제 습관 형성
                </p>
            </div>
        </div>

        <!-- CTA 버튼 -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('course.l1.intro') }}"
               class="bg-point1 hover:bg-point1/90 text-white px-6 md:px-8 py-3 rounded-lg font-semibold text-center transition transform hover:scale-105">
                프로그램 시작하기 →
            </a>
            <a href="{{ route('introduce') }}"
               class="bg-white hover:bg-gray-50 text-gray-800 px-6 md:px-8 py-3 rounded-lg font-semibold border-2 border-gray-200 text-center transition transform hover:scale-105">
                MQWAY 자세히 보기 →
            </a>
        </div>
    </div>
</section>

<!-- 주요 경제지표 차트 -->
<div class="container mx-auto px-4 mb-12">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h2 class="text-xl md:text-2xl font-bold">매달 <input type="number" id="monthlyInvestment" class="border border-gray-300 rounded px-2 py-1 w-20 text-center font-bold text-blue-600" value="1" min="1" max="1000" step="1" onfocus="this.select()"> 만원씩 투자한다면?<br><span class="text-sm md:text-base font-medium text-gray-600">(최근 5년간 애플/나스닥/예금 투자 비교)</span></h2>
        <button id="updateChartBtn" class="bg-point hover:bg-point/90 text-cdark px-4 py-2 rounded-lg shadow-sm font-medium transition-all duration-300 hover:scale-105 w-full md:w-auto">확인하기</button>
    </div>
    <div id="lineRaceChart" class="w-full bg-white p-4 rounded-lg shadow-md" style="width: 100%; height: 450px !important; display: block; overflow: hidden;"></div>
</div>

<!-- 콘텐츠1 슬라이더 -->
<div class="container mx-auto px-4 mb-16">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold mb-6">추천 콘텐츠</h2>
        <a href="{{ url('/board-content') }}" class="text-dark hover:text-dark/80 transition-colors flex items-center">
            더보기
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
    <div class="swiper contentSlider">
        <div class="swiper-wrapper">
            @foreach($recommendedContents as $post)
            <div class="swiper-slide">
                <a href="{{ route('board-content.show', $post->idx) }}" class="block h-full">
                <div class="bg-white rounded-lg overflow-hidden shadow-lg h-full flex flex-col">
                    <div class="bg-gray-50 flex items-center justify-center" style="height: 240px;">
                        @if($post->mq_image)
                            <img src="{{ asset($post->mq_image) }}"
                                 alt="게시글 이미지"
                                 class="w-full h-full object-contain p-2">
                        @else
                            <!-- 썸네일 없음 플레이스홀더 -->
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm">이미지 없음</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="mb-2">
                            <span class="inline-block px-2 py-1 {{ $boardContentColors[$post->mq_category] }} text-xs font-medium rounded-md">
                                {{ $post->mq_category }}
                            </span>
                        </div>
                        <h3 class="font-bold text-lg mb-2">{{ $post->mq_title }}</h3>
                        <div class="mt-auto flex items-center justify-between text-sm text-text-dark">
                            <span>{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y-m-d') : '' }}</span>
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ $post->mq_view_cnt }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    {{ $post->mq_like_cnt }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            @endforeach
        </div>
        <!-- 네비게이션 버튼 -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <!-- 페이지네이션 -->
        <div class="swiper-pagination"></div>
    </div>
</div>

<!-- 쉽게 보는 경제 슬라이더 -->
<div class="container mx-auto px-4 mb-16">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold mb-6">쉽게 보는 경제</h2>
        <a href="{{ url('/board-video') }}" class="text-dark hover:text-dark/80 transition-colors flex items-center">
            더보기
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
    <div class="swiper videoSlider">
        <div class="swiper-wrapper">
            @foreach($videoContents as $post)
            <div class="swiper-slide">
                <a href="{{ route('board-video.show', $post->idx) }}" class="block h-full">
                <div class="bg-white rounded-lg overflow-hidden shadow-lg h-full flex flex-col">
                    <div class="bg-gray-50 flex items-center justify-center relative" style="height: 240px;">
                        <img src="{{ asset($post->mq_image) }}" 
                             alt="비디오 썸네일" 
                             class="w-full h-full object-cover">
                        @if(isset($post->mq_video_url) && !empty($post->mq_video_url))
                        <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-80" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="mb-2">
                            <span class="inline-block px-2 py-1 {{ $boardVideoColors[$post->mq_category] }} text-xs font-medium rounded-md">
                                {{ $post->mq_category }}
                            </span>
                        </div>
                        <h3 class="font-bold text-lg mb-2">{{ $post->mq_title }}</h3>
                        <div class="mt-auto flex items-center justify-between text-sm text-text-dark">
                            <span>{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y-m-d') : '' }}</span>
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ $post->mq_view_cnt }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    {{ $post->mq_like_cnt }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            @endforeach
        </div>
        <!-- 네비게이션 버튼 -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <!-- 페이지네이션 -->
        <div class="swiper-pagination"></div>
    </div>
</div>

<!-- 투자 리서치 슬라이더 -->
@if(Auth::check())
<div class="container mx-auto px-4 mb-16">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold mb-6">투자 리서치</h2>
        <a href="{{ url('/board-research') }}" class="text-dark hover:text-dark/80 transition-colors flex items-center">
            더보기
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
    <div class="swiper researchSlider">
        <div class="swiper-wrapper">
            @foreach($researchContents as $post)
            <div class="swiper-slide">
                <a href="{{ route('board-research.show', $post->idx) }}" class="block h-full">
                <div class="bg-white rounded-lg overflow-hidden shadow-lg h-full flex flex-col">
                    <div class="bg-gray-50 flex items-center justify-center" style="height: 240px;">
                        @if($post->mq_image)
                            <img src="{{ asset($post->mq_image) }}"
                                 alt="리서치 이미지"
                                 class="w-full h-full object-contain p-2">
                        @else
                            <!-- 썸네일 없음 플레이스홀더 -->
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm">이미지 없음</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="mb-2">
                            <span class="inline-block px-2 py-1 {{ $boardResearchColors[$post->mq_category] }} text-xs font-medium rounded-md">
                                {{ $post->mq_category }}
                            </span>
                        </div>
                        <h3 class="font-bold text-lg mb-2">{{ $post->mq_title }}</h3>
                        <div class="mt-auto flex items-center justify-between text-sm text-text-dark">
                            <span>{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y-m-d') : '' }}</span>
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ $post->mq_view_cnt }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    {{ $post->mq_like_cnt }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            @endforeach
        </div>
        <!-- 네비게이션 버튼 -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <!-- 페이지네이션 -->
        <div class="swiper-pagination"></div>
    </div>
</div>
@else
<!-- 비회원을 위한 로그인 유도 섹션 -->
<div class="container mx-auto px-4 mb-16">
    <div class="bg-gray-50 p-8 rounded-lg shadow-md text-center">
        <h2 class="text-2xl font-bold mb-4">투자 리서치</h2>
        <p class="text-gray-600 mb-6">전문적인 투자 리서치 콘텐츠는 로그인 후 이용하실 수 있습니다.</p>
        <a href="{{ route('login') }}" class="bg-point hover:bg-point/90 text-cdark px-8 py-3 rounded-lg shadow-lg font-medium transition-all duration-300 inline-block transform hover:scale-105 hover:shadow-xl">
            로그인하고 리서치 보기
        </a>
    </div>
</div>
@endif

<!-- 콘텐츠2 2열 그리드 -->
<div class="container mx-auto px-4 mb-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">주요 뉴스</h2>
        <a href="{{ url('/board-news') }}" class="text-dark hover:text-dark/80 transition-colors flex items-center">
            더보기
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($latestNews as $news)
        <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center gap-3 text-sm text-text-dark mb-3">
                <span class="{{ $newsCategoryColors[$news->mq_category] }} px-3 py-1 rounded-md">
                    {{ $news->mq_category }}
                </span>
                <time datetime="{{ $news->mq_reg_date }}">
                    {{ $news->mq_reg_date->format('Y.m.d') }}
                </time>
                <span>·</span>
                <span>{{ $news->mq_company }}</span>
            </div>
            <h3 class="font-bold text-xl mb-2 hover:text-dark/80 transition-colors">
                <a href="{{ $news->mq_source_url }}" target="_blank">
                    {{ $news->mq_title }}
                </a>
            </h3>
            <p class="text-gray-600 line-clamp-2">
                {{ Str::limit(html_entity_decode($news->mq_content), 100) }}
            </p>
        </div>
        @endforeach
    </div>
</div>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

@push('scripts')
<script>
    // 메인 배너 슬라이더
    const mainBanner = new Swiper('.mainBanner', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.mainBanner .swiper-button-next',
            prevEl: '.mainBanner .swiper-button-prev',
        },
        pagination: {
            el: '.mainBanner .swiper-pagination',
            clickable: true,
        },
    });

    // 콘텐츠 슬라이더
    const contentSlider = new Swiper('.contentSlider', {
        slidesPerView: 1,
        spaceBetween: 20,
        navigation: {
            nextEl: '.contentSlider .swiper-button-next',
            prevEl: '.contentSlider .swiper-button-prev',
        },
        pagination: {
            el: '.contentSlider .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 4,
            },
        },
    });
    
    // 쉽게 보는 경제 슬라이더
    const videoSlider = new Swiper('.videoSlider', {
        slidesPerView: 1,
        spaceBetween: 20,
        navigation: {
            nextEl: '.videoSlider .swiper-button-next',
            prevEl: '.videoSlider .swiper-button-prev',
        },
        pagination: {
            el: '.videoSlider .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 4,
            },
        },
    });
    
    // 투자 리서치 슬라이더
    const researchSlider = new Swiper('.researchSlider', {
        slidesPerView: 1,
        spaceBetween: 20,
        navigation: {
            nextEl: '.researchSlider .swiper-button-next',
            prevEl: '.researchSlider .swiper-button-prev',
        },
        pagination: {
            el: '.researchSlider .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 4,
            },
        },
    });
    
    // 배너 CTA 버튼 클릭 이벤트
    document.addEventListener('DOMContentLoaded', function() {
        const bannerCtaBtn = document.getElementById('bannerCtaBtn');
        if (bannerCtaBtn) {
            bannerCtaBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // 로그인 상태에 따라 다른 동작 수행
                @if(Auth::check())
                    window.location.href = "{{ url('/board-content') }}";
                @else
                    window.location.href = "{{ route('login') }}";
                @endif
            });
        }
        
        // 챗봇 이벤트 연결
        const chatbotBtn = document.getElementById('chatbotBtn');
        if (chatbotBtn) {
            chatbotBtn.addEventListener('click', function() {
                openChatbotModal();
            });
        }
    });

    function calculateCumulativeValues(data, monthlyInvestment) {
        let results = [];
        let cumulativeAAPL = 0;
        let cumulativeQQQ = 0;
        let cumulativeBank = 0;

        for (let i = 0; i < data.length; i++) {
            const monthData = data[i];

            // 매달 동일 금액 신규 투자
            cumulativeAAPL = cumulativeAAPL * (1 + monthData.AAPL) + monthlyInvestment;
            cumulativeQQQ = cumulativeQQQ * (1 + monthData.QQQ) + monthlyInvestment;
            cumulativeBank = cumulativeBank * (1 + monthData.bank) + monthlyInvestment;

            results.push({
                date: monthData.date,
                애플: Math.round(cumulativeAAPL),
                나스닥: Math.round(cumulativeQQQ),
                예금: Math.round(cumulativeBank)
            });
        }

        return results;
    }
    
    // ECharts Line Race 차트
    window.addEventListener('load', function() {
        // 숫자 형식 함수 - 천단위 콤마 추가
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
        
        // 차트 초기화
        const chartContainer = document.getElementById('lineRaceChart');
        if (!chartContainer || typeof echarts === 'undefined') {
            if (chartContainer) {
                chartContainer.innerHTML = '<div class="p-4 text-red-500">차트 라이브러리 로딩 실패</div>';
            }
            return;
        }
        
        // 차트 인스턴스 생성
        const myChart = echarts.init(chartContainer, null, {
            renderer: 'canvas',
            useDirtyRect: false
        });
        
        // 리사이즈 핸들러 등록
        window.addEventListener('resize', function() {
            myChart.resize();
        });
        
        // 로딩 인디케이터 표시
        myChart.showLoading({
            text: '데이터 로딩 중...',
            color: '#4e79a7',
            textColor: '#34383d',
            maskColor: 'rgba(255, 255, 255, 0.8)'
        });
        
        // 전역 변수로 원시 데이터 저장
        let globalRawData = null;
        
        // 데이터 가져오기
        fetch('/storage/etc/2019_2024_market_month_change_rate.json')
            .then(response => {
                if (!response.ok) throw new Error('데이터를 불러오는 중 오류가 발생했습니다.');
                return response.json();
            })
            .then(rawData => {
                globalRawData = rawData; // 전역 변수에 데이터 저장
                loadChart(rawData);
            })
            .catch(error => {
                console.error('데이터 로딩 오류:', error);
                myChart.hideLoading();
                chartContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full">
                        <p class="text-red-500 font-semibold">데이터를 불러오는 중 오류가 발생했습니다.</p>
                        <p class="text-gray-600">잠시 후 다시 시도해주세요.</p>
                    </div>
                `;
            });
            
        // 확인하기 버튼 클릭 이벤트 핸들러
        document.getElementById('updateChartBtn').addEventListener('click', function() {
            if (globalRawData) {
                // 버튼 애니메이션 효과
                this.classList.add('scale-95');
                setTimeout(() => {
                    this.classList.remove('scale-95');
                }, 150);
                
                // 차트 다시 로드 (애니메이션 효과와 함께)
                myChart.clear();
                myChart.showLoading({
                    text: '차트 업데이트 중...',
                    color: '#4e79a7',
                    textColor: '#34383d',
                    maskColor: 'rgba(255, 255, 255, 0.8)'
                });
                
                setTimeout(() => {
                    loadChart(globalRawData);
                    myChart.hideLoading();
                }, 500);
            }
        });
        
        // 입력 필드에서 Enter 키 이벤트 처리
        document.getElementById('monthlyInvestment').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && globalRawData) {
                // Enter 키를 누르면 차트 다시 로드 (애니메이션 효과와 함께)
                document.getElementById('updateChartBtn').click();
            }
        });
            
        // 차트 로드 함수    
        function loadChart(rawData) {
            try {
                // 자산 목록 정의
                const assets = ['애플', '나스닥', '예금'];
                
                // 데이터셋 필터 정의
                const datasetsWithFilters = [];
                const seriesList = [];
                
                // 원시 데이터를 날짜 기준으로 정렬
                rawData.sort((a, b) => new Date(a.date) - new Date(b.date));

                // 월별 투자 금액 가져오기 (기본값 1만원)
                let inputValue = parseInt(document.getElementById('monthlyInvestment').value) || 1;
                
                // 유효성 검사
                if (inputValue < 1) {
                    inputValue = 1;
                    document.getElementById('monthlyInvestment').value = 1;
                } else if (inputValue > 1000) {
                    inputValue = 1000;
                    document.getElementById('monthlyInvestment').value = 1000;
                }
                
                // 입력값을 만원 단위로 변환 (1 → 10000)
                const monthlyInvestment = inputValue * 10000;
                
                const processedData = calculateCumulativeValues(rawData, monthlyInvestment);
                
                // 시리즈 구성
                assets.forEach(asset => {
                    seriesList.push({
                        type: 'line',
                        name: asset,
                        showSymbol: false,
                        smooth: true,
                        lineStyle: { width: 2 },
                        emphasis: {
                            focus: 'series',
                            lineStyle: { width: 4 }
                        },
                        encode: {
                            x: 'date',
                            y: asset
                        },
                        endLabel: {
                            show: true,
                            formatter: function(params) {
                                return params.seriesName;
                            },
                            fontSize: 12,
                            padding: [3, 8],
                            color: '#333'
                        },
                        labelLayout: {
                            moveOverlap: 'shiftY',
                        }
                    });
                });
                
                // 차트 옵션 설정
                const option = {
                    animationDuration: 1000,
                    animationEasing: 'cubicInOut',
                    dataset: {
                        source: processedData
                    },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: { type: 'cross' },
                        formatter: function(params) {
                            const date = params[0].axisValue;
                            let result = `<div style="font-weight:bold;margin-bottom:5px;">${date}</div>`;
                            params.forEach(param => {
                                const value = param.value[param.seriesName].toLocaleString();
                                const color = param.color;
                                const name = param.seriesName;
                                result += `<div style="display:flex;align-items:center;margin:3px 0;">
                                    <span style="display:inline-block;width:10px;height:10px;background:${color};margin-right:5px;border-radius:50%;"></span>
                                    <span style="margin-right:5px;display:inline-block;min-width:60px;">${name}</span>
                                    <span style="font-weight:bold;">${value}</span>
                                </div>`;
                            });
                            return result;
                        }
                    },
                    grid: {
                        top: '10%',
                        left: '5%',
                        right: '15%',
                        bottom: '5%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        nameLocation: 'middle',
                        axisLine: { lineStyle: { color: '#aaa' } },
                        axisLabel: {
                            formatter: function (value) {
                                return value.substring(2, 7).replace('-', '.');
                            },
                            rotate: 45,
                            fontSize: 10
                        }
                    },
                    yAxis: {
                        type: 'value',
                        name: '잔고',
                        nameTextStyle: { color: '#666' },
                        axisLine: { show: true, lineStyle: { color: '#aaa' } },
                        splitLine: { lineStyle: { color: '#eee' } },
                        axisLabel: {
                            color: '#666',
                            margin: 8,
                            formatter: function(value) {
                                if (value >= 1000000000) return Math.floor(value / 100000000) + '억만원';
                                if (value >= 10000000) return Math.floor(value / 10000000) + '천만원';
                                if (value >= 1000000) return Math.floor(value / 10000) + '만원';
                                if (value >= 10000) return Math.floor(value / 10000) + '만원';
                                if (value >= 1000) return Math.floor(value / 10000) + '만원';
                                return value;
                            }
                        }
                    },
                    color: [
                        '#4e79a7',
                        '#f28e2c',
                        '#bab0ab'
                    ],
                    series: seriesList
                };
                
                // 차트 옵션 적용
                myChart.setOption(option);
                
                // 로딩 숨기기
                myChart.hideLoading();
                
            } catch (error) {
                console.error('차트 렌더링 오류:', error);
                myChart.hideLoading();
                chartContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full">
                        <p class="text-red-500 font-semibold">차트 렌더링 중 오류가 발생했습니다.</p>
                        <p class="text-gray-600">${error.message}</p>
                    </div>
                `;
            }
        }
    });
</script>


@endpush
@endsection 