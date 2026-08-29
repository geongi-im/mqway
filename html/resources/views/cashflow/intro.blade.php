@extends('layouts.app')

@section('content')
{{--
    캐시플로우 소개 페이지.

    제공 기능은 두 가지다. (1) 게임 설명 (2) 챗봇.
    챗봇을 여는 버튼은 히어로 / 하단 CTA / 플로팅 버튼 세 곳에 있고,
    모두 data-chatbot-open 속성으로 public/js/cashflow/chatbot.js 가 잡는다.
    마크다운 파서(marked)와 DOMPurify 는 _chatbot.blade.php 가 직접 불러온다.
--}}

<!-- ===== Hero ===== -->
<section class="relative overflow-hidden bg-[#2D3047] pt-16 pb-28 md:pt-24 md:pb-36">
    <div class="absolute inset-0" aria-hidden="true">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29]"></div>
        <div class="absolute -top-32 -right-20 w-80 h-80 md:w-96 md:h-96 bg-[#4ECDC4] rounded-full blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-32 -left-20 w-80 h-80 md:w-96 md:h-96 bg-[#FF4D4D] rounded-full blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10 max-w-3xl text-center animate-slideUp">
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-sm font-medium mb-5 backdrop-blur-md">
            🎲 CASHFLOW
        </span>

        <h1 class="text-3xl md:text-5xl font-bold text-white mb-5 leading-tight tracking-tight">
            돈이 나를 위해 일하게<br class="hidden sm:block"> 만드는 연습
        </h1>

        <p class="text-base md:text-xl text-gray-300 leading-relaxed font-light mb-8">
            캐시플로우는 실제 돈을 잃지 않고 투자와 재정 결정을 경험해보는 교육용 보드게임입니다.
            규칙이 헷갈리면 챗봇에게 바로 물어보세요.
        </p>

        <div class="flex flex-wrap justify-center gap-2 mb-9">
            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full bg-white/5 border border-white/10 text-gray-300 text-xs md:text-sm">
                ⏱ 2~3시간
            </span>
            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full bg-white/5 border border-white/10 text-gray-300 text-xs md:text-sm">
                🎯 쥐의 레이스 탈출
            </span>
            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full bg-white/5 border border-white/10 text-gray-300 text-xs md:text-sm">
                🧑‍🏫 로버트 기요사키
            </span>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            {{-- 챗봇 API 는 auth 가 걸려 있다. 비로그인 방문자에게는 같은 자리에서 로그인으로 안내한다. --}}
            @auth
            <button type="button" data-chatbot-open
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-[#FF4D4D] text-white font-semibold shadow-lg shadow-[#FF4D4D]/25 hover:bg-[#FF6B6B] hover:-translate-y-0.5 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                챗봇에게 물어보기
            </button>
            @else
            <a href="{{ route('login') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-[#FF4D4D] text-white font-semibold shadow-lg shadow-[#FF4D4D]/25 hover:bg-[#FF6B6B] hover:-translate-y-0.5 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                로그인하고 챗봇 이용하기
            </a>
            @endauth

            <a href="#overview"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium backdrop-blur-md hover:bg-white/20 transition-all duration-300">
                게임 알아보기
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- ===== 게임 이미지 ===== -->
<div class="container mx-auto px-4 -mt-16 md:-mt-24 relative z-20 mb-10 animate-slideUp" style="animation-delay: 0.15s;">
    <div class="bg-white rounded-2xl shadow-xl p-3 md:p-4 max-w-4xl mx-auto">
        <div class="swiper gameImageSlider rounded-xl overflow-hidden">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="{{ asset('/images/cashflow/intro_1.png') }}"
                         alt="캐시플로우 게임 보드" loading="lazy"
                         class="w-full h-auto object-cover">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('/images/cashflow/job_1.png') }}"
                         alt="캐시플로우 직업 카드" loading="lazy"
                         class="w-full h-auto object-cover">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('/images/cashflow/card_1.png') }}"
                         alt="캐시플로우 기회 카드" loading="lazy"
                         class="w-full h-auto object-cover">
                </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>

<!-- ===== 섹션 이동 ===== -->
<div class="sticky top-0 z-30 bg-white/85 backdrop-blur-md border-y border-gray-200 mb-14">
    <div class="container mx-auto px-4 max-w-4xl">
        <nav class="flex gap-1 overflow-x-auto scrollbar-hide py-2.5" aria-label="섹션 이동">
            <a href="#overview" class="section-link">게임 개요</a>
            <a href="#philosophy" class="section-link">개발 배경</a>
            <a href="#goals" class="section-link">학습 목표</a>
            <a href="#howto" class="section-link">게임 방법</a>
            <a href="#tips" class="section-link">전략과 팁</a>
        </nav>
    </div>
</div>

<div class="container mx-auto px-4 max-w-4xl pb-20 space-y-16">

    <!-- ===== 게임 개요 ===== -->
    <section id="overview" class="section-anchor">
        <div class="flex items-center gap-3 mb-6">
            <span class="w-11 h-11 rounded-xl bg-[#4ECDC4]/15 text-[#4ECDC4] flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <h2 class="text-xl md:text-2xl font-bold text-[#2D3047]">게임 개요</h2>
        </div>

        <div class="grid md:grid-cols-5 gap-5">
            <div class="md:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4 text-gray-600 leading-relaxed">
                <p>캐시플로우(Cashflow)는 로버트 기요사키가 개발한 교육용 보드게임으로, 재정 지능을 향상시키기 위해 설계되었습니다. 금융 교육과 투자 원칙을 실제와 유사한 시뮬레이션으로 경험할 수 있게 해줍니다.</p>
                <p>플레이어는 서로 다른 직업과 재정 상황에서 시작해 <strong class="text-[#2D3047] font-semibold">'쥐의 레이스(Rat Race)'</strong>에서 벗어나 <strong class="text-[#2D3047] font-semibold">'빠른 트랙(Fast Track)'</strong>으로 이동하는 과정을 겪습니다. 그 과정에서 부동산·주식·사업체 같은 투자로 수동적 수입을 만들고 자산을 쌓는 법을 배웁니다.</p>
            </div>

            <div class="md:col-span-2 space-y-3">
                <div class="bg-[#2D3047] rounded-2xl p-5 text-white">
                    <p class="text-xs text-gray-400 mb-1">목표</p>
                    <p class="font-bold text-lg leading-snug">수동적 수입 &gt; 총지출</p>
                    <p class="text-sm text-gray-300 mt-2 leading-relaxed">이 조건을 만족하면 쥐의 레이스에서 탈출합니다.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs text-gray-400 mb-1">플레이 시간</p>
                    <p class="font-bold text-lg text-[#2D3047]">약 2~3시간</p>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">플레이어 수에 따라 달라집니다.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== 개발 배경 및 철학 ===== -->
    <section id="philosophy" class="section-anchor">
        <div class="flex items-center gap-3 mb-6">
            <span class="w-11 h-11 rounded-xl bg-[#FFB347]/15 text-[#FFB347] flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </span>
            <h2 class="text-xl md:text-2xl font-bold text-[#2D3047]">개발 배경 및 철학</h2>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-gray-600 leading-relaxed mb-5">
                로버트 기요사키는 베스트셀러 <em class="not-italic font-semibold text-[#2D3047]">'부자 아빠 가난한 아빠'</em>에서 주장한 재정 교육 철학을 실천하기 위해 이 게임을 만들었습니다. 핵심은 세 가지입니다.
            </p>

            <div class="grid sm:grid-cols-3 gap-3 mb-5">
                <div class="bg-[#F8F9FA] rounded-xl p-4 border-t-2 border-[#FFB347]">
                    <p class="text-sm text-gray-600 leading-relaxed">진정한 재정적 자유는 <strong class="text-[#2D3047]">자산이 부채보다 더 많은 수동적 수입</strong>을 만들 때 달성됩니다.</p>
                </div>
                <div class="bg-[#F8F9FA] rounded-xl p-4 border-t-2 border-[#FFB347]">
                    <p class="text-sm text-gray-600 leading-relaxed">대부분의 학교 교육은 <strong class="text-[#2D3047]">실용적인 재정 지식</strong>을 가르치지 않습니다.</p>
                </div>
                <div class="bg-[#F8F9FA] rounded-xl p-4 border-t-2 border-[#FFB347]">
                    <p class="text-sm text-gray-600 leading-relaxed"><strong class="text-[#2D3047]">실수를 통해 배우는 것</strong>이 가장 효과적인 학습 방법 중 하나입니다.</p>
                </div>
            </div>

            <p class="text-gray-600 leading-relaxed">
                캐시플로우는 이 철학을 안전한 환경에서 실험해보는 도구입니다. 실제 돈을 위험에 노출시키지 않고도 투자와 재정 결정의 결과를 체험할 수 있습니다.
            </p>
        </div>
    </section>

    <!-- ===== 학습 목표 ===== -->
    <section id="goals" class="section-anchor">
        <div class="flex items-center gap-3 mb-6">
            <span class="w-11 h-11 rounded-xl bg-[#4ECDC4]/15 text-[#4ECDC4] flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-[#2D3047]">학습 목표</h2>
                <p class="text-sm text-gray-500">게임을 통해 익히게 되는 여섯 가지</p>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="goal-card">
                <span class="goal-icon">💰</span>
                <h3 class="goal-title">자산과 부채의 차이</h3>
                <p class="goal-desc">돈을 내 주머니에 넣어주는 것(자산)과 꺼내가는 것(부채)의 차이를 명확히 구분합니다.</p>
            </div>
            <div class="goal-card">
                <span class="goal-icon">🌊</span>
                <h3 class="goal-title">현금 흐름 관리</h3>
                <p class="goal-desc">수입, 지출, 투자, 부채 상환을 포함한 현금 흐름을 효과적으로 관리하는 법을 배웁니다.</p>
            </div>
            <div class="goal-card">
                <span class="goal-icon">🧺</span>
                <h3 class="goal-title">투자 다각화</h3>
                <p class="goal-desc">부동산·주식·사업체 등 투자 유형별 위험과 보상을 평가하는 방법을 익힙니다.</p>
            </div>
            <div class="goal-card">
                <span class="goal-icon">🔍</span>
                <h3 class="goal-title">재정적 기회 평가</h3>
                <p class="goal-desc">어떤 투자 기회가 가치 있고 어떤 것이 위험한지 판단하는 능력을 기릅니다.</p>
            </div>
            <div class="goal-card">
                <span class="goal-icon">🛡</span>
                <h3 class="goal-title">위험 관리</h3>
                <p class="goal-desc">예상치 못한 지출과 시장 변동에 대비하는 방법을 연습합니다.</p>
            </div>
            <div class="goal-card">
                <span class="goal-icon">🗺</span>
                <h3 class="goal-title">장기적 재정 계획</h3>
                <p class="goal-desc">눈앞의 수익보다 오래 지속되는 구조를 만드는 관점을 갖춥니다.</p>
            </div>
        </div>
    </section>

    <!-- ===== 게임 방법 ===== -->
    <section id="howto" class="section-anchor">
        <div class="flex items-center gap-3 mb-6">
            <span class="w-11 h-11 rounded-xl bg-[#FF4D4D]/15 text-[#FF4D4D] flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </span>
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-[#2D3047]">게임 방법</h2>
                <p class="text-sm text-gray-500">먼저 흐름만 보고, 필요하면 상세 규칙을 펼쳐보세요</p>
            </div>
        </div>

        <div class="inline-flex p-1 bg-gray-100 rounded-xl mb-5" role="tablist">
            <button type="button" class="rule-tab is-active" data-rule-tab="simple" role="tab" aria-selected="true">간단 규칙</button>
            <button type="button" class="rule-tab" data-rule-tab="detailed" role="tab" aria-selected="false">상세 규칙</button>
        </div>

        <!-- 간단 규칙 -->
        <div data-rule-panel="simple">
            <div class="grid md:grid-cols-3 gap-4 mb-5">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <span class="step-badge">1</span>
                    <h3 class="font-bold text-[#2D3047] mb-1.5">게임 준비</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">직업 카드를 뽑아 초기 재정 상태를 정하고, '쥐의 레이스' 트랙에서 시작합니다.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <span class="step-badge">2</span>
                    <h3 class="font-bold text-[#2D3047] mb-1.5">게임 진행</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">주사위를 던져 이동하고, 급여를 받고 지출을 처리하며, 투자로 수동적 수입을 늘립니다.</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <span class="step-badge">3</span>
                    <h3 class="font-bold text-[#2D3047] mb-1.5">게임 승리</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">월 수동적 수입이 월 총지출을 넘으면 '빠른 트랙'으로 이동하고, 최종 목표를 달성하면 승리합니다.</p>
                </div>
            </div>

            <div class="flex gap-3 bg-[#FF4D4D]/5 border border-[#FF4D4D]/20 rounded-xl p-4">
                <span class="text-lg leading-none flex-shrink-0">💡</span>
                <p class="text-sm text-gray-700 leading-relaxed">
                    <strong class="text-[#2D3047]">중요:</strong> 캐시플로우의 진정한 가치는 승리 자체보다, 게임을 통해 얻는 재정 지식과 통찰력에 있습니다.
                </p>
            </div>
        </div>

        <!-- 상세 규칙 -->
        <div data-rule-panel="detailed" class="hidden space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="flex items-center gap-2.5 font-bold text-[#2D3047] text-lg mb-4">
                    <span class="step-badge mb-0">1</span> 게임 준비
                </h3>
                <ol class="rule-list">
                    <li>각 플레이어는 직업 카드를 무작위로 뽑아 직업, 월급, 지출, 부채, 자산 등 초기 재정 상태를 결정합니다.</li>
                    <li>모든 플레이어는 '쥐의 레이스' 트랙의 시작 지점에서 출발합니다.</li>
                    <li>각 플레이어는 개인 재무제표를 작성하고 관리합니다.</li>
                    <li>필요한 구성품: 게임 보드, 직업 카드, 기회 카드, 시장 카드, 주사위, 게임 말, 재정 상태 시트, 현금 등</li>
                    <li>게임 버전에 따라 추가 준비 사항이 있을 수 있습니다.</li>
                </ol>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="flex items-center gap-2.5 font-bold text-[#2D3047] text-lg mb-4">
                    <span class="step-badge mb-0">2</span> 게임 진행
                </h3>
                <ol class="rule-list mb-5">
                    <li>순서대로 주사위를 던져 보드 위를 이동합니다.</li>
                    <li>급여일(Payday) 칸을 지나거나 정확히 도착하면 월급을 받습니다.</li>
                    <li>도착한 칸에 따라 아래 액션을 수행합니다.</li>
                </ol>

                <p class="text-xs font-semibold text-gray-400 tracking-wide mb-2">칸의 종류</p>
                <div class="grid sm:grid-cols-2 gap-2 mb-5">
                    <div class="tile"><strong>기회 (Opportunity)</strong><span>기회 카드를 뽑아 투자 기회를 얻습니다.</span></div>
                    <div class="tile"><strong>시장 (Market)</strong><span>시장 카드를 뽑아 시장 상황 이벤트를 겪습니다.</span></div>
                    <div class="tile"><strong>자선 (Charity)</strong><span>자선 활동으로 다음 턴에 혜택을 받습니다.</span></div>
                    <div class="tile"><strong>하향거래 (Doodad)</strong><span>예상치 못한 지출이 발생합니다.</span></div>
                    <div class="tile"><strong>유아 (Baby)</strong><span>아이가 태어나 지출이 늘어납니다.</span></div>
                    <div class="tile"><strong>다운사이징 (Downsized)</strong><span>실직하거나 급여가 줄어 턴을 쉬거나 현금을 냅니다.</span></div>
                </div>

                <p class="text-xs font-semibold text-gray-400 tracking-wide mb-2">투자 결정</p>
                <div class="grid sm:grid-cols-2 gap-2 mb-5">
                    <div class="tile"><strong>부동산</strong><span>월세 또는 현금 흐름을 주는 물리적 자산</span></div>
                    <div class="tile"><strong>주식</strong><span>가치가 변동하고 배당을 줄 수 있는 기업 소유권</span></div>
                    <div class="tile"><strong>사업체</strong><span>지속적인 수입을 만드는 자영업</span></div>
                    <div class="tile"><strong>기타 투자</strong><span>채권, 뮤추얼 펀드 등</span></div>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed">
                    각 턴이 끝날 때 수동적 수입과 지출을 계산해 현금 흐름을 파악합니다.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="flex items-center gap-2.5 font-bold text-[#2D3047] text-lg mb-4">
                    <span class="step-badge mb-0">3</span> 쥐의 레이스 탈출과 승리
                </h3>
                <ol class="rule-list mb-5">
                    <li>수동적 수입이 총지출을 넘으면 '레이스 탈출' 칸으로 이동할 수 있습니다.</li>
                    <li>탈출한 플레이어는 '빠른 트랙'으로 넘어갑니다.</li>
                    <li>'빠른 트랙'에서는 더 큰 투자 기회를 만나게 됩니다.</li>
                </ol>

                <p class="text-xs font-semibold text-gray-400 tracking-wide mb-2">승리 조건</p>
                <div class="space-y-2 mb-5">
                    <div class="tile"><strong>기본 목표</strong><span>수동적 수입이 총지출의 두 배 이상</span></div>
                    <div class="tile"><strong>꿈 목표</strong><span>자신이 설정한 꿈을 살 수 있는 현금 확보</span></div>
                    <div class="tile"><strong>최종 목표</strong><span>가장 먼저 초기 꿈을 사거나 가장 많은 현금 흐름 달성</span></div>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed">
                    게임 시간은 대략 2~3시간이며 플레이어 수에 따라 달라집니다.
                </p>
            </div>

            <div class="flex gap-3 bg-[#FF4D4D]/5 border border-[#FF4D4D]/20 rounded-xl p-4">
                <span class="text-lg leading-none flex-shrink-0">💡</span>
                <p class="text-sm text-gray-700 leading-relaxed">
                    <strong class="text-[#2D3047]">중요:</strong> 캐시플로우의 진정한 가치는 승리 자체보다, 게임을 통해 얻는 재정 지식과 통찰력에 있습니다.
                </p>
            </div>
        </div>
    </section>

    <!-- ===== 전략과 팁 ===== -->
    <section id="tips" class="section-anchor">
        <div class="flex items-center gap-3 mb-6">
            <span class="w-11 h-11 rounded-xl bg-[#9F5AFF]/15 text-[#9F5AFF] flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </span>
            <h2 class="text-xl md:text-2xl font-bold text-[#2D3047]">전략과 팁</h2>
        </div>

        <div class="space-y-2.5 mb-5">
            <div class="tip-card">
                <span class="tip-num">1</span>
                <div>
                    <h3 class="tip-title">현금 보유량 관리</h3>
                    <p class="tip-desc">예상치 못한 비용이나 투자 기회에 대비해 항상 현금을 남겨두세요.</p>
                </div>
            </div>
            <div class="tip-card">
                <span class="tip-num">2</span>
                <div>
                    <h3 class="tip-title">부채 관리</h3>
                    <p class="tip-desc">자산을 사기 위한 '좋은 부채'와 부채를 사기 위한 '나쁜 부채'를 구분하세요.</p>
                </div>
            </div>
            <div class="tip-card">
                <span class="tip-num">3</span>
                <div>
                    <h3 class="tip-title">수동적 수입 우선</h3>
                    <p class="tip-desc">단기 자본 이득보다 지속적인 현금 흐름을 만드는 투자에 집중하세요.</p>
                </div>
            </div>
            <div class="tip-card">
                <span class="tip-num">4</span>
                <div>
                    <h3 class="tip-title">기회 극대화</h3>
                    <p class="tip-desc">시장 붕괴나 특별 거래 같은 드문 기회를 놓치지 마세요.</p>
                </div>
            </div>
            <div class="tip-card">
                <span class="tip-num">5</span>
                <div>
                    <h3 class="tip-title">위험 분산</h3>
                    <p class="tip-desc">모든 달걀을 한 바구니에 담지 말고 투자를 다양화하세요.</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3 bg-[#FFB347]/10 border border-[#FFB347]/30 rounded-xl p-4">
            <span class="text-lg leading-none flex-shrink-0">⭐</span>
            <p class="text-sm text-gray-700 leading-relaxed">
                <strong class="text-[#2D3047]">전문가 팁:</strong> 대출로 더 많은 자산을 사는 레버리지를 현명하게 쓰되, 현금 흐름이 마이너스가 되지 않도록 항상 주의하세요.
            </p>
        </div>
    </section>

    <!-- ===== 챗봇 CTA ===== -->
    <section class="relative overflow-hidden rounded-3xl bg-[#2D3047] px-6 py-12 md:px-12 md:py-14 text-center">
        <div class="absolute inset-0" aria-hidden="true">
            <div class="absolute -top-24 -right-16 w-72 h-72 bg-[#4ECDC4] rounded-full blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-24 -left-16 w-72 h-72 bg-[#FF4D4D] rounded-full blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        </div>

        <div class="relative z-10 max-w-xl mx-auto">
            <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-xs font-medium mb-4 backdrop-blur-md">
                💬 캐시플로우 챗봇
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-3 leading-snug">
                규칙이 헷갈릴 때 바로 물어보세요
            </h2>
            <p class="text-gray-300 leading-relaxed mb-7">
                카드 사진을 찍어 올리면 어떤 카드인지, 지금 어떤 선택지가 있는지 정리해 드립니다.
            </p>

            @auth
            <button type="button" data-chatbot-open
                    class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-[#FF4D4D] text-white font-semibold shadow-lg shadow-[#FF4D4D]/25 hover:bg-[#FF6B6B] hover:-translate-y-0.5 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                챗봇 열기
            </button>
            @else
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-[#FF4D4D] text-white font-semibold shadow-lg shadow-[#FF4D4D]/25 hover:bg-[#FF6B6B] hover:-translate-y-0.5 transition-all duration-300">
                로그인하고 챗봇 이용하기
            </a>
            @endauth
        </div>
    </section>
</div>

@auth
{{--
    페이지가 길어 히어로의 버튼이 금방 화면 밖으로 나간다. 스크롤하면 나타나는 버튼을 둔다.
    푸터의 .fab-wrap (TOP / 상담) 과 같은 바닥선에, 그 왼쪽에 놓는다.
    원형 두 개와 구분되도록 라벨이 붙은 알약 모양으로 만든다.
--}}
<button type="button" data-chatbot-open id="chatbotFab" class="chatbot-fab" aria-label="캐시플로우 챗봇 열기">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
    </svg>
    <span>챗봇</span>
</button>

@include('cashflow._chatbot')
@endauth

@if(session('alert'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert("{{ session('alert') }}");
    });
</script>
@endif
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
    html { scroll-behavior: smooth; }

    /* 앵커로 이동할 때 상단 sticky 네비게이션에 가려지지 않게 한다. */
    .section-anchor { scroll-margin-top: 72px; }

    .section-link {
        flex-shrink: 0;
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #6B7280;
        white-space: nowrap;
        transition: background-color 0.2s, color 0.2s;
    }
    .section-link:hover { background: #F1F3F5; color: #2D3047; }

    .goal-card {
        background: #fff;
        border: 1px solid #F1F3F5;
        border-radius: 1rem;
        padding: 1.25rem;
        transition: transform 0.25s, box-shadow 0.25s;
    }
    .goal-card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -12px rgba(45, 48, 71, 0.18); }
    .goal-icon { display: block; font-size: 1.5rem; margin-bottom: 0.6rem; }
    .goal-title { font-weight: 700; color: #2D3047; margin-bottom: 0.35rem; }
    .goal-desc { font-size: 0.875rem; color: #6B7280; line-height: 1.65; }

    .rule-tab {
        padding: 0.5rem 1.1rem;
        border-radius: 0.65rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6B7280;
        transition: background-color 0.2s, color 0.2s, box-shadow 0.2s;
    }
    .rule-tab.is-active { background: #fff; color: #2D3047; box-shadow: 0 1px 3px rgba(45, 48, 71, 0.12); }

    .step-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 0.6rem;
        background: #FF4D4D;
        color: #fff;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        flex-shrink: 0;
    }

    .rule-list { list-style: decimal; padding-left: 1.15rem; color: #4B5563; font-size: 0.9rem; line-height: 1.75; }
    .rule-list > li { margin: 0.35rem 0; }

    .tile {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        background: #F8F9FA;
        border-radius: 0.65rem;
        padding: 0.7rem 0.9rem;
    }
    .tile > strong { font-size: 0.85rem; font-weight: 700; color: #2D3047; }
    .tile > span { font-size: 0.8rem; color: #6B7280; line-height: 1.6; }

    .tip-card {
        display: flex;
        gap: 0.9rem;
        background: #fff;
        border: 1px solid #F1F3F5;
        border-radius: 1rem;
        padding: 1.1rem 1.25rem;
        transition: border-color 0.2s;
    }
    .tip-card:hover { border-color: rgba(159, 90, 255, 0.25); }
    .tip-num {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.75rem;
        height: 1.75rem;
        flex-shrink: 0;
        border-radius: 0.6rem;
        background: rgba(159, 90, 255, 0.12);
        color: #9F5AFF;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .tip-title { font-weight: 700; color: #2D3047; margin-bottom: 0.2rem; }
    .tip-desc { font-size: 0.875rem; color: #6B7280; line-height: 1.65; }

    /* 플로팅 챗봇 버튼: 히어로를 지나야 나타난다.
       Tailwind Play CDN 은 초기 HTML 에 없는 클래스를 만들어주지 않으므로
       JS 로 토글하는 상태는 순수 CSS 로 둔다.
       위치는 푸터 .fab-wrap (bottom/right 2rem, 버튼 3.2rem) 왼쪽에 맞춘다. */
    .chatbot-fab {
        position: fixed;
        bottom: 2rem;
        right: calc(2rem + 3.2rem + 0.75rem);
        z-index: 50;
        height: 3.2rem;
        padding: 0 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.45rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 9999px;
        background: rgba(45, 48, 71, 0.92);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: #fff;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.22);
        opacity: 0;
        visibility: hidden;
        transform: translateY(12px);
        transition: opacity 0.25s, transform 0.25s, visibility 0.25s, background-color 0.2s;
    }
    .chatbot-fab.is-visible { opacity: 1; visibility: visible; transform: none; }
    .chatbot-fab:hover { background: #2D3047; border-color: rgba(78, 205, 196, 0.5); }
    .chatbot-fab svg { color: #4ECDC4; }

    @media (max-width: 640px) {
        /* 모바일에서는 .fab-wrap 이 bottom/right 1.5rem, 버튼 2.8rem 로 줄어든다. */
        .chatbot-fab {
            bottom: 1.5rem;
            right: calc(1.5rem + 2.8rem + 0.6rem);
            height: 2.8rem;
            padding: 0 0.9rem;
            font-size: 0.78rem;
        }
    }

    /* Swiper */
    .gameImageSlider .swiper-button-next,
    .gameImageSlider .swiper-button-prev {
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        background: rgba(45, 48, 71, 0.45);
        color: #fff;
        backdrop-filter: blur(4px);
        transition: background-color 0.2s;
    }
    .gameImageSlider .swiper-button-next:hover,
    .gameImageSlider .swiper-button-prev:hover { background: rgba(45, 48, 71, 0.75); }
    .gameImageSlider .swiper-button-next:after,
    .gameImageSlider .swiper-button-prev:after { font-size: 14px; font-weight: 700; }
    .gameImageSlider .swiper-pagination-bullet { width: 8px; height: 8px; background: #fff; opacity: 0.5; }
    .gameImageSlider .swiper-pagination-bullet-active { opacity: 1; background: #FF4D4D; }

    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .animate-blob, .animate-slideUp { animation: none; }
        .goal-card:hover { transform: none; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swiper !== 'undefined') {
        new Swiper('.gameImageSlider', {
            loop: true,
            autoplay: { delay: 4000, disableOnInteraction: false },
            navigation: {
                nextEl: '.gameImageSlider .swiper-button-next',
                prevEl: '.gameImageSlider .swiper-button-prev'
            },
            pagination: { el: '.gameImageSlider .swiper-pagination', clickable: true }
        });
    }

    // 규칙 탭
    var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-rule-tab]'));
    var panels = Array.prototype.slice.call(document.querySelectorAll('[data-rule-panel]'));

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var name = tab.getAttribute('data-rule-tab');

            tabs.forEach(function (other) {
                var active = other === tab;
                other.classList.toggle('is-active', active);
                other.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            panels.forEach(function (panel) {
                panel.classList.toggle('hidden', panel.getAttribute('data-rule-panel') !== name);
            });
        });
    });

    // 플로팅 챗봇 버튼 노출 제어
    var fab = document.getElementById('chatbotFab');

    if (fab) {
        var toggleFab = function () {
            fab.classList.toggle('is-visible', window.pageYOffset > 400);
        };

        toggleFab();
        window.addEventListener('scroll', toggleFab, { passive: true });
    }
});
</script>
@endpush
