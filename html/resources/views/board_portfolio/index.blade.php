@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">투자대가의 포트폴리오</h1>
    </div>

    <!-- 검색 영역 -->
    <div class="bg-primary rounded-xl p-4 mb-6">
        <form action="{{ route('board-portfolio.index') }}" method="GET">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           class="w-full h-12 px-4 rounded-xl border-0 focus:ring-2 focus:ring-yellow-500" 
                           placeholder="검색어를 입력하세요">
                    <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-cdark hover:text-cgray transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
                <a href="{{ route('board-portfolio.index') }}" class="inline-flex items-center justify-center h-12 px-5 bg-white text-cdark rounded-xl hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    초기화
                </a>
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
        <div class="flex items-center gap-2">
            @auth
                <a href="{{ route('board-portfolio.create') }}" 
                   class="inline-flex items-center justify-center h-10 px-4 bg-point text-cdark rounded-lg hover:bg-opacity-90 transition-all text-sm whitespace-nowrap">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    글쓰기
                </a>
            @endauth
        </div>
    </div>

    <!-- 숨겨진 폼 (필터용) -->
    <form id="filterForm" action="{{ route('board-portfolio.index') }}" method="GET" class="hidden">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="page" value="{{ request('page') }}">
    </form>

    <!-- 게시글 목록 (테이블 형태) -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if($posts->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">게시글이 없습니다</h3>
                <p class="text-sm text-text-dark text-center">
                    @if(request('search'))
                        '{{ request('search') }}'에 대한 검색 결과가 없습니다.<br>
                        다른 검색어로 다시 시도해보세요.
                    @else
                        아직 등록된 게시글이 없습니다.<br>
                        첫 번째 게시글의 주인공이 되어보세요!
                    @endif
                </p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">번호</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">카테고리</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">제목</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">작성자</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">등록일</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">조회</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">좋아요</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($posts as $post)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $post->idx }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $categoryColors[$post->mq_investor_code] ?? 'bg-blue-100 text-blue-800' }}">
                                    {{ $post->mq_investor_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('board-portfolio.show', $post->idx) }}" class="text-dark hover:text-yellow-500 font-medium">
                                    {{ $post->mq_title }}
                                </a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $post->mq_user_id }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y-m-d') : '' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">{{ $post->mq_view_cnt }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">{{ $post->mq_like_cnt }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-8 flex justify-center">
        {{ $posts->appends(request()->query())->links() }}
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