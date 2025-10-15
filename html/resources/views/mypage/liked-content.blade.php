@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary py-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-point mb-2">좋아요 콘텐츠</h1>
            <p class="text-secondary">내가 좋아한 게시물을 모아보세요</p>
        </div>

        <!-- 좋아요 콘텐츠 -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-semibold text-point">좋아요한 게시물</h2>
                    <span class="text-sm text-secondary bg-gray-100 px-3 py-1 rounded-full">총 {{ $likedContent->sum(function($items) { return count($items); }) }}개</span>
                </div>
            </div>

            @if($likedContent->isEmpty())
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-point mb-2">아직 좋아요한 콘텐츠가 없습니다</h3>
                <p class="text-secondary mb-4">관심있는 콘텐츠에 좋아요를 눌러보세요!</p>
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-point1 text-white rounded-md hover:bg-opacity-90 transition-colors">
                    홈으로 가기
                </a>
            </div>
            @else
            @foreach($likedContent as $boardName => $items)
            <div class="mb-8 last:mb-0">
                <h3 class="text-lg font-medium text-point mb-4 border-b border-gray-200 pb-2">
                    {{ $boardLabels[$boardName] ?? $boardName }}
                    <span class="text-sm text-gray-500 ml-2">({{ count($items) }}개)</span>
                </h3>
                <!-- 스크롤 가능한 영역 -->
                <div class="max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                    <div class="space-y-4">
                        @foreach($items as $item)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-center justify-between space-x-2 mb-2">
                                <div class="flex items-center space-x-2">
                                    @if($item->post && !empty($item->post->mq_category))
                                    <span class="inline-block {{ $categoryColors[$item->post->mq_category] ?? 'bg-gray-100 text-gray-800' }} text-xs px-2 py-1 rounded-full">{{ $item->post->mq_category }}</span>
                                    @endif
                                    @if($item->post)
                                    <span class="text-sm text-gray-500">작성자: {{ $item->post->mq_user_id }}</span>
                                    @endif
                                </div>
                                <!-- 좋아요 취소 버튼 -->
                                <button onclick="unlikePost('{{ $boardName }}', {{ $item->mq_board_idx }})" class="text-red-500 text-sm hover:underline whitespace-nowrap">
                                    좋아요 취소
                                </button>
                            </div>

                            @if($item->post)
                            <a href="{{ $item->boardUrl }}" class="block">
                                <h4 class="font-semibold text-point mb-2 hover:text-point1 transition-colors line-clamp-2">
                                    {{ $item->post->mq_title }}
                                </h4>
                                @if($item->post->mq_content)
                                <p class="text-sm text-secondary mb-3 line-clamp-2">{{ strip_tags($item->post->mq_content) }}</p>
                                @endif
                            </a>
                            @else
                            <div class="text-sm text-gray-400 mb-2">삭제된 게시물입니다</div>
                            @endif

                            <div class="flex justify-between items-center text-xs text-gray-500">
                                @if($item->post)
                                <div class="flex items-center space-x-4">
                                    <span>게시일: {{ \Carbon\Carbon::parse($item->post->mq_reg_date)->format('Y-m-d') }}</span>
                                    <span>조회수: {{ $item->post->mq_view_cnt ?? 0 }}</span>
                                    <span>좋아요: {{ $item->post->mq_like_cnt ?? 0 }}</span>
                                </div>
                                @endif
                                <span>스크랩: {{ \Carbon\Carbon::parse($item->mq_reg_date)->format('Y-m-d') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
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

/* 커스텀 스크롤바 스타일 */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection