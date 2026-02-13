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
            📰 Economy News
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight tracking-tight">
            주요 뉴스
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            경제·금융 분야의 핵심 뉴스를 한눈에 확인하세요.<br class="hidden md:block">
            매일 엄선된 1면 뉴스와 최신 소식을 전달합니다.
        </p>
    </div>
</section>

<!-- ===== 오늘의 뉴스 1면 ===== -->
<div class="container mx-auto px-4 -mt-10 relative z-20 mb-10 animate-slideUp" style="animation-delay: 0.2s;">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 max-w-7xl mx-auto">
        <!-- 헤더: 타이틀 + 날짜 네비게이션 -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#4ECDC4] to-[#2AA9A0] flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-[#2D3047]">오늘의 뉴스 1면</h2>
            </div>

            <!-- 날짜 네비게이션 -->
            <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-4 py-2.5 border border-gray-100">
                <button id="prevDate"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-[#2D3047] hover:bg-white transition-all"
                        title="이전 날짜">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <div class="flex items-center gap-2 relative">
                    <div id="currentDate" class="text-base font-semibold text-[#2D3047] min-w-[150px] text-center">
                        <!-- JavaScript로 업데이트 -->
                    </div>
                    <button type="button" id="calendarButton" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-[#4ECDC4] hover:bg-[#4ECDC4]/10 transition-all" title="날짜 선택">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <input type="date"
                           id="datePickerInput"
                           class="absolute left-0 top-0 w-full h-full opacity-0 cursor-pointer"
                           style="z-index: -1;">
                </div>

                <button id="nextDate"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-[#2D3047] hover:bg-white transition-all"
                        title="다음 날짜">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-[#4ECDC4] border-t-transparent"></div>
            <p class="mt-4 text-gray-500 font-medium">뉴스를 불러오는 중...</p>
        </div>

        <!-- 빈 상태 -->
        <div id="topNewsEmpty" class="hidden text-center py-12">
            <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">해당 날짜의 1면 뉴스가 없습니다.</p>
        </div>
    </div>
</div>

<!-- ===== Search & Filter Section ===== -->
<div class="container mx-auto px-4 mb-8 max-w-7xl animate-slideUp" style="animation-delay: 0.3s;">
    <form action="{{ route('board-news.index') }}" method="GET" id="filterForm">
        <div class="flex flex-row items-center justify-between gap-2 md:gap-4">
            <!-- 좌측: 카테고리 필터 (약 20~30% 비율, 최소 너비 보장) -->
            <div class="relative w-[28%] md:w-auto min-w-[95px] flex-shrink-0">
                <select name="category" 
                        onchange="document.getElementById('filterForm').submit()"
                        class="appearance-none w-full h-10 pl-3 pr-8 bg-white border border-gray-200 text-gray-600 text-sm rounded-xl focus:ring-2 focus:ring-[#4ECDC4]/30 focus:border-[#4ECDC4] hover:border-gray-300 transition-all cursor-pointer font-medium truncate">
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category', '전체') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <!-- 우측: 검색 (나머지 영역 채움) -->
            <div class="relative flex-grow md:flex-grow-0 md:w-96">
                @if(request('category') && request('category') !== '전체')
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       class="w-full h-10 pl-4 pr-10 md:pr-12 bg-white border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent transition-all placeholder-gray-400 text-sm" 
                       placeholder="뉴스, 키워드 검색">
                <button type="submit" class="absolute inset-y-0 right-0 pr-3 md:pr-4 flex items-center text-gray-400 hover:text-[#4ECDC4] transition-colors" title="검색">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- ===== News List ===== -->
<div class="container mx-auto px-4 pb-20 max-w-7xl animate-slideUp" style="animation-delay: 0.4s;">
    @if($news->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 px-4 bg-white rounded-3xl shadow-sm border border-gray-100 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">등록된 뉴스가 없습니다</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                @if(request('search'))
                    '{{ request('search') }}'에 대한 검색 결과가 없습니다.<br>
                    다른 키워드로 검색해보세요.
                @else
                    곧 최신 경제 뉴스로 채워질 예정입니다.<br>
                    잠시만 기다려주세요!
                @endif
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($news as $item)
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-gray-200 group">
                <div class="p-6 md:p-8">
                    <!-- 상단 메타 정보 -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="inline-block px-3 py-1 text-xs font-bold rounded-full {{ $categoryColors[$item->mq_category] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $item->mq_category }}
                            </span>
                            <div class="flex items-center gap-2 text-xs text-gray-400 font-medium">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ date('Y.m.d H:i', strtotime($item->mq_published_date)) }}
                                </span>
                                <span class="text-gray-200">·</span>
                                <span class="font-semibold text-gray-500">{{ $item->mq_company }}</span>
                            </div>
                        </div>

                        <!-- 스크랩 버튼 -->
                        @php
                            $isScrapped = in_array($item->mq_source_url, $scrappedUrls ?? []);
                        @endphp
                        <button onclick="handleScrap('{{ addslashes($item->mq_title) }}', '{{ $item->mq_source_url }}', {{ $isScrapped ? 'true' : 'false' }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all duration-200 text-xs font-medium border hover:shadow-lg
                                {{ $isScrapped 
                                    ? 'bg-[#4ECDC4] text-white border-[#4ECDC4] cursor-default' 
                                    : 'bg-gray-50 hover:bg-[#4ECDC4] text-gray-500 hover:text-white border-gray-100 hover:border-[#4ECDC4] hover:shadow-[#4ECDC4]/20' }}"
                                title="{{ $isScrapped ? '이미 스크랩함' : '스크랩하기' }}">
                            <svg class="w-3.5 h-3.5 {{ $isScrapped ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            <span class="hidden sm:inline">{{ $isScrapped ? '스크랩됨' : '스크랩' }}</span>
                        </button>
                    </div>

                    <!-- 제목 -->
                    <h2 class="text-xl font-bold text-[#2D3047] mb-3 group-hover:text-[#4ECDC4] transition-colors leading-snug">
                        <a href="{{ $item->mq_source_url }}" target="_blank" class="hover:underline decoration-[#4ECDC4]/30 underline-offset-4">
                            {{ $item->mq_title }}
                        </a>
                    </h2>

                    <!-- 내용 미리보기 -->
                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-2">
                        {{ Str::limit(html_entity_decode($item->mq_content), 200) }}
                    </p>
                </div>
            </article>
            @endforeach
        </div>

        <!-- 페이지네이션 -->
        <div class="mt-12 flex justify-center">
            {{ $news->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
/**
 * 오늘의 뉴스 1면 관련 변수 및 함수
 */
let selectedDate = new Date();
let maxFutureDate = new Date();

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', function() {
    // 오늘이 일요일(0)인 경우, 토요일로 설정
    if (selectedDate.getDay() === 0) {
        selectedDate.setDate(selectedDate.getDate() - 1);
        maxFutureDate.setDate(maxFutureDate.getDate() - 1);
    }
    
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
            const pickedDate = new Date(this.value + 'T00:00:00');
            
            // 일요일 체크
            if (pickedDate.getDay() === 0) {
                alert('일요일은 뉴스가 발행되지 않습니다.\n다른 요일을 선택해주세요.');
                updateDateInputValue(); // 원래 날짜로 복구
                return;
            }

            selectedDate = pickedDate;
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

    // 최대 날짜 설정
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

    // 일요일 건너뛰기 로직
    if (newDate.getDay() === 0) {
        // 이동 방향으로 하루 더 이동 (토->일->월 OR 월->일->토)
        newDate.setDate(newDate.getDate() + (days > 0 ? 1 : -1));
    }

    // 미래 날짜 제한
    if (newDate > maxFutureDate) {
        alert('오늘(또는 최근 평일) 이후의 날짜는 조회할 수 없습니다.');
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

    newsArray.forEach((news, index) => {
        const card = createNewsCard(news, index);
        container.appendChild(card);
    });
}

/**
 * 뉴스 카드 생성
 */
function createNewsCard(news, index) {
    const isScrapped = news.is_scrapped;
    const div = document.createElement('div');
    div.className = 'bg-gray-50 rounded-xl border border-gray-100 hover:border-[#4ECDC4]/30 hover:shadow-md transition-all duration-300 p-4 flex flex-col h-[140px] group';
    div.style.animationDelay = `${index * 0.05}s`;
    
    // 버튼 스타일 결정
    const btnClass = isScrapped 
        ? 'bg-[#4ECDC4] text-white border-[#4ECDC4] cursor-default' 
        : 'bg-white hover:bg-[#4ECDC4] text-gray-400 hover:text-white border-gray-100 hover:border-[#4ECDC4]';
    
    const iconClass = isScrapped ? 'fill-current' : 'fill-none';
    const btnText = isScrapped ? '스크랩됨' : '스크랩';

    div.innerHTML = `
        <!-- 뉴스 제목 (메인) -->
        <h3 class="text-sm font-bold text-[#2D3047] line-clamp-3 mb-3 flex-grow group-hover:text-[#4ECDC4] transition-colors leading-snug">
            <a href="${news.source_url}" target="_blank" class="hover:underline decoration-[#4ECDC4]/30 underline-offset-2">
                ${escapeHtml(news.title)}
            </a>
        </h3>

        <!-- 하단: 신문사 정보 + 스크랩 버튼 -->
        <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-200/60 gap-2">
            <!-- 신문사 로고 + 이름 -->
            <div class="flex items-center gap-1.5 flex-shrink min-w-0">
                <img src="${news.company_logo}"
                     alt="${escapeHtml(news.company)}"
                     class="h-4 w-auto object-contain flex-shrink-0"
                     onerror="this.src='/images/logo/company/default.png'">
                <span class="text-[11px] text-gray-400 truncate font-medium">${escapeHtml(news.company)}</span>
            </div>

            <!-- 스크랩 버튼 -->
            <button onclick="event.stopPropagation(); handleScrap('${escapeJs(news.title)}', '${news.source_url}', ${isScrapped})"
                    class="inline-flex items-center gap-1 px-2 py-1 text-[11px] rounded-md transition-all border flex-shrink-0 font-medium ${btnClass}">
                <svg class="w-3 h-3 ${iconClass}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <span class="hidden sm:inline">${btnText}</span>
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
async function handleScrap(title, url, isScrapped = false) {
    // 0. 이미 스크랩된 경우
    if (isScrapped) {
        alert('이미 스크랩 보관함에 저장된 뉴스입니다.');
        return;
    }

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
/* 2줄 말줄임 표시 */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 3줄 말줄임 표시 */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
@endsection