@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <!-- 카테고리 -->
            <div class="mb-4">
                <span class="inline-block px-3 py-1 {{ $categoryColors[$post->mq_category] }} rounded-md text-sm font-medium">
                    {{ $post->mq_category }}
                </span>
            </div>

            <!-- 제목 -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->mq_title }}</h1>

            <!-- 메타 정보 -->
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

            <!-- 이미지 -->
            @if($post->mq_image)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                    @foreach($post->mq_image as $index => $image)
                        <a href="{{ asset($image) }}" 
                           target="_blank"
                           class="block aspect-square hover:opacity-90 transition-opacity">
                            <img src="{{ asset($image) }}" 
                                 alt="{{ $post->mq_original_image[$index] ?? '게시글 이미지' }}"
                                 class="w-full h-full object-contain bg-gray-50 rounded-lg shadow-md cursor-pointer p-2">
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- 내용 -->
            <div class="prose max-w-none mb-8">
                {!! $post->mq_content !!}
            </div>

            <!-- 버튼 영역 -->
            <div class="flex justify-between items-center">
                <!-- 좌측 버튼 -->
                <a href="{{ route('board-research.index') }}" 
                   class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                    목록
                </a>
                
                <!-- 우측 버튼 그룹 -->
                <div class="flex items-center gap-2">
                    @if(auth()->check() && auth()->user()->mq_user_id === $post->mq_user_id)
                        <a href="{{ route('board-research.edit', $post->idx) }}" 
                           class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                            수정
                        </a>
                        <form action="{{ route('board-research.destroy', $post->idx) }}"
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
        const button = event.currentTarget;
        const response = await fetch(`/board-research/${idx}/like`, {
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
        
        // 좋아요 수 업데이트
        const likeCountElements = document.querySelectorAll('button[onclick^="likePost"] span');
        likeCountElements.forEach(element => {
            element.textContent = new Intl.NumberFormat().format(data.likes);
        });
        
        // 버튼 스타일 변경 (좋아요 상태에 따라)
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
</script>
@endpush
@endsection 