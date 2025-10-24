@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">주요 뉴스</h1>
    </div>

    <!-- 오늘의 뉴스 1면 -->
    <div class="mb-12 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6">
        <!-- 헤더: 타이틀 + 날짜 네비게이션 -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
            <div class="flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-900">오늘의 뉴스 1면</h2>
            </div>

            <!-- 날짜 네비게이션 -->
            <div class="flex items-center gap-3 bg-white rounded-lg px-4 py-2 shadow-sm">
                <button id="prevDate"
                        class="text-gray-600 hover:text-blue-600 transition-colors p-1"
                        title="이전 날짜">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <div class="flex items-center gap-2 relative">
                    <div id="currentDate" class="text-lg font-semibold text-gray-800 min-w-[160px] text-center">
                        <!-- JavaScript로 업데이트 -->
                    </div>
                    <button type="button" id="calendarButton" class="text-gray-600 hover:text-blue-600 transition-colors p-1" title="날짜 선택">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <input type="date"
                           id="datePickerInput"
                           class="absolute left-0 top-0 w-full h-full opacity-0 cursor-pointer"
                           style="z-index: -1;">
                </div>

                <button id="nextDate"
                        class="text-gray-600 hover:text-blue-600 transition-colors p-1"
                        title="다음 날짜">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- 뉴스 카드 그리드 -->
        <div id="topNewsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <!-- AJAX로 동적 로딩 -->
        </div>

        <!-- 로딩 상태 -->
        <div id="topNewsLoading" class="hidden text-center py-12">
            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-600 border-t-transparent"></div>
            <p class="mt-4 text-gray-600">뉴스를 불러오는 중...</p>
        </div>

        <!-- 빈 상태 -->
        <div id="topNewsEmpty" class="hidden text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-600">해당 날짜의 1면 뉴스가 없습니다.</p>
        </div>
    </div>

    <!-- 검색 및 필터 영역 -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- 카테고리 필터 -->
            <div class="flex gap-2 overflow-x-auto pb-2 w-full md:w-auto">
                @foreach($categories as $category)
                <a href="{{ $category === '전체' ? url('/board-news') : request()->fullUrlWithQuery(['category' => $category]) }}" 
                   class="px-4 py-2 rounded-md transition-colors whitespace-nowrap text-cdark
                         {{ (request('category', '전체') === $category) ? 'bg-point1' : 'bg-point' }}">
                    {{ $category }}
                </a>
                @endforeach
            </div>

            <!-- 검색창 -->
            <form method="GET" class="relative w-full md:w-96">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="뉴스 검색" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark">
                <!-- 현재 선택된 카테고리 유지 -->
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-text-dark hover:text-dark">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- 뉴스 목록 -->
    <div class="space-y-6">
        @foreach($news as $item)
        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <!-- 상단 메타 정보 -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3 text-sm text-text-dark">
                        <span class="{{ $categoryColors[$item->mq_category] }} px-3 py-1 rounded-md">
                            {{ $item->mq_category }}
                        </span>
                        <time datetime="{{ $item->mq_reg_date }}">
                            {{ date('Y.m.d H:i', strtotime($item->mq_published_date)) }}
                        </time>
                        <span>·</span>
                        <span>{{ $item->mq_company }}</span>
                    </div>

                    <!-- 스크랩 버튼 -->
                    <button onclick="handleScrap('{{ addslashes($item->mq_title) }}', '{{ $item->mq_source_url }}')"
                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-point1 text-gray-700 hover:text-white rounded-md transition-colors group"
                            title="스크랩하기">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        <span class="text-sm">스크랩</span>
                    </button>
                </div>

                <!-- 제목 -->
                <h2 class="text-xl font-bold mb-3 text-dark hover:text-dark/80">
                    <a href="{{ $item->mq_source_url }}" target="_blank">{{ $item->mq_title }}</a>
                </h2>

                <!-- 내용 미리보기 -->
                <p class="text-gray-600 mb-4">
                    {{ Str::limit(html_entity_decode($item->mq_content), 200) }}
                </p>
            </div>
        </article>
        @endforeach
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-12 flex justify-center">
        {{ $news->appends(request()->query())->links('vendor.pagination.tailwind') }}
    </div>
</div>

@push('scripts')
<script>
/**
 * 오늘의 뉴스 1면 관련 변수 및 함수
 */
let selectedDate = new Date();
const maxFutureDate = new Date();

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', function() {
    initTopNews();
    initDatePicker();

    // 이벤트 리스너
    document.getElementById('prevDate').addEventListener('click', () => changeDate(-1));
    document.getElementById('nextDate').addEventListener('click', () => changeDate(1));
});

/**
 * 오늘의 뉴스 1면 초기화
 */
function initTopNews() {
    updateDateDisplay();
    loadTopNewsByDate(selectedDate);
}

/**
 * 날짜 선택기 초기화
 */
function initDatePicker() {
    const dateInput = document.getElementById('datePickerInput');
    const calendarBtn = document.getElementById('calendarButton');

    // 초기 설정
    updateDateInputValue();

    // 캘린더 버튼 클릭 시 date picker 열기
    calendarBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // 값 업데이트
        updateDateInputValue();

        // date picker 열기
        try {
            if (dateInput.showPicker) {
                dateInput.showPicker();
            } else {
                // showPicker 미지원 브라우저
                dateInput.focus();
                dateInput.click();
            }
        } catch (error) {
            console.log('Date picker error:', error);
            dateInput.focus();
            dateInput.click();
        }
    });

    // 날짜 선택 시
    dateInput.addEventListener('change', function() {
        if (this.value) {
            selectedDate = new Date(this.value + 'T00:00:00');
            updateDateDisplay();
            loadTopNewsByDate(selectedDate);
        }
    });
}

/**
 * date input 값 업데이트
 */
function updateDateInputValue() {
    const dateInput = document.getElementById('datePickerInput');

    // 현재 선택된 날짜로 설정
    const year = selectedDate.getFullYear();
    const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
    const day = String(selectedDate.getDate()).padStart(2, '0');
    dateInput.value = `${year}-${month}-${day}`;

    // 최대 날짜 설정 (오늘)
    const maxYear = maxFutureDate.getFullYear();
    const maxMonth = String(maxFutureDate.getMonth() + 1).padStart(2, '0');
    const maxDay = String(maxFutureDate.getDate()).padStart(2, '0');
    dateInput.max = `${maxYear}-${maxMonth}-${maxDay}`;
}

/**
 * 날짜 변경
 */
function changeDate(days) {
    const newDate = new Date(selectedDate);
    newDate.setDate(newDate.getDate() + days);

    // 미래 날짜 제한
    if (newDate > maxFutureDate) {
        alert('오늘 이후의 날짜는 조회할 수 없습니다.');
        return;
    }

    selectedDate = newDate;
    updateDateDisplay();
    loadTopNewsByDate(selectedDate);
}

/**
 * 날짜 표시 업데이트
 */
function updateDateDisplay() {
    document.getElementById('currentDate').textContent = formatDateWithDay(selectedDate);

    const nextBtn = document.getElementById('nextDate');
    const isToday = selectedDate.toDateString() === maxFutureDate.toDateString();

    nextBtn.disabled = isToday;
    if (isToday) {
        nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

/**
 * 날짜 포맷팅 (YYYY.MM.DD (요일))
 */
function formatDateWithDay(date) {
    const days = ['일', '월', '화', '수', '목', '금', '토'];
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const dayOfWeek = days[date.getDay()];

    return `${year}.${month}.${day} (${dayOfWeek})`;
}

/**
 * AJAX로 날짜별 뉴스 로드
 */
async function loadTopNewsByDate(date) {
    const container = document.getElementById('topNewsContainer');
    const loading = document.getElementById('topNewsLoading');
    const empty = document.getElementById('topNewsEmpty');

    // 상태 초기화
    container.innerHTML = '';
    container.classList.add('hidden');
    empty.classList.add('hidden');
    loading.classList.remove('hidden');

    try {
        const dateStr = date.toISOString().split('T')[0]; // YYYY-MM-DD
        const response = await fetch(`/board-news/top-news/${dateStr}`);
        const data = await response.json();

        loading.classList.add('hidden');

        if (!data.success || data.news.length === 0) {
            empty.classList.remove('hidden');
            return;
        }

        renderTopNewsCards(data.news);
        container.classList.remove('hidden');

    } catch (error) {
        console.error('뉴스 로드 실패:', error);
        loading.classList.add('hidden');
        empty.classList.remove('hidden');
    }
}

/**
 * 뉴스 카드 렌더링
 */
function renderTopNewsCards(newsArray) {
    const container = document.getElementById('topNewsContainer');

    newsArray.forEach(news => {
        const card = createNewsCard(news);
        container.appendChild(card);
    });
}

/**
 * 뉴스 카드 생성
 */
function createNewsCard(news) {
    const div = document.createElement('div');
    div.className = 'bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-4 flex flex-col h-[140px] group';
    div.innerHTML = `
        <!-- 뉴스 제목 (메인) -->
        <h3 class="text-base font-bold text-gray-900 line-clamp-3 mb-3 flex-grow group-hover:text-blue-600 transition-colors leading-tight">
            <a href="${news.source_url}" target="_blank" class="hover:underline">
                ${escapeHtml(news.title)}
            </a>
        </h3>

        <!-- 하단: 신문사 정보 + 스크랩 버튼 -->
        <div class="flex items-center justify-between mt-auto pt-3 border-t gap-2">
            <!-- 신문사 로고 + 이름 -->
            <div class="flex items-center gap-1.5 flex-shrink min-w-0">
                <img src="${news.company_logo}"
                     alt="${escapeHtml(news.company)}"
                     class="h-4 w-auto object-contain flex-shrink-0"
                     onerror="this.src='/images/logo/company/default.png'">
                <span class="text-xs text-gray-500 truncate">${escapeHtml(news.company)}</span>
            </div>

            <!-- 스크랩 버튼 -->
            <button onclick="event.stopPropagation(); handleScrap('${escapeJs(news.title)}', '${news.source_url}')"
                    class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-gray-100 hover:bg-point1 text-gray-700 hover:text-white rounded transition-colors flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <span class="hidden sm:inline">스크랩</span>
            </button>
        </div>
    `;
    return div;
}

/**
 * XSS 방지 - HTML 이스케이프
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * XSS 방지 - JavaScript 이스케이프
 */
function escapeJs(text) {
    return text.replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
}

/**
 * 스크랩 버튼 클릭 핸들러
 */
async function handleScrap(title, url) {
    // 1. 사용자 확인
    if (!confirm('이 뉴스를 스크랩하시겠습니까?')) {
        return;
    }

    try {
        // 2. 중복 체크 API 호출
        const response = await fetch('{{ route('mypage.news-scrap.check-duplicate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ url: url })
        });

        const data = await response.json();

        // 3. 로그인 필요
        if (data.requireLogin) {
            alert('로그인이 필요합니다.');
            window.location.href = '{{ route('login') }}';
            return;
        }

        // 4. 중복 체크 결과 처리
        if (data.exists) {
            alert('이미 스크랩된 뉴스입니다.');
            return;
        }

        // 5. 중복 아닌 경우 글쓰기 페이지로 이동 (제목과 URL 전달)
        const createUrl = new URL('{{ route('mypage.news-scrap.create') }}');
        createUrl.searchParams.append('title', title);
        createUrl.searchParams.append('url', url);
        window.location.href = createUrl.toString();

    } catch (error) {
        console.error('스크랩 중 오류 발생:', error);
        alert('스크랩 처리 중 오류가 발생했습니다. 다시 시도해주세요.');
    }
}
</script>

<style>
/* 3줄 말줄임 표시 */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 4줄 말줄임 표시 */
.line-clamp-4 {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
@endsection 