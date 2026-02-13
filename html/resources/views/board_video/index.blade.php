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
            📺 Economy Video
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight tracking-tight">
            쉽게 보는 경제
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            복잡한 경제 개념을 영상으로 쉽고 재미있게 배워보세요.<br class="hidden md:block">
            엄선된 영상 콘텐츠가 여러분의 금융 지능을 높여드립니다.
        </p>
    </div>
</section>

<!-- ===== Search Section ===== -->
<div class="container mx-auto px-4 -mt-10 relative z-20 mb-10 animate-slideUp" style="animation-delay: 0.2s;">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 max-w-4xl mx-auto">
        <form action="{{ route('board-video.index') }}" method="GET">
            <!-- 현재 필터 상태 유지 -->
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            
            <div class="flex items-center gap-2">
                <!-- 검색 입력창 (내부에 돋보기 아이콘 포함) -->
                <div class="relative flex-grow">
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           class="w-full h-12 pl-4 pr-12 bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#9F5AFF] focus:border-transparent transition-all placeholder-gray-400" 
                           placeholder="영상 주제나 키워드를 검색해보세요">
                    <button type="submit" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#9F5AFF] transition-colors" title="검색">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- 초기화 (검색/필터 활성 시에만 노출) -->
                @if(request('search') || request('category') || (request('sort') && request('sort') !== 'latest'))
                <a href="{{ route('board-video.index') }}" class="h-10 w-10 flex-shrink-0 flex items-center justify-center text-gray-400 hover:text-[#FF4D4D] hover:bg-red-50 rounded-lg transition-all" title="초기화">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
                @endif

                <!-- 글쓰기 (로그인 시에만) -->
                @auth
                <a href="{{ route('board-video.create') }}" 
                   class="h-10 w-10 flex-shrink-0 flex items-center justify-center text-gray-400 hover:text-[#9F5AFF] hover:bg-[#9F5AFF]/10 rounded-lg transition-all" title="영상 등록">
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
    <form action="{{ route('board-video.index') }}" method="GET" id="filterForm">
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

<!-- ===== Content Grid ===== -->
<div class="container mx-auto px-4 pb-20 max-w-7xl animate-slideUp" style="animation-delay: 0.4s;">
    @if($posts->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 px-4 bg-white rounded-3xl shadow-sm border border-gray-100 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">등록된 영상이 없습니다</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                @if(request('search'))
                    '{{ request('search') }}'에 대한 검색 결과가 없습니다.<br>
                    다른 키워드로 검색해보세요.
                @else
                    곧 유익한 경제 영상으로 채워질 예정입니다.<br>
                    조금만 기다려주세요!
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group flex flex-col h-full">
                    <a href="{{ route('board-video.show', $post->idx) }}" class="block relative overflow-hidden aspect-video bg-black group">
                        @if(isset($post->mq_video_url) && !empty($post->mq_video_url))
                            <!-- 비디오 썸네일 (YouTube 등) -->
                            <img src="{{ $post->mq_image }}" 
                                 alt="{{ $post->mq_title }}"
                                 class="w-full h-full object-cover opacity-90 group-hover:opacity-75 transition-opacity duration-300">
                            
                            <!-- 재생 버튼 오버레이 -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 border border-white/40">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-4 h-4 text-[#FF4D4D] ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @else
                            <img src="{{ $post->mq_image }}" 
                                 alt="{{ $post->mq_title }}"
                                 class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity">
                        @endif

                        <div class="absolute top-4 left-4">
                            <span class="inline-block px-3 py-1 text-xs font-bold rounded-full border border-white/10 {{ $categoryColors[$post->mq_category] ?? 'bg-black/60 text-white' }}">
                                {{ $post->mq_category }}
                            </span>
                        </div>
                    </a>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <a href="{{ route('board-video.show', $post->idx) }}" class="block mb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-[#FF4D4D] transition-colors">
                                {{ $post->mq_title }}
                            </h3>
                            <p class="text-gray-500 text-sm line-clamp-2">
                                {!! strip_tags($post->mq_content) !!}
                            </p>
                        </a>
                        
                        <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400 font-medium">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ $post->user->mq_user_id ?? '익명' }}
                                </span>
                                <span class="text-gray-200">·</span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $post->mq_reg_date ? $post->mq_reg_date->format('Y.m.d') : '' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="flex items-center hover:text-[#4ECDC4] transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ number_format($post->mq_view_cnt) }}
                                </span>
                                <span class="flex items-center hover:text-[#FF4D4D] transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    {{ number_format($post->mq_like_cnt) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- 페이지네이션 -->
        <div class="mt-12 flex justify-center">
            {{ $posts->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection