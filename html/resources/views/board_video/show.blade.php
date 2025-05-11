@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <!-- 게시글 상세 컨테이너 -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- 게시글 헤더 -->
        <div class="p-6 border-b border-gray-200">
            <!-- 카테고리 및 작성일 -->
            <div class="flex justify-between items-center mb-3">
                <span class="inline-block px-3 py-1 {{ $categoryColors[$post->mq_category] ?? 'bg-gray-100 text-gray-800' }} rounded-lg text-sm font-medium">
                    {{ $post->mq_category }}
                </span>
                <span class="text-sm text-gray-500">{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y-m-d H:i') : '' }}</span>
            </div>
            
            <!-- 제목 -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $post->mq_title }}</h1>
            
            <!-- 작성자 정보 및 조회수/좋아요 -->
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">
                        {{ $post->user->mq_user_id }}
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ $post->mq_view_cnt }}
                    </span>
                    <span class="flex items-center text-sm text-gray-500 like-count" id="likeCount">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span id="likeCountValue">{{ $post->mq_like_cnt }}</span>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- 비디오 영역 -->
        @if(isset($post->mq_video_url) && !empty($post->mq_video_url))
            <div class="p-6 border-b border-gray-200">
                <div class="video-container w-full" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%;">
                    @php
                        $videoUrl = $post->mq_video_url;
                        $videoId = '';
                        
                        // YouTube URL 패턴 인식
                        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $videoUrl, $matches) || 
                            preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                            $videoId = $matches[1];
                            echo '<iframe src="https://www.youtube.com/embed/' . $videoId . '" 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                                    frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>';
                        } 
                        // Vimeo URL 패턴 인식
                        else if (preg_match('/vimeo\.com\/([0-9]+)/', $videoUrl, $matches)) {
                            $videoId = $matches[1];
                            echo '<iframe src="https://player.vimeo.com/video/' . $videoId . '" 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                                    frameborder="0" allow="autoplay; fullscreen; picture-in-picture" 
                                    allowfullscreen></iframe>';
                        }
                        // 기타 동영상은 링크로 표시
                        else {
                            echo '<div class="w-full h-full bg-gray-100 flex items-center justify-center">';
                            echo '<a href="' . $videoUrl . '" target="_blank" class="text-blue-500 underline">동영상 바로가기</a>';
                            echo '</div>';
                        }
                    @endphp
                </div>
            </div>
        @endif
        
        <!-- 이미지 슬라이더 (이미지가 있을 경우) -->
        @if(is_array($post->mq_image) && count($post->mq_image) > 0)
            <div class="border-b border-gray-200">
                <div class="image-slider">
                    @foreach($post->mq_image as $image)
                        <div class="slider-item">
                            <img src="{{ $image }}" alt="게시글 이미지" class="w-full h-auto object-contain mx-auto" style="max-height: 500px;">
                        </div>
                    @endforeach
                </div>
                
                @if(count($post->mq_image) > 1)
                    <div class="flex justify-center items-center py-2">
                        <button class="slider-prev mx-2 p-1 bg-gray-200 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <span class="slider-indicator text-sm text-gray-500">1 / {{ count($post->mq_image) }}</span>
                        <button class="slider-next mx-2 p-1 bg-gray-200 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        @endif
        
        <!-- 본문 내용 -->
        <div class="p-6 border-b border-gray-200">
            <div class="prose max-w-none">
                {!! $post->mq_content !!}
            </div>
        </div>
        
        <!-- 게시글 하단 버튼 영역 -->
        <div class="p-6 flex justify-between items-center">
            <div>
                <a href="{{ route('board-video.index') }}" class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                    목록
                </a>
            </div>
            
            <!-- 우측 버튼 그룹 -->
            <div class="flex items-center gap-2">
                @if(auth()->check() && auth()->user()->mq_user_id === $post->mq_user_id)
                    <a href="{{ route('board-video.edit', $post->idx) }}" 
                       class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                        수정
                    </a>
                    <form action="{{ route('board-video.destroy', $post->idx) }}"
                          method="POST"
                          onsubmit="return confirmDelete()">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center h-10 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-200 transition-all text-sm">
                            삭제
                        </button>
                    </form>
                @endif
                <button onclick="likePost(event, {{ $post->idx }})" 
                        class="inline-flex items-center justify-center gap-2 h-10 px-4 {{ auth()->check() ? 'bg-gray-100 hover:bg-yellow-100 hover:text-yellow-800' : 'bg-gray-50 cursor-not-allowed' }} text-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition-all group"
                        title="{{ auth()->check() ? '좋아요' : '로그인이 필요합니다' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span>{{ number_format($post->mq_like_cnt) }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
    $(document).ready(function(){
        // 이미지 슬라이더 초기화
        var $slider = $('.image-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            infinite: true
        });
        
        // 슬라이더 인디케이터 업데이트
        $slider.on('afterChange', function(event, slick, currentSlide){
            $('.slider-indicator').text((currentSlide + 1) + ' / ' + slick.slideCount);
        });
        
        // 이전/다음 버튼 이벤트
        $('.slider-prev').click(function(){
            $slider.slick('slickPrev');
        });
        
        $('.slider-next').click(function(){
            $slider.slick('slickNext');
        });
    });
    
    // 삭제 확인 함수
    function confirmDelete() {
        return confirm('정말 삭제하시겠습니까?');
    }

    async function likePost(event, idx) {
        event.preventDefault();
        
        @guest
            alert('로그인이 필요한 기능입니다.');
            return;
        @endguest
        
        try {
            const button = event.currentTarget;
            const response = await fetch(`/board-video/${idx}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            });

            if (response.status === 401) {
                alert('로그인이 필요한 기능입니다.');
                return;
            }

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            
            // 좋아요 수 업데이트
            const likeCountElements = document.querySelectorAll('button[onclick^="likePost"] span');
            likeCountElements.forEach(element => {
                element.textContent = new Intl.NumberFormat().format(data.likes);
            });
            
            // 버튼 스타일 변경
            button.classList.remove('bg-gray-100', 'text-gray-600');
            button.classList.add('bg-yellow-100', 'text-yellow-800');
            
            // 0.2초 후 원래 스타일로 복귀
            setTimeout(() => {
                button.classList.remove('bg-yellow-100', 'text-yellow-800');
                button.classList.add('bg-gray-100', 'text-gray-600');
            }, 200);
            
        } catch (error) {
            console.error('Error:', error);
            alert('좋아요 처리 중 오류가 발생했습니다.');
        }
    }
</script>
@endpush
@endsection 