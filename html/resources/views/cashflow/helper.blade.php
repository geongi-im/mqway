@extends('layouts.app')

@section('title', '캐시플로우 도우미')

@push('styles')
<style>
/* 커스텀 스타일 */
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* 모달 내부 앱 컨테이너 스타일 */
#cashflow-app {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    min-height: 100dvh;
}

#main-content {
    flex-grow: 1;
    overflow-y: auto;
}

/* 직업 카드 스타일 */
.profession-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.profession-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.profession-card.selected {
    position: relative;
}

.profession-card.selected::before {
    content: '';
    position: absolute;
    top: -1px;
    left: -1px;
    right: -1px;
    bottom: -1px;
    border: 2px solid #3b82f6;
    border-radius: 0.5rem;
    pointer-events: none;
}

/* 네비게이션 버튼 활성화 스타일 */
.nav-btn.text-blue-600 {
    color: #2563eb;
    background-color: #eff6ff;
    border-radius: 8px;
}
.nav-btn {
    transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}
.nav-btn svg {
    transition: transform 0.2s ease-in-out;
}
.nav-btn.text-blue-600 svg {
    transform: scale(1.1);
}

/* 스크롤바 스타일 */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}
.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* 터치 피드백 */
button:active, .profession-card:active {
    transform: scale(0.97);
    opacity: 0.9;
}

/* Input focus style */
input[type="text"]:focus, input[type="number"]:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}

/* 재무제표 색상 */
#financial-report-container dt {
    color: #4b5563;
}
#financial-report-container dd {
    color: #1f2937;
    font-weight: 500;
}
#financial-report-container .text-green-600 { color: #059669; }
#financial-report-container .text-green-700 { color: #047857; }
#financial-report-container .text-red-600 { color: #dc2626; }
#financial-report-container .text-red-700 { color: #b91c1c; }
#financial-report-container .text-blue-600 { color: #2563eb; }

/* 지출 세부 항목 애니메이션 */
.expenses-details {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out, opacity 0.2s ease-out;
    opacity: 0;
}

.expenses-details:not(.hidden) {
    max-height: 500px;
    opacity: 1;
    transition: max-height 0.5s ease-in, opacity 0.3s ease-in;
}

/* 모바일 화면에서 컨텐츠 패딩 조정 */
@media (max-width: 640px) {
    #profession-selection {
        padding-bottom: 80px;
    }
    
    .profession-card {
        padding: 1rem;
    }
}

/* 고정 하단 버튼 스타일 */
#start-game-fixed-button {
    transition: transform 0.3s ease-in-out;
    z-index: 40;
}

body.scrolled #start-game-fixed-button {
    transform: translateY(100%);
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- 헤더 섹션 -->
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">캐시플로우 도우미</h1>
                <p class="text-lg text-gray-600 mb-8">로버트 기요사키의 캐시플로우 게임을 디지털로 체험해보세요!</p>
                
                <!-- 시작하기 버튼 -->
                <button id="startGameBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-4 px-8 rounded-full transition-all duration-300 text-lg transform hover:scale-105 hover:shadow-lg">
                    도우미 시작하기
                </button>
            </div>
            
            <!-- 게임 소개 섹션 -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">도우미 안내</h2>
                <ul class="list-disc pl-6 space-y-2 text-gray-600">
                    <li>직업을 선택하고 재무 상황을 관리하면서 경제적 자유를 향해 나아가세요.</li>
                    <li>Small Deal, Big Deal, Doodad 카드를 통해 다양한 투자와 소비 상황을 경험할 수 있습니다.</li>
                    <li>월급을 받고, 투자하고, 자산을 관리하면서 캐시플로우를 개선해보세요.</li>
                    <li>당신의 꿈을 설정하고 그 꿈을 이루기 위한 재정 계획을 세워보세요.</li>
                </ul>
            </div>
            
            <!-- 기능 소개 섹션 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">재무 관리</h3>
                    <p class="text-gray-600">현금흐름을 추적하고 자산과 부채를 효과적으로 관리</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">투자 시뮬레이션</h3>
                    <p class="text-gray-600">다양한 투자 기회를 경험하고 포트폴리오를 구성</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">실시간 분석</h3>
                    <p class="text-gray-600">재무제표와 현금흐름을 실시간으로 분석하고 확인</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">목표 달성</h3>
                    <p class="text-gray-600">개인의 꿈과 목표를 설정하고 달성 과정을 추적</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 게임 모달 -->
<div id="gameModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-white overflow-y-auto">
        <!-- 게임 콘텐츠 컨테이너 -->
        <div id="cashflow-app" class="max-w-md mx-auto bg-white min-h-screen shadow-lg flex flex-col">
            
            <!-- 헤더 -->
            <header class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 sticky top-0 z-50 w-full">
                <div class="flex items-center justify-center relative">
                    <h1 class="text-lg font-bold">캐시플로우 도우미</h1>
                    
                    <!-- 닫기 버튼 -->
                    <button id="closeGameBtn" class="absolute right-0 bg-white/20 hover:bg-white/30 text-white rounded-full p-2 transition-colors z-[70]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </header>

            <!-- 메인 콘텐츠 영역 -->
            <main id="main-content" class="flex-grow overflow-y-auto pb-20">
                
                <!-- 직업 선택 화면 -->
                <div id="profession-selection" class="p-4 pb-20">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">게임 시작</h2>
                        <p class="text-gray-600">플레이어 이름과 직업을 선택하세요</p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="player-name" class="block text-sm font-medium text-gray-700 mb-2">플레이어 이름</label>
                        <input 
                            type="text" 
                            id="player-name" 
                            placeholder="이름을 입력하세요"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    
                    <div class="mb-6">
                        <label for="player-dream" class="block text-sm font-medium text-gray-700 mb-2">당신의 꿈</label>
                        <input 
                            type="text" 
                            id="player-dream" 
                            placeholder="당신의 꿈을 입력하세요"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2"
                        >
                        <div class="flex items-center space-x-2">
                            <input 
                                type="number" 
                                id="dream-cost" 
                                placeholder="꿈을 이루기 위한 비용($)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div id="dream-cost-krw" class="mt-2 text-sm text-gray-600 hidden">
                            참고: 약 <span id="dream-cost-krw-value">0</span>원
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">직업 선택</h3>
                        <div id="profession-list" class="space-y-3">
                            <!-- 직업 카드들이 동적으로 생성됩니다 -->
                        </div>
                    </div>

                </div>

                <!-- 탭 콘텐츠: 대시보드 -->
                <div id="tab-content-dashboard" class="tab-content hidden p-4 space-y-4">
                    <!-- 경주 탈출 진행도 차트 -->
                    <div class="bg-white p-3 rounded-lg shadow-sm border">
                        <h3 class="text-sm font-semibold mb-2 text-gray-700">캐시플로우 경주 탈출 진행도</h3>
                        <div id="cashflow-gauge-chart" class="w-full h-14"></div>
                    </div>
                    
                    <!-- 재무 상태 요약 -->
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-lg shadow">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-green-500">
                                <div class="text-xs text-gray-600">현재 현금</div>
                                <div id="current-cash" class="text-lg font-bold text-green-600">$0</div>
                            </div>
                            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-blue-500">
                                <div class="text-xs text-gray-600">월 현금흐름</div>
                                <div id="monthly-cashflow" class="text-lg font-bold text-blue-600">$0</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                <div class="text-xs text-gray-600">총 수입</div>
                                <div id="total-income" class="text-sm font-semibold text-gray-800">$0</div>
                            </div>
                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                <div class="text-xs text-gray-600">총 지출</div>
                                <div id="total-expenses" class="text-sm font-semibold text-gray-800">$0</div>
                            </div>
                        </div>
                    </div>

                    <!-- 카드 선택 섹션 -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border">
                        <h3 class="text-lg font-semibold mb-3">사용자 액션</h3>
                        <div class="grid grid-cols-3 gap-3">
                            <button id="smalldeal-btn" class="bg-green-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors text-sm">
                                작은 기회<br/>(Small Deal)
                            </button>
                            <button id="bigdeal-btn" class="bg-blue-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-sm">
                                큰 기회<br/>(Big Deal)
                            </button>
                            <button id="doodad-btn" class="bg-red-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors text-sm">
                                소비<br/>(Doodad)
                            </button>
                            <button id="payday-btn" class="bg-yellow-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition-colors text-sm">
                                월급 받기<br/>(Payday)
                            </button>
                            <button id="charity-btn" class="bg-purple-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors text-sm">
                                기부하기<br/>(Charity)
                            </button>
                            <button id="downsized-btn" class="bg-gray-700 text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors text-sm">
                                실직<br/>(Downsized)
                            </button>
                            <button id="have-child-btn" class="bg-blue-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-sm">
                                출산하기<br/>(자녀 0/3)
                            </button>
                            <button id="emergency-loan-btn" class="bg-orange-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-orange-700 transition-colors text-sm">
                                긴급 대출<br/>(Emergency Loan)
                            </button>
                        </div>
                    </div>

                    <!-- 게임 로그 (요약) 섹션 -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border">
                        <h3 class="text-lg font-semibold mb-3">최근 활동</h3>
                        <div id="dashboard-game-log" class="space-y-2 max-h-40 overflow-y-auto">
                            <div class="text-sm text-gray-500 text-center py-4">
                                게임 활동이 여기에 표시됩니다.
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 탭 콘텐츠: 자산/부채 -->
                <div id="tab-content-assets" class="tab-content hidden p-4 space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800">자산 및 부채 현황</h2>
                    
                    <!-- 꿈 정보 섹션 -->
                    <div id="dream-info-section" class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg border">
                        <!-- 꿈 정보가 여기에 동적으로 생성됩니다 -->
                    </div>
                    
                    <div id="assets-list-container" class="space-y-3">
                        <!-- 자산 목록이 여기에 동적으로 생성됩니다 -->
                    </div>
                    <hr class="my-4">
                    <div id="liabilities-list-container" class="space-y-3">
                        <!-- 부채 목록이 여기에 동적으로 생성됩니다 -->
                    </div>
                </div>

                <!-- 탭 콘텐츠: 재무제표 -->
                <div id="tab-content-report" class="tab-content hidden p-4 space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800">재무제표</h2>
                    <div id="financial-report-container" class="space-y-4">
                        <!-- 재무제표 내용이 여기에 동적으로 생성됩니다 -->
                    </div>
                </div>

                <!-- 탭 콘텐츠: 카드 목록 -->
                <div id="tab-content-cards" class="tab-content hidden p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-xl font-semibold text-gray-800">카드 목록</h2>
                        <button id="back-to-dashboard-btn" class="bg-gray-200 text-gray-700 px-3 py-1 rounded-md text-sm hover:bg-gray-300 transition-colors">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                뒤로가기
                            </span>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <select id="card-type-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="SmallDeals">작은 기회 (Small Deal)</option>
                            <option value="BigDeals">큰 기회 (Big Deal)</option>
                            <option value="Doodads">소비 (Doodad)</option>
                        </select>
                    </div>
                    
                    <!-- 카드 검색 -->
                    <div class="mb-4">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="card-search-input" 
                                placeholder="카드명으로 검색..." 
                                class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                            >
                            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- 카테고리 탭 -->
                    <div id="category-tabs-container" class="mb-4 hidden">
                        <div class="flex flex-wrap gap-2" id="category-tabs">
                            <!-- 카테고리 탭 버튼들이 동적으로 생성됩니다 -->
                        </div>
                    </div>
                    
                    <div id="card-list-container" class="space-y-3 max-h-[calc(100vh-280px)] overflow-y-auto">
                        <!-- 카드 목록이 여기에 동적으로 생성됩니다 -->
                    </div>
                </div>

                <!-- 탭 콘텐츠: 게임 로그 (전체) -->
                <div id="tab-content-log" class="tab-content hidden p-4">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">전체 게임 로그</h2>
                    <div id="full-game-log" class="space-y-2 max-h-[calc(100vh-200px)] overflow-y-auto bg-gray-100 p-3 rounded-lg">
                        <div class="text-sm text-gray-500 text-center py-4">
                            게임 로그가 여기에 표시됩니다.
                        </div>
                    </div>
                </div>

            </main>

            <!-- 직업 선택 화면의 고정 하단 버튼 -->
            <div id="start-game-fixed-button" class="fixed bottom-0 left-0 right-0 w-full max-w-md mx-auto bg-white border-t border-gray-200 p-3 shadow-lg">
                <button 
                    id="start-game-btn" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled
                >
                    게임 시작
                </button>
            </div>

            <!-- 하단 네비게이션 -->
            <nav id="bottom-nav" class="fixed bottom-0 left-0 right-0 w-full max-w-md mx-auto bg-white border-t border-gray-200 hidden">
                <div class="flex justify-around py-1">
                    <button class="nav-btn p-2 flex flex-col items-center w-1/5" data-tab="dashboard">
                        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span class="text-xs">대시보드</span>
                    </button>
                    <button class="nav-btn p-2 flex flex-col items-center w-1/5" data-tab="assets">
                         <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                        <span class="text-xs">자산/부채</span>
                    </button>
                    <button class="nav-btn p-2 flex flex-col items-center w-1/5" data-tab="report">
                        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-xs">재무제표</span>
                    </button>
                    <button class="nav-btn p-2 flex flex-col items-center w-1/5" data-tab="cards">
                        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span class="text-xs">카드목록</span>
                    </button>
                    <button class="nav-btn p-2 flex flex-col items-center w-1/5" data-tab="log">
                        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M3 7l9-4 9 4M3 7h18M5 5h14a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"></path></svg>
                        <span class="text-xs">게임로그</span>
                    </button>
                </div>
            </nav>

        </div>

        <!-- 모달들 -->
        <!-- 모달: 카드 이벤트 / 알림 -->
        <div id="card-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-[100] p-4">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full transform transition-all opacity-0 scale-95">
                <h3 id="card-title" class="text-xl font-bold mb-3 text-gray-800"></h3>
                <p id="card-description" class="text-sm text-gray-700 mb-4"></p>
                <div id="card-details-inputs" class="space-y-3 mb-4">
                    <!-- 동적 입력 필드 (예: 주식 수량) -->
                </div>
                <div id="card-actions" class="space-y-2">
                    <!-- 카드 액션 버튼들이 동적으로 생성됩니다 -->
                </div>
            </div>
        </div>

        <!-- 모달: 자산 판매 -->
        <div id="sell-asset-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 p-4">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full transform transition-all opacity-0 scale-95">
                <h3 id="sell-asset-modal-title" class="text-xl font-bold mb-3 text-gray-800">자산 판매</h3>
                <div id="sell-asset-modal-details" class="text-sm text-gray-700 mb-4">
                    <!-- 판매할 자산 정보 -->
                </div>
                <div class="mb-4">
                    <label for="sell-price" class="block text-sm font-medium text-gray-700 mb-1">판매 가격 ($)</label>
                    <input type="number" id="sell-price" name="sell-price" placeholder="판매 가격 입력" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div id="sell-asset-modal-actions" class="flex justify-end space-x-3">
                    <button id="cancel-sell-asset-btn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors">취소</button>
                    <button id="confirm-sell-asset-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">판매 확정</button>
                </div>
            </div>
        </div>

        <!-- 모달: 지불 방법 선택 -->
        <div id="payment-options-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 p-4">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full transform transition-all opacity-0 scale-95">
                <h3 id="payment-modal-title" class="text-xl font-bold mb-3 text-gray-800">지불 방법 선택</h3>
                <div id="payment-modal-description" class="text-sm text-gray-700 mb-4">
                    <!-- 아이템 설명 -->
                </div>
                <div id="payment-options-container" class="space-y-3 mb-4">
                    <!-- 지불 옵션들이 동적으로 생성됩니다 -->
                </div>
                <div id="payment-modal-actions" class="flex justify-end space-x-3">
                    <button id="cancel-payment-btn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors">취소</button>
                </div>
            </div>
        </div>

        <!-- 긴급 대출 모달 -->
        <div id="emergency-loan-modal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="modal-content bg-white p-6 rounded-lg shadow-lg max-w-sm w-full mx-4">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">은행 긴급 대출</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">대출 금액 선택</label>
                    
                    <!-- 현재 선택된 금액 표시 -->
                    <div class="text-center mb-4 p-3 bg-gray-50 rounded-lg border">
                        <div class="text-sm text-gray-600">선택된 대출 금액</div>
                        <div id="selected-loan-amount" class="text-xl font-bold text-orange-600">$0</div>
                    </div>
                    
                    <!-- 대출 금액 선택 버튼들 -->
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <button type="button" class="loan-amount-btn bg-orange-100 hover:bg-orange-200 text-orange-800 py-2 px-3 rounded-lg text-sm font-medium transition-colors" data-amount="1000">
                            +$1,000
                        </button>
                        <button type="button" class="loan-amount-btn bg-orange-100 hover:bg-orange-200 text-orange-800 py-2 px-3 rounded-lg text-sm font-medium transition-colors" data-amount="5000">
                            +$5,000
                        </button>
                        <button type="button" class="loan-amount-btn bg-orange-100 hover:bg-orange-200 text-orange-800 py-2 px-3 rounded-lg text-sm font-medium transition-colors" data-amount="10000">
                            +$10,000
                        </button>
                        <button type="button" class="loan-amount-btn bg-orange-100 hover:bg-orange-200 text-orange-800 py-2 px-3 rounded-lg text-sm font-medium transition-colors" data-amount="25000">
                            +$25,000
                        </button>
                    </div>
                    
                    <!-- 초기화 및 전체 제거 버튼 -->
                    <div class="flex space-x-2">
                        <button type="button" id="reset-loan-amount" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                            초기화
                        </button>
                        <button type="button" id="subtract-1000" class="flex-1 bg-red-200 hover:bg-red-300 text-red-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                            -$1,000
                        </button>
                    </div>
                </div>
                
                <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="text-sm text-gray-700 mb-2">
                        <strong>대출 조건:</strong>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div>• 금리: 월 10%</div>
                        <div>• 신청 단위: $1,000 단위만 가능</div>
                        <div>• 상환 방식: 전체 일시 상환만 가능</div>
                        <div>• 월 이자: <span id="monthly-interest-preview">$0</span></div>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <button id="confirm-loan-btn" class="flex-1 bg-orange-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-orange-700 transition-colors">
                        대출 신청
                    </button>
                    <button id="cancel-loan-btn" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg font-semibold hover:bg-gray-400 transition-colors">
                        취소
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startGameBtn = document.getElementById('startGameBtn');
    const closeGameBtn = document.getElementById('closeGameBtn');
    const gameModal = document.getElementById('gameModal');
    
    // 팝업 열릴 때 외부 스크롤 비활성화
    function disableBodyScroll() {
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
    }
    
    // 팝업 닫힐 때 외부 스크롤 활성화
    function enableBodyScroll() {
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
    }
    
    // 게임 초기화 플래그
    let gameInitialized = false;
    
    // 게임 시작 버튼 클릭 이벤트
    startGameBtn.addEventListener('click', function() {
        // 모달 열기 전에 localStorage 완전 초기화
        localStorage.removeItem('cashflowGameState');
        localStorage.removeItem('cashflow_player_stats');
        
        gameModal.classList.remove('hidden');
        gameModal.classList.add('flex');
        disableBodyScroll();
        
        // 게임을 항상 새로 초기화 (이전 데이터 무시)
        gameInitialized = false;
        initializeCashflowGame();
        gameInitialized = true;
    });
    
    // 캐시플로우 게임 초기화 함수
    function initializeCashflowGame() {
        // DOM 요소들이 준비될 때까지 최대 5초 대기
        let attempts = 0;
        const maxAttempts = 50; // 5초 (100ms * 50)
        
        const waitForElements = () => {
            attempts++;
            
            // 필수 DOM 요소들이 존재하는지 확인
            const requiredElements = [
                'start-game-btn',
                'player-name', 
                'player-dream',
                'dream-cost',
                'smalldeal-btn',
                'bigdeal-btn',
                'doodad-btn'
            ];
            
            const allElementsExist = requiredElements.every(id => {
                const element = document.getElementById(id);
                return element !== null;
            });
            
            if (allElementsExist) {
                try {
                    console.log('모든 DOM 요소가 준비되었습니다. 게임을 초기화합니다.');
                    if (typeof CashflowGame !== 'undefined') {
                        window.cashflowGameInstance = new CashflowGame();
                        console.log('캐시플로우 게임이 성공적으로 초기화되었습니다.');
                    }
                } catch (error) {
                    console.error('캐시플로우 게임 초기화 오류:', error);
                }
            } else if (attempts < maxAttempts) {
                // 아직 모든 요소가 준비되지 않았다면 다시 시도
                console.log(`DOM 요소 대기 중... (시도: ${attempts}/${maxAttempts})`);
                setTimeout(waitForElements, 100);
            } else {
                console.error('DOM 요소를 찾을 수 없어 게임 초기화에 실패했습니다.');
                console.log('누락된 요소들:', requiredElements.filter(id => !document.getElementById(id)));
            }
        };
        
        // 초기화 시작
        waitForElements();
    }
    
    // 모달 닫기 버튼 클릭 이벤트
    closeGameBtn.addEventListener('click', function() {
        if(confirm('캐시플로우 도우미를 종료하시겠습니까? 종료하면 초기화 됩니다.')) {
            // 페이지 새로고침으로 완전한 초기 상태로 복원
            location.reload();
        }
    });
    
    // 게임 초기화 함수 (단순화 - 새로고침 사용)
    function resetGame() {
        // 페이지 새로고침으로 완전한 초기화
        location.reload();
    }
});
</script>

<!-- CDN 및 JavaScript 파일들 로드 -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<!-- 게임 자동 실행 방지를 위한 플래그 설정 -->
<script>
window.DISABLE_AUTO_GAME_INIT = true;
</script>

<script src="{{ asset('js/cashflow/professionData.js') }}"></script>
<script src="{{ asset('js/cashflow/cardData.js') }}"></script>
<script src="{{ asset('js/cashflow/gameData.js') }}"></script>
<script src="{{ asset('js/cashflow/game-core.js') }}"></script>
<script src="{{ asset('js/cashflow/game-ui.js') }}"></script>
<script src="{{ asset('js/cashflow/game-events.js') }}"></script>
@endpush