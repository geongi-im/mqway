@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">추천 콘텐츠</h1>
    </div>

    <!-- 검색 영역 -->
    <div class="bg-yellow-200 rounded-xl p-4 mb-6">
        <form action="{{ route('board.index') }}" method="GET">
            <div class="flex gap-2">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       class="flex-1 h-12 px-4 rounded-xl border-0 focus:ring-2 focus:ring-yellow-500" 
                       placeholder="검색어를 입력하세요">
                <button type="submit" class="w-20 h-12 bg-dark text-white rounded-xl hover:bg-opacity-90 transition-all">
                    검색
                </button>
            </div>
        </form>
    </div>

    <!-- 필터 및 글쓰기 영역 -->
    <div class="flex justify-between items-center mb-6">
        <!-- 필터 영역 -->
        <div class="flex gap-2">
            <select name="category" 
                    onchange="this.form.submit()"
                    form="filterForm" 
                    class="h-10 px-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-500 text-sm">
                <option value="">전체</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
            
            <select name="sort" 
                    onchange="this.form.submit()"
                    form="filterForm" 
                    class="h-10 px-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-500 text-sm">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>최신순</option>
                <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>조회순</option>
                <option value="likes" {{ request('sort') == 'likes' ? 'selected' : '' }}>좋아요순</option>
            </select>
        </div>

        <!-- 버튼 영역 -->
        <div class="sm:flex items-center gap-2">
            @if(request('search'))
                <a href="{{ route('board.index') }}" 
                   class="inline-flex items-center justify-center h-10 px-6 bg-gray-500 text-white rounded-lg hover:bg-opacity-90 transition-all text-sm">
                    전체목록
                </a>
            @endif
            <a href="{{ route('board.create') }}" 
               class="inline-flex items-center justify-center h-10 px-6 bg-dark text-white rounded-lg hover:bg-opacity-90 transition-all text-sm">
                글쓰기
            </a>
        </div>
    </div>

    <!-- 숨겨진 폼 (필터용) -->
    <form id="filterForm" action="{{ route('board.index') }}" method="GET" class="hidden">
        <input type="hidden" name="search" value="{{ request('search') }}">
    </form>

    <!-- 게시글 그리드 -->
    @if($posts->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 px-4 bg-white rounded-xl shadow-sm">
            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">게시글이 없습니다</h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                @if(request('search'))
                    '{{ request('search') }}'에 대한 검색 결과가 없습니다.<br>
                    다른 검색어로 다시 시도해보세요.
                @else
                    아직 등록된 게시글이 없습니다.<br>
                    첫 번째 게시글의 주인공이 되어보세요!
                @endif
            </p>
            <a href="{{ route('board.create') }}" 
               class="inline-flex items-center justify-center px-6 h-10 bg-dark text-white rounded-lg hover:bg-opacity-90 transition-all text-sm">
                글쓰기
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow h-full">
                <a href="{{ route('board.show', $post->idx) }}" class="block h-full">
                    <div class="flex flex-col h-full">
                        <div class="bg-gray-50 flex items-center justify-center" style="height: 240px;">
                            <img src="{{ asset('storage/' . $post->mq_image) }}" 
                                 alt="게시글 이미지" 
                                 class="w-full h-full object-contain p-2">
                        </div>
                        <div class="p-4 flex-1 flex flex-col">
                            <div class="mb-2">
                                <span class="inline-block px-2 py-1 {{ $categoryColors[$post->mq_category] }} text-xs font-medium rounded-md">
                                    {{ $post->mq_category }}
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $post->mq_title }}</h3>
                            <p class="text-sm text-gray-500 mb-4 h-12 overflow-hidden">{{ strip_tags($post->mq_content) }}</p>
                            <div class="flex items-center justify-between text-sm text-gray-500 mt-auto">
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
    @endif

    <!-- 글쓰기 버튼 - 모바일 -->
    <div class="fixed bottom-4 right-4 sm:hidden z-10">
        <a href="{{ route('board.create') }}" 
           class="flex items-center justify-center w-14 h-14 bg-dark text-white rounded-full hover:bg-opacity-90 transition-all shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>

@push('scripts')
<script>
// 필터 변경 시 자동 서브밋
document.querySelectorAll('select[form="filterForm"]').forEach(select => {
    select.addEventListener('change', () => {
        document.getElementById('filterForm').submit();
    });
});
</script>
@endpush
@endsection 