@extends('layouts.app')

@section('content')
<div class="min-h-screen" style="background-color: rgb(244 225 118)">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <!-- 카테고리 -->
            <div class="mb-4">
                <span class="inline-block px-3 py-1 {{ $categoryColors[$post->mq_category] }} rounded-full text-sm font-medium">
                    {{ $post->mq_category }}
                </span>
            </div>

            <!-- 제목 -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->mq_title }}</h1>

            <!-- 메타 정보 -->
            <div class="flex items-center text-gray-600 text-sm mb-8">
                <span class="mr-4">{{ $post->mq_writer }}</span>
                <span class="mr-4">{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y-m-d H:i') : '' }}</span>
                <span class="mr-4">조회 {{ $post->mq_view_cnt }}</span>
                <span>좋아요 {{ $post->mq_like_cnt }}</span>
            </div>

            <!-- 이미지 -->
            @if($post->mq_image)
                <div class="mb-8">
                    <img src="{{ asset('storage/' . $post->mq_image) }}" 
                         alt="{{ $post->mq_original_image ?? '게시글 이미지' }}"
                         class="max-w-full h-auto rounded-lg shadow-md">
                </div>
            @endif

            <!-- 내용 -->
            <div class="prose max-w-none mb-8">
                {!! $post->mq_content !!}
            </div>

            <!-- 버튼 영역 -->
            <div class="flex justify-between items-center">
                <a href="{{ route('board.index') }}" 
                   class="inline-flex items-center justify-center px-6 h-12 border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    목록으로
                </a>
                
                <div class="flex gap-3">
                    <button onclick="likePost({{ $post->idx }})" 
                            class="inline-flex items-center justify-center px-6 h-12 bg-yellow-100 text-yellow-800 rounded-xl hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-200 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        좋아요
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function likePost(idx) {
    fetch(`/board/${idx}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // 좋아요 수 업데이트 로직 추가
        window.location.reload();
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection 