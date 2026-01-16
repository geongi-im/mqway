@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <div class="mb-4">
                <span class="inline-block px-3 py-1 {{ $categoryColors[$post->mq_category] ?? 'bg-blue-100 text-blue-800' }} rounded-md text-sm font-medium">
                    {{ $post->mq_category }}
                </span>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->mq_title }}</h1>

            <div class="flex flex-wrap items-center text-gray-600 text-sm mb-8 gap-3">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ $post->mq_user_id }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y.m.d H:i') : '' }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ number_format($post->mq_view_cnt) }}</span>
                </div>
            </div>

            @if($post->mq_image)
                <div class="gallery grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8" id="gallery-main">
                    @foreach($post->mq_image as $index => $image)
                        @if(strpos($image, 'no_image') === false)
                        <a href="{{ asset($image) }}"
                           data-pswp-width=""
                           data-pswp-height=""
                           class="block aspect-square hover:opacity-90 transition-opacity gallery-item">
                            <img src="{{ asset($image) }}"
                                 alt="{{ $post->mq_original_image[$index] ?? '게시글 이미지' }}"
                                 class="w-full h-full object-contain bg-gray-50 rounded-lg shadow-md cursor-pointer p-2">
                        </a>
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="prose max-w-none mb-8">
                {!! $post->mq_content !!}
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('board-insights.index') }}" 
                   class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                    목록
                </a>
                
                <div class="flex items-center gap-2">
                    @if(auth()->check() && auth()->user()->mq_user_id === $post->mq_user_id)
                        <a href="{{ route('board-insights.edit', $post->idx) }}" 
                           class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                            수정
                        </a>
                        <form action="{{ route('board-insights.destroy', $post->idx) }}"
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
                            class="inline-flex items-center justify-center gap-2 h-10 px-4 {{ $isLiked ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition-all group"
                            title="좋아요">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>{{ number_format($post->mq_like_cnt) }}</span>
                    </button>
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

    try {
        const button = event.currentTarget;
        const response = await fetch(`/board-insights/${idx}/like`, {
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

        const likeCountElements = document.querySelectorAll('button[onclick^="likePost"] span');
        likeCountElements.forEach(element => {
            element.textContent = new Intl.NumberFormat().format(data.likes);
        });

        if (data.isLiked) {
            button.classList.remove('bg-gray-100', 'text-gray-600');
            button.classList.add('bg-yellow-100', 'text-yellow-800');
        } else {
            button.classList.remove('bg-yellow-100', 'text-yellow-800');
            button.classList.add('bg-gray-100', 'text-gray-600');
        }

    } catch (error) {
        console.error('Error:', error);
        alert('좋아요 처리 중 오류가 발생했습니다.');
    }
}

document.addEventListener('DOMContentLoaded', async function() {
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

            img.style.cursor = 'pointer';
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
            img.style.borderRadius = '8px';
            img.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
            img.style.transition = 'opacity 0.3s ease';
            img.style.willChange = 'auto';
            img.style.backfaceVisibility = 'hidden';

            img.addEventListener('mouseenter', function() {
                this.style.opacity = '0.9';
            });
            img.addEventListener('mouseleave', function() {
                this.style.opacity = '1';
            });
        }
    }

    initContentImages();
});
</script>
@endpush
@endsection
