@extends('layouts.app')

@section('content')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
/* 메인 배너 스타일 */
.mainBanner {
    position: relative;
    width: 100%;
    height: 600px;
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
}

.mainBanner .banner-image {
    position: relative;
    width: 100%;
    height: 100%;
}

.mainBanner .banner-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
}

.mainBanner .banner-content {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));
    display: flex;
    align-items: center;
    padding: 0 20px;
}

.mainBanner .banner-content .text-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 60px;
    text-align: center;
}

.mainBanner .banner-content h2 {
    font-size: 3rem;
    line-height: 1.3;
    font-weight: 700;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    text-align: center;
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
.contentSlider {
    position: relative;
    padding-bottom: 50px; /* 페이지네이션을 위한 하단 여백 */
}

.contentSlider .swiper-button-next,
.contentSlider .swiper-button-prev {
    color: #34383d;
    background: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.contentSlider .swiper-button-next {
    right: -5px;
}

.contentSlider .swiper-button-prev {
    left: -5px;
}

.contentSlider .swiper-button-next:hover,
.contentSlider .swiper-button-prev:hover {
    background: #f8f8f8;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.contentSlider .swiper-button-next:after,
.contentSlider .swiper-button-prev:after {
    font-size: 18px;
}

.contentSlider .swiper-pagination {
    bottom: 0 !important;
    position: absolute;
}

.contentSlider .swiper-pagination-bullet {
    width: 8px;
    height: 8px;
    background: rgba(0, 0, 0, 0.2);
    margin: 0 4px !important;
}

.contentSlider .swiper-pagination-bullet-active {
    background: #34383d;
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .mainBanner {
        height: 300px;
        padding: 15px 0;
    }

    .mainBanner .banner-image {
        height: 100%;
    }

    .mainBanner .banner-content {
        background: linear-gradient(to bottom, 
            rgba(0,0,0,0.3) 0%,
            rgba(0,0,0,0.5) 50%,
            rgba(0,0,0,0.3) 100%
        );
    }

    .mainBanner .banner-content .text-container {
        padding: 0 20px;
        max-width: 100%;
    }

    .mainBanner .banner-content h2 {
        font-size: 1.125rem;
        line-height: 1.5;
        letter-spacing: -0.02em;
        text-align: center;
        word-break: keep-all;
    }

    .mainBanner .banner-content h2 br {
        display: inline;
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

    .contentSlider {
        padding: 0 15px 40px 15px;
    }

    .contentSlider .swiper-button-next,
    .contentSlider .swiper-button-prev {
        display: none;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .mainBanner {
        height: 400px;
        padding: 20px 0;
    }

    .mainBanner .banner-content h2 {
        font-size: 1.75rem;
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
                <img src="{{ asset('images/banner/main_banner_01.png') }}" alt="배너1">
                <div class="banner-content">
                    <div class="text-container">
                        <h2>라떼 한잔으로<br>원하는 삶을 누리세요</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="banner-image">
                <img src="{{ asset('images/banner/main_banner_02.png') }}" alt="배너2">
                <div class="banner-content">
                    <div class="text-container">
                        <h2>한 달의 구독료로<br>당신만의 자산을 시작하세요</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="banner-image">
                <img src="{{ asset('images/banner/main_banner_03.png') }}" alt="배너3">
                <div class="banner-content">
                    <div class="text-container">
                        <h2>취침 전에 클릭 한 번,<br>꿈 같은 미래가 시작됩니다</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="banner-image">
                <img src="{{ asset('images/banner/main_banner_04.png') }}" alt="배너4">
                <div class="banner-content">
                    <div class="text-container">
                        <h2>출근길 10분,<br>투자 지식을 쌓는 최적의 시간</h2>
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

<!-- 캐시플로우 챗봇 버튼 -->
<div class="container mx-auto px-4 mb-8 text-center">
    <button id="chatbotBtn" class="bg-point hover:bg-point/90 text-cdark px-8 py-3 rounded-lg shadow-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-xl flex items-center justify-center mx-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        캐시플로우 챗봇과 대화하기
    </button>
</div>

<!-- 콘텐츠1 슬라이더 -->
<div class="container mx-auto px-4 mb-16">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold mb-6">추천 콘텐츠</h2>
        <a href="{{ url('/board') }}" class="text-dark hover:text-dark/80 transition-colors flex items-center">
            더보기
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
    <div class="swiper contentSlider">
        <div class="swiper-wrapper">
            @foreach($posts as $post)
            <div class="swiper-slide">
                <a href="{{ route('board.show', $post->idx) }}" class="block h-full">
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
</script>

<!-- 챗봇 모달 -->
<div id="chatbotModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <!-- 모달 컨테이너 -->
    <div class="w-full h-full md:h-[80vh] sm:max-w-[650px] flex">
        <!-- 모달 내용 -->
        <div class="w-full h-full bg-white sm:rounded-lg overflow-hidden shadow-xl flex flex-col">
            <!-- 모달 헤더 -->
            <div class="sticky top-0 z-10 bg-point border-b border-gray-200">
                <div class="flex items-center justify-between p-4">
                    <h2 class="text-xl font-bold text-cdark">캐시플로우 챗봇</h2>
                    <button onclick="closeChatbotModal()" class="text-cdark hover:text-cgray">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- 챗봇 대화창 본문 -->
            <div class="flex-grow overflow-y-auto p-4 bg-gray-50 h-0" id="chatMessages">
                <!-- 초기 메시지는 JavaScript에서 동적으로 추가됩니다 -->
            </div>
            
            <!-- 입력창 -->
            <div class="border-t border-gray-200 p-4 bg-white">
                <form id="chatForm" class="flex flex-col">
                    <!-- 이미지 미리보기 영역 -->
                    <div id="imagePreview" class="hidden mb-3 relative flex justify-center">
                        <img id="previewImage" class="max-h-32 rounded-lg" src="" alt="첨부 이미지">
                        <button id="removeImageBtn" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="flex items-center">
                        <!-- 이미지 첨부 버튼 -->
                        <button type="button" id="imageAttachBtn" class="text-gray-500 hover:text-gray-700 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                        <input type="file" id="imageInput" accept="image/*" class="hidden">
                        <input type="text" id="messageInput" placeholder="메시지를 입력하세요..." autocomplete="off" class="flex-grow px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-point focus:border-transparent">
                        <button type="submit" class="bg-point text-cdark px-4 py-2 rounded-r-lg hover:bg-point/90 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 챗봇 관련 스크립트 -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatbotBtn = document.getElementById('chatbotBtn');
        const chatbotModal = document.getElementById('chatbotModal');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const chatMessages = document.getElementById('chatMessages');
        const imageAttachBtn = document.getElementById('imageAttachBtn');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        const removeImageBtn = document.getElementById('removeImageBtn');
        
        // Enter 키 이벤트 처리 (포커스가 메시지 입력창에 있을 때)
        messageInput.addEventListener('keydown', function(e) {
            // Enter 키가 눌리면서 Shift 키는 눌리지 않은 경우에만 폼 제출
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault(); // 기본 엔터 동작 방지
                chatForm.dispatchEvent(new Event('submit')); // 폼 제출 이벤트 발생
            }
        });
        
        // 이미지 첨부 버튼 클릭 이벤트
        imageAttachBtn.addEventListener('click', function() {
            imageInput.click();
        });
        
        // 이미지 선택 이벤트
        imageInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // 최대 이미지 크기 제한 (2MB로 설정)
                const maxSizeMB = 2;
                const maxSizeBytes = maxSizeMB * 1024 * 1024;
                
                if (file.size > maxSizeBytes) {
                    console.log(`이미지 압축 전 크기: ${(file.size / 1024 / 1024).toFixed(2)}MB`);
                    
                    // 로딩 스피너 표시
                    if (typeof LoadingManager !== 'undefined') {
                        LoadingManager.show();
                    }
                    
                    // 이미지 리사이징 및 압축
                    resizeImage(file, 800, 0.8).then(resizedImage => {
                        // 압축된 이미지 미리보기 표시
                        previewImage.src = resizedImage;
                        imagePreview.classList.remove('hidden');
                        console.log(`이미지 압축 후 크기: 약 ${resizedImage.length / 10000}KB`);
                        
                        // 로딩 스피너 숨기기
                        if (typeof LoadingManager !== 'undefined') {
                            LoadingManager.hide();
                        }
                    }).catch(err => {
                        console.error('이미지 압축 중 오류:', err);
                        alert('이미지 압축 중 오류가 발생했습니다.');
                        
                        // 에러 발생 시에도 로딩 스피너 숨기기
                        if (typeof LoadingManager !== 'undefined') {
                            LoadingManager.hide();
                        }
                    });
                } else {
                    // 작은 이미지는 바로 미리보기 표시
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
        
        // 이미지 리사이징 함수
        function resizeImage(file, maxWidth, quality) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        // 원본 이미지 크기 유지를 위한 비율 계산
                        let width = img.width;
                        let height = img.height;
                        
                        if (width > maxWidth) {
                            const ratio = maxWidth / width;
                            width = maxWidth;
                            height = height * ratio;
                        }
                        
                        // 캔버스에 리사이즈된 이미지 그리기
                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // 압축된 이미지를 base64로 변환
                        const resizedImage = canvas.toDataURL('image/jpeg', quality);
                        resolve(resizedImage);
                    };
                    img.onerror = function() {
                        reject(new Error('이미지 로드 실패'));
                    };
                    img.src = e.target.result;
                };
                reader.onerror = function() {
                    reject(new Error('파일 읽기 실패'));
                };
                reader.readAsDataURL(file);
            });
        }
        
        // 이미지 제거 버튼 이벤트
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.classList.add('hidden');
            previewImage.src = '';
        });
        
        // 챗봇 버튼 클릭 시 모달 열기
        chatbotBtn.addEventListener('click', function() {
            openChatbotModal();
        });
        
        // 폼 제출 시 메시지 추가
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            const hasImage = !imagePreview.classList.contains('hidden');
            let imageSrc = null;
            
            // API 요청 데이터 준비
            const requestData = {
                message: message
            };
            
            // 이미지가 있으면 추가
            if (hasImage && previewImage.src) {
                // 확실하게 이미지 소스 재확인
                imageSrc = previewImage.src;
                requestData.image = imageSrc;
                console.log("API 요청에 이미지 포함됨");
            }
            
            // 메시지나 이미지 중 하나는 있어야 함
            if (!message && !hasImage) return;
            
            // 폼 비활성화 (중복 제출 방지)
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            
            // 디버깅: 이미지 상태 로깅
            console.log('폼 제출 시 상태:', {
                hasMessage: !!message,
                hasImage: hasImage,
                imagePreviewHidden: imagePreview.classList.contains('hidden'),
                imageSourceExists: !!previewImage.src
            });
            
            // 사용자 메시지 추가
            addUserMessage(message, imageSrc);
            
            // 입력창 비우기 (메시지 추가 후 초기화)
            messageInput.value = '';
            // 이미지 초기화는 메시지 추가 후에 진행
            if (hasImage) {
                setTimeout(() => {
                    imageInput.value = '';
                    imagePreview.classList.add('hidden');
                    previewImage.src = '';
                }, 100); // 약간의 지연 추가
            }
            
            // 로딩 메시지 추가
            const loadingElement = document.createElement('div');
            loadingElement.className = 'flex mb-4 loading-message';
            loadingElement.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center flex-shrink-0 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cdark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div class="bg-white p-3 rounded-lg shadow-sm max-w-[80%]">
                    <p class="text-gray-800">응답 생성 중<span class="dot-animation">...</span></p>
                </div>
            `;
            chatMessages.appendChild(loadingElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Gemini API에 메시지 전송 (서버 사이드 라우트를 통해)
            fetch('/api/chatbot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                // 폼 다시 활성화
                submitButton.disabled = false;
                
                // 에러 응답 처리
                if (!response.ok) {
                    // 로딩 메시지 제거
                    document.querySelector('.loading-message')?.remove();
                    
                    // HTTP 상태 코드에 따른 에러 메시지
                    let errorMessage = '서버 응답이 올바르지 않습니다.';
                    
                    if (response.status === 413) {
                        errorMessage = '이미지 크기가 너무 큽니다. 더 작은 이미지를 사용해주세요.';
                    }
                    
                    // 에러 메시지 표시
                    addBotMessage('죄송합니다. ' + errorMessage);
                    throw new Error(errorMessage);
                }
                
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let botResponseText = '';
                let isStreamComplete = false; // 스트림 완료 상태 추적
                
                // 로딩 메시지 제거
                document.querySelector('.loading-message')?.remove();
                
                // 봇 응답을 위한 메시지 요소 생성
                const botMessageElement = document.createElement('div');
                botMessageElement.className = 'flex mb-4';
                botMessageElement.innerHTML = `
                    <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center flex-shrink-0 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cdark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow-sm max-w-[80%]">
                        <p class="text-gray-800 bot-response"></p>
                        <span class="text-xs text-gray-500 mt-1 block">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                `;
                chatMessages.appendChild(botMessageElement);
                const botResponseElement = botMessageElement.querySelector('.bot-response');
                
                // 스트리밍 응답 처리 함수
                function processStream() {
                    return reader.read().then(({ done, value }) => {
                        if (done) {
                            console.log('스트림 읽기 완료');
                            isStreamComplete = true;
                            
                            // 스트림이 완료되었을 때 최종 확인
                            if (!botResponseElement.textContent.trim()) {
                                botResponseElement.textContent = '응답 생성 중 오류가 발생했습니다. 다시 시도해주세요.';
                            }
                            
                            // 디버깅: 최종 응답 로깅
                            console.log('최종 응답 완료:', botResponseText);
                            return;
                        }
                        
                        const chunk = decoder.decode(value, { stream: true });
                        console.log('원본 청크:', chunk); // 디버깅용
                        const lines = chunk.split('\n\n');
                        
                        lines.forEach(line => {
                            if (line.startsWith('data: ')) {
                                const data = line.substring(6).trim(); // 'data: ' 접두사 제거 및 공백 제거
                                
                                if (data === '[DONE]') {
                                    console.log('스트림 종료 신호 받음');
                                    isStreamComplete = true;
                                    // 응답 텍스트 최종 확인
                                    console.log('최종 텍스트:', botResponseText);
                                    return;
                                }
                                
                                if (data.startsWith('[ERROR]')) {
                                    botResponseElement.textContent = '죄송합니다. 응답 생성 중 오류가 발생했습니다.';
                                    return;
                                }
                                
                                try {
                                    // 배열의 시작, 끝 또는 빈 데이터 처리
                                    if (data === '[' || data === ']' || data === '') {
                                        console.log('배열 토큰 또는 빈 데이터:', data);
                                        return;
                                    }
                                    
                                    // 데이터 정제: 쉼표로 시작하는 경우 제거
                                    let cleanData = data;
                                    if (cleanData.startsWith(',')) {
                                        cleanData = cleanData.substring(1).trim();
                                    }
                                    
                                    // JSON 파싱 및 텍스트 추출
                                    let extractedText = null;
                                    
                                    // 방법 1: 완전한 JSON 파싱
                                    if (cleanData.startsWith('{')) {
                                        try {
                                            const jsonData = JSON.parse(cleanData);
                                            
                                            // 후보 응답 추출
                                            if (jsonData.candidates && 
                                                jsonData.candidates.length > 0 && 
                                                jsonData.candidates[0].content && 
                                                jsonData.candidates[0].content.parts && 
                                                jsonData.candidates[0].content.parts.length > 0) {
                                                
                                                extractedText = jsonData.candidates[0].content.parts[0].text;
                                                console.log('JSON 파싱으로 추출된 텍스트:', extractedText);
                                            }
                                        } catch (parseError) {
                                            console.warn('JSON 파싱 실패:', parseError);
                                        }
                                    }
                                    
                                    // 방법 2: 정규식으로 텍스트 필드 추출 (개선된 패턴 - 멀티라인 지원)
                                    if (extractedText === null && cleanData.includes('"text"')) {
                                        // 개선된 정규식: /s 플래그로 멀티라인 지원, 이스케이프된 쌍따옴표 허용
                                        const textMatch = /"text"\s*:\s*"((?:\\"|[^"])*)"/s.exec(cleanData);
                                        if (textMatch && textMatch[1]) {
                                            extractedText = textMatch[1];
                                            console.log('정규식으로 추출된 텍스트:', extractedText);
                                        }
                                    }
                                    
                                    // 방법 3: 마지막 리조트로 finishReason 확인
                                    if (extractedText === null && cleanData.includes('"finishReason"')) {
                                        console.log('finishReason 감지됨, 마지막 청크일 가능성 있음');
                                        // 다른 정규식으로 한번 더 시도 (멀티라인 지원)
                                        const altTextMatch = /"text"\s*:\s*"((?:\\"|[^"])*)"/s.exec(cleanData);
                                        if (altTextMatch && altTextMatch[1]) {
                                            extractedText = altTextMatch[1];
                                            console.log('finishReason 포함 청크에서 추출된 텍스트:', extractedText);
                                        }
                                    }
                                    
                                    // 텍스트가 추출되었으면 화면에 표시
                                    if (extractedText !== null && extractedText !== undefined) {
                                        console.log('추출된 텍스트:', extractedText);
                                        
                                        // 텍스트 정제: 다양한 줄바꿈 및 특수 문자 처리
                                        extractedText = extractedText
                                            // 이중 이스케이프된 개행 문자(\\n)를 HTML <br>로 변환
                                            .replace(/\\\\n/g, '<br>')
                                            // 단일 이스케이프된 개행 문자(\n)를 HTML <br>로 변환
                                            .replace(/\\n/g, '<br>')
                                            // 순수 개행 문자를 HTML <br>로 변환
                                            .replace(/\n/g, '<br>')
                                            // 백슬래시와 별표 정리 (\*)
                                            .replace(/\\\*/g, '*')
                                            // 이스케이프된 특수 문자 정리
                                            .replace(/\\"/g, '"')
                                            .replace(/\\'/g, "'")
                                            .replace(/\\\\/g, "\\");
                                        
                                        botResponseText += extractedText;
                                        
                                        // HTML 태그가 포함된 텍스트 처리를 위해 innerHTML 사용
                                        botResponseElement.innerHTML = botResponseText
                                            // 텍스트 마지막에 남아있는 불필요한 백슬래시 제거
                                            .replace(/\\$/g, '')
                                            // 불필요한 백슬래시 + 공백 제거
                                            .replace(/\\ /g, ' ');
                                        
                                        // 디버깅: 현재까지 누적된 전체 응답 확인
                                        console.log('누적된 전체 응답:', botResponseText);
                                            
                                        chatMessages.scrollTop = chatMessages.scrollHeight;
                                    } else {
                                        console.warn('텍스트를 추출할 수 없음:', cleanData);
                                    }
                                } catch (e) {
                                    console.error('데이터 처리 오류:', e, '데이터:', data);
                                }
                            }
                        });
                        
                        // 다음 청크 처리
                        return processStream();
                    }).catch(error => {
                        console.error('스트림 처리 중 오류:', error);
                        if (!botResponseElement.textContent.trim()) {
                            botResponseElement.textContent = '응답 처리 중 오류가 발생했습니다.';
                        }
                    });
                }
                
                // 스트리밍 처리 시작
                return processStream();
            })
            .catch(error => {
                console.error('API 요청 오류:', error);
                
                // 로딩 메시지가 있으면 제거
                document.querySelector('.loading-message')?.remove();
                
                // 에러 메시지 추가
                addBotMessage('죄송합니다. 메시지 처리 중 오류가 발생했습니다. 다시 시도해주세요.');
            });
        });
        
        // 사용자 메시지 추가 함수
        function addUserMessage(message, imageSrc = null) {
            const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            const messageElement = document.createElement('div');
            messageElement.className = 'flex justify-end mb-4';
            
            let contentHtml = '';
            
            // 이미지 처리 로직 강화
            if (imageSrc && imageSrc !== '') {
                console.log("이미지 소스: ", imageSrc); // 디버깅
                contentHtml += `<div class="flex justify-center w-full mb-2"><img src="${imageSrc}" class="max-w-full max-h-48 rounded-lg" alt="첨부 이미지"></div>`;
            }
            
            if (message) {
                contentHtml += `<p class="text-cdark">${message}</p>`;
            }
            
            messageElement.innerHTML = `
                <div class="bg-point p-3 rounded-lg shadow-sm max-w-[80%]">
                    ${contentHtml}
                    <span class="text-xs text-cdark/70 mt-1 block">${time}</span>
                </div>
            `;
            
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // 챗봇 메시지 추가 함수
        function addBotMessage(message) {
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
                    <p class="text-gray-800">${message}</p>
                    <span class="text-xs text-gray-500 mt-1 block">${time}</span>
                </div>
            `;
            
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
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
@endpush
@endsection 