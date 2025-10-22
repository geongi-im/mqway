@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <!-- 상단 타이틀 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">뉴스 스크랩</h1>
        <p class="text-secondary">관심있는 경제 뉴스를 모아보세요</p>
    </div>

    <!-- 검색 영역 -->
    <div class="bg-primary rounded-xl p-4 mb-6">
        <form action="{{ route('mypage.news-scrap.index') }}" method="GET">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="w-full h-12 px-4 rounded-xl border-0 focus:ring-2 focus:ring-yellow-500"
                           placeholder="제목, 내용, 용어로 검색">
                    <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-dark hover:bg-opacity-90 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
                <a href="{{ route('mypage.news-scrap.index') }}" class="inline-flex items-center justify-center h-12 px-5 bg-point text-cdark rounded-xl hover:bg-opacity-90 transition-all">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    초기화
                </a>
            </div>
        </form>
    </div>

    <!-- 글쓰기 버튼 영역 -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-secondary bg-gray-100 px-3 py-1.5 rounded-full">총 {{ $scraps->total() }}개</span>
        </div>
        <a href="{{ route('mypage.news-scrap.create') }}"
           class="inline-flex items-center justify-center h-10 px-4 bg-point1 text-cdark rounded-lg hover:bg-opacity-90 transition-all text-sm whitespace-nowrap">
            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            글쓰기
        </a>
    </div>

    @if($scraps->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 px-4 bg-white rounded-xl shadow-sm">
            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                @if(request('search'))
                    검색 결과가 없습니다
                @else
                    아직 스크랩한 뉴스가 없습니다
                @endif
            </h3>
            <p class="text-sm text-text-dark text-center">
                @if(request('search'))
                    '{{ request('search') }}'에 대한 검색 결과가 없습니다.<br>
                    다른 검색어로 다시 시도해보세요.
                @else
                    관심있는 경제 뉴스를 스크랩해보세요!<br>
                    첫 번째 뉴스의 주인공이 되어보세요!
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($scraps as $scrap)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow h-full">
                    <div class="flex flex-col h-full">
                        <!-- 썸네일 이미지 (클릭 시 상세페이지) -->
                        <a href="{{ route('mypage.news-scrap.show', $scrap->idx) }}" class="block">
                            <div class="bg-gray-50 flex items-center justify-center" style="height: 240px;">
                                @if($scrap->hasThumbnail())
                                    <img src="{{ $scrap->getThumbnailUrl() }}"
                                         alt="{{ $scrap->mq_title }}"
                                         class="w-full h-full object-cover"
                                         onerror="this.parentElement.innerHTML='<div class=\'flex flex-col items-center justify-center text-gray-400\'><svg class=\'w-16 h-16 mb-2\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg><span class=\'text-sm\'>이미지 없음</span></div>';">
                                @else
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm">이미지 없음</span>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <!-- 뉴스 정보 -->
                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('mypage.news-scrap.show', $scrap->idx) }}" class="hover:text-point transition-colors">
                                    {{ $scrap->mq_title }}
                                </a>
                            </h3>

                            <div class="flex items-center justify-between text-sm text-text-dark mt-auto">
                                <span>{{ $scrap->mq_reg_date ? $scrap->mq_reg_date->format('Y-m-d') : '' }}</span>
                                <a href="{{ $scrap->mq_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-md hover:bg-blue-200 transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    원문 보기
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- 페이지네이션 -->
    <div class="mt-8 flex justify-center">
        {{ $scraps->appends(request()->query())->links() }}
    </div>
</div>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
