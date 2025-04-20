@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-[900px]">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">캐시플로우(Cashflow) 게임 소개</h1>
        <p class="text-gray-700">재정 지능을 향상시키는 교육용 보드게임</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <!-- 게임 이미지 섹션 - Swiper 슬라이더로 변경 -->
        <div class="px-6 py-4 bg-white">
            <!-- Swiper 슬라이더 -->
            <div class="swiper gameImageSlider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="{{ asset('/images/cashflow/intro_1.png') }}" 
                             alt="캐시플로우 게임 이미지 1" 
                             class="w-full h-auto object-cover rounded-lg">
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('/images/cashflow/job_1.png') }}" 
                             alt="캐시플로우 게임 이미지 2" 
                             class="w-full h-auto object-cover rounded-lg">
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('/images/cashflow/card_1.png') }}" 
                             alt="캐시플로우 게임 이미지 3" 
                             class="w-full h-auto object-cover rounded-lg">
                    </div>
                </div>
                <!-- 네비게이션 버튼 -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <!-- 페이지네이션 -->
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <!-- 게임 개요 섹션 -->
        <div class="px-6 py-4 bg-point bg-opacity-20 border-b border-gray-200">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-800">게임 개요</h2>
            </div>
            <div class="pl-8 space-y-3 text-gray-700">
                <p>캐시플로우(Cashflow)는 로버트 기요사키(Robert Kiyosaki)에 의해 개발된 교육용 보드게임으로, 재정 지능을 향상시키기 위해 설계되었습니다. 이 게임은 금융 교육과 투자 원칙을 실제와 유사한 시뮬레이션을 통해 경험할 수 있게 해줍니다.</p>
                <p>플레이어들은 다양한 직업과 재정 상황에서 시작하여 '쥐의 레이스(Rat Race)'라고 불리는 상태에서 벗어나 '빠른 트랙(Fast Track)'으로 이동하는 과정을 경험합니다. 이 과정에서 부동산, 주식, 사업체 등 다양한 투자 기회를 통해 수동적 수입을 창출하고 자산을 구축하는 방법을 배웁니다.</p>
            </div>
        </div>        

        <!-- 개발 배경 및 철학 섹션 -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-800">개발 배경 및 철학</h2>
            </div>
            <div class="pl-8 space-y-3 text-gray-700">
                <p>로버트 기요사키는 그의 베스트셀러 '부자 아빠 가난한 아빠(Rich Dad Poor Dad)' 시리즈를 통해 주장한 재정 교육 철학을 실천하기 위해 이 게임을 개발했습니다. 그의 핵심 철학은 다음과 같습니다:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>진정한 재정적 자유는 '자산'이 '부채'보다 더 많은 수동적 수입을 창출할 때 달성됩니다.</li>
                    <li>대부분의 학교 교육은 실용적인 재정 지식을 가르치지 않습니다.</li>
                    <li>실수를 통해 배우는 것이 가장 효과적인 학습 방법 중 하나입니다.</li>
                </ul>
                <p>캐시플로우 게임은 이러한 철학을 안전한 환경에서 실험하고 경험할 수 있는 교육적 도구입니다. 실제 돈을 위험에 노출시키지 않고도 투자와 재정 결정의 결과를 체험할 수 있습니다.</p>
            </div>
        </div>

        <!-- 학습 목표 섹션 -->
        <div class="px-6 py-4 bg-white border-t border-gray-200">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-800">학습 목표</h2>
            </div>
            <div class="pl-8 space-y-3 text-gray-700">
                <p>캐시플로우 게임을 통해 다음과 같은 핵심 재정 개념과 기술을 배울 수 있습니다:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <h3 class="font-bold text-green-700 mb-1">자산과 부채의 차이 이해</h3>
                        <p class="text-sm text-gray-700">돈을 당신의 주머니에 넣어주는 것(자산)과 돈을 당신의 주머니에서 꺼내가는 것(부채)의 차이를 명확히 이해합니다.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <h3 class="font-bold text-green-700 mb-1">현금 흐름 관리</h3>
                        <p class="text-sm text-gray-700">수입, 지출, 투자, 부채 상환 등을 포함한 현금 흐름을 효과적으로 관리하는 방법을 배웁니다.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <h3 class="font-bold text-green-700 mb-1">투자 다각화</h3>
                        <p class="text-sm text-gray-700">다양한 투자 유형(부동산, 주식, 사업체 등)의 위험과 보상을 평가하는 방법을 배웁니다.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <h3 class="font-bold text-green-700 mb-1">재정적 기회 평가</h3>
                        <p class="text-sm text-gray-700">어떤 투자 기회가 가치 있는지, 어떤 것이 위험한지 판단하는 능력을 개발합니다.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <h3 class="font-bold text-green-700 mb-1">위험 관리</h3>
                        <p class="text-sm text-gray-700">금융 위기와 예상치 못한 비용에 대처하는 방법을 배웁니다.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <h3 class="font-bold text-green-700 mb-1">장기적 재정 계획</h3>
                        <p class="text-sm text-gray-700">단기적인 만족보다 장기적인 재정적 자유를 우선시하는 사고방식을 기릅니다.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 게임 규칙 및 플레이 방법 섹션 -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-800">게임 규칙 및 플레이 방법</h2>
            </div>
            <div class="pl-8 space-y-3 text-gray-700">
                <p>캐시플로우 게임은 다음과 같은 기본 규칙과 순서로 진행됩니다:</p>
                
                <!-- 탭 네비게이션 -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button id="tab-simple" class="tab-btn px-4 py-2 border-b-2 border-yellow-500 font-medium text-sm leading-5 text-yellow-600 focus:outline-none focus:text-yellow-800 focus:border-yellow-700">
                            간단 규칙
                        </button>
                        <button id="tab-detailed" class="tab-btn px-4 py-2 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                            상세 규칙
                        </button>
                    </nav>
                </div>
                
                <!-- 간단 규칙 탭 내용 -->
                <div id="content-simple" class="tab-content">
                    <div class="py-4">
                        <div class="space-y-4">
                            <div class="flex">
                                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-800 font-bold mr-3">1</div>
                                <div>
                                    <h3 class="font-bold text-gray-800">게임 준비</h3>
                                    <p>직업 카드를 뽑아 초기 재정 상태를 결정하고, '쥐의 레이스' 트랙에서 시작합니다.</p>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-800 font-bold mr-3">2</div>
                                <div>
                                    <h3 class="font-bold text-gray-800">게임 진행</h3>
                                    <p>주사위를 던져 이동하고, 급여를 받고 지출을 처리하며, 다양한 투자를 통해 수동적 수입을 늘립니다.</p>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-800 font-bold mr-3">3</div>
                                <div>
                                    <h3 class="font-bold text-gray-800">게임 승리</h3>
                                    <p>월간 수동적 수입이 월간 총 지출을 초과하면 '빠른 트랙'으로 이동하고, 최종 목표를 달성하면 승리합니다.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 my-4 rounded-r">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800">
                                        <span class="font-bold">중요:</span> 캐시플로우 게임의 진정한 가치는 승리 자체보다, 게임을 통해 얻는 재정 지식과 통찰력에 있습니다!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 상세 규칙 탭 내용 -->
                <div id="content-detailed" class="tab-content hidden">
                    <div class="py-4">
                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h3 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                                <span class="flex items-center justify-center w-6 h-6 bg-red-100 rounded-full text-red-800 text-sm mr-2">1</span>
                                게임 준비
                            </h3>
                            <ol class="list-decimal pl-10 space-y-2">
                                <li>각 플레이어는 직업 카드를 무작위로 뽑아 자신의 직업, 월급, 지출, 부채, 자산 등 초기 재정 상태를 결정합니다.</li>
                                <li>모든 플레이어는 '쥐의 레이스' 트랙의 시작 지점에서 시작합니다.</li>
                                <li>각 플레이어는 개인 재무제표를 작성하고 관리합니다.</li>
                                <li>필요한 게임 구성품: 게임 보드, 직업 카드, 기회 카드, 시장 카드, 주사위, 게임 말, 재정 상태 시트, 현금 등</li>
                                <li>캐시플로우 게임 버전에 따라 추가적인 준비 사항이 있을 수 있습니다.</li>
                            </ol>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h3 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                                <span class="flex items-center justify-center w-6 h-6 bg-red-100 rounded-full text-red-800 text-sm mr-2">2</span>
                                게임 진행
                            </h3>
                            <ol class="list-decimal pl-10 space-y-2">
                                <li>플레이어들은 순서대로 주사위를 던져 보드 위에서 이동합니다.</li>
                                <li>각 턴의 시작에 급여일(Payday) 칸을 지나거나 정확히 도착하면 월급을 받습니다.</li>
                                <li>게임 말이 도착한 칸에 따라 다음 액션을 수행합니다:
                                    <ul class="list-disc pl-8 mt-1 space-y-1 text-sm">
                                        <li><strong>기회(Opportunity) 칸</strong>: 기회 카드를 뽑아 투자 기회를 얻을 수 있습니다.</li>
                                        <li><strong>시장(Market) 칸</strong>: 시장 카드를 뽑아 시장 상황에 따른 이벤트를 경험합니다.</li>
                                        <li><strong>자선(Charity) 칸</strong>: 자선 활동을 통해 다음 턴에 혜택을 받을 수 있습니다.</li>
                                        <li><strong>하향거래(Doodad) 칸</strong>: 예상치 못한 지출이 발생합니다.</li>
                                        <li><strong>유아(Baby) 칸</strong>: 아이가 태어나 지출이 증가합니다.</li>
                                        <li><strong>다운사이징(Downsized) 칸</strong>: 실직하거나 급여가 줄어 턴을 쉬거나 현금을 지불합니다.</li>
                                    </ul>
                                </li>
                                <li>투자 결정: 플레이어는 기회 카드나 다른 플레이어와의 거래를 통해 다음과 같은 투자를 할 수 있습니다:
                                    <ul class="list-disc pl-8 mt-1 space-y-1 text-sm">
                                        <li><strong>부동산</strong>: 월세 또는 현금 흐름을 제공하는 물리적 자산</li>
                                        <li><strong>주식</strong>: 가치가 변동하고 배당금을 지급할 수 있는 기업 소유권</li>
                                        <li><strong>사업체</strong>: 지속적인 수입을 창출하는 자영업</li>
                                        <li><strong>기타 투자</strong>: 채권, 뮤추얼 펀드 등</li>
                                    </ul>
                                </li>
                                <li>현금 흐름 계산: 각 턴이 끝날 때 자신의 수동적 수입과 지출을 계산하여 현금 흐름을 파악합니다.</li>
                            </ol>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h3 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                                <span class="flex items-center justify-center w-6 h-6 bg-red-100 rounded-full text-red-800 text-sm mr-2">3</span>
                                쥐의 레이스 탈출 및 게임 승리
                            </h3>
                            <ol class="list-decimal pl-10 space-y-2">
                                <li>수동적 수입이 총 지출을 초과하면 '레이스 탈출' 칸으로 이동할 수 있습니다.</li>
                                <li>레이스를 탈출한 플레이어는 '빠른 트랙'으로 이동합니다.</li>
                                <li>'빠른 트랙'에서 플레이어들은 더 큰 투자 기회를 만나게 됩니다.</li>
                                <li>게임의 승리 조건:
                                    <ul class="list-disc pl-8 mt-1 space-y-1 text-sm">
                                        <li>기본 목표: 수동적 수입이 총 지출의 두 배 이상 달성</li>
                                        <li>꿈 목표: 자신이 설정한 꿈을 구매할 수 있는 충분한 현금 확보</li>
                                        <li>최종 목표: 가장 먼저 초기 꿈을 구매하거나 가장 많은 현금 흐름 달성</li>
                                    </ul>
                                </li>
                                <li>게임 시간은 대략 2~3시간 소요되며, 플레이어 수에 따라 달라질 수 있습니다.</li>
                            </ol>
                        </div>

                        <div class="bg-red-50 border-l-4 border-red-400 p-4 my-4 rounded-r">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800">
                                        <span class="font-bold">중요:</span> 캐시플로우 게임의 진정한 가치는 승리 자체보다, 게임을 통해 얻는 재정 지식과 통찰력에 있습니다!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 탭 작동을 위한 자바스크립트 -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabSimple = document.getElementById('tab-simple');
                const tabDetailed = document.getElementById('tab-detailed');
                const contentSimple = document.getElementById('content-simple');
                const contentDetailed = document.getElementById('content-detailed');
                
                tabSimple.addEventListener('click', function() {
                    // 활성화 스타일 변경
                    tabSimple.classList.add('border-yellow-500', 'text-yellow-600');
                    tabSimple.classList.remove('border-transparent', 'text-gray-500');
                    tabDetailed.classList.remove('border-yellow-500', 'text-yellow-600');
                    tabDetailed.classList.add('border-transparent', 'text-gray-500');
                    
                    // 콘텐츠 표시/숨김
                    contentSimple.classList.remove('hidden');
                    contentDetailed.classList.add('hidden');
                });
                
                tabDetailed.addEventListener('click', function() {
                    // 활성화 스타일 변경
                    tabDetailed.classList.add('border-yellow-500', 'text-yellow-600');
                    tabDetailed.classList.remove('border-transparent', 'text-gray-500');
                    tabSimple.classList.remove('border-yellow-500', 'text-yellow-600');
                    tabSimple.classList.add('border-transparent', 'text-gray-500');
                    
                    // 콘텐츠 표시/숨김
                    contentDetailed.classList.remove('hidden');
                    contentSimple.classList.add('hidden');
                });
            });
        </script>
        
        <!-- 플레이 팁과 전략 섹션 -->
        <div class="px-6 py-4 bg-white border-t border-gray-200">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-800">플레이 팁과 전략</h2>
            </div>
            <div class="pl-8 space-y-3 text-gray-700">
                <p>캐시플로우 게임에서 성공하기 위한 몇 가지 핵심 전략과 팁은 다음과 같습니다:</p>
                <div class="space-y-3">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-800 font-bold mr-3">1</div>
                        <div>
                            <h3 class="font-bold text-gray-800">현금 보유량 관리</h3>
                            <p>항상 예상치 못한 비용이나 투자 기회를 위한 현금을 보유하세요.</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-800 font-bold mr-3">2</div>
                        <div>
                            <h3 class="font-bold text-gray-800">부채 관리</h3>
                            <p>'좋은 부채'(자산을 구입하기 위한)와 '나쁜 부채'(부채를 구입하기 위한)를 구분하세요.</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-800 font-bold mr-3">3</div>
                        <div>
                            <h3 class="font-bold text-gray-800">수동적 수입 우선</h3>
                            <p>단기적인 자본 이득보다 지속적인 현금 흐름을 창출하는 투자에 집중하세요.</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-800 font-bold mr-3">4</div>
                        <div>
                            <h3 class="font-bold text-gray-800">기회 극대화</h3>
                            <p>시장 붕괴나 특별 거래 같은 특별한 기회를 활용하세요.</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-800 font-bold mr-3">5</div>
                        <div>
                            <h3 class="font-bold text-gray-800">위험 분산</h3>
                            <p>모든 달걀을 한 바구니에 담지 마세요 - 투자를 다양화하세요.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-4 rounded-r">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-800">
                                <span class="font-bold">전문가 팁:</span> 대출을 통해 더 많은 자산을 구매하는 레버리지 효과를 현명하게 활용하되, 항상 현금 흐름이 마이너스가 되지 않도록 주의하세요!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 하단 CTA 섹션 -->
        <div class="px-6 py-8 bg-point bg-opacity-10 border-t border-gray-200 text-center">
            <h2 class="text-xl font-bold text-gray-800 mb-4">캐시플로우를 통해 재정 지능을 키우세요!</h2>
            <p class="text-gray-700 mb-6">실전 경험을 통한 학습으로 재정적 자유를 향한 첫 걸음을 내딛어보세요.</p>
            <button onclick="alert('캐시플로우 게임 진행은 준비 중입니다. 곧 만나요!');" class="inline-flex items-center px-6 py-3 bg-point text-cdark font-medium rounded-md shadow-sm hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition-colors">
                <span>게임 체험하기</span>
                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

@if(session('alert'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert("{{ session('alert') }}");
    });
</script>
@endif
@endsection

@push('scripts')
<!-- Swiper JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 게임 이미지 슬라이더
        const gameImageSlider = new Swiper('.gameImageSlider', {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.gameImageSlider .swiper-button-next',
                prevEl: '.gameImageSlider .swiper-button-prev',
            },
            pagination: {
                el: '.gameImageSlider .swiper-pagination',
                clickable: true,
            },
        });

        // 탭 관련 기존 스크립트
        const tabSimple = document.getElementById('tab-simple');
        const tabDetailed = document.getElementById('tab-detailed');
        const contentSimple = document.getElementById('content-simple');
        const contentDetailed = document.getElementById('content-detailed');
        
        if (tabSimple && tabDetailed && contentSimple && contentDetailed) {
            tabSimple.addEventListener('click', function() {
                // 활성화 스타일 변경
                tabSimple.classList.add('border-yellow-500', 'text-yellow-600');
                tabSimple.classList.remove('border-transparent', 'text-gray-500');
                tabDetailed.classList.remove('border-yellow-500', 'text-yellow-600');
                tabDetailed.classList.add('border-transparent', 'text-gray-500');
                
                // 콘텐츠 표시/숨김
                contentSimple.classList.remove('hidden');
                contentDetailed.classList.add('hidden');
            });
            
            tabDetailed.addEventListener('click', function() {
                // 활성화 스타일 변경
                tabDetailed.classList.add('border-yellow-500', 'text-yellow-600');
                tabDetailed.classList.remove('border-transparent', 'text-gray-500');
                tabSimple.classList.remove('border-yellow-500', 'text-yellow-600');
                tabSimple.classList.add('border-transparent', 'text-gray-500');
                
                // 콘텐츠 표시/숨김
                contentDetailed.classList.remove('hidden');
                contentSimple.classList.add('hidden');
            });
        }
    });
</script>

<style>
    .card-header.bg-point {
        background-color: #fecc41;
    }
    
    h2.h4, h3.h5 {
        color: #2b2b2b;
        font-weight: 600;
    }
    
    .alert-info {
        background-color: #f2f8ff;
        border-left: 4px solid #0d6efd;
        color: #0c5460;
    }
    
    .alert-warning {
        background-color: #fff9e6;
        border-left: 4px solid #ffc107;
        color: #856404;
    }
    
    /* Swiper 스타일 */
    .gameImageSlider {
        height: 400px;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .gameImageSlider .swiper-slide {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9f9f9;
    }
    
    .gameImageSlider .swiper-slide img {
        max-height: 100%;
        width: auto;
        margin: 0 auto;
    }
    
    .gameImageSlider .swiper-button-next,
    .gameImageSlider .swiper-button-prev {
        color: #fff;
        background: rgba(0, 0, 0, 0.3);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .gameImageSlider .swiper-button-next:hover,
    .gameImageSlider .swiper-button-prev:hover {
        background: rgba(0, 0, 0, 0.6);
    }
    
    .gameImageSlider .swiper-button-next:after,
    .gameImageSlider .swiper-button-prev:after {
        font-size: 18px;
    }
    
    .gameImageSlider .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(0, 0, 0, 0.3);
    }
    
    .gameImageSlider .swiper-pagination-bullet-active {
        background: #fecc41;
    }
</style>
@endpush 