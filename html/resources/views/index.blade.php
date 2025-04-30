@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Facades\Auth;
@endphp

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<!-- Marked.js - 마크다운 파서 -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
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
.contentSlider, .researchSlider {
    position: relative;
    padding-bottom: 50px; /* 페이지네이션을 위한 하단 여백 */
}

.contentSlider .swiper-button-next,
.contentSlider .swiper-button-prev,
.researchSlider .swiper-button-next,
.researchSlider .swiper-button-prev {
    color: #34383d;
    background: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.contentSlider .swiper-button-next,
.researchSlider .swiper-button-next {
    right: -5px;
}

.contentSlider .swiper-button-prev,
.researchSlider .swiper-button-prev {
    left: -5px;
}

.contentSlider .swiper-button-next:hover,
.contentSlider .swiper-button-prev:hover,
.researchSlider .swiper-button-next:hover,
.researchSlider .swiper-button-prev:hover {
    background: #f8f8f8;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.contentSlider .swiper-button-next:after,
.contentSlider .swiper-button-prev:after,
.researchSlider .swiper-button-next:after,
.researchSlider .swiper-button-prev:after {
    font-size: 18px;
}

.contentSlider .swiper-pagination,
.researchSlider .swiper-pagination {
    bottom: 0 !important;
    position: absolute;
}

.contentSlider .swiper-pagination-bullet,
.researchSlider .swiper-pagination-bullet {
    width: 8px;
    height: 8px;
    background: rgba(0, 0, 0, 0.2);
    margin: 0 4px !important;
}

.contentSlider .swiper-pagination-bullet-active,
.researchSlider .swiper-pagination-bullet-active {
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

    .contentSlider, .researchSlider {
        padding: 0 15px 40px 15px;
    }

    .contentSlider .swiper-button-next,
    .contentSlider .swiper-button-prev,
    .researchSlider .swiper-button-next,
    .researchSlider .swiper-button-prev {
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
                        <a href="javascript:void(0)" class="cta-button" onclick="document.getElementById('startQuizBtn').click(); return false;">경제 상식 테스트</a>
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

<!-- 챗봇과 경제 상식 테스트 버튼 -->
<!--
<div class="container mx-auto px-4 mb-8 text-center flex flex-col md:flex-row justify-center gap-4">
    <button id="chatbotBtn" class="bg-point hover:bg-point/90 text-cdark px-8 py-3 rounded-lg shadow-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-xl flex items-center justify-center mx-auto w-full md:w-72">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <span>캐시플로우 챗봇 대화하기</span>
    </button>
    
    <button id="startQuizBtn" class="bg-point hover:bg-point/90 text-cdark px-8 py-3 rounded-lg shadow-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-xl flex items-center justify-center mx-auto w-full md:w-72">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>경제 상식 테스트</span>
    </button>
</div>
-->

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
                            <span class="inline-block px-2 py-1 {{ $boardCategoryColors[$post->mq_category] }} text-xs font-medium rounded-md">
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
                            <span class="inline-block px-2 py-1 {{ $boardCategoryColors[$post->mq_category] }} text-xs font-medium rounded-md">
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

    // 챗봇 모달 닫기 함수
    function closeChatbotModal() {
        // 확인 다이얼로그 표시
        if (confirm("대화 내용이 초기화됩니다. 계속 진행하시겠습니까?")) {
            const modal = document.getElementById('chatbotModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }

    // 챗봇 모달 열기 함수
    function openChatbotModal() {
        const modal = document.getElementById('chatbotModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        // 대화창 초기화
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.innerHTML = '';
        
        // 세션에서 대화 내용 초기화
        resetChatbotConversation();
        
        // 초기 메시지 추가
        const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        const messageElement = document.createElement('div');
        messageElement.className = 'flex mb-4';
        messageElement.innerHTML = `
            <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center flex-shrink-0 mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cdark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
            <div class="bg-white p-3 rounded-lg shadow-sm max-w-[80%]">
                <p class="text-gray-800">안녕하세요. 캐시플로우 챗봇입니다. 캐시플로우 설명에 대해 궁금한점을 물어보세요!</p>
                <span class="text-xs text-gray-500 mt-1 block">${time}</span>
            </div>
        `;
        
        chatMessages.appendChild(messageElement);
    }
    
    // 챗봇 대화 내용 초기화 함수
    function resetChatbotConversation() {
        // 서버에 세션 초기화 요청
        fetch('/api/chatbot/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                console.warn('대화 내용 초기화 실패');
            } else {
                console.log('대화 내용 초기화 성공');
            }
        })
        .catch(error => {
            console.error('대화 내용 초기화 오류:', error);
        });
    }
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
                    <button onclick="quizModal.classList.add('hidden'); quizModal.classList.remove('flex');" 
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
                    <button onclick="quizModal.classList.add('hidden'); quizModal.classList.remove('flex');" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-full transition-colors text-lg">
                        완료
                    </button>
                </div>
            </div>
        `;
        quizContent.innerHTML = html;
    }
});
</script>
@endpush
@endsection 