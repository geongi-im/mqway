@extends('layouts.app')

@section('content')
<!-- ===== Hero Background ===== -->
<div class="relative bg-[#3D4148] pb-32 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
    </div>
    <div class="container mx-auto px-4 pt-32 relative z-10">
        <div class="max-w-4xl mx-auto">
            <a href="{{ route('board-content.index') }}" class="inline-flex items-center text-gray-400 hover:text-white mb-6 transition-colors group">
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center mr-2 group-hover:bg-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </div>
                목록으로 돌아가기
            </a>
            
            <div class="flex flex-wrap items-center gap-3 mb-4 animate-slideUp">
                <span class="inline-block px-3 py-1 text-sm font-bold rounded-full shadow-lg {{ $categoryColors[$post->mq_category] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $post->mq_category }}
                </span>
                <span class="text-gray-400 text-sm flex items-center bg-white/5 px-3 py-1 rounded-full">
                    <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $post->mq_reg_date ? $post->mq_reg_date->format('Y.m.d') : '' }}
                </span>
            </div>
            
            <h1 class="text-3xl md:text-5xl font-bold text-white mb-6 leading-tight animate-slideUp" style="animation-delay: 0.1s;">
                {{ $post->mq_title }}
            </h1>

            <div class="flex items-center justify-between border-t border-white/10 pt-6 animate-slideUp" style="animation-delay: 0.2s;">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4ECDC4] to-[#2AA9A0] flex items-center justify-center text-white font-bold shadow-lg">
                            {{ mb_substr($post->mq_user_id, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-white font-medium">{{ $post->mq_user_id }}</p>
                            <p class="text-xs text-gray-400">Contributor</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center text-gray-400/80 text-sm font-medium" title="조회수">
                        <svg class="w-5 h-5 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ number_format($post->mq_view_cnt) }}
                    </div>
                    
                    <div class="h-4 w-px bg-white/10"></div>

                    <button onclick="likePost(event, {{ $post->idx }})" 
                            class="flex items-center px-4 py-2 rounded-full transition-all duration-300 group ring-1 ring-inset {{ $isLiked ? 'bg-[#FF4D4D] text-white ring-[#FF4D4D] shadow-[0_0_15px_rgba(255,77,77,0.4)]' : 'bg-white/5 text-gray-300 ring-white/10 hover:bg-white/10 hover:ring-white/30 hover:text-white' }}"
                            id="headerLikeBtn">
                        <svg class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:scale-110 {{ $isLiked ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="font-medium text-sm mr-1.5">좋아요</span>
                        <span id="headerLikeCount" class="font-bold text-sm">{{ number_format($post->mq_like_cnt) }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== Content Section ===== -->
<div class="container mx-auto px-4 -mt-20 relative z-20 pb-20">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-slideUp" style="animation-delay: 0.3s;">
            <div class="p-8 md:p-12">
                <!-- 이미지 갤러리 -->
                @if($post->mq_image)
                    <div class="gallery grid grid-cols-2 md:grid-cols-3 gap-4 mb-10" id="gallery-main">
                        @foreach($post->mq_image as $index => $image)
                            @if(strpos($image, 'no_image') === false)
                            <a href="{{ asset($image) }}"
                               data-pswp-width=""
                               data-pswp-height=""
                               class="block aspect-square rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all hover:scale-[1.02] gallery-item group relative">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors z-10"></div>
                                <img src="{{ asset($image) }}"
                                     alt="{{ $post->mq_original_image[$index] ?? '게시글 이미지' }}"
                                     class="w-full h-full object-cover">
                            </a>
                            @endif
                        @endforeach
                    </div>
                @endif

                <!-- 본문 내용 -->
                <div class="prose prose-lg max-w-none prose-headings:text-[#2D3047] prose-p:text-gray-600 prose-a:text-[#4ECDC4] prose-strong:text-[#2D3047] prose-img:rounded-xl prose-img:shadow-lg">
                    {!! $post->mq_content !!}
                </div>

                <!-- Bottom Like CTA -->
                <div class="flex flex-col items-center justify-center py-12 mt-12 mb-8 border-y border-gray-100 bg-gray-50/50 rounded-2xl">
                    <p class="text-[#2D3047] font-bold text-lg mb-6">이 콘텐츠가 도움이 되셨나요?</p>
                    <button onclick="likePost(event, {{ $post->idx }})"
                            id="bottomLikeBtn"
                            class="group relative flex items-center justify-center w-16 h-16 rounded-full transition-all duration-300 shadow-xl hover:scale-110 hover:-translate-y-1 mb-4
                            {{ $isLiked ? 'bg-[#FF4D4D] text-white shadow-[#FF4D4D]/40' : 'bg-white text-gray-300 border border-gray-200 hover:border-[#FF4D4D] hover:text-[#FF4D4D] hover:shadow-[#FF4D4D]/20' }}">
                        <svg class="w-8 h-8 transition-transform duration-300 group-hover:scale-110 {{ $isLiked ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                    <p class="text-gray-400 text-sm font-medium">
                        <span id="bottomLikeCount" class="font-bold text-[#2D3047]">{{ number_format($post->mq_like_cnt) }}</span>명이 좋아합니다
                    </p>
                </div>

                <!-- 하단 액션 버튼 -->
                <div class="mt-12 pt-8 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('board-content.index') }}" 
                       class="inline-flex items-center px-6 py-3 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-all font-medium">
                        목록으로
                    </a>
                    
                    <div class="flex items-center gap-3">
                        @if(auth()->check() && auth()->user()->mq_user_id === $post->mq_user_id)
                            <a href="{{ route('board-content.edit', $post->idx) }}" 
                               class="inline-flex items-center px-5 py-3 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-all font-medium">
                                수정
                            </a>
                            <form action="{{ route('board-content.destroy', $post->idx) }}"
                                  method="POST"
                                  onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-5 py-3 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-all font-medium">
                                    삭제
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
        const button = event.currentTarget; // 클릭된 버튼 (헤더 등)
        const response = await fetch(`/board-content/${idx}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
        });

        const data = await response.json();

        if (!data.success) {
            alert(data.message);
            return;
        }

        // 좋아요 수 및 버튼 스타일 업데이트 (헤더 버튼)
        updateLikeButtonStyles(data.isLiked, data.likes);

    } catch (error) {
        console.error('Error:', error);
        alert('좋아요 처리 중 오류가 발생했습니다.');
    }
}

function updateLikeButtonStyles(isLiked, likesCount) {
    const formattedCount = new Intl.NumberFormat().format(likesCount);
    
    // Header Button Update
    const headerBtn = document.getElementById('headerLikeBtn');
    const headerCount = document.getElementById('headerLikeCount');
    if(headerBtn) {
        const icon = headerBtn.querySelector('svg');
        if (isLiked) {
            headerBtn.className = 'flex items-center px-4 py-2 rounded-full transition-all duration-300 group ring-1 ring-inset bg-[#FF4D4D] text-white ring-[#FF4D4D] shadow-[0_0_15px_rgba(255,77,77,0.4)]';
            icon.classList.add('fill-current');
        } else {
            headerBtn.className = 'flex items-center px-4 py-2 rounded-full transition-all duration-300 group ring-1 ring-inset bg-white/5 text-gray-300 ring-white/10 hover:bg-white/10 hover:ring-white/30 hover:text-white';
            icon.classList.remove('fill-current');
        }
        if(headerCount) headerCount.textContent = formattedCount;
    }

    // Bottom Button Update
    const bottomBtn = document.getElementById('bottomLikeBtn');
    const bottomCount = document.getElementById('bottomLikeCount');
    if(bottomBtn) {
        const icon = bottomBtn.querySelector('svg');
        if (isLiked) {
            bottomBtn.className = 'group relative flex items-center justify-center w-16 h-16 rounded-full transition-all duration-300 shadow-xl hover:scale-110 hover:-translate-y-1 mb-4 bg-[#FF4D4D] text-white shadow-[#FF4D4D]/40';
            icon.classList.add('fill-current');
        } else {
            bottomBtn.className = 'group relative flex items-center justify-center w-16 h-16 rounded-full transition-all duration-300 shadow-xl hover:scale-110 hover:-translate-y-1 mb-4 bg-white text-gray-300 border border-gray-200 hover:border-[#FF4D4D] hover:text-[#FF4D4D] hover:shadow-[#FF4D4D]/20';
            icon.classList.remove('fill-current');
        }
        if(bottomCount) bottomCount.textContent = formattedCount;
    }
}

// PhotoSwipe 초기화 및 본문 이미지 처리 (기존 로직 유지 및 개선)
document.addEventListener('DOMContentLoaded', async function() {
    // ... (기존 PhotoSwipe 및 이미지 처리 스크립트 그대로 유지하되, 스타일만 조정된 HTML 구조에 맞게 동작) ...
    // PhotoSwipe 로직은 HTML 구조에 의존적이므로, 기존 스크립트를 가져와서 렌더링하도록 하겠습니다.
    // 여기서는 편의상 핵심 로직을 그대로 포함시킵니다.
    
    const mainGallery = document.getElementById('gallery-main');
    if (mainGallery) {
        const galleryItems = mainGallery.querySelectorAll('.gallery-item');
        for (const item of galleryItems) {
            const img = item.querySelector('img');
            if (img) {
                const dimensions = await getImageDimensions(img);
                item.setAttribute('data-pswp-width', dimensions.width.toString());
                item.setAttribute('data-pswp-height', dimensions.height.toString());
            }
        }

        const lightbox = new PhotoSwipeLightbox({
            gallery: '#gallery-main',
            children: 'a',
            pswpModule: PhotoSwipe,
            bgOpacity: 0.9,
            showHideOpacity: true,
            easing: 'cubic-bezier(0.4, 0, 0.22, 1)',
            initialZoomLevel: 'fit',
            secondaryZoomLevel: 1.5,
            maxZoomLevel: 3
        });
        lightbox.init();
    }

    function getImageDimensions(img) {
        return new Promise((resolve) => {
            if (img.naturalWidth && img.naturalHeight) {
                resolve({ width: img.naturalWidth, height: img.naturalHeight });
            } else {
                const tempImg = new Image();
                tempImg.onload = function() {
                    resolve({ width: this.width, height: this.height });
                };
                tempImg.src = img.src;
            }
        });
    }

    async function initContentImages() {
        const contentDiv = document.querySelector('.prose');
        if (!contentDiv) return;

        const images = contentDiv.querySelectorAll('img');

        for (const img of images) {
            const dimensions = await getImageDimensions(img);
            let link = img.parentElement;
            if (link.tagName !== 'A') {
                link = document.createElement('a');
                link.href = img.src;
                img.parentNode.insertBefore(link, img);
                link.appendChild(img);
            } else {
                link.href = img.src;
            }

            link.setAttribute('data-pswp-width', dimensions.width.toString());
            link.setAttribute('data-pswp-height', dimensions.height.toString());
            link.setAttribute('data-gallery', 'content-gallery');

            link.addEventListener('click', function(e) {
                e.preventDefault();
                const galleryItems = Array.from(contentDiv.querySelectorAll('a[data-gallery="content-gallery"]')).map(link => ({
                    src: link.href,
                    width: parseInt(link.getAttribute('data-pswp-width')),
                    height: parseInt(link.getAttribute('data-pswp-height')),
                    alt: link.querySelector('img')?.alt || ''
                }));
                const clickedIndex = Array.from(contentDiv.querySelectorAll('a[data-gallery="content-gallery"]')).indexOf(this);
                const pswp = new PhotoSwipe({
                    dataSource: galleryItems,
                    index: clickedIndex,
                    bgOpacity: 0.9,
                    showHideOpacity: true,
                    easing: 'cubic-bezier(0.4, 0, 0.22, 1)',
                    initialZoomLevel: 'fit',
                    secondaryZoomLevel: 1.5,
                    maxZoomLevel: 3
                });
                pswp.init();
            });

            // 스타일 개선
            img.classList.add('rounded-xl', 'shadow-lg', 'mx-auto', 'block');
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
        }
    }

    initContentImages();
});
</script>
@endpush
@endsection 