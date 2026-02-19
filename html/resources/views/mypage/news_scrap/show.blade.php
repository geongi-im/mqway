@extends('layouts.app')

@section('content')
<!-- ===== Hero Background ===== -->
<div class="relative bg-[#3D4148] pb-32 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>

    <div class="container mx-auto px-4 pt-28 pb-8 relative z-10 max-w-4xl animate-slideUp">
        <a href="{{ route('mypage.news-scrap.index') }}" class="inline-flex items-center text-gray-400 hover:text-white mb-6 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center mr-2 group-hover:bg-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            목록으로 돌아가기
        </a>
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-xs font-medium mb-4 backdrop-blur-md">
            📰 News Scrap
        </span>
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-4 leading-tight tracking-tight">{{ $scrap->mq_title }}</h1>
        <div class="flex flex-wrap items-center text-gray-400 text-sm gap-4">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ $scrap->mq_reg_date ? $scrap->mq_reg_date->format('Y.m.d H:i') : '' }}</span>
            </div>
            @if($scrap->mq_update_date)
            <div class="flex items-center text-gray-500">
                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>수정: {{ $scrap->mq_update_date->format('Y.m.d H:i') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ===== Content Section ===== -->
<div class="container mx-auto px-4 -mt-20 relative z-20 pb-20">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-4xl mx-auto animate-slideUp" style="animation-delay: 0.3s;">
        <div class="p-8 md:p-10">

            <!-- 원문 링크 -->
            <div class="mb-8 text-center">
                <a href="{{ $scrap->mq_url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    뉴스 원문 보러가기
                </a>
            </div>

            <!-- 썸네일 이미지 -->
            @if($scrap->hasThumbnail())
            <div class="mb-8">
                <img src="{{ $scrap->getThumbnailUrl() }}"
                     alt="{{ $scrap->mq_title }}"
                     class="w-full max-w-2xl mx-auto rounded-xl shadow-md"
                     onerror="this.parentElement.style.display='none';">
            </div>
            @endif

            <!-- 뉴스를 선택한 이유 -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#FF4D4D] to-[#e03e3e] flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-[#2D3047]">뉴스를 선택한 이유</h2>
                </div>
                <div class="prose max-w-none bg-gray-50 rounded-xl p-6 border border-gray-100">
                    {!! $scrap->mq_reason !!}
                </div>
            </div>

            <!-- 새로 알게된 용어 -->
            @if($scrap->mq_new_terms)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#4ECDC4] to-[#2AA9A0] flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-[#2D3047]">새로 알게된 용어</h2>
                </div>
                <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-6">
                    <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $scrap->mq_new_terms }}</p>
                </div>
            </div>
            @endif

            <!-- 버튼 영역 -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-100">
                <!-- 좌측 버튼 -->
                <a href="{{ route('mypage.news-scrap.index') }}"
                   class="inline-flex items-center justify-center h-11 px-5 border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all text-gray-600 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    목록
                </a>

                <!-- 우측 버튼 그룹 -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('mypage.news-scrap.edit', $scrap->idx) }}"
                       class="inline-flex items-center justify-center h-11 px-5 border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all text-gray-600 text-sm font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        수정
                    </a>
                    <form action="{{ route('mypage.news-scrap.destroy', $scrap->idx) }}"
                          method="POST"
                          onsubmit="return confirmDelete()"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center h-11 px-5 bg-gradient-to-r from-[#FF4D4D] to-[#e03e3e] text-white rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all text-sm font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            삭제
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete() {
    return confirm('정말 삭제하시겠습니까?\n삭제된 스크랩은 복구할 수 없습니다.');
}
</script>

<style>
/* CKEditor 콘텐츠 스타일 */
.prose {
    color: #374151;
    line-height: 1.75;
}

.prose p {
    margin-bottom: 1em;
}

.prose h1, .prose h2, .prose h3 {
    margin-top: 1.5em;
    margin-bottom: 0.75em;
    font-weight: 600;
    color: #111827;
}

.prose h1 {
    font-size: 1.875rem;
}

.prose h2 {
    font-size: 1.5rem;
}

.prose h3 {
    font-size: 1.25rem;
}

.prose ul, .prose ol {
    margin-left: 1.5em;
    margin-bottom: 1em;
}

.prose li {
    margin-bottom: 0.5em;
}

.prose a {
    color: #2563eb;
    text-decoration: underline;
}

.prose a:hover {
    color: #1d4ed8;
}

.prose img {
    max-width: 100%;
    height: auto;
    border-radius: 0.75rem;
    margin: 1.5em auto;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.prose blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1em;
    margin-left: 0;
    margin-right: 0;
    font-style: italic;
    color: #6b7280;
}

.prose code {
    background-color: #f3f4f6;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-size: 0.875em;
    font-family: 'Courier New', monospace;
}

.prose pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1em;
    border-radius: 0.75rem;
    overflow-x: auto;
}

.prose pre code {
    background-color: transparent;
    padding: 0;
    color: inherit;
}

.prose table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5em 0;
}

.prose th, .prose td {
    border: 1px solid #e5e7eb;
    padding: 0.75em;
    text-align: left;
}

.prose th {
    background-color: #f9fafb;
    font-weight: 600;
}
</style>
@endpush
@endsection
