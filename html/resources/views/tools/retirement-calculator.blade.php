@extends('layouts.app')

@section('title', '노후 자금 계산기')

<!-- ECharts 라이브러리 -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

@push('styles')
<style>
/* 한국 통화 표시를 위한 함수 */
function formatKoreanCurrency(value, isShort = true) {
    if (value >= 100000000) {
        return isShort ? Math.round(value / 100000000) + '억' : Math.round(value / 100000000) + '억';
    } else if (value >= 10000) {
        return isShort ? Math.round(value / 10000) + '만' : Math.round(value / 10000) + '만';
    } else {
        return value.toString();
    }
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- 헤더 섹션 -->
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">노후 자금 계산기</h1>
                <p class="text-lg text-gray-600 mb-8">은퇴 후 필요한 자금을 계산하고 준비 계획을 세워보세요!</p>
                
                <!-- 시작하기 버튼 -->
                <button id="startCalcBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-4 px-8 rounded-full transition-all duration-300 text-lg transform hover:scale-105 hover:shadow-lg">
                    계산 시작하기
                </button>
            </div>
            
            <!-- 설명 섹션 -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">계산기 안내</h2>
                <ul class="list-disc pl-6 space-y-2 text-gray-600">
                    <li>현재 상황과 은퇴 계획을 입력하여 필요한 노후 자금을 계산합니다.</li>
                    <li>물가상승률 2%가 자동으로 적용되어 미래 가치를 반영합니다.</li>
                    <li>개인 맞춤형 재무 조언과 저축 계획을 제공합니다.</li>
                    <li>노후 자금 변화 추이를 시각적으로 확인할 수 있습니다.</li>
                </ul>
            </div>
            
            <!-- 기능 소개 섹션 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">정확한 계산</h3>
                    <p class="text-gray-600">복리 효과와 물가상승률을 반영한 정확한 노후 자금 계산</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">시각적 분석</h3>
                    <p class="text-gray-600">노후 자금 변화 추이를 차트로 한눈에 확인</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">맞춤 조언</h3>
                    <p class="text-gray-600">개인 상황에 맞는 재무 조언과 저축 계획 제공</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 계산기 모달 -->
<div id="retirementCalcModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-white">
        <!-- 닫기 버튼 -->
        <button id="closeRetirementCalcBtn" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- 콘텐츠 컨테이너 -->
        <div class="w-full h-full overflow-y-auto">
            <div class="max-w-3xl mx-auto px-4 py-8">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">노후 자금 계산기</h2>
                    <p class="text-gray-600">은퇴 후 필요한 자금을 계산해보세요</p>
                </div>

                <!-- 입력 폼과 결과 영역 컨테이너 -->
                <div class="calc-container">
                    <!-- 입력 폼 영역 -->
                    <div id="inputFormSection">
                        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
                            <form id="retirementCalcForm" class="space-y-6">
                                <div class="mb-2 px-2 py-2 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-blue-700">※ 예상 물가상승률은 <span class="font-bold">2%</span>로 고정 적용됩니다.</p>
                                </div>
                                
                                <!-- 현재 정보 섹션 -->
                                <div class="border-b border-gray-200 pb-4 mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">현재 정보</h4>
                                    <div>
                                    <label class="block text-gray-700 font-medium mb-2">현재 나이</label>
                                    <input type="number" id="currentAge" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="20" max="80" value="30" required>
                                </div>
                                </div>
                                
                                <!-- 저축 정보 섹션 -->
                                <div class="border-b border-gray-200 pb-4 mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">저축 정보</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">현재까지 누적 저축액 (만원)</label>
                                            <input type="number" id="currentSavings" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" value="5000" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">월 저축 금액 (만원)</label>
                                            <input type="number" id="monthlySaving" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" value="50" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">예상 연간 수익률 (%)</label>
                                            <input type="number" id="returnRate" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" max="15" step="0.5" value="4" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 은퇴 정보 섹션 -->
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">은퇴 정보</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">예상 은퇴 나이</label>
                                            <input type="number" id="retirementAge" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="50" max="90" value="65" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">예상 기대 수명</label>
                                            <input type="number" id="lifeExpectancy" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="70" max="110" value="85" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">은퇴 후 월 생활비 (현재 가치, 만원)</label>
                                            <input type="number" id="monthlyExpense" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="50" value="280" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-6">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-full transition-colors text-lg">
                                        계산하기
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 계산 결과 영역 -->
                    <div id="resultSection" class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md hidden">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">노후 자금 분석 결과</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="border border-gray-200 rounded-lg p-4 bg-blue-50">
                                <div class="text-gray-700 mb-1">은퇴까지 남은 기간</div>
                                <div class="text-2xl font-bold text-blue-700" id="yearsToRetirement"></div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4 bg-indigo-50">
                                <div class="text-gray-700 mb-1">예상 은퇴 후 생활 기간</div>
                                <div class="text-2xl font-bold text-indigo-700" id="retirementDuration"></div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4 bg-green-50">
                                <div class="text-gray-700 mb-1">필요한 총 노후자금</div>
                                <div class="text-2xl font-bold text-green-700" id="totalNeeded"></div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4 bg-amber-50">
                                <div class="text-gray-700 mb-1">은퇴 후 월 생활비 (미래 가치)</div>
                                <div class="text-2xl font-bold text-amber-700" id="monthlyNeeded"></div>
                                <div class="text-xs text-gray-500 mt-1">* 물가상승률 2% 적용</div>
                            </div>
                        </div>
                        
                        <div class="mb-8">
                            <h4 class="font-semibold text-gray-800 mb-3">노후 자금 변화 추이</h4>
                            <div id="retirementChart" class="w-full bg-white p-4 rounded-lg shadow-md" style="width: 100%; height: 300px !important; display: block; overflow: hidden;"></div>
                        </div>
                        
                        <div class="mb-8">
                            <h4 class="font-semibold text-gray-800 mb-3">목표 달성을 위한 저축 계획</h4>
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <div class="text-gray-700 mb-1">월 필요 저축액</div>
                                        <div class="text-xl font-bold text-blue-600" id="monthlySavingsNeeded"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-700 mb-1">연간 필요 저축액</div>
                                        <div class="text-xl font-bold text-blue-600" id="annualSavingsNeeded"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-700 mb-1">현재 달성률</div>
                                        <div class="text-xl font-bold text-blue-600" id="currentProgressRate"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="adviceSection" class="p-4 rounded-lg bg-gray-50 mb-6">
                            <h4 class="font-semibold text-gray-800 mb-2">재무 조언</h4>
                            <p class="text-gray-600" id="financialAdvice"></p>
                        </div>
                        
                        <div class="text-center mt-6">
                            <button id="recalculateBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-full transition-colors">
                                다시 계산하기
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// 한국 통화 표시를 위한 함수
function formatKoreanCurrency(value, isShort = true) {
    if (value >= 100000000) {
        return isShort ? Math.round(value / 100000000) + '억' : Math.round(value / 100000000) + '억';
    } else if (value >= 10000) {
        return isShort ? Math.round(value / 10000) + '만' : Math.round(value / 10000) + '만';
    } else {
        return value.toString();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const startCalcBtn = document.getElementById('startCalcBtn'); // 시작 버튼
    const closeRetirementCalcBtn = document.getElementById('closeRetirementCalcBtn');
    const retirementCalcModal = document.getElementById('retirementCalcModal');
    const retirementCalcForm = document.getElementById('retirementCalcForm');
    const resultSection = document.getElementById('resultSection');
    const recalculateBtn = document.getElementById('recalculateBtn');
    
    // 숫자 형식 함수 - 천단위 콤마 추가
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    // 팝업 열릴 때 외부 스크롤 비활성화
    function disableBodyScroll() {
        document.body.style.overflow = 'hidden';
    }
    
    // 팝업 닫힐 때 외부 스크롤 활성화
    function enableBodyScroll() {
        document.body.style.overflow = '';
    }
    
    // 시작하기 버튼 클릭 이벤트
    startCalcBtn.addEventListener('click', function() {
        retirementCalcModal.classList.remove('hidden');
        retirementCalcModal.classList.add('flex');
        // 결과 섹션 숨기기 (초기 상태)
        resultSection.classList.add('hidden');
        document.getElementById('inputFormSection').style.display = 'block';
        disableBodyScroll(); // 외부 스크롤 비활성화
    });
    
    // 닫기 버튼 클릭 이벤트
    closeRetirementCalcBtn.addEventListener('click', function() {
        retirementCalcModal.classList.add('hidden');
        retirementCalcModal.classList.remove('flex');
        enableBodyScroll(); // 외부 스크롤 활성화
    });
    
    // 다시 계산하기 버튼 클릭 이벤트
    recalculateBtn.addEventListener('click', function() {
        resultSection.classList.add('hidden');
        document.getElementById('inputFormSection').style.display = 'block';
    });
    
    // 폼 제출 이벤트
    retirementCalcForm.addEventListener('submit', function(e) {
        e.preventDefault();
        calculateRetirement();
        
        // 결과 섹션 표시
        resultSection.classList.remove('hidden');
        document.getElementById('inputFormSection').style.display = 'none';
        
        // 결과 섹션이 DOM에 보여진 후 약간의 지연을 두고 차트 렌더링 (브라우저 렌더링 시간 확보)
        setTimeout(() => {
            if (document.getElementById('retirementChart')) {
                window.dispatchEvent(new Event('resize')); // 차트 영역 크기 재계산 트리거
            }
        }, 100);
    });
    
    // 노후자금 계산 함수
    function calculateRetirement() {
        // 입력값 가져오기
        const currentAge = parseInt(document.getElementById('currentAge').value);
        const retirementAge = parseInt(document.getElementById('retirementAge').value);
        const lifeExpectancy = parseInt(document.getElementById('lifeExpectancy').value);
        const monthlyExpense = parseInt(document.getElementById('monthlyExpense').value);
        const returnRate = parseFloat(document.getElementById('returnRate').value) / 100;
        const currentSavings = parseInt(document.getElementById('currentSavings').value);
        const monthlySaving = parseInt(document.getElementById('monthlySaving').value);
        
        // 물가상승률 고정값 설정
        const inflationRate = 0.02;
        
        // 계산
        const yearsToRetirement = retirementAge - currentAge;
        const retirementDuration = lifeExpectancy - retirementAge;
        
        // 은퇴 시 월 필요 금액 (현재 가치 기준)
        const monthlyNeededNow = monthlyExpense;
        
        // 물가상승률 적용한 은퇴 시점의 월 필요 금액
        const monthlyNeededAtRetirement = monthlyNeededNow * Math.pow(1 + inflationRate, yearsToRetirement);
        
        // 은퇴 후 필요한 총 자금 (실질 수익률 고려)
        const realReturnRate = (1 + returnRate) / (1 + inflationRate) - 1;
        let totalNeeded;
        
        if (Math.abs(realReturnRate) < 0.0001) {
            // 실질 수익률이 0에 가까운 경우
            totalNeeded = monthlyNeededAtRetirement * 12 * retirementDuration;
        } else {
            // 연금 현재가치 공식 사용
            totalNeeded = monthlyNeededAtRetirement * 12 * ((1 - Math.pow(1 + realReturnRate, -retirementDuration)) / realReturnRate);
        }
        
        // 현재부터 은퇴까지 매달 저축해야 할 금액
        const futureValueFactor = (Math.pow(1 + returnRate, yearsToRetirement) - 1) / returnRate;
        const monthlySavingsNeeded = (totalNeeded - currentSavings * Math.pow(1 + returnRate, yearsToRetirement)) / (futureValueFactor * 12);
        
        // 현재 달성률
        const targetFutureValue = currentSavings * Math.pow(1 + returnRate, yearsToRetirement);
        const currentProgressRate = (targetFutureValue / totalNeeded) * 100;
        
        // 결과 표시
        document.getElementById('yearsToRetirement').textContent = `${yearsToRetirement}년`;
        document.getElementById('retirementDuration').textContent = `${retirementDuration}년`;
        // 기존 값은 만원 단위이므로 10000을 곱해 원 단위로 변환
        document.getElementById('totalNeeded').textContent = `${formatKoreanCurrency(Math.round(totalNeeded * 10000), false)}원`;
        document.getElementById('monthlyNeeded').textContent = `${formatKoreanCurrency(Math.round(monthlyNeededAtRetirement * 10000), false)}원`;
        document.getElementById('monthlySavingsNeeded').textContent = `${formatKoreanCurrency(Math.round(monthlySavingsNeeded * 10000), false)}원`;
        document.getElementById('annualSavingsNeeded').textContent = `${formatKoreanCurrency(Math.round(monthlySavingsNeeded * 12 * 10000), false)}원`;
        document.getElementById('currentProgressRate').textContent = `${currentProgressRate.toFixed(1)}%`;
        
        // 재무 조언 제공
        provideFinancialAdvice(currentAge, yearsToRetirement, monthlySavingsNeeded, monthlySaving, currentProgressRate);
        
        // 노후 자금 변화 추이 차트 생성
        createRetirementChart(currentAge, retirementAge, lifeExpectancy, currentSavings, monthlySaving, totalNeeded, returnRate, inflationRate, monthlyExpense);
    }
    
    // 노후 자금 변화 추이 차트 생성 함수
    function createRetirementChart(currentAge, retirementAge, lifeExpectancy, currentSavings, monthlySaving, totalNeeded, returnRate, inflationRate, monthlyExpense) {
        // 차트 컨테이너 가져오기
        const chartContainer = document.getElementById('retirementChart');
        
        if (!chartContainer) {
            return;
        }
        
        // 이미 차트가 있으면 인스턴스 삭제
        if (window.retirementChartInstance) {
            window.retirementChartInstance.dispose();
        }
        
        // 새 차트 인스턴스 생성 - 모바일 대응
        if (typeof echarts === 'undefined') {
            return;
        }
        
        const chart = echarts.init(chartContainer, null, {
            renderer: 'canvas',
            useDirtyRect: false
        });
        window.retirementChartInstance = chart;
        
        // 차트 데이터 생성
        const totalYears = lifeExpectancy - currentAge;
        const xAxisData = [];
        const savingsPhaseData = [];
        const withdrawalPhaseData = [];
        
        let currentSavingsValue = currentSavings;
        
        // 현재 나이부터 수명까지의 연도 데이터 생성
        for (let i = 0; i <= totalYears; i++) {
            const age = currentAge + i;
            xAxisData.push(age);
            
            // 은퇴 전: 적립 단계
            if (age < retirementAge) {
                // 매월 저축액을 더하고 수익률 적용
                currentSavingsValue = currentSavingsValue * (1 + returnRate) + monthlySaving * 12;
                savingsPhaseData.push(Math.round(currentSavingsValue));
                withdrawalPhaseData.push(null); // 은퇴 전에는 인출 없음
            } 
            // 은퇴 후: 인출 단계
            else {
                if (age === retirementAge) {
                    // 은퇴 시점의 저축액을 마지막으로 저장
                    savingsPhaseData.push(Math.round(currentSavingsValue));
                } else {
                    savingsPhaseData.push(null); // 은퇴 후에는 적립 없음
                }
                
                // 은퇴 후 예상 월 필요 금액 (물가상승률 고려)
                const inflationFactor = Math.pow(1 + inflationRate, i - (retirementAge - currentAge));
                const yearlyWithdrawal = monthlyExpense * inflationFactor * 12;
                
                // 인출 단계 계산 명확화:
                // 1. 먼저 현재 자산에 수익률 적용 (투자수익 발생)
                currentSavingsValue = currentSavingsValue * (1 + returnRate);
                
                // 2. 연간 생활비 인출 (저축 없음)
                currentSavingsValue -= yearlyWithdrawal;
                
                // 3. 잔액이 음수가 되지 않도록 조정
                currentSavingsValue = Math.max(0, currentSavingsValue);
                withdrawalPhaseData.push(Math.round(currentSavingsValue));
            }
        }
        
        // 차트 옵션 설정
        const option = {
            tooltip: {
                trigger: 'axis',
                formatter: function(params) {
                    const age = params[0].axisValue;
                    let content = `<div style="font-weight:bold;margin-bottom:5px;">${age}세</div>`;
                    
                    params.forEach(param => {
                        if (param.value !== null && param.value !== undefined && !isNaN(param.value)) {
                            // 유효한 값이 있는 경우만 표시
                            const value = numberWithCommas(Math.round(param.value));
                            let status = '';
                            
                            if (param.seriesName === '적립 단계') {
                                status = age < retirementAge ? '적립 중' : '은퇴 시점';
                            } else if (param.seriesName === '인출 단계') {
                                status = '생활비 인출 중';
                            } else {
                                status = '';
                            }
                            
                            content += `<div style="display:flex;align-items:center;margin:3px 0;">
                                <span style="display:inline-block;width:10px;height:10px;background:${param.color};margin-right:5px;border-radius:50%;"></span>
                                <span style="margin-right:5px;min-width:60px;">${param.seriesName}</span>
                                <span style="font-weight:bold;">${value}만원</span>
                                <span style="margin-left:8px;color:#666;">(${status})</span>
                            </div>`;
                        }
                    });
                    
                    return content;
                }
            },
            legend: {
                data: ['적립 단계', '인출 단계'],
                bottom: 5,
                padding: [5, 10],
                itemGap: 20,
                itemWidth: 14,
                itemHeight: 8,
                textStyle: {
                    fontSize: 12,
                    padding: [0, 4]
                },
                selected: {  // 은퇴 시점은 범례에서 숨김
                    '은퇴 시점': false
                }
            },
            grid: {
                left: '5%',
                right: '5%',
                bottom: '12%',
                top: '8%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: xAxisData,
                axisLine: { lineStyle: { color: '#aaa' } },
                axisLabel: {
                    formatter: function(value) {
                        // 10년 단위로만 표시
                        return (value % 5 === 0) ? value + '세' : '';
                    },
                    fontSize: 10,
                    interval: 'auto',
                    rotate: 0
                }
            },
            yAxis: {
                type: 'value',
                name: '자산 (만원)',
                nameTextStyle: { 
                    color: '#666',
                    fontSize: 12,
                    padding: [0, 0, 10, 0]  // 이름 주변 패딩 추가
                },
                nameGap: 25,  // 이름과 축 사이 간격 증가
                axisLine: { show: true, lineStyle: { color: '#aaa' } },
                splitLine: { lineStyle: { color: '#eee' } },
                axisLabel: {
                    color: '#666',
                    margin: 14,  // 레이블과 축 사이 여백 증가
                    formatter: function(value) {
                        // 차트 데이터는 이미 만원 단위로 저장되어 있음 (10000 곱하지 않음)
                        return formatKoreanCurrency(value * 10000, false);
                    },
                    fontSize: 10,
                    padding: [3, 0, 3, 0]  // 레이블 주변 패딩 추가
                }
            },
            series: [
                {
                    name: '적립 단계',
                    type: 'line',
                    data: savingsPhaseData,
                    smooth: true,
                    showSymbol: false,
                    lineStyle: {
                        width: 3
                    },
                    itemStyle: {
                        color: '#4e79a7'
                    },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(78, 121, 167, 0.5)' },
                            { offset: 1, color: 'rgba(78, 121, 167, 0.2)' }
                        ])
                    }
                },
                {
                    name: '인출 단계',
                    type: 'line',
                    data: withdrawalPhaseData,
                    smooth: true,
                    showSymbol: false,
                    lineStyle: {
                        width: 3,
                        type: 'solid'
                    },
                    itemStyle: {
                        color: '#e15759'  // 인출 단계는 더 눈에 띄는 빨간색 계열로 변경
                    },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(225, 87, 89, 0.5)' },
                            { offset: 1, color: 'rgba(225, 87, 89, 0.2)' }
                        ])
                    }
                }
            ],
            // 수직선 마커 추가
            visualMap: {
                show: false,
                type: 'piecewise',
                pieces: [
                    {
                        gt: 0,
                        lte: retirementAge,
                        label: '은퇴 전'
                    },
                    {
                        gt: retirementAge,
                        label: '은퇴 후'
                    }
                ]
            }
        };
        
        // 은퇴 시점 마커 추가 (별도 시리즈로 추가)
        option.series.push({
            name: '은퇴 시점',
            type: 'line',
            markLine: {
                silent: true,
                lineStyle: {
                    color: '#ff0000',
                    type: 'dashed',
                    width: 2
                },
                label: {
                    formatter: '은퇴 시점',
                    position: 'middle',
                    color: '#ff0000',
                    fontSize: 14,
                    fontWeight: 'bold'
                },
                data: [
                    {
                        name: '은퇴 시점',
                        xAxis: retirementAge
                    }
                ]
            },
            data: [],  // 빈 데이터로 실제 차트에 표시되지 않음
            tooltip: {
                show: false  // 이 시리즈에 대한 툴팁 비활성화
            }
        });
        
        // 차트에 옵션 적용
        chart.setOption(option);
        
        // 차트 크기 조정 - 반응형 대응 개선
        function resizeChart() {
            if (chart && !chart.isDisposed()) {
                chart.resize();
            }
        }
        
        // 즉시 리사이즈 함수 호출하여 초기 화면에 맞게 조정
        setTimeout(resizeChart, 200);
        
        // 창 크기 변경 시 차트 리사이즈
        window.addEventListener('resize', resizeChart);
        
        // 모바일 기기 회전 이벤트에 대응
        window.addEventListener('orientationchange', function() {
            setTimeout(resizeChart, 200);
        });
    }
    
    // 재무 조언 함수
    function provideFinancialAdvice(age, yearsToRetirement, monthlySavingsNeeded, monthlySaving, currentProgressRate) {
        let advice = '';
        
        // 월 저축금액 대비 필요 저축액 비율 계산
        const savingsRatio = (monthlySavingsNeeded / monthlySaving) * 100;
        
        if (currentProgressRate >= 80) {
            advice = '축하합니다! 은퇴 준비가 잘 진행되고 있습니다. 투자 포트폴리오를 정기적으로 검토하고 필요에 따라 조정하면서 현재 상태를 유지하세요.';
        } else if (currentProgressRate >= 50) {
            advice = '은퇴 준비가 절반 이상 진행되었습니다. 추가적인 저축으로 준비율을 더 높이고, 투자 전략을 최적화하면 목표에 더 빠르게 도달할 수 있습니다.';
        } else if (currentProgressRate >= 20) {
            advice = '은퇴 준비가 시작되었지만, 더 많은 관심이 필요합니다. 불필요한 지출을 줄이고 저축을 늘려 은퇴 준비를 가속화하는 것이 좋습니다.';
        } else {
            advice = '은퇴 준비가 아직 초기 단계입니다. 정기적인 저축 습관을 형성하고, 장기적인 재무 계획을 세우는 것이 중요합니다.';
        }
        
        if (savingsRatio > 200) {
            advice += ' 필요한 저축액이 현재 저축액보다 훨씬 많습니다. 저축 금액을 늘리거나, 은퇴 후 생활비 기대치를 현실적으로 조정하는 것을 고려해보세요.';
        } else if (savingsRatio > 120) {
            advice += ' 필요한 저축액이 현재 저축액보다 다소 높습니다. 가능하다면 저축 금액을 점진적으로 늘려보세요.';
        } else {
            advice += ' 현재 저축액이 필요 저축액을 충족하거나 그 이상입니다. 꾸준히 유지하면서 투자 수익률을 높이는 방안도 모색해보세요.';
        }
        
        if (age < 30) {
            advice += ' 젊은 나이에 은퇴 준비를 시작한 것은 매우 현명한 결정입니다. 시간이 충분하므로 장기적인 투자에 집중하세요.';
        } else if (age < 40) {
            advice += ' 30대에 은퇴 준비를 하는 것은 시간의 이점을 활용할 수 있는 좋은 시기입니다. 균형 잡힌 포트폴리오로 안정적인 성장을 추구하세요.';
        } else if (age < 50) {
            advice += ' 40대는 은퇴 준비에 가속도를 붙여야 할 시기입니다. 가능하다면 저축률을 높이고, 재무 목표를 정기적으로 검토하세요.';
        } else {
            advice += ' 50대 이상이라면 은퇴를 앞두고 있으므로, 보다 보수적인 투자 전략과 함께 은퇴 계획을 구체화할 필요가 있습니다.';
        }
        
        document.getElementById('financialAdvice').textContent = advice;
    }
    
});
</script>
@endpush
