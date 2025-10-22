@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <!-- 제목 -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $scrap->mq_title }}</h1>

            <!-- 메타 정보 -->
            <div class="flex flex-wrap items-center text-gray-600 text-sm mb-6 gap-3 pb-6 border-b">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ $scrap->mq_reg_date ? $scrap->mq_reg_date->format('Y.m.d H:i') : '' }}</span>
                </div>
                @if($scrap->mq_update_date)
                <div class="flex items-center text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>수정: {{ $scrap->mq_update_date->format('Y.m.d H:i') }}</span>
                </div>
                @endif
            </div>

            <!-- 원문 링크 -->
            <div class="mb-6 text-center">
                <a href="{{ $scrap->mq_url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors shadow-md hover:shadow-lg">
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
                     class="w-full max-w-2xl mx-auto rounded-lg shadow-md"
                     onerror="this.parentElement.style.display='none';">
            </div>
            @endif

            <!-- 뉴스를 선택한 이유 -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-point1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    뉴스를 선택한 이유
                </h2>
                <div class="prose max-w-none bg-gray-50 rounded-lg p-6">
                    {!! $scrap->mq_reason !!}
                </div>
            </div>

            <!-- 새로 알게된 용어 -->
            @if($scrap->mq_new_terms)
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-point1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    새로 알게된 용어
                </h2>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-6">
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $scrap->mq_new_terms }}</p>
                </div>
            </div>
            @endif

            <!-- 버튼 영역 -->
            <div class="flex justify-between items-center pt-6 border-t">
                <!-- 좌측 버튼 -->
                <a href="{{ route('mypage.news-scrap.index') }}"
                   class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    목록
                </a>

                <!-- 우측 버튼 그룹 -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('mypage.news-scrap.edit', $scrap->idx) }}"
                       class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="inline-flex items-center justify-center h-10 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-200 transition-all text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    border-radius: 0.5rem;
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
    border-radius: 0.5rem;
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
