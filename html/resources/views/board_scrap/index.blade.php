@extends('layouts.app')

@section('content')
<!-- ===== Hero Section ===== -->
<section class="relative pt-24 pb-14 overflow-hidden bg-[#3D4148]">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10 text-center animate-slideUp">
        @if($mine)
        <a href="{{ route('mypage.index') }}" class="inline-flex items-center text-gray-400 hover:text-white mb-6 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center mr-2 group-hover:bg-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            마이페이지로 돌아가기
        </a>
        <br />
        @endif
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-sm font-medium mb-4 backdrop-blur-md">
            📰 News Scrap
        </span>
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-3 leading-tight tracking-tight">
            {{ $mine ? '내 뉴스 스크랩' : '뉴스 스크랩' }}
        </h1>
        {{-- 내 스크랩 탭은 아래 등급 카드가 자리를 쓰므로 설명 문구를 줄인다 --}}
        <p class="text-base md:text-lg text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            @if($mine)
                기록이 쌓일수록 등급이 올라갑니다.
            @else
                다른 회원들이 공유한 뉴스와 그 뉴스를 고른 이유를 살펴보세요.
            @endif
        </p>
    </div>
</section>

<!-- ===== Search Section ===== -->
<div class="container mx-auto px-4 -mt-8 relative z-20 mb-6 animate-slideUp" style="animation-delay: 0.2s;">
    <div class="bg-white rounded-2xl shadow-xl p-3 md:p-4 max-w-3xl mx-auto">
        <form action="{{ route('board-scrap.index') }}" method="GET">
            @if($mine)
                <input type="hidden" name="mine" value="1">
            @endif
            @if($visibility)
                <input type="hidden" name="visibility" value="{{ $visibility }}">
            @endif
            @if($sort !== 'latest')
                <input type="hidden" name="sort" value="{{ $sort }}">
            @endif

            <div class="flex items-center gap-2">
                <!-- 검색 입력창 -->
                <div class="relative flex-grow">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="w-full h-10 pl-4 pr-12 bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#9F5AFF] focus:border-transparent transition-all placeholder-gray-400"
                           placeholder="제목, 내용, 용어로 검색해보세요">
                    <button type="submit" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#9F5AFF] transition-colors" title="검색">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- 초기화 (검색 활성 시에만 노출) -->
                @if(request('search'))
                <a href="{{ route('board-scrap.index', array_filter(['mine' => $mine ? 1 : null, 'visibility' => $visibility])) }}" class="h-10 w-10 flex-shrink-0 flex items-center justify-center text-gray-400 hover:text-[#FF4D4D] hover:bg-red-50 rounded-lg transition-all" title="초기화">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
                @endif

                <!-- 글쓰기 -->
                @auth
                <a href="{{ route('board-scrap.create') }}"
                   class="group flex-shrink-0 inline-flex items-center justify-center gap-2 h-10 px-4 bg-point1 text-white font-bold rounded-xl shadow-lg shadow-point1/25 hover:bg-[#E63E3E] hover:shadow-xl hover:shadow-point1/35 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300"
                   title="글쓰기">
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline text-sm tracking-wide">글쓰기</span>
                </a>
                @endauth
            </div>
        </form>
    </div>
</div>

@if($mine && $expSummary)
@include('board_scrap._streak_card', ['expSummary' => $expSummary])
@endif

<!-- ===== Tabs ===== -->
<div class="container mx-auto px-4 mb-6 max-w-7xl animate-slideUp" style="animation-delay: 0.25s;">
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('board-scrap.index', array_filter(['search' => request('search'), 'sort' => $sort !== 'latest' ? $sort : null])) }}"
           class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ !$mine ? 'bg-[#2D3047] text-white shadow-lg' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-300' }}">
            전체 공개 스크랩
        </a>
        @auth
        <a href="{{ route('board-scrap.index', array_filter(['mine' => 1, 'search' => request('search'), 'sort' => $sort !== 'latest' ? $sort : null])) }}"
           class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $mine ? 'bg-[#2D3047] text-white shadow-lg' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-300' }}">
            내 스크랩
        </a>
        @endauth

        @if($mine && $myCounts)
        <span class="mx-1 h-6 w-px bg-gray-200"></span>
        <a href="{{ route('board-scrap.index', array_filter(['mine' => 1, 'search' => request('search')])) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ !$visibility ? 'bg-gray-100 text-gray-700' : 'text-gray-400 hover:bg-gray-50' }}">
            전체 {{ $myCounts['public'] + $myCounts['private'] }}
        </a>
        <a href="{{ route('board-scrap.index', array_filter(['mine' => 1, 'visibility' => 'public', 'search' => request('search')])) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $visibility === 'public' ? 'bg-emerald-100 text-emerald-700' : 'text-gray-400 hover:bg-gray-50' }}">
            공개 {{ $myCounts['public'] }}
        </a>
        <a href="{{ route('board-scrap.index', array_filter(['mine' => 1, 'visibility' => 'private', 'search' => request('search')])) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $visibility === 'private' ? 'bg-gray-200 text-gray-700' : 'text-gray-400 hover:bg-gray-50' }}">
            나만보기 {{ $myCounts['private'] }}
        </a>
        @endif
    </div>
</div>

<!-- ===== Filter Toolbar ===== -->
<div class="container mx-auto px-4 mb-8 max-w-7xl animate-slideUp" style="animation-delay: 0.3s;">
    <div class="flex items-center justify-between gap-4">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-50 text-blue-600 flex-shrink-0">
            총 {{ $scraps->total() }}개
        </span>

        <!-- 정렬 -->
        <div class="flex items-center gap-1 text-sm">
            @foreach(['latest' => '최신순', 'likes' => '좋아요순', 'views' => '조회순'] as $key => $label)
            <a href="{{ route('board-scrap.index', array_filter([
                    'mine' => $mine ? 1 : null,
                    'visibility' => $visibility,
                    'search' => request('search'),
                    'sort' => $key !== 'latest' ? $key : null,
               ])) }}"
               class="px-3 py-1.5 rounded-lg font-medium transition-all {{ $sort === $key ? 'bg-[#2D3047] text-white' : 'text-gray-500 hover:bg-white' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>
</div>

<!-- ===== Content Grid ===== -->
<div class="container mx-auto px-4 pb-20 max-w-7xl animate-slideUp" style="animation-delay: 0.4s;">
    @if($scraps->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 px-4 bg-white rounded-3xl shadow-sm border border-gray-100 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">
                @if(request('search'))
                    검색 결과가 없습니다
                @elseif($mine)
                    아직 스크랩한 뉴스가 없습니다
                @else
                    아직 공개된 스크랩이 없습니다
                @endif
            </h3>
            <p class="text-gray-500 max-w-md mx-auto">
                @if(request('search'))
                    '{{ request('search') }}'에 대한 검색 결과가 없습니다.<br>
                    다른 키워드로 검색해보시거나 필터를 변경해보세요.
                @elseif($mine)
                    관심있는 경제 뉴스를 스크랩해보세요!<br>
                    첫 번째 뉴스의 주인공이 되어보세요!
                @else
                    첫 번째로 뉴스 스크랩을 공유해보세요!<br>
                    글을 쓸 때 '공개' 옵션을 켜면 이 목록에 올라옵니다.
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($scraps as $scrap)
                <article class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group flex flex-col h-full">
                    <a href="{{ route('board-scrap.show', $scrap->idx) }}" class="block relative overflow-hidden aspect-video bg-gray-100">
                        @if($scrap->hasThumbnail())
                            <img src="{{ $scrap->getThumbnailUrl() }}"
                                 alt="{{ $scrap->mq_title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 onerror="this.parentElement.innerHTML='<div class=\'absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-gray-50\'><svg class=\'w-12 h-12 mb-2 opacity-50\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg><span class=\'text-xs font-medium opacity-50\'>No Image</span></div>';">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                                <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs font-medium opacity-50">No Image</span>
                            </div>
                        @endif

                        <!-- 공개 여부 배지 (내 스크랩 목록에서만) -->
                        @if($mine)
                            @if($scrap->isPublic())
                            <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                공개
                            </span>
                            @else
                            <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-700 text-white shadow-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                나만보기
                            </span>
                            @endif
                        @endif
                    </a>

                    <div class="p-6 flex-1 flex flex-col">
                        <a href="{{ route('board-scrap.show', $scrap->idx) }}" class="block mb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-[#4ECDC4] transition-colors">
                                {{ $scrap->mq_title }}
                            </h3>
                        </a>

                        <!-- 작성자 · 지표 -->
                        <div class="flex items-center justify-between text-xs text-gray-400 font-medium mb-3">
                            <span class="inline-flex items-center gap-1.5 min-w-0">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="truncate">{{ $scrap->getAuthorName() }}</span>
                            </span>
                            <span class="inline-flex items-center gap-3 flex-shrink-0">
                                <span class="inline-flex items-center gap-1" title="좋아요">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    {{ number_format($scrap->mq_like_cnt) }}
                                </span>
                                <span class="inline-flex items-center gap-1" title="조회수">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ number_format($scrap->mq_view_cnt) }}
                                </span>
                            </span>
                        </div>

                        <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400 font-medium">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @php $listedDate = $mine ? $scrap->mq_reg_date : ($scrap->mq_public_date ?: $scrap->mq_reg_date); @endphp
                                {{ $listedDate ? $listedDate->format('Y.m.d') : '' }}
                            </span>
                            <a href="{{ $scrap->mq_url }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors font-medium">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                원문 보기
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- 페이지네이션 -->
        <div class="mt-12 flex justify-center">
            {{ $scraps->appends(request()->query())->links() }}
        </div>
    @endif
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
