@extends('layouts.app')

@section('content')
<!-- ===== Hero Section ===== -->
<section class="relative pt-32 pb-24 overflow-hidden bg-[#3D4148]">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#FF4D4D] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10 text-center animate-slideUp">
        <a href="{{ route('mypage.index') }}" class="inline-flex items-center text-gray-400 hover:text-white mb-6 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center mr-2 group-hover:bg-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            마이페이지로 돌아가기
        </a>
        <br />
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-sm font-medium mb-4 backdrop-blur-md">
            ❤️ Liked Contents
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight tracking-tight">
            좋아요 콘텐츠
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            내가 좋아한 게시물을 모아보세요.
        </p>
    </div>
</section>

<!-- ===== Content Section ===== -->
<div class="container mx-auto px-4 -mt-10 relative z-20 pb-20 max-w-5xl">
    <!-- 통계 카드 -->
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-8 animate-slideUp" style="animation-delay: 0.2s;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FF4D4D] to-[#e03e3e] flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-[#2D3047]">좋아요한 게시물</h2>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-[#FF4D4D]/10 text-[#FF4D4D]">
                총 {{ $likedContent->sum(function($items) { return count($items); }) }}개
            </span>
        </div>
    </div>

    @if($likedContent->isEmpty())
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-slideUp" style="animation-delay: 0.3s;">
        <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-[#2D3047] mb-2">아직 좋아요한 콘텐츠가 없습니다</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-6">관심있는 콘텐츠에 좋아요를 눌러보세요!</p>
            <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#4ECDC4] to-[#2AA9A0] text-white rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                홈으로 가기
            </a>
        </div>
    </div>
    @else
    @foreach($likedContent as $boardName => $items)
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 animate-slideUp" style="animation-delay: {{ 0.3 + $loop->index * 0.1 }}s;">
        <!-- 섹션 헤더 -->
        <div class="px-8 py-5 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-[#4ECDC4]"></div>
                <h3 class="text-lg font-bold text-[#2D3047]">
                    {{ $boardLabels[$boardName] ?? $boardName }}
                </h3>
            </div>
            <span class="text-sm text-gray-400 font-medium bg-white px-3 py-1 rounded-full border border-gray-100">{{ count($items) }}개</span>
        </div>

        <!-- 스크롤 가능한 영역 -->
        <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
            <div class="divide-y divide-gray-50">
                @foreach($items as $item)
                <div class="p-6 hover:bg-gray-50/50 transition-colors group">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($item->post && !empty($item->post->mq_category))
                            <span class="inline-block {{ $categoryColors[$item->post->mq_category] ?? 'bg-gray-100 text-gray-800' }} text-xs px-2.5 py-1 rounded-full font-medium">{{ $item->post->mq_category }}</span>
                            @endif
                            @if($item->post)
                            <span class="text-xs text-gray-400 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $item->post->mq_user_id }}
                            </span>
                            @endif
                        </div>
                        <!-- 좋아요 취소 버튼 -->
                        <button onclick="unlikePost('{{ $boardName }}', {{ $item->mq_board_idx }})" 
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium text-gray-400 hover:text-[#FF4D4D] hover:bg-[#FF4D4D]/5 transition-all whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            취소
                        </button>
                    </div>

                    @if($item->post)
                    <a href="{{ $item->boardUrl }}" class="block group/link">
                        <h4 class="text-base font-bold text-[#2D3047] mb-2 group-hover/link:text-[#4ECDC4] transition-colors line-clamp-2">
                            {{ $item->post->mq_title }}
                        </h4>
                        @if($item->post->mq_content)
                        <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">{{ strip_tags($item->post->mq_content) }}</p>
                        @endif
                    </a>
                    @else
                    <div class="flex items-center gap-2 text-sm text-gray-400 py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        삭제된 게시물입니다
                    </div>
                    @endif

                    <div class="flex justify-between items-center mt-4 text-xs text-gray-400 font-medium">
                        @if($item->post)
                        <div class="flex items-center gap-4">
                            <span class="flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ \Carbon\Carbon::parse($item->post->mq_reg_date)->format('Y.m.d') }}
                            </span>
                            <span class="flex items-center hover:text-[#4ECDC4] transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ $item->post->mq_view_cnt ?? 0 }}
                            </span>
                            <span class="flex items-center hover:text-[#FF4D4D] transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                {{ $item->post->mq_like_cnt ?? 0 }}
                            </span>
                        </div>
                        @endif
                        <span class="flex items-center text-gray-300">
                            스크랩: {{ \Carbon\Carbon::parse($item->mq_reg_date)->format('Y.m.d') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>

<script>
function unlikePost(boardName, boardIdx) {
    if (confirm('좋아요를 취소하시겠습니까?')) {
        // AJAX로 좋아요 취소 처리
        fetch(`/mypage/liked-content/unlike`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                board_name: boardName,
                board_idx: boardIdx
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('좋아요 취소에 실패했습니다.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('오류가 발생했습니다.');
        });
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection