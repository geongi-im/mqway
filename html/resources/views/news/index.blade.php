@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">주요 뉴스</h1>
    </div>

    <!-- 검색 및 필터 영역 -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- 카테고리 필터 -->
            <div class="flex gap-2 overflow-x-auto pb-2 w-full md:w-auto">
                @foreach($categories as $category)
                <button class="px-4 py-2 rounded-full border border-gray-300 hover:bg-dark hover:text-white transition-colors whitespace-nowrap
                            {{ $category === '전체' ? 'bg-dark text-white' : 'bg-white text-dark' }}">
                    {{ $category }}
                </button>
                @endforeach
            </div>

            <!-- 검색창 -->
            <div class="relative w-full md:w-96">
                <input type="text" 
                       placeholder="뉴스 검색" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark">
                <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-dark">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- 뉴스 목록 -->
    <div class="space-y-6">
        @foreach($news as $item)
        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <!-- 상단 메타 정보 -->
                <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                    <span class="{{ $categoryColors[$item->mq_category] }} px-3 py-1 rounded-full">
                        {{ $item->mq_category }}
                    </span>
                    <time datetime="{{ $item->mq_reg_date }}">
                        {{ date('Y.m.d H:i', strtotime($item->mq_reg_date)) }}
                    </time>
                    <span>·</span>
                    <span>{{ $item->mq_company }}</span>
                </div>

                <!-- 제목 -->
                <h2 class="text-xl font-bold mb-3 text-dark hover:text-dark/80">
                    <a href="{{ $item->mq_source_url }}" target="_blank">{{ $item->mq_title }}</a>
                </h2>

                <!-- 내용 미리보기 -->
                <p class="text-gray-600 mb-4">
                    {{ Str::limit($item->mq_content, 200) }}
                </p>
            </div>
        </article>
        @endforeach
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-12">
        {{ $news->links() }}
    </div>
</div>
@endsection 