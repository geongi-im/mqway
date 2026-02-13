@extends('layouts.app')

@section('content')
<!-- ===== Hero Section ===== -->
<section class="relative pt-32 pb-24 overflow-hidden bg-[#3D4148]">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#FF4D4D] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10 text-center animate-slideUp">
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-sm font-medium mb-4 backdrop-blur-md">
            💡 Insight & Trends
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight tracking-tight">
            투자 인사이트
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            시장을 읽는 새로운 시각, 투자 영감을 드립니다.<br class="hidden md:block">
            변화하는 트렌드 속에서 투자의 기회를 발견하세요.
        </p>
    </div>
</section>

<!-- ===== Search Section ===== -->
<div class="container mx-auto px-4 -mt-10 relative z-20 mb-10 animate-slideUp" style="animation-delay: 0.2s;">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 max-w-4xl mx-auto">
        <form action="{{ route('board-insights.index') }}" method="GET">
            <!-- 현재 필터 상태 유지 -->
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            
            <div class="flex items-center gap-2">
                <!-- 검색 입력창 -->
                <div class="relative flex-grow">
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           class="w-full h-12 pl-4 pr-12 bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#9F5AFF] focus:border-transparent transition-all placeholder-gray-400" 
                           placeholder="관심있는 주제나 키워드를 검색해보세요">
                    <button type="submit" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#9F5AFF] transition-colors" title="검색">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- 초기화 -->
                @if(request('search') || request('category') || (request('sort') && request('sort') !== 'latest'))
                <a href="{{ route('board-insights.index') }}" class="h-10 w-10 flex-shrink-0 flex items-center justify-center text-gray-400 hover:text-[#FF4D4D] hover:bg-red-50 rounded-lg transition-all" title="초기화">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
                @endif

                <!-- 글쓰기 -->
                @auth
                <a href="{{ route('board-insights.create') }}" 
                   class="h-10 w-10 flex-shrink-0 flex items-center justify-center text-gray-400 hover:text-[#9F5AFF] hover:bg-[#9F5AFF]/10 rounded-lg transition-all" title="새 글 작성">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                @endauth
            </div>
        </form>
    </div>
</div>

<!-- ===== Filter Toolbar ===== -->
<div class="container mx-auto px-4 mb-8 max-w-7xl animate-slideUp" style="animation-delay: 0.3s;">
    <form action="{{ route('board-insights.index') }}" method="GET" id="filterForm">
        <!-- 검색어 상태 유지 -->
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        <div class="flex flex-row items-center justify-between gap-4">
            <!-- 좌측: 카테고리 필터 -->
            <div class="relative">
                <select name="category" 
                        onchange="document.getElementById('filterForm').submit()"
                        class="appearance-none h-9 pl-3 pr-9 bg-white border border-gray-200 text-gray-600 text-sm rounded-full focus:ring-2 focus:ring-[#9F5AFF]/30 focus:border-[#9F5AFF] hover:border-gray-300 transition-all cursor-pointer font-medium">
                    <option value="">전체 카테고리</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <!-- 우측: 정렬 -->
            <div class="relative">
                <select name="sort" 
                        onchange="document.getElementById('filterForm').submit()"
                        class="appearance-none h-9 pl-3 pr-9 bg-white border border-gray-200 text-gray-600 text-sm rounded-full focus:ring-2 focus:ring-[#9F5AFF]/30 focus:border-[#9F5AFF] hover:border-gray-300 transition-all cursor-pointer font-medium">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>최신순</option>
                    <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>조회순</option>
                    <option value="likes" {{ request('sort') == 'likes' ? 'selected' : '' }}>좋아요순</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- ===== List View ===== -->
<div class="container mx-auto px-4 pb-20 max-w-7xl animate-slideUp" style="animation-delay: 0.4s;">
    @if($posts->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 px-4 bg-white rounded-3xl shadow-sm border border-gray-100 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">아직 등록된 게시글이 없습니다</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                @if(request('search'))
                    '{{ request('search') }}'에 대한 검색 결과가 없습니다.<br>
                    다른 키워드로 검색해보시거나 필터를 변경해보세요.
                @else
                    곧 유익한 인사이트로 채워질 예정입니다.<br>
                    잠시만 기다려주세요!
                @endif
            </p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- 테이블 헤더 (데스크탑) -->
            <div class="hidden md:grid md:grid-cols-12 gap-4 px-6 py-3.5 bg-gray-50/80 border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                <div class="col-span-1 text-center">번호</div>
                <div class="col-span-2">카테고리</div>
                <div class="col-span-5">제목</div>
                <div class="col-span-1 text-center">작성자</div>
                <div class="col-span-1 text-center">등록일</div>
                <div class="col-span-1 text-center">조회</div>
                <div class="col-span-1 text-center">좋아요</div>
            </div>

            <!-- 게시글 목록 -->
            <div class="divide-y divide-gray-100">
                @foreach($posts as $post)
                    <a href="{{ route('board-insights.show', $post->idx) }}" 
                       class="block group hover:bg-[#F8F7FF] transition-colors duration-200">
                        
                        <!-- 데스크탑 레이아웃 -->
                        <div class="hidden md:grid md:grid-cols-12 gap-4 px-6 py-4 items-center">
                            <!-- 번호 -->
                            <div class="col-span-1 text-center text-sm text-gray-400 font-medium">
                                {{ $post->idx }}
                            </div>

                            <!-- 카테고리 -->
                            <div class="col-span-2">
                                <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full {{ $categoryColors[$post->mq_category] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $post->mq_category }}
                                </span>
                            </div>

                            <!-- 제목 -->
                            <div class="col-span-5">
                                <h3 class="text-sm font-semibold text-gray-800 group-hover:text-[#4ECDC4] transition-colors truncate">
                                    {{ $post->mq_title }}
                                    @if(!empty($post->mq_image) && is_array($post->mq_image) && count($post->mq_image) > 0)
                                        <svg class="w-4 h-4 inline-block text-gray-300 ml-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @endif
                                </h3>
                            </div>

                            <!-- 작성자 -->
                            <div class="col-span-1 text-center text-xs text-gray-400 font-medium truncate">
                                {{ $post->user->mq_user_id ?? $post->mq_user_id ?? '익명' }}
                            </div>

                            <!-- 등록일 -->
                            <div class="col-span-1 text-center text-xs text-gray-400">
                                {{ $post->mq_reg_date ? $post->mq_reg_date->format('Y.m.d') : '' }}
                            </div>

                            <!-- 조회수 -->
                            <div class="col-span-1 text-center">
                                <span class="inline-flex items-center text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5 mr-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ number_format($post->mq_view_cnt) }}
                                </span>
                            </div>

                            <!-- 좋아요 -->
                            <div class="col-span-1 text-center">
                                <span class="inline-flex items-center text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5 mr-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    {{ number_format($post->mq_like_cnt) }}
                                </span>
                            </div>
                        </div>

                        <!-- 모바일 레이아웃 -->
                        <div class="md:hidden px-5 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full {{ $categoryColors[$post->mq_category] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $post->mq_category }}
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-800 group-hover:text-[#4ECDC4] transition-colors line-clamp-1 mb-1.5">
                                        {{ $post->mq_title }}
                                        @if(!empty($post->mq_image) && is_array($post->mq_image) && count($post->mq_image) > 0)
                                            <svg class="w-3.5 h-3.5 inline-block text-gray-300 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        @endif
                                    </h3>
                                    <div class="flex items-center gap-3 text-[11px] text-gray-400">
                                        <span>{{ $post->user->mq_user_id ?? $post->mq_user_id ?? '익명' }}</span>
                                        <span class="text-gray-200">·</span>
                                        <span>{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y.m.d') : '' }}</span>
                                        <span class="text-gray-200">·</span>
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-0.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ number_format($post->mq_view_cnt) }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-0.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            {{ number_format($post->mq_like_cnt) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- 페이지네이션 -->
        <div class="mt-12 flex justify-center">
            {{ $posts->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
