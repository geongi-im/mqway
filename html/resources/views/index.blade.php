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
    background-color: #ffd100; /* 포인트 색상 */
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
<div class="swiper mainBanner mb-12">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <div class="banner-image">
                <img src="{{ asset('images/banner/main_banner_10.png') }}" alt="배너1">
                <div class="banner-content">
                    <div class="text-container">
                        <h2>경제는 지식이 아닌 습관입니다.<br>지금 바로 시작하세요.</h2>
                        <a href="javascript:void(0)" class="cta-button" id="startQuizBtn">경제 상식 테스트</a>
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
                        <a href="{{ url('/news') }}" class="cta-button">주요 뉴스 보기</a>
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
                        <a href="javascript:void(0)" class="cta-button" id="retirementCalcBtn">노후 자금 계산기</a>
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

<!-- 주요 경제지표 차트 -->
<div class="container mx-auto px-4 mb-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">매달 만원씩 투자한다면<br><span class="text-base font-medium text-gray-600">(최근 5년간 애플/나스닥/예금 투자 비교)</span></h2>
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
                        <img src="{{ asset($post->mq_image) }}" 
                             alt="게시글 이미지" 
                             class="w-full h-full object-contain p-2">
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
                        <img src="{{ asset($post->mq_image) }}" 
                             alt="리서치 이미지" 
                             class="w-full h-full object-contain p-2">
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
        <a href="{{ url('/news') }}" class="text-dark hover:text-dark/80 transition-colors flex items-center">
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
                {{ Str::limit($news->mq_content, 100) }}
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
    
    // ECharts Line Race 차트
    window.addEventListener('load', function() {
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
        
        // 데이터 가져오기
        fetch('/storage/etc/2019_2024_market_data.json')
            .then(response => {
                if (!response.ok) throw new Error('데이터를 불러오는 중 오류가 발생했습니다.');
                return response.json();
            })
            .then(rawData => {
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
                
                // 데이터 처리 및 구성
                const processedData = rawData.map(item => {
                    return {
                        date: item.date,
                        '애플': parseFloat(item.AAPL.replace(/,/g, '')) || 0,
                        '나스닥': parseFloat(item.QQQ.replace(/,/g, '')) || 0,
                        '예금': parseFloat(item.bank.replace(/,/g, '')) || 0
                    };
                });
                
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
                    animationDuration: 3000,
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

<!-- 퀴즈 모달 -->
<div id="quizModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-white">
        <!-- 닫기 버튼 -->
        <button id="closeQuizBtn" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- 콘텐츠 컨테이너 -->
        <div class="w-full h-full overflow-y-auto">
            <div class="max-w-3xl mx-auto px-4 py-8">
                <!-- 진행 상태 바 -->
                <div class="mb-8">
                    <div class="relative pt-1">
                        <div class="flex items-center justify-between">
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-blue-600">
                                    <span id="currentQuestionNumber">0</span>/10
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-blue-100">
                            <div id="progressBar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <div id="quizContent" class="space-y-8">
                    <!-- 퀴즈 내용이 여기에 동적으로 로드됩니다 -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startQuizBtn = document.getElementById('startQuizBtn');
    const closeQuizBtn = document.getElementById('closeQuizBtn');
    const quizModal = document.getElementById('quizModal');
    const quizContent = document.getElementById('quizContent');
    const progressBar = document.getElementById('progressBar');
    const currentQuestionNumber = document.getElementById('currentQuestionNumber');
    
    let currentQuestion = 0;
    let score = 0;
    let quizData = [];
    let userAnswers = [];
    
    // 팝업 열릴 때 외부 스크롤 비활성화
    function disableBodyScroll() {
        document.body.style.overflow = 'hidden';
    }
    
    // 팝업 닫힐 때 외부 스크롤 활성화
    function enableBodyScroll() {
        document.body.style.overflow = '';
    }
    
    // 퀴즈 시작 버튼 클릭 이벤트
    startQuizBtn.addEventListener('click', function() {
        fetch('/api/quiz')
            .then(response => response.json())
            .then(data => {
                quizData = data;
                currentQuestion = 0;
                score = 0;
                showQuestion();
                quizModal.classList.remove('hidden');
                quizModal.classList.add('flex');
                updateProgress();
                disableBodyScroll(); // 외부 스크롤 비활성화
            })
            .catch(error => {
                console.error('Error loading quiz data:', error);
                alert('퀴즈 데이터를 불러오는 중 오류가 발생했습니다.');
            });
    });
    
    // 모달 닫기 버튼 클릭 이벤트
    closeQuizBtn.addEventListener('click', function() {
        if(confirm('퀴즈를 종료하시겠습니까?')) {
            quizModal.classList.add('hidden');
            quizModal.classList.remove('flex');
            enableBodyScroll(); // 외부 스크롤 활성화
        }
    });
    
    // 진행 상태 업데이트 함수
    function updateProgress() {
        const progress = (currentQuestion / quizData.length) * 100;
        progressBar.style.width = `${progress}%`;
        currentQuestionNumber.textContent = currentQuestion;
    }
    
    // 퀴즈 문제 표시 함수
    function showQuestion() {
        if (currentQuestion < quizData.length) {
            const question = quizData[currentQuestion];
            let html = `
                <div class="max-w-2xl mx-auto">
                    <div class="text-center mb-12">
                        <div class="text-blue-500 text-3xl font-bold mb-6">Q${currentQuestion + 1}.</div>
                        <h3 class="text-2xl font-semibold text-gray-800">${question.question}</h3>
                    </div>
                    <div class="space-y-4 max-w-xl mx-auto">
                        ${question.options.map((option, index) => `
                            <button onclick="checkAnswer(${index})" 
                                    class="w-full p-4 text-center text-gray-700 bg-white border-2 border-gray-200 rounded-full hover:border-blue-500 hover:bg-blue-50 transition-all duration-200">
                                ${option}
                            </button>
                        `).join('')}
                    </div>
                </div>
            `;
            quizContent.innerHTML = html;
        } else {
            showResults();
        }
        updateProgress();
    }
    
    // 정답 확인 함수
    window.checkAnswer = function(selectedIndex) {
        const correctAnswer = quizData[currentQuestion].correctAnswer;
        
        // 사용자의 답안 저장
        userAnswers[currentQuestion] = selectedIndex;
        
        if (selectedIndex === correctAnswer) {
            score++;
        }
        
        currentQuestion++;
        showQuestion();
    };
    
    // 결과 메시지 함수 추가
    function getResultMessage(score, total) {
        if (score === total) {
            return {
                emoji: '✅',
                title: '완벽해요! 🎉\n당신은 진정한 경제 상식 마스터!',
                message: '경제 흐름이 눈에 보이기 시작했어요.\n지금 바로 다음 퀴즈에도 도전해보세요!'
            };
        } else if (score >= 8) {
            return {
                emoji: '🥳',
                title: '아주 훌륭해요! 💪\n거의 다 왔어요!',
                message: '경제를 보는 눈이 남다르네요.\n아쉬운 한두 문제만 복습하면 금방 만점입니다!'
            };
        } else if (score >= 6) {
            return {
                emoji: '👍',
                title: '좋은 출발이에요! 🚀\n기본은 충분히 갖췄어요.',
                message: '이제 조금만 더 공부하면 만점도 가능해요.\n틀린 문제는 다시 한 번 체크해보는 건 어떨까요?'
            };
        } else {
            return {
                emoji: '🙈',
                title: '아직은 조금 아쉬워요... 😅\n하지만 시작이 반이에요!',
                message: '경제 상식은 누구나 처음엔 어렵지만,\n계속 풀다 보면 분명 실력이 쑥쑥 올라갈 거예요!'
            };
        }
    }

    // 결과 표시 함수 수정
    function showResults() {
        const resultMessage = getResultMessage(score, quizData.length);
        let html = `
            <div class="max-w-2xl mx-auto text-center">
                <div class="mb-12">
                    <div class="text-6xl mb-6">${resultMessage.emoji}</div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">테스트 완료!</h3>
                    <p class="text-2xl text-blue-600 font-semibold mb-4">당신의 점수: ${score} / ${quizData.length}</p>
                    <div class="space-y-2">
                        <p class="text-xl font-semibold text-gray-800 whitespace-pre-line">${resultMessage.title}</p>
                        <p class="text-gray-600 whitespace-pre-line">${resultMessage.message}</p>
                    </div>
                </div>
                
                <div class="flex justify-center gap-6">
                    <button onclick="showAnswers()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-full transition-colors text-lg">
                        정답 확인하기
                    </button>
                    <button onclick="closeQuiz()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-8 rounded-full transition-colors text-lg">
                        종료하기
                    </button>
                </div>
            </div>
        `;
        quizContent.innerHTML = html;
        progressBar.style.width = '100%';
        currentQuestionNumber.textContent = quizData.length;
    }

    // 정답 확인 화면 함수 수정
    window.showAnswers = function() {
        let html = `
            <div class="max-w-2xl mx-auto">
                <div class="space-y-6">
                    ${quizData.map((question, index) => {
                        const isCorrect = userAnswers[index] === question.correctAnswer;
                        const userAnswer = userAnswers[index];
                        
                        return `
                            <div class="p-6 rounded-lg ${isCorrect ? 'bg-green-50' : 'bg-red-50'}">
                                <div class="flex items-start gap-4">
                                    <span class="text-blue-500 text-xl font-bold">Q${index + 1}.</span>
                                    <div class="flex-1">
                                        <p class="text-lg font-semibold text-gray-800">${question.question}</p>
                                        
                                        <div class="mt-3 space-y-2">
                                            ${question.options.map((option, optionIndex) => `
                                                <div class="flex items-center">
                                                    <span class="w-6 h-6 flex items-center justify-center rounded-full mr-2 text-sm
                                                        ${optionIndex === question.correctAnswer ? 'bg-blue-500 text-white' : 
                                                          optionIndex === userAnswer ? 'bg-red-500 text-white' : 'bg-gray-200'}"
                                                    >
                                                        ${optionIndex === question.correctAnswer ? '✓' : 
                                                          optionIndex === userAnswer ? '×' : ''}
                                                    </span>
                                                    <span class="${optionIndex === question.correctAnswer ? 'font-semibold text-blue-700' : 
                                                                 optionIndex === userAnswer && !isCorrect ? 'text-red-700' : 'text-gray-700'}"
                                                    >
                                                        ${option}
                                                    </span>
                                                </div>
                                            `).join('')}
                                        </div>
                                        
                                        <div class="mt-4">
                                            <span class="inline-block px-2 py-1 rounded text-sm ${isCorrect ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'}">
                                                ${isCorrect ? '정답입니다!' : '틀렸습니다'}
                                            </span>
                                            <p class="mt-2 text-gray-600">${question.explanation}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
                <div class="mt-8 text-center">
                    <button onclick="closeQuiz()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-full transition-colors text-lg">
                        완료
                    </button>
                </div>
            </div>
        `;
        quizContent.innerHTML = html;
    }
    
    // 퀴즈 종료 함수 (전역에서 접근 가능)
    window.closeQuiz = function() {
        quizModal.classList.add('hidden');
        quizModal.classList.remove('flex');
        enableBodyScroll(); // 외부 스크롤 활성화
    }
});
</script>

<!-- 노후 자금 계산기 모달 -->
<div id="retirementCalcModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-white">
        <!-- 닫기 버튼 -->
        <button id="closeRetirementCalcBtn" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- 콘텐츠 컨테이너 -->
        <div class="w-full h-full overflow-y-auto">
            <div class="max-w-3xl mx-auto px-4 py-8">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">노후 자금 계산기</h2>
                    <p class="text-gray-600">은퇴 후 필요한 자금을 계산해보세요</p>
                </div>

                <!-- 입력 폼과 결과 영역 컨테이너 -->
                <div class="calc-container">
                    <!-- 입력 폼 영역 -->
                    <div id="inputFormSection">
                        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
                            <form id="retirementCalcForm" class="space-y-6">
                                <div class="mb-2 px-2 py-2 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-blue-700">※ 예상 물가상승률은 <span class="font-bold">2%</span>로 고정 적용됩니다.</p>
                                </div>
                                
                                <!-- 현재 정보 섹션 -->
                                <div class="border-b border-gray-200 pb-4 mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">현재 정보</h4>
                                    <div>
                                    <label class="block text-gray-700 font-medium mb-2">현재 나이</label>
                                    <input type="number" id="currentAge" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="20" max="80" value="30" required>
                                </div>
                                </div>
                                
                                <!-- 저축 정보 섹션 -->
                                <div class="border-b border-gray-200 pb-4 mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">저축 정보</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">현재까지 누적 저축액 (만원)</label>
                                            <input type="number" id="currentSavings" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" value="5000" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">월 저축 금액 (만원)</label>
                                            <input type="number" id="monthlySaving" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" value="50" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">예상 연간 수익률 (%)</label>
                                            <input type="number" id="returnRate" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" max="15" step="0.5" value="4" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 은퇴 정보 섹션 -->
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">은퇴 정보</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">예상 은퇴 나이</label>
                                            <input type="number" id="retirementAge" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="50" max="90" value="65" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">예상 기대 수명</label>
                                            <input type="number" id="lifeExpectancy" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="70" max="110" value="85" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">은퇴 후 월 생활비 (현재 가치, 만원)</label>
                                            <input type="number" id="monthlyExpense" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="50" value="280" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-6">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-full transition-colors text-lg">
                                        계산하기
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 계산 결과 영역 -->
                    <div id="resultSection" class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md hidden">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">노후 자금 분석 결과</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="border border-gray-200 rounded-lg p-4 bg-blue-50">
                                <div class="text-gray-700 mb-1">은퇴까지 남은 기간</div>
                                <div class="text-2xl font-bold text-blue-700" id="yearsToRetirement"></div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4 bg-indigo-50">
                                <div class="text-gray-700 mb-1">예상 은퇴 후 생활 기간</div>
                                <div class="text-2xl font-bold text-indigo-700" id="retirementDuration"></div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4 bg-green-50">
                                <div class="text-gray-700 mb-1">필요한 총 노후자금</div>
                                <div class="text-2xl font-bold text-green-700" id="totalNeeded"></div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4 bg-amber-50">
                                <div class="text-gray-700 mb-1">은퇴 후 월 생활비 (미래 가치)</div>
                                <div class="text-2xl font-bold text-amber-700" id="monthlyNeeded"></div>
                                <div class="text-xs text-gray-500 mt-1">* 물가상승률 2% 적용</div>
                            </div>
                        </div>
                        
                        <div class="mb-8">
                            <h4 class="font-semibold text-gray-800 mb-3">노후 자금 변화 추이</h4>
                            <div id="retirementChart" class="w-full bg-white p-4 rounded-lg shadow-md" style="width: 100%; height: 300px !important; display: block; overflow: hidden;"></div>
                        </div>
                        
                        <div class="mb-8">
                            <h4 class="font-semibold text-gray-800 mb-3">목표 달성을 위한 저축 계획</h4>
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <div class="text-gray-700 mb-1">월 필요 저축액</div>
                                        <div class="text-xl font-bold text-blue-600" id="monthlySavingsNeeded"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-700 mb-1">연간 필요 저축액</div>
                                        <div class="text-xl font-bold text-blue-600" id="annualSavingsNeeded"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-700 mb-1">현재 달성률</div>
                                        <div class="text-xl font-bold text-blue-600" id="currentProgressRate"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="adviceSection" class="p-4 rounded-lg bg-gray-50 mb-6">
                            <h4 class="font-semibold text-gray-800 mb-2">재무 조언</h4>
                            <p class="text-gray-600" id="financialAdvice"></p>
                        </div>
                        
                        <div class="text-center mt-6">
                            <button id="recalculateBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-full transition-colors">
                                다시 계산하기
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const retirementCalcBtn = document.getElementById('retirementCalcBtn');
    const closeRetirementCalcBtn = document.getElementById('closeRetirementCalcBtn');
    const retirementCalcModal = document.getElementById('retirementCalcModal');
    const retirementCalcForm = document.getElementById('retirementCalcForm');
    const resultSection = document.getElementById('resultSection');
    const recalculateBtn = document.getElementById('recalculateBtn');
    
    // 숫자 형식 함수 - 천단위 콤마 추가
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    // 팝업 열릴 때 외부 스크롤 비활성화
    function disableBodyScroll() {
        document.body.style.overflow = 'hidden';
    }
    
    // 팝업 닫힐 때 외부 스크롤 활성화
    function enableBodyScroll() {
        document.body.style.overflow = '';
    }
    
    // 노후자금 계산기 버튼 클릭 이벤트
    retirementCalcBtn.addEventListener('click', function() {
        retirementCalcModal.classList.remove('hidden');
        retirementCalcModal.classList.add('flex');
        // 결과 섹션 숨기기 (초기 상태)
        resultSection.classList.add('hidden');
        document.getElementById('inputFormSection').style.display = 'block';
        disableBodyScroll(); // 외부 스크롤 비활성화
    });
    
    // 닫기 버튼 클릭 이벤트
    closeRetirementCalcBtn.addEventListener('click', function() {
        retirementCalcModal.classList.add('hidden');
        retirementCalcModal.classList.remove('flex');
        enableBodyScroll(); // 외부 스크롤 활성화
    });
    
    // 다시 계산하기 버튼 클릭 이벤트
    recalculateBtn.addEventListener('click', function() {
        resultSection.classList.add('hidden');
        document.getElementById('inputFormSection').style.display = 'block';
    });
    
    // 폼 제출 이벤트
    retirementCalcForm.addEventListener('submit', function(e) {
        e.preventDefault();
        calculateRetirement();
        
        // 결과 섹션 표시
        resultSection.classList.remove('hidden');
        document.getElementById('inputFormSection').style.display = 'none';
        
        // 결과 섹션이 DOM에 보여진 후 약간의 지연을 두고 차트 렌더링 (브라우저 렌더링 시간 확보)
        setTimeout(() => {
            if (document.getElementById('retirementChart')) {
                window.dispatchEvent(new Event('resize')); // 차트 영역 크기 재계산 트리거
            }
        }, 100);
    });
    
    // 노후자금 계산 함수
    function calculateRetirement() {
        // 입력값 가져오기
        const currentAge = parseInt(document.getElementById('currentAge').value);
        const retirementAge = parseInt(document.getElementById('retirementAge').value);
        const lifeExpectancy = parseInt(document.getElementById('lifeExpectancy').value);
        const monthlyExpense = parseInt(document.getElementById('monthlyExpense').value);
        const returnRate = parseFloat(document.getElementById('returnRate').value) / 100;
        const currentSavings = parseInt(document.getElementById('currentSavings').value);
        const monthlySaving = parseInt(document.getElementById('monthlySaving').value);
        
        // 물가상승률 고정값 설정
        const inflationRate = 0.02;
        
        // 계산
        const yearsToRetirement = retirementAge - currentAge;
        const retirementDuration = lifeExpectancy - retirementAge;
        
        // 은퇴 시 월 필요 금액 (현재 가치 기준)
        const monthlyNeededNow = monthlyExpense;
        
        // 물가상승률 적용한 은퇴 시점의 월 필요 금액
        const monthlyNeededAtRetirement = monthlyNeededNow * Math.pow(1 + inflationRate, yearsToRetirement);
        
        // 은퇴 후 필요한 총 자금 (실질 수익률 고려)
        const realReturnRate = (1 + returnRate) / (1 + inflationRate) - 1;
        let totalNeeded;
        
        if (Math.abs(realReturnRate) < 0.0001) {
            // 실질 수익률이 0에 가까운 경우
            totalNeeded = monthlyNeededAtRetirement * 12 * retirementDuration;
        } else {
            // 연금 현재가치 공식 사용
            totalNeeded = monthlyNeededAtRetirement * 12 * ((1 - Math.pow(1 + realReturnRate, -retirementDuration)) / realReturnRate);
        }
        
        // 현재부터 은퇴까지 매달 저축해야 할 금액
        const futureValueFactor = (Math.pow(1 + returnRate, yearsToRetirement) - 1) / returnRate;
        const monthlySavingsNeeded = (totalNeeded - currentSavings * Math.pow(1 + returnRate, yearsToRetirement)) / (futureValueFactor * 12);
        
        // 현재 달성률
        const targetFutureValue = currentSavings * Math.pow(1 + returnRate, yearsToRetirement);
        const currentProgressRate = (targetFutureValue / totalNeeded) * 100;
        
        // 결과 표시
        document.getElementById('yearsToRetirement').textContent = `${yearsToRetirement}년`;
        document.getElementById('retirementDuration').textContent = `${retirementDuration}년`;
        // 기존 값은 만원 단위이므로 10000을 곱해 원 단위로 변환
        document.getElementById('totalNeeded').textContent = `${formatKoreanCurrency(Math.round(totalNeeded * 10000), false)}원`;
        document.getElementById('monthlyNeeded').textContent = `${formatKoreanCurrency(Math.round(monthlyNeededAtRetirement * 10000), false)}원`;
        document.getElementById('monthlySavingsNeeded').textContent = `${formatKoreanCurrency(Math.round(monthlySavingsNeeded * 10000), false)}원`;
        document.getElementById('annualSavingsNeeded').textContent = `${formatKoreanCurrency(Math.round(monthlySavingsNeeded * 12 * 10000), false)}원`;
        document.getElementById('currentProgressRate').textContent = `${currentProgressRate.toFixed(1)}%`;
        
        // 재무 조언 제공
        provideFinancialAdvice(currentAge, yearsToRetirement, monthlySavingsNeeded, monthlySaving, currentProgressRate);
        
        // 노후 자금 변화 추이 차트 생성
        createRetirementChart(currentAge, retirementAge, lifeExpectancy, currentSavings, monthlySaving, totalNeeded, returnRate, inflationRate, monthlyExpense);
    }
    
    // 노후 자금 변화 추이 차트 생성 함수
    function createRetirementChart(currentAge, retirementAge, lifeExpectancy, currentSavings, monthlySaving, totalNeeded, returnRate, inflationRate, monthlyExpense) {
        // 차트 컨테이너 가져오기
        const chartContainer = document.getElementById('retirementChart');
        
        if (!chartContainer) {
            return;
        }
        
        // 이미 차트가 있으면 인스턴스 삭제
        if (window.retirementChartInstance) {
            window.retirementChartInstance.dispose();
        }
        
        // 새 차트 인스턴스 생성 - 모바일 대응
        if (typeof echarts === 'undefined') {
            return;
        }
        
        const chart = echarts.init(chartContainer, null, {
            renderer: 'canvas',
            useDirtyRect: false
        });
        window.retirementChartInstance = chart;
        
        // 차트 데이터 생성
        const totalYears = lifeExpectancy - currentAge;
        const xAxisData = [];
        const savingsPhaseData = [];
        const withdrawalPhaseData = [];
        
        let currentSavingsValue = currentSavings;
        
        // 현재 나이부터 수명까지의 연도 데이터 생성
        for (let i = 0; i <= totalYears; i++) {
            const age = currentAge + i;
            xAxisData.push(age);
            
            // 은퇴 전: 적립 단계
            if (age < retirementAge) {
                // 매월 저축액을 더하고 수익률 적용
                currentSavingsValue = currentSavingsValue * (1 + returnRate) + monthlySaving * 12;
                savingsPhaseData.push(Math.round(currentSavingsValue));
                withdrawalPhaseData.push(null); // 은퇴 전에는 인출 없음
            } 
            // 은퇴 후: 인출 단계
            else {
                if (age === retirementAge) {
                    // 은퇴 시점의 저축액을 마지막으로 저장
                    savingsPhaseData.push(Math.round(currentSavingsValue));
                } else {
                    savingsPhaseData.push(null); // 은퇴 후에는 적립 없음
                }
                
                // 은퇴 후 예상 월 필요 금액 (물가상승률 고려)
                const inflationFactor = Math.pow(1 + inflationRate, i - (retirementAge - currentAge));
                const yearlyWithdrawal = monthlyExpense * inflationFactor * 12;
                
                // 인출 단계 계산 명확화:
                // 1. 먼저 현재 자산에 수익률 적용 (투자수익 발생)
                currentSavingsValue = currentSavingsValue * (1 + returnRate);
                
                // 2. 연간 생활비 인출 (저축 없음)
                currentSavingsValue -= yearlyWithdrawal;
                
                // 3. 잔액이 음수가 되지 않도록 조정
                currentSavingsValue = Math.max(0, currentSavingsValue);
                withdrawalPhaseData.push(Math.round(currentSavingsValue));
            }
        }
        
        // 차트 옵션 설정
        const option = {
            tooltip: {
                trigger: 'axis',
                formatter: function(params) {
                    const age = params[0].axisValue;
                    let content = `<div style="font-weight:bold;margin-bottom:5px;">${age}세</div>`;
                    
                    params.forEach(param => {
                        if (param.value !== null && param.value !== undefined && !isNaN(param.value)) {
                            // 유효한 값이 있는 경우만 표시
                            const value = numberWithCommas(Math.round(param.value));
                            let status = '';
                            
                            if (param.seriesName === '적립 단계') {
                                status = age < retirementAge ? '적립 중' : '은퇴 시점';
                            } else if (param.seriesName === '인출 단계') {
                                status = '생활비 인출 중';
                            } else {
                                status = '';
                            }
                            
                            content += `<div style="display:flex;align-items:center;margin:3px 0;">
                                <span style="display:inline-block;width:10px;height:10px;background:${param.color};margin-right:5px;border-radius:50%;"></span>
                                <span style="margin-right:5px;min-width:60px;">${param.seriesName}</span>
                                <span style="font-weight:bold;">${value}만원</span>
                                <span style="margin-left:8px;color:#666;">(${status})</span>
                            </div>`;
                        }
                    });
                    
                    return content;
                }
            },
            legend: {
                data: ['적립 단계', '인출 단계'],
                bottom: 5,
                padding: [5, 10],
                itemGap: 20,
                itemWidth: 14,
                itemHeight: 8,
                textStyle: {
                    fontSize: 12,
                    padding: [0, 4]
                },
                selected: {  // 은퇴 시점은 범례에서 숨김
                    '은퇴 시점': false
                }
            },
            grid: {
                left: '5%',
                right: '5%',
                bottom: '12%',
                top: '8%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: xAxisData,
                axisLine: { lineStyle: { color: '#aaa' } },
                axisLabel: {
                    formatter: function(value) {
                        // 10년 단위로만 표시
                        return (value % 5 === 0) ? value + '세' : '';
                    },
                    fontSize: 10,
                    interval: 'auto',
                    rotate: 0
                }
            },
            yAxis: {
                type: 'value',
                name: '자산 (만원)',
                nameTextStyle: { 
                    color: '#666',
                    fontSize: 12,
                    padding: [0, 0, 10, 0]  // 이름 주변 패딩 추가
                },
                nameGap: 25,  // 이름과 축 사이 간격 증가
                axisLine: { show: true, lineStyle: { color: '#aaa' } },
                splitLine: { lineStyle: { color: '#eee' } },
                axisLabel: {
                    color: '#666',
                    margin: 14,  // 레이블과 축 사이 여백 증가
                    formatter: function(value) {
                        // 차트 데이터는 이미 만원 단위로 저장되어 있음 (10000 곱하지 않음)
                        return formatKoreanCurrency(value * 10000, false);
                    },
                    fontSize: 10,
                    padding: [3, 0, 3, 0]  // 레이블 주변 패딩 추가
                }
            },
            series: [
                {
                    name: '적립 단계',
                    type: 'line',
                    data: savingsPhaseData,
                    smooth: true,
                    showSymbol: false,
                    lineStyle: {
                        width: 3
                    },
                    itemStyle: {
                        color: '#4e79a7'
                    },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(78, 121, 167, 0.5)' },
                            { offset: 1, color: 'rgba(78, 121, 167, 0.2)' }
                        ])
                    }
                },
                {
                    name: '인출 단계',
                    type: 'line',
                    data: withdrawalPhaseData,
                    smooth: true,
                    showSymbol: false,
                    lineStyle: {
                        width: 3,
                        type: 'solid'
                    },
                    itemStyle: {
                        color: '#e15759'  // 인출 단계는 더 눈에 띄는 빨간색 계열로 변경
                    },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(225, 87, 89, 0.5)' },
                            { offset: 1, color: 'rgba(225, 87, 89, 0.2)' }
                        ])
                    }
                }
            ],
            // 수직선 마커 추가
            visualMap: {
                show: false,
                type: 'piecewise',
                pieces: [
                    {
                        gt: 0,
                        lte: retirementAge,
                        label: '은퇴 전'
                    },
                    {
                        gt: retirementAge,
                        label: '은퇴 후'
                    }
                ]
            }
        };
        
        // 은퇴 시점 마커 추가 (별도 시리즈로 추가)
        option.series.push({
            name: '은퇴 시점',
            type: 'line',
            markLine: {
                silent: true,
                lineStyle: {
                    color: '#ff0000',
                    type: 'dashed',
                    width: 2
                },
                label: {
                    formatter: '은퇴 시점',
                    position: 'middle',
                    color: '#ff0000',
                    fontSize: 14,
                    fontWeight: 'bold'
                },
                data: [
                    {
                        name: '은퇴 시점',
                        xAxis: retirementAge
                    }
                ]
            },
            data: [],  // 빈 데이터로 실제 차트에 표시되지 않음
            tooltip: {
                show: false  // 이 시리즈에 대한 툴팁 비활성화
            }
        });
        
        // 차트에 옵션 적용
        chart.setOption(option);
        
        // 차트 크기 조정 - 반응형 대응 개선
        function resizeChart() {
            if (chart && !chart.isDisposed()) {
                chart.resize();
            }
        }
        
        // 즉시 리사이즈 함수 호출하여 초기 화면에 맞게 조정
        setTimeout(resizeChart, 200);
        
        // 창 크기 변경 시 차트 리사이즈
        window.addEventListener('resize', resizeChart);
        
        // 모바일 기기 회전 이벤트에 대응
        window.addEventListener('orientationchange', function() {
            setTimeout(resizeChart, 200);
        });
    }
    
    // 재무 조언 함수
    function provideFinancialAdvice(age, yearsToRetirement, monthlySavingsNeeded, monthlySaving, currentProgressRate) {
        let advice = '';
        
        // 월 저축금액 대비 필요 저축액 비율 계산
        const savingsRatio = (monthlySavingsNeeded / monthlySaving) * 100;
        
        if (currentProgressRate >= 80) {
            advice = '축하합니다! 은퇴 준비가 잘 진행되고 있습니다. 투자 포트폴리오를 정기적으로 검토하고 필요에 따라 조정하면서 현재 상태를 유지하세요.';
        } else if (currentProgressRate >= 50) {
            advice = '은퇴 준비가 절반 이상 진행되었습니다. 추가적인 저축으로 준비율을 더 높이고, 투자 전략을 최적화하면 목표에 더 빠르게 도달할 수 있습니다.';
        } else if (currentProgressRate >= 20) {
            advice = '은퇴 준비가 시작되었지만, 더 많은 관심이 필요합니다. 불필요한 지출을 줄이고 저축을 늘려 은퇴 준비를 가속화하는 것이 좋습니다.';
        } else {
            advice = '은퇴 준비가 아직 초기 단계입니다. 정기적인 저축 습관을 형성하고, 장기적인 재무 계획을 세우는 것이 중요합니다.';
        }
        
        if (savingsRatio > 200) {
            advice += ' 필요한 저축액이 현재 저축액보다 훨씬 많습니다. 저축 금액을 늘리거나, 은퇴 후 생활비 기대치를 현실적으로 조정하는 것을 고려해보세요.';
        } else if (savingsRatio > 120) {
            advice += ' 필요한 저축액이 현재 저축액보다 다소 높습니다. 가능하다면 저축 금액을 점진적으로 늘려보세요.';
        } else {
            advice += ' 현재 저축액이 필요 저축액을 충족하거나 그 이상입니다. 꾸준히 유지하면서 투자 수익률을 높이는 방안도 모색해보세요.';
        }
        
        if (age < 30) {
            advice += ' 젊은 나이에 은퇴 준비를 시작한 것은 매우 현명한 결정입니다. 시간이 충분하므로 장기적인 투자에 집중하세요.';
        } else if (age < 40) {
            advice += ' 30대에 은퇴 준비를 하는 것은 시간의 이점을 활용할 수 있는 좋은 시기입니다. 균형 잡힌 포트폴리오로 안정적인 성장을 추구하세요.';
        } else if (age < 50) {
            advice += ' 40대는 은퇴 준비에 가속도를 붙여야 할 시기입니다. 가능하다면 저축률을 높이고, 재무 목표를 정기적으로 검토하세요.';
        } else {
            advice += ' 50대 이상이라면 은퇴를 앞두고 있으므로, 보다 보수적인 투자 전략과 함께 은퇴 계획을 구체화할 필요가 있습니다.';
        }
        
        document.getElementById('financialAdvice').textContent = advice;
    }
    
});
</script>
@endpush
@endsection 