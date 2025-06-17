@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-[768px]">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">로드맵 작성하기</h1>
        <p class="text-gray-700">목표 달성을 위한 재무 계획을 확인하세요</p>
    </div>

    @if(!$hasLifeGoal)
        <!-- 목표금액이 없는 경우 -->
        <div class="flex flex-col items-center justify-center min-h-[400px] bg-white rounded-lg shadow-lg p-8">
            <svg class="w-12 h-12 md:w-16 md:h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4">목표 설정을 먼저 진행해주세요.</h2>
            <a href="{{ route('guidebook.life-search') }}" 
               class="inline-flex items-center px-6 py-3 bg-point text-cdark rounded-lg hover:bg-opacity-90 transition-all">
                <span class="mr-2">바로가기</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    @elseif(!$hasExpenses)
        <!-- 지출 데이터가 없는 경우 -->
        <div class="flex flex-col items-center justify-center min-h-[400px] bg-white rounded-lg shadow-lg p-8">
            <svg class="w-12 h-12 md:w-16 md:h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4">월별 지출 내역을 먼저 작성해주세요.</h2>
            <a href="{{ route('guidebook.reality-check') }}" 
               class="inline-flex items-center px-6 py-3 bg-point text-cdark rounded-lg hover:bg-opacity-90 transition-all">
                <span class="mr-2">바로가기</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    @else
        <div class="space-y-6">
            <!-- 목표 금액 및 진행 상황 -->
            <div class="bg-white rounded-lg shadow-lg p-4 md:p-8">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <h2 class="text-lg md:text-2xl font-bold text-gray-800 mb-2">목표 금액</h2>
                        <p class="text-xl md:text-4xl font-bold text-gray-800">
                            {{ number_format($data['targetAmount']) }}<span class="text-base md:text-xl text-gray-600 ml-1">원</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <h3 class="text-lg md:text-2xl font-bold text-gray-800 mb-2">남은 기간</h3>
                        <p class="text-xl md:text-4xl font-bold text-gray-800">
                            {{ $data['remainingMonths'] }}<span class="text-base md:text-xl text-gray-600 ml-1">개월</span>
                        </p>
                    </div>
                </div>

                <!-- 현재 재정 상태 -->
                <div class="mt-6 pt-6 border-t">
                    <h2 class="text-lg md:text-2xl font-bold text-gray-800 mb-4">월별 수입/지출 내역</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-3 border rounded-lg">
                            <h3 class="text-base font-semibold text-green-600 mb-1">총 수입</h3>
                            <p class="text-lg md:text-2xl font-bold text-gray-800">
                                {{ number_format($data['totalIncome']) }}<span class="text-sm md:text-base text-gray-600 ml-1">원</span>
                            </p>
                        </div>
                        <div class="text-center p-3 border rounded-lg">
                            <h3 class="text-base font-semibold text-red-600 mb-1">총 지출</h3>
                            <p class="text-lg md:text-2xl font-bold text-gray-800">
                                {{ number_format($data['totalExpense']) }}<span class="text-sm md:text-base text-gray-600 ml-1">원</span>
                            </p>
                        </div>
                        <div class="text-center p-3 border rounded-lg">
                            <h3 class="text-base font-semibold {{ $data['difference'] >= 0 ? 'text-blue-600' : 'text-red-600' }} mb-1">수입-지출</h3>
                            <p class="text-lg md:text-2xl font-bold {{ $data['difference'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                {{ number_format($data['difference']) }}<span class="text-sm md:text-base text-gray-600 ml-1">원</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 월 저축액 게이지 바 -->
                <div class="mt-6 pt-6 border-t">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-base md:text-lg font-bold text-gray-800">월 저축액</h3>
                        <div class="flex items-center">
                            <input type="text" 
                                   id="monthlySavingInput"
                                   class="w-32 text-right px-2 py-1 border rounded mr-1 text-base md:text-lg font-bold text-gray-800" maxlength="9"
                                   value="{{ number_format(max(10000, $data['difference'])) }}">
                            <span class="text-base md:text-lg font-bold text-gray-800">원</span>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="range" 
                               id="monthlySaving" 
                               min="10000" 
                               max="{{ $data['totalIncome'] }}" 
                               step="10000" 
                               value="{{ max(10000, $data['difference']) }}"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between mt-2 text-xs md:text-sm text-gray-600">
                            <span>1만원</span>
                            <span>{{ number_format($data['totalIncome']) }}원</span>
                        </div>
                    </div>
                </div>

                <!-- 투자 포트폴리오 선택 -->
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-base md:text-lg font-bold text-gray-800 mb-4">투자 포트폴리오 선택</h3>
                    <div class="space-y-4">
                        <!-- 5% 수익률 포트폴리오 -->
                        <div class="portfolio-option border-2 rounded-lg p-4 cursor-pointer active hover:shadow-lg transition-all duration-300" data-return="5">
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="text-base font-bold text-blue-800">안정형 포트폴리오</h4>
                                    <p class="text-sm text-blue-600">보수적인 투자자에게 적합</p>
                                </div>
                                <div class="flex items-center bg-blue-100 px-3 py-1 rounded-full">
                                    <span class="text-lg font-bold text-blue-800">5%</span>
                                    <span class="text-sm text-blue-600 ml-1">수익률</span>
                                </div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4 mb-3">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">국채 및 정부채권</p>
                                            <p class="text-lg font-bold text-blue-800">50%</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">회사채</p>
                                            <p class="text-lg font-bold text-blue-800">30%</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-blue-300 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">우량 배당주</p>
                                            <p class="text-lg font-bold text-blue-800">15%</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-blue-200 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">현금성 자산</p>
                                            <p class="text-lg font-bold text-blue-800">5%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 안정형 투자 대가 리스트 (초기에 표시됨) -->
                            <div class="portfolio-masters portfolio-masters-5 mt-4 border-t pt-4">
                                <h5 class="text-sm font-bold text-gray-700 mb-3">추천 포트폴리오</h5>
                                <div class="space-y-3">
                                    @if(isset($data['recommendedPortfolios']['stable']) && !isset($data['recommendedPortfolios']['stable']['message']))
                                        @foreach($data['recommendedPortfolios']['stable'] as $portfolio)
                                            <a href="{{ route('board-portfolio.show', $portfolio['board_portfolio_idx']) }}" class="flex items-center justify-between p-3 bg-white border rounded-lg hover:bg-blue-50 transition-all">
                                                <p class="font-semibold text-gray-800">{{ $portfolio['investor_name'] }} - {{ number_format($portfolio['portfolio_avg_return'], 2) }}%</p>
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-3 bg-blue-50 rounded-lg text-center">
                                            <p class="text-blue-800">추천 포트폴리오를 준비중입니다</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- 10% 수익률 포트폴리오 -->
                        <div class="portfolio-option border-2 rounded-lg p-4 cursor-pointer hover:shadow-lg transition-all duration-300" data-return="10">
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="text-base font-bold text-green-800">성장형 포트폴리오</h4>
                                    <p class="text-sm text-green-600">중립적인 투자자에게 적합</p>
                                </div>
                                <div class="flex items-center bg-green-100 px-3 py-1 rounded-full">
                                    <span class="text-lg font-bold text-green-800">10%</span>
                                    <span class="text-sm text-green-600 ml-1">수익률</span>
                                </div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 mb-3">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">주식(국내/해외)</p>
                                            <p class="text-lg font-bold text-green-800">50%</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">채권</p>
                                            <p class="text-lg font-bold text-green-800">30%</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-300 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">리츠(REITs)</p>
                                            <p class="text-lg font-bold text-green-800">10%</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-200 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">대체 투자</p>
                                            <p class="text-lg font-bold text-green-800">10%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 성장형 투자 대가 리스트 (초기에 숨겨짐) -->
                            <div class="portfolio-masters portfolio-masters-10 mt-4 border-t pt-4 hidden">
                                <h5 class="text-sm font-bold text-gray-700 mb-3">추천 포트폴리오</h5>
                                <div class="space-y-3">
                                    @if(isset($data['recommendedPortfolios']['growth']) && !isset($data['recommendedPortfolios']['growth']['message']))
                                        @foreach($data['recommendedPortfolios']['growth'] as $portfolio)
                                            <a href="{{ route('board-portfolio.show', $portfolio['board_portfolio_idx']) }}" class="flex items-center justify-between p-3 bg-white border rounded-lg hover:bg-green-50 transition-all">
                                                <p class="font-semibold text-gray-800">{{ $portfolio['investor_name'] }} - {{ number_format($portfolio['portfolio_avg_return'], 2) }}%</p>
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-3 bg-green-50 rounded-lg text-center">
                                            <p class="text-green-800">추천 포트폴리오를 준비중입니다</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- 15% 수익률 포트폴리오 -->
                        <div class="portfolio-option border-2 rounded-lg p-4 cursor-pointer hover:shadow-lg transition-all duration-300" data-return="15">
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="text-base font-bold text-purple-800">공격형 포트폴리오</h4>
                                    <p class="text-sm text-purple-600">적극적인 투자자에게 적합</p>
                                </div>
                                <div class="flex items-center bg-purple-100 px-3 py-1 rounded-full">
                                    <span class="text-lg font-bold text-purple-800">15%</span>
                                    <span class="text-sm text-purple-600 ml-1">수익률</span>
                                </div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4 mb-3">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">고성장 주식</p>
                                            <p class="text-lg font-bold text-purple-800">60%</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-purple-400 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">벤처/스타트업</p>
                                            <p class="text-lg font-bold text-purple-800">20%</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-purple-300 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">대체 투자</p>
                                            <p class="text-lg font-bold text-purple-800">15%</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-purple-200 rounded-full mr-2"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">현금성 자산</p>
                                            <p class="text-lg font-bold text-purple-800">5%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 공격형 투자 대가 리스트 (초기에 숨겨짐) -->
                            <div class="portfolio-masters portfolio-masters-15 mt-4 border-t pt-4 hidden">
                                <h5 class="text-sm font-bold text-gray-700 mb-3">추천 포트폴리오</h5>
                                <div class="space-y-3">
                                    @if(isset($data['recommendedPortfolios']['aggressive']) && !isset($data['recommendedPortfolios']['aggressive']['message']))
                                        @foreach($data['recommendedPortfolios']['aggressive'] as $portfolio)
                                            <a href="{{ route('board-portfolio.show', $portfolio['board_portfolio_idx']) }}" class="flex items-center justify-between p-3 bg-white border rounded-lg hover:bg-purple-50 transition-all">
                                                <p class="font-semibold text-gray-800">{{ $portfolio['investor_name'] }} - {{ number_format($portfolio['portfolio_avg_return'], 2) }}%</p>
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-3 bg-purple-50 rounded-lg text-center">
                                            <p class="text-purple-800">추천 포트폴리오를 준비중입니다</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<!-- eCharts CDN 추가 -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js"></script>

<script>
@if(isset($data))
document.addEventListener('DOMContentLoaded', function() {
    const expenses = @json($data['monthlyExpenses']);
    const colors = [
        '#2C3E50', '#E67E22', '#27AE60', '#2980B9', 
        '#8E44AD', '#C0392B', '#16A085', '#D35400',
        '#2ECC71', '#3498DB', '#9B59B6', '#E74C3C',
        '#1ABC9C', '#F1C40F', '#E91E63', '#9C27B0',
        '#3F51B5', '#00BCD4', '#4CAF50', '#FF9800'
    ];

    // 게이지바 관련 요소들
    const monthlySaving = document.getElementById('monthlySaving');
    const monthlySavingInput = document.getElementById('monthlySavingInput');
    
    // 포트폴리오 선택 관련 변수 및 함수
    let selectedPortfolioReturn = 5; // 기본값 5%

    // 게이지바 배경 업데이트 함수
    function updateRangeBackground(element) {
        if (!element) return;
        const min = element.min || 0;
        const max = element.max || 100;
        const value = element.value;
        const percentage = ((value - min) / (max - min)) * 100;
        element.style.backgroundSize = `${percentage}% 100%`;
    }

    // 숫자만 추출하는 함수
    function extractNumber(str) {
        return parseInt(str.replace(/[^\d]/g, '')) || 0;
    }

    // 숫자에 콤마 추가하는 함수
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // 남은 기간 표시 업데이트 함수
    function updateRemainingPeriod(months) {
        const periodElement = document.querySelector('.text-right p.text-xl');
        if (!periodElement) return;

        // months가 null이면 텍스트를 비움
        if (months === null) {
            periodElement.innerHTML = '';
            return;
        }
        
        const years = Math.floor(months / 12);
        const remainingMonths = months % 12;
        
        let periodText = '';
        if (years > 0) {
            periodText = `${years}년 ${remainingMonths}개월`;
        } else {
            periodText = `${remainingMonths}개월`;
        }
        
        periodElement.innerHTML = `${periodText}<span class="text-base md:text-xl text-gray-600 ml-1"></span>`;
    }

    // 수익률을 고려한 남은 기간 계산 함수
    function calculateRemainingPeriodWithReturn(targetAmount, currentAmount, monthlySaving, annualReturn) {
        // 월 저축액이 0이거나 음수인 경우 null 반환
        if (monthlySaving <= 0) {
            return null;
        }
        
        // 목표금액에서 현재금액을 뺀 나머지 금액
        const remainingAmount = targetAmount - currentAmount;
        
        // 연평균 수익률이 0%인 경우
        if (annualReturn === 0) {
            return Math.ceil(remainingAmount / monthlySaving);
        }
        
        // 월 이자율 계산 (연 수익률을 12로 나누어 계산)
        const monthlyRate = (1 + annualReturn/100)**(1/12) - 1;
        
        // 남은 기간 계산 (로그 공식 사용)
        const n = Math.ceil(Math.log((remainingAmount * monthlyRate / monthlySaving) + 1) / Math.log(1 + monthlyRate));
        
        return n;
    }
    
    // 남은 기간 재계산 함수
    function recalculateRemainingPeriod() {
        const targetAmount = {{ $data['targetAmount'] }};
        const currentAmount = {{ $data['currentAmount'] }};
        const monthlySavingValue = parseInt(monthlySaving.value);
        
        // 선택된 포트폴리오의 수익률로 계산
        const months = calculateRemainingPeriodWithReturn(targetAmount, currentAmount, monthlySavingValue, selectedPortfolioReturn);
        updateRemainingPeriod(months);
    }

    // 월 저축액 변경 시 남은 기간 재계산
    function updateMonthlySaving(value, isFromInput = false) {
        if (!isFromInput) {
            // 슬라이더나 blur 이벤트에서 호출된 경우
            value = Math.max(10000, Math.min({{ $data['totalIncome'] }}, value));
            monthlySavingInput.value = numberWithCommas(value);
            monthlySaving.value = value;
            updateRangeBackground(monthlySaving);
        } else {
            // 직접 입력 중인 경우
            monthlySaving.value = value;
            updateRangeBackground(monthlySaving);
        }
        
        // 선택된 포트폴리오의 수익률로 계산
        recalculateRemainingPeriod();
    }

    // 초기 게이지바 배경 설정
    if (monthlySaving) updateRangeBackground(monthlySaving);

    // 포트폴리오 선택 이벤트 처리
    const portfolioOptions = document.querySelectorAll('.portfolio-option');
    
    portfolioOptions.forEach(option => {
        option.addEventListener('click', function() {
            // 기존 선택 해제
            portfolioOptions.forEach(opt => opt.classList.remove('active'));
            
            // 현재 선택 활성화
            this.classList.add('active');
            
            // 선택된 수익률 저장
            selectedPortfolioReturn = parseFloat(this.getAttribute('data-return'));
            
            // 모든 투자 대가 리스트 숨기기
            document.querySelectorAll('.portfolio-masters').forEach(list => {
                if (!list.classList.contains(`portfolio-masters-${selectedPortfolioReturn}`)) {
                    // 선택되지 않은 리스트만 숨김
                    list.style.maxHeight = '0px';
                    list.classList.add('hidden');
                }
            });
            
            // 선택된 포트폴리오에 해당하는 투자 대가 리스트 표시
            const selectedMastersList = document.querySelector(`.portfolio-masters-${selectedPortfolioReturn}`);
            if (selectedMastersList) {
                // hidden 클래스 제거
                selectedMastersList.classList.remove('hidden');
                // 슬라이드 다운 애니메이션 적용
                setTimeout(() => {
                    selectedMastersList.style.maxHeight = selectedMastersList.scrollHeight + 'px';
                }, 10);
            }
            
            // 남은 기간 재계산
            recalculateRemainingPeriod();
        });
    });

    // 이벤트 리스너
    if (monthlySaving) {
        monthlySaving.addEventListener('input', (e) => {
            updateMonthlySaving(e.target.value);
        });
    }

    // 텍스트 입력 이벤트
    if (monthlySavingInput) {
        monthlySavingInput.addEventListener('input', (e) => {
            // 입력 중에는 콤마를 제거하고 숫자만 추출
            let value = e.target.value.replace(/[^\d]/g, '');
            // 콤마 추가
            e.target.value = value ? numberWithCommas(value) : '';
            updateMonthlySaving(value || 0, true);
        });
        
        monthlySavingInput.addEventListener('blur', (e) => {
            let value = extractNumber(e.target.value);
            value = Math.max(10000, Math.min({{ $data['totalIncome'] }}, value));
            updateMonthlySaving(value);
        });

        monthlySavingInput.addEventListener('focus', (e) => {
            // 포커스 시 콤마 제거
            let value = e.target.value.replace(/,/g, '');
            e.target.value = value;
        });
    }

    // 초기값 설정
    updateMonthlySaving(monthlySaving ? monthlySaving.value : {{ max(10000, $data['difference']) }});
    
    // 초기 계산 실행
    recalculateRemainingPeriod();

    // 차트 설정
    if (document.getElementById('expenseChart')) {
        const chart = echarts.init(document.getElementById('expenseChart'));
        const option = {
            color: colors,
            tooltip: {
                trigger: 'item',
                formatter: function(params) {
                    return `${params.name}<br/>
                            ${numberWithCommas(params.value)}원<br/>
                            ${params.percent}%`;
                }
            },
            legend: {
                orient: 'horizontal',
                bottom: '0',
                left: 'center',
                itemWidth: 12,
                itemHeight: 12,
                textStyle: { 
                    color: '#2C3E50',
                    fontSize: '12'
                },
                type: 'scroll',
                pageTextStyle: {
                    color: '#2C3E50'
                }
            },
            series: [{
                name: '월간 지출',
                type: 'pie',
                radius: ['44%', '70%'],
                center: ['50%', '45%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 6,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: false,
                    position: 'center'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: '20',
                        fontWeight: 'bold'
                    }
                },
                labelLine: {
                    show: true
                },
                data: expenses
            }]
        };
        chart.setOption(option);
        window.addEventListener('resize', () => chart.resize());
    }
});
@endif
</script>

<style>
/* 게이지바 스타일링 */
input[type="range"] {
    -webkit-appearance: none;
    height: 8px;
    background: #e5e7eb;
    border-radius: 5px;
    background-image: linear-gradient(#1f2937, #1f2937);
    background-size: 0 100%;
    background-repeat: no-repeat;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #1f2937;
    cursor: pointer;
    box-shadow: 0 0 2px 0 #555;
}

/* 포트폴리오 선택 스타일 */
.portfolio-option {
    transition: all 0.3s ease;
    border-color: #e5e7eb;
}

.portfolio-option:hover {
    transform: translateY(-2px);
}

.portfolio-option[data-return="5"].active {
    border-color: #1e40af;
    background-color: #f8fafc;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1), 0 2px 4px -1px rgba(59, 130, 246, 0.06);
}

.portfolio-option[data-return="10"].active {
    border-color: #15803d;
    background-color: #f8fafc;
    box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.1), 0 2px 4px -1px rgba(34, 197, 94, 0.06);
}

.portfolio-option[data-return="15"].active {
    border-color: #6b21a8;
    background-color: #f8fafc;
    box-shadow: 0 4px 6px -1px rgba(168, 85, 247, 0.1), 0 2px 4px -1px rgba(168, 85, 247, 0.06);
}

/* 투자 대가 리스트 슬라이드 애니메이션 */
.portfolio-masters {
    overflow: hidden;
    transition: max-height 0.5s ease-in-out;
    max-height: 1000px; /* 충분히 큰 값으로 설정 */
}

.portfolio-masters.hidden {
    max-height: 0;
    overflow: hidden;
    padding-top: 0;
    padding-bottom: 0;
    margin-top: 0;
    border-top: 0;
}
</style>
@endpush
@endsection 