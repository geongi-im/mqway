@extends('layouts.app')

@section('content')
<!-- ===== Hero Section ===== -->
<section class="relative pt-32 pb-24 overflow-hidden bg-[#3D4148]">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#9F5AFF] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10 text-center animate-slideUp">
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-sm font-medium mb-4 backdrop-blur-md">
            🎨 Insight Cartoon
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight tracking-tight">
            인사이트 만화
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            어려운 경제 용어와 트렌드를 웹툰으로 만나보세요.<br class="hidden md:block">
            한 컷, 한 컷 의미 있는 인사이트를 전달합니다.
        </p>
    </div>
</section>

<!-- ===== Search & Filter Section ===== -->
<div class="container mx-auto px-4 -mt-10 relative z-20 mb-12 animate-slideUp" style="animation-delay: 0.2s;">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 max-w-5xl mx-auto">
        <form action="{{ route('board-cartoon.index') }}" method="GET" class="space-y-6">
            <!-- 검색바 -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#9F5AFF] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           class="w-full h-12 pl-12 pr-4 bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#9F5AFF] focus:border-transparent transition-all placeholder-gray-400" 
                           placeholder="만화 제목이나 내용을 검색해보세요">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="h-12 px-8 bg-[#2D3047] text-white font-medium rounded-xl hover:bg-[#3D4148] transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 whitespace-nowrap">
                        검색하기
                    </button>
                    <a href="{{ route('board-cartoon.index') }}" class="h-12 w-12 flex items-center justify-center bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all" title="초기화">
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
                            class="h-10 pl-3 pr-8 bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-2 focus:ring-[#9F5AFF] focus:border-transparent hover:border-gray-300 transition-colors cursor-pointer">
                        <option value="">전체 카테고리</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="sort" 
                            onchange="this.form.submit()"
                            class="h-10 pl-3 pr-8 bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-2 focus:ring-[#9F5AFF] focus:border-transparent hover:border-gray-300 transition-colors cursor-pointer">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>최신순</option>
                        <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>조회순</option>
                        <option value="likes" {{ request('sort') == 'likes' ? 'selected' : '' }}>좋아요순</option>
                    </select>
                </div>

                @auth
                <a href="{{ route('board-cartoon.create') }}" 
                   class="inline-flex items-center justify-center h-10 px-6 bg-[#9F5AFF]/10 text-[#7B2CBF] font-medium rounded-lg hover:bg-[#9F5AFF]/20 transition-all text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    만화 등록
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">등록된 만화가 없습니다</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                @if(request('search'))
                    '{{ request('search') }}'에 대한 검색 결과가 없습니다.<br>
                    다른 검색어로 다시 시도해보세요.
                @else
                    곧 재미있는 경제 만화로 채워질 예정입니다.<br>
                    기대해주세요!
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($posts as $post)
                <article class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group flex flex-col h-full overflow-hidden border border-gray-100">
                    <a href="{{ route('board-cartoon.show', $post->idx) }}" class="block relative aspect-square bg-[#F8F9FA] overflow-hidden">
                        @if($post->hasThumbnail())
                            <img src="{{ $post->getThumbnailUrl() }}"
                                 alt="{{ $post->mq_title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs font-medium opacity-50">No Image</span>
                            </div>
                        @endif
                        
                        <!-- 그라데이션 오버레이 (하단 텍스트 가독성) -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <div class="absolute top-3 left-3">
                            <span class="inline-block px-2 py-1 text-[10px] font-bold rounded-md shadow-sm {{ $categoryColors[$post->mq_category] ?? 'bg-white/90 text-[#2D3047]' }}">
                                {{ $post->mq_category }}
                            </span>
                        </div>
                    </a>
                    
                    <div class="p-5 flex-1 flex flex-col">
                        <a href="{{ route('board-cartoon.show', $post->idx) }}" class="block mb-2">
                            <h3 class="text-lg font-bold text-gray-900 mb-1 leading-snug line-clamp-2 group-hover:text-[#9F5AFF] transition-colors">
                                {{ $post->mq_title }}
                            </h3>
                        </a>
                        
                        <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400 font-medium">
                            <span>{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y.m.d') : '' }}</span>
                            <div class="flex items-center gap-3">
                                <span class="flex items-center hover:text-[#9F5AFF] transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ number_format($post->mq_view_cnt) }}
                                </span>
                                <span class="flex items-center hover:text-[#FF4D4D] transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

@push('scripts')
<script>
// 필터 변경 시 자동 서브밋
document.querySelectorAll('select[name="category"], select[name="sort"]').forEach(select => {
    select.addEventListener('change', () => {
        select.closest('form').submit();
    });
});
</script>
@endpush