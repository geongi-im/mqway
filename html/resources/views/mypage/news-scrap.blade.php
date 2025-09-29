@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary py-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-point mb-2">뉴스 스크랩</h1>
            <p class="text-secondary">관심있는 경제 뉴스를 모아보세요</p>
        </div>

        <!-- 뉴스 스크랩 콘텐츠 -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-semibold text-point">스크랩한 뉴스</h2>
                    <span class="text-sm text-secondary bg-gray-100 px-3 py-1 rounded-full">총 {{ count($newsScrap) }}개</span>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- 정렬 옵션 -->
                    <select class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-point1 focus:border-point1">
                        <option>최신 순</option>
                        <option>제목 순</option>
                        <option>출처 순</option>
                    </select>
                </div>
            </div>

            @if(empty($newsScrap))
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-point mb-2">아직 스크랩한 뉴스가 없습니다</h3>
                <p class="text-secondary mb-4">관심있는 뉴스를 스크랩해보세요!</p>
                <a href="{{ url('/board-news') }}" class="inline-flex items-center px-4 py-2 bg-point1 text-white rounded-md hover:bg-opacity-90 transition-colors">
                    뉴스 게시판 바로가기
                </a>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($newsScrap as $news)
                <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow group">
                    <!-- 뉴스 이미지 -->
                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                        @if(isset($news['image']) && $news['image'])
                        <img src="{{ $news['image'] }}" alt="{{ $news['title'] }}" class="w-full h-full object-cover">
                        @else
                        <div class="text-gray-500 text-center">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm">이미지 없음</span>
                        </div>
                        @endif
                    </div>

                    <!-- 뉴스 정보 -->
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $news['source'] }}</span>
                            <button onclick="unscrapNews({{ $news['id'] }})" class="text-gray-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>

                        <h3 class="font-semibold text-point mb-2 line-clamp-2 cursor-pointer hover:text-point1 transition-colors">
                            {{ $news['title'] }}
                        </h3>

                        <p class="text-sm text-secondary mb-3 line-clamp-2">{{ $news['summary'] }}</p>

                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>스크랩: {{ $news['scraped_date'] }}</span>
                            <div class="flex items-center space-x-2">
                                <button onclick="shareNews({{ $news['id'] }})" class="hover:text-point1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                    </svg>
                                </button>
                                <button onclick="viewNews({{ $news['id'] }})" class="text-point1 hover:text-point hover:underline transition-colors">
                                    자세히 보기
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- 페이지네이션 (향후 구현 시) -->
            <div class="mt-8 flex justify-center">
                <!-- 페이지네이션 버튼들 -->
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function unscrapNews(newsId) {
    if (confirm('이 뉴스의 스크랩을 해제하시겠습니까?')) {
        // AJAX로 스크랩 해제 처리 (향후 구현)
        console.log('뉴스 스크랩 해제:', newsId);
        // 실제 구현 시에는 서버로 요청을 보내고 성공 시 해당 요소 제거
    }
}

function shareNews(newsId) {
    // 뉴스 공유 기능 (향후 구현)
    console.log('뉴스 공유:', newsId);
    alert('공유 기능은 곧 구현 예정입니다!');
}

function viewNews(newsId) {
    // 뉴스 상세 보기 (향후 구현)
    console.log('뉴스 상세 보기:', newsId);
    alert('뉴스 상세 보기 기능은 곧 구현 예정입니다!');
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