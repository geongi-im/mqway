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
            📚 Knowledge Base
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight tracking-tight">
            추천 콘텐츠
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            엄선된 금융 지식과 투자 인사이트를 만나보세요.<br class="hidden md:block">
            성공적인 투자를 위한 양질의 정보를 큐레이션해 드립니다.
        </p>
    </div>
</section>

<!-- ===== Search & Filter Section ===== -->
<div class="container mx-auto px-4 -mt-10 relative z-20 mb-12 animate-slideUp" style="animation-delay: 0.2s;">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 max-w-5xl mx-auto">
        <form action="{{ route('board-content.index') }}" method="GET" class="space-y-6">
            <!-- 검색바 -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#4ECDC4] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           class="w-full h-12 pl-12 pr-4 bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent transition-all placeholder-gray-400" 
                           placeholder="관심있는 주제나 키워드를 검색해보세요">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="h-12 px-8 bg-[#2D3047] text-white font-medium rounded-xl hover:bg-[#3D4148] transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 whitespace-nowrap">
                        검색하기
                    </button>
                    <a href="{{ route('board-content.index') }}" class="h-12 w-12 flex items-center justify-center bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all" title="초기화">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- 필터 옵션 -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-100">
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                    <select name="category" 
                            onchange="this.form.submit()"
                            class="h-10 pl-3 pr-8 bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent hover:border-gray-300 transition-colors cursor-pointer">
                        <option value="">전체 카테고리</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="sort" 
                            onchange="this.form.submit()"
                            class="h-10 pl-3 pr-8 bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent hover:border-gray-300 transition-colors cursor-pointer">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>최신순</option>
                        <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>조회순</option>
                        <option value="likes" {{ request('sort') == 'likes' ? 'selected' : '' }}>좋아요순</option>
                    </select>
                </div>

                @auth
                <a href="{{ route('board-content.create') }}" 
                   class="inline-flex items-center justify-center h-10 px-6 bg-[#4ECDC4]/10 text-[#2AA9A0] font-medium rounded-lg hover:bg-[#4ECDC4]/20 transition-all text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    새 글 작성
                </a>
                @endauth
            </div>
        </form>
    </div>
</div>

<!-- ===== Content Grid ===== -->
<div class="container mx-auto px-4 pb-20 max-w-7xl animate-slideUp" style="animation-delay: 0.4s;">
    @if($posts->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 px-4 bg-white rounded-3xl shadow-sm border border-gray-100 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">아직 등록된 콘텐츠가 없습니다</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                @if(request('search'))
                    '{{ request('search') }}'에 대한 검색 결과가 없습니다.<br>
                    다른 키워드로 검색해보시거나 필터를 변경해보세요.
                @else
                    곧 유익한 콘텐츠로 채워질 예정입니다.<br>
                    잠시만 기다려주세요!
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group flex flex-col h-full">
                    <a href="{{ route('board-content.show', $post->idx) }}" class="block relative overflow-hidden aspect-video bg-gray-100">
                        @if($post->mq_image)
                            <img src="{{ asset($post->mq_image) }}"
                                 alt="{{ $post->mq_title }}"
                                 class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                                <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs font-medium opacity-50">No Image</span>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="inline-block px-3 py-1 text-xs font-bold rounded-full shadow-sm {{ $categoryColors[$post->mq_category] ?? 'bg-white/90 text-[#2D3047] border border-gray-100' }}">
                                {{ $post->mq_category }}
                            </span>
                        </div>
                    </a>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <a href="{{ route('board-content.show', $post->idx) }}" class="block mb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-[#4ECDC4] transition-colors">
                                {{ $post->mq_title }}
                            </h3>
                            <p class="text-gray-500 text-sm line-clamp-2">
                                {!! strip_tags($post->mq_content) !!}
                            </p>
                        </a>
                        
                        <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400 font-medium">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $post->mq_reg_date ? $post->mq_reg_date->format('Y.m.d') : '' }}
                            </span>
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