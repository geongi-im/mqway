@extends('layouts.app')

@section('title', '노후 자금 계산기')

@push('styles')
<style>
/* 입력 필드 포커스 스타일 */
.calc-input {
    transition: all 0.2s ease;
    border: 2px solid #E5E7EB;
}
.calc-input:focus {
    border-color: #4ECDC4;
    box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.1);
    outline: none;
}
</style>
@endpush

@section('content')
<!-- ===== Hero Background ===== -->
<div class="relative bg-[#3D4148] pb-32 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
    </div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#FF4D4D] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>

    <div class="relative z-10 container mx-auto px-4 pt-12 pb-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 text-white/90 py-1.5 px-4 rounded-full text-sm font-medium mb-4 border border-white/10 backdrop-blur-md animate-fadeIn">
            <span>🧮</span> <span>학습 도구</span>
        </div>
        <h1 class="font-outfit text-3xl md:text-5xl font-extrabold text-white mb-4 tracking-tight animate-slideUp" style="animation-delay: 0.1s;">
            노후 자금 <span class="text-[#4ECDC4]">계산기</span>
        </h1>
        <p class="text-white/70 text-base md:text-lg max-w-2xl mx-auto leading-relaxed animate-slideUp" style="animation-delay: 0.2s;">
            은퇴 후 필요한 자금을 계산하고 준비 계획을 세워보세요!
        </p>
    </div>
</div>

<!-- ===== Main Content ===== -->
<div class="relative z-20 -mt-24 pb-16">
    <div class="container mx-auto px-4 max-w-4xl">

        <!-- 설명 카드 -->
        <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6 animate-slideUp" style="animation-delay: 0.3s;">
            <h2 class="text-xl font-bold text-[#2D3047] mb-5 flex items-center gap-2">
                <span class="w-8 h-8 bg-gradient-to-br from-[#4ECDC4] to-[#26D0CE] rounded-lg flex items-center justify-center text-white text-sm">📋</span>
                계산기 안내
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-6 h-6 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs">✓</span>
                    <p class="text-gray-600 text-sm">현재 상황과 은퇴 계획을 입력하여 필요한 노후 자금을 계산합니다.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-6 h-6 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs">✓</span>
                    <p class="text-gray-600 text-sm">물가상승률 2%가 자동으로 적용되어 미래 가치를 반영합니다.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-6 h-6 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs">✓</span>
                    <p class="text-gray-600 text-sm">개인 맞춤형 재무 조언과 저축 계획을 제공합니다.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-6 h-6 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs">✓</span>
                    <p class="text-gray-600 text-sm">노후 자금 변화 추이를 시각적으로 확인할 수 있습니다.</p>
                </div>
            </div>
            <div class="text-center">
                <button id="startCalcBtn" class="bg-gradient-to-r from-[#FF4D4D] to-[#FF6B6B] hover:from-[#FF3333] hover:to-[#FF4D4D] text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 text-lg shadow-[0_8px_25px_rgba(255,77,77,0.35)] hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(255,77,77,0.45)]">
                    🧮 계산 시작하기
                </button>
            </div>
        </div>

        <!-- 기능 소개 카드 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-slideUp" style="animation-delay: 0.4s;">
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center group hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-gradient-to-br from-[#4ECDC4]/20 to-[#4ECDC4]/5 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-[#4ECDC4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-[#2D3047] mb-2">정확한 계산</h3>
                <p class="text-gray-500 text-sm leading-relaxed">복리 효과와 물가상승률을 반영한 정확한 계산</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center group hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-gradient-to-br from-[#FF4D4D]/20 to-[#FF4D4D]/5 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-[#FF4D4D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-[#2D3047] mb-2">시각적 분석</h3>
                <p class="text-gray-500 text-sm leading-relaxed">노후 자금 변화 추이를 차트로 한눈에 확인</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center group hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-gradient-to-br from-[#FFB347]/20 to-[#FFB347]/5 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-[#FFB347]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-[#2D3047] mb-2">맞춤 조언</h3>
                <p class="text-gray-500 text-sm leading-relaxed">개인 상황에 맞는 재무 조언과 저축 계획</p>
            </div>
        </div>
    </div>
</div>

<!-- 계산기 모달 -->
<div id="retirementCalcModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-[#F8F9FB]">
        <!-- 닫기 버튼 -->
        <button id="closeRetirementCalcBtn" class="absolute top-5 right-5 w-10 h-10 bg-white hover:bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-700 z-10 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- 콘텐츠 컨테이너 -->
        <div class="w-full h-full overflow-y-auto">
            <div class="max-w-3xl mx-auto px-4 py-8">
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-[#4ECDC4]/20 to-[#4ECDC4]/5 rounded-2xl text-2xl mb-4">🧮</div>
                    <h2 class="text-2xl font-extrabold text-[#2D3047] mb-2">노후 자금 계산기</h2>
                    <p class="text-gray-500 text-sm">은퇴 후 필요한 자금을 계산해보세요</p>
                </div>

                <!-- 입력 폼과 결과 영역 컨테이너 -->
                <div class="calc-container">
                    <!-- 입력 폼 영역 -->
                    <div id="inputFormSection">
                        <div class="max-w-2xl mx-auto bg-white p-6 md:p-8 rounded-2xl shadow-xl">
                            <form id="retirementCalcForm" class="space-y-6">
                                <div class="mb-4 p-4 bg-gradient-to-r from-[#4ECDC4]/5 to-[#4ECDC4]/10 rounded-xl border border-[#4ECDC4]/20">
                                    <p class="text-sm text-[#2D3047]">💡 예상 물가상승률은 <span class="font-bold text-[#4ECDC4]">2%</span>로 고정 적용됩니다.</p>
                                </div>
                                
                                <!-- 현재 정보 섹션 -->
                                <div class="border-b border-gray-100 pb-6 mb-6">
                                    <h4 class="text-base font-bold text-[#2D3047] mb-4 flex items-center gap-2">
                                        <span class="w-6 h-6 bg-[#4ECDC4] text-white rounded-lg flex items-center justify-center text-xs">1</span>
                                        현재 정보
                                    </h4>
                                    <div>
                                        <label class="block text-gray-600 font-medium text-sm mb-2">현재 나이</label>
                                        <input type="number" id="currentAge" class="calc-input w-full px-4 py-3 rounded-xl text-sm" min="20" max="80" value="30" required>
                                    </div>
                                </div>
                                
                                <!-- 저축 정보 섹션 -->
                                <div class="border-b border-gray-100 pb-6 mb-6">
                                    <h4 class="text-base font-bold text-[#2D3047] mb-4 flex items-center gap-2">
                                        <span class="w-6 h-6 bg-[#FFB347] text-white rounded-lg flex items-center justify-center text-xs">2</span>
                                        저축 정보
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-600 font-medium text-sm mb-2">현재까지 누적 저축액 (만원)</label>
                                            <input type="number" id="currentSavings" class="calc-input w-full px-4 py-3 rounded-xl text-sm" min="0" value="5000" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-600 font-medium text-sm mb-2">월 저축 금액 (만원)</label>
                                            <input type="number" id="monthlySaving" class="calc-input w-full px-4 py-3 rounded-xl text-sm" min="0" value="50" required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-gray-600 font-medium text-sm mb-2">예상 연간 수익률 (%)</label>
                                            <input type="number" id="returnRate" class="calc-input w-full px-4 py-3 rounded-xl text-sm" min="0" max="15" step="0.5" value="4" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 은퇴 정보 섹션 -->
                                <div>
                                    <h4 class="text-base font-bold text-[#2D3047] mb-4 flex items-center gap-2">
                                        <span class="w-6 h-6 bg-[#FF4D4D] text-white rounded-lg flex items-center justify-center text-xs">3</span>
                                        은퇴 정보
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-600 font-medium text-sm mb-2">예상 은퇴 나이</label>
                                            <input type="number" id="retirementAge" class="calc-input w-full px-4 py-3 rounded-xl text-sm" min="50" max="90" value="65" required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-600 font-medium text-sm mb-2">예상 기대 수명</label>
                                            <input type="number" id="lifeExpectancy" class="calc-input w-full px-4 py-3 rounded-xl text-sm" min="70" max="110" value="85" required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-gray-600 font-medium text-sm mb-2">은퇴 후 월 생활비 (현재 가치, 만원)</label>
                                            <input type="number" id="monthlyExpense" class="calc-input w-full px-4 py-3 rounded-xl text-sm" min="50" value="280" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center pt-4">
                                    <button type="submit" class="bg-gradient-to-r from-[#4ECDC4] to-[#26D0CE] text-white font-bold py-3.5 px-10 rounded-xl transition-all duration-300 text-base shadow-[0_8px_25px_rgba(78,205,196,0.35)] hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(78,205,196,0.45)]">
                                        계산하기
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 계산 결과 영역 -->
                    <div id="resultSection" class="max-w-2xl mx-auto hidden">
                        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-xl mb-6">
                            <h3 class="text-xl font-extrabold text-[#2D3047] mb-6 flex items-center gap-2">
                                <span class="w-8 h-8 bg-gradient-to-br from-[#4ECDC4] to-[#26D0CE] rounded-lg flex items-center justify-center text-white text-sm">📊</span>
                                노후 자금 분석 결과
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-8">
                                <div class="bg-gradient-to-br from-[#4ECDC4]/5 to-[#4ECDC4]/10 rounded-2xl p-5 border border-[#4ECDC4]/10">
                                    <div class="text-gray-500 text-xs mb-1">은퇴까지 남은 기간</div>
                                    <div class="text-xl font-extrabold text-[#2D3047]" id="yearsToRetirement"></div>
                                </div>
                                <div class="bg-gradient-to-br from-[#7C5CFC]/5 to-[#7C5CFC]/10 rounded-2xl p-5 border border-[#7C5CFC]/10">
                                    <div class="text-gray-500 text-xs mb-1">예상 은퇴 후 생활 기간</div>
                                    <div class="text-xl font-extrabold text-[#2D3047]" id="retirementDuration"></div>
                                </div>
                                <div class="bg-gradient-to-br from-[#FF4D4D]/5 to-[#FF4D4D]/10 rounded-2xl p-5 border border-[#FF4D4D]/10">
                                    <div class="text-gray-500 text-xs mb-1">필요한 총 노후자금</div>
                                    <div class="text-xl font-extrabold text-[#FF4D4D]" id="totalNeeded"></div>
                                </div>
                                <div class="bg-gradient-to-br from-[#FFB347]/5 to-[#FFB347]/10 rounded-2xl p-5 border border-[#FFB347]/10">
                                    <div class="text-gray-500 text-xs mb-1">은퇴 후 월 생활비 (미래 가치)</div>
                                    <div class="text-xl font-extrabold text-[#FFB347]" id="monthlyNeeded"></div>
                                    <div class="text-[10px] text-gray-400 mt-1">* 물가상승률 2% 적용</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-xl mb-6">
                            <h4 class="font-bold text-[#2D3047] mb-4 flex items-center gap-2">
                                <span>📈</span> 노후 자금 변화 추이
                            </h4>
                            <div id="retirementChart" class="w-full bg-[#F8F9FB] rounded-xl" style="width: 100%; height: 300px !important; display: block; overflow: hidden;"></div>
                        </div>

                        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-xl mb-6">
                            <h4 class="font-bold text-[#2D3047] mb-4 flex items-center gap-2">
                                <span>💰</span> 목표 달성을 위한 저축 계획
                            </h4>
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="bg-gradient-to-br from-[#4ECDC4]/5 to-[#4ECDC4]/10 rounded-xl p-4 text-center">
                                    <div class="text-gray-500 text-xs mb-1">월 필요 저축액</div>
                                    <div class="text-lg font-extrabold text-[#4ECDC4]" id="monthlySavingsNeeded"></div>
                                </div>
                                <div class="bg-gradient-to-br from-[#4ECDC4]/5 to-[#4ECDC4]/10 rounded-xl p-4 text-center">
                                    <div class="text-gray-500 text-xs mb-1">연간 필요 저축액</div>
                                    <div class="text-lg font-extrabold text-[#4ECDC4]" id="annualSavingsNeeded"></div>
                                </div>
                                <div class="bg-gradient-to-br from-[#4ECDC4]/5 to-[#4ECDC4]/10 rounded-xl p-4 text-center">
                                    <div class="text-gray-500 text-xs mb-1">현재 달성률</div>
                                    <div class="text-lg font-extrabold text-[#4ECDC4]" id="currentProgressRate"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="adviceSection" class="bg-white p-6 md:p-8 rounded-2xl shadow-xl mb-6">
                            <h4 class="font-bold text-[#2D3047] mb-3 flex items-center gap-2">
                                <span>💡</span> 재무 조언
                            </h4>
                            <p class="text-gray-600 text-sm leading-relaxed" id="financialAdvice"></p>
                        </div>
                        
                        <div class="text-center">
                            <button id="recalculateBtn" class="bg-gradient-to-r from-[#4ECDC4] to-[#26D0CE] text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-[0_8px_25px_rgba(78,205,196,0.35)] hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(78,205,196,0.45)]">
                                🔄 다시 계산하기
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
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
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
    const startCalcBtn = document.getElementById('startCalcBtn');
    const closeRetirementCalcBtn = document.getElementById('closeRetirementCalcBtn');
    const retirementCalcModal = document.getElementById('retirementCalcModal');
    const retirementCalcForm = document.getElementById('retirementCalcForm');
    const resultSection = document.getElementById('resultSection');
    const recalculateBtn = document.getElementById('recalculateBtn');
    
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    function disableBodyScroll() {
        document.body.style.overflow = 'hidden';
    }
    
    function enableBodyScroll() {
        document.body.style.overflow = '';
    }
    
    startCalcBtn.addEventListener('click', function() {
        retirementCalcModal.classList.remove('hidden');
        retirementCalcModal.classList.add('flex');
        resultSection.classList.add('hidden');
        document.getElementById('inputFormSection').style.display = 'block';
        disableBodyScroll();
    });
    
    closeRetirementCalcBtn.addEventListener('click', function() {
        retirementCalcModal.classList.add('hidden');
        retirementCalcModal.classList.remove('flex');
        enableBodyScroll();
    });
    
    recalculateBtn.addEventListener('click', function() {
        resultSection.classList.add('hidden');
        document.getElementById('inputFormSection').style.display = 'block';
    });
    
    retirementCalcForm.addEventListener('submit', function(e) {
        e.preventDefault();
        calculateRetirement();
        
        resultSection.classList.remove('hidden');
        document.getElementById('inputFormSection').style.display = 'none';
        
        setTimeout(() => {
            if (document.getElementById('retirementChart')) {
                window.dispatchEvent(new Event('resize'));
            }
        }, 100);
    });
    
    function calculateRetirement() {
        const currentAge = parseInt(document.getElementById('currentAge').value);
        const retirementAge = parseInt(document.getElementById('retirementAge').value);
        const lifeExpectancy = parseInt(document.getElementById('lifeExpectancy').value);
        const monthlyExpense = parseInt(document.getElementById('monthlyExpense').value);
        const returnRate = parseFloat(document.getElementById('returnRate').value) / 100;
        const currentSavings = parseInt(document.getElementById('currentSavings').value);
        const monthlySaving = parseInt(document.getElementById('monthlySaving').value);
        
        const inflationRate = 0.02;
        
        const yearsToRetirement = retirementAge - currentAge;
        const retirementDuration = lifeExpectancy - retirementAge;
        
        const monthlyNeededNow = monthlyExpense;
        const monthlyNeededAtRetirement = monthlyNeededNow * Math.pow(1 + inflationRate, yearsToRetirement);
        
        const realReturnRate = (1 + returnRate) / (1 + inflationRate) - 1;
        let totalNeeded;
        
        if (Math.abs(realReturnRate) < 0.0001) {
            totalNeeded = monthlyNeededAtRetirement * 12 * retirementDuration;
        } else {
            totalNeeded = monthlyNeededAtRetirement * 12 * ((1 - Math.pow(1 + realReturnRate, -retirementDuration)) / realReturnRate);
        }
        
        const futureValueFactor = (Math.pow(1 + returnRate, yearsToRetirement) - 1) / returnRate;
        const monthlySavingsNeeded = (totalNeeded - currentSavings * Math.pow(1 + returnRate, yearsToRetirement)) / (futureValueFactor * 12);
        
        const targetFutureValue = currentSavings * Math.pow(1 + returnRate, yearsToRetirement);
        const currentProgressRate = (targetFutureValue / totalNeeded) * 100;
        
        document.getElementById('yearsToRetirement').textContent = `${yearsToRetirement}년`;
        document.getElementById('retirementDuration').textContent = `${retirementDuration}년`;
        document.getElementById('totalNeeded').textContent = `${formatKoreanCurrency(Math.round(totalNeeded * 10000), false)}원`;
        document.getElementById('monthlyNeeded').textContent = `${formatKoreanCurrency(Math.round(monthlyNeededAtRetirement * 10000), false)}원`;
        document.getElementById('monthlySavingsNeeded').textContent = `${formatKoreanCurrency(Math.round(monthlySavingsNeeded * 10000), false)}원`;
        document.getElementById('annualSavingsNeeded').textContent = `${formatKoreanCurrency(Math.round(monthlySavingsNeeded * 12 * 10000), false)}원`;
        document.getElementById('currentProgressRate').textContent = `${currentProgressRate.toFixed(1)}%`;
        
        provideFinancialAdvice(currentAge, yearsToRetirement, monthlySavingsNeeded, monthlySaving, currentProgressRate);
        createRetirementChart(currentAge, retirementAge, lifeExpectancy, currentSavings, monthlySaving, totalNeeded, returnRate, inflationRate, monthlyExpense);
    }
    
    function createRetirementChart(currentAge, retirementAge, lifeExpectancy, currentSavings, monthlySaving, totalNeeded, returnRate, inflationRate, monthlyExpense) {
        const chartContainer = document.getElementById('retirementChart');
        
        if (!chartContainer) return;
        
        if (window.retirementChartInstance) {
            window.retirementChartInstance.dispose();
        }
        
        if (typeof echarts === 'undefined') return;
        
        const chart = echarts.init(chartContainer, null, {
            renderer: 'canvas',
            useDirtyRect: false
        });
        window.retirementChartInstance = chart;
        
        const totalYears = lifeExpectancy - currentAge;
        const xAxisData = [];
        const savingsPhaseData = [];
        const withdrawalPhaseData = [];
        
        let currentSavingsValue = currentSavings;
        
        for (let i = 0; i <= totalYears; i++) {
            const age = currentAge + i;
            xAxisData.push(age);
            
            if (age < retirementAge) {
                currentSavingsValue = currentSavingsValue * (1 + returnRate) + monthlySaving * 12;
                savingsPhaseData.push(Math.round(currentSavingsValue));
                withdrawalPhaseData.push(null);
            } else {
                if (age === retirementAge) {
                    savingsPhaseData.push(Math.round(currentSavingsValue));
                } else {
                    savingsPhaseData.push(null);
                }
                
                const inflationFactor = Math.pow(1 + inflationRate, i - (retirementAge - currentAge));
                const yearlyWithdrawal = monthlyExpense * inflationFactor * 12;
                
                currentSavingsValue = currentSavingsValue * (1 + returnRate);
                currentSavingsValue -= yearlyWithdrawal;
                currentSavingsValue = Math.max(0, currentSavingsValue);
                withdrawalPhaseData.push(Math.round(currentSavingsValue));
            }
        }
        
        const option = {
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(45,48,71,0.95)',
                borderColor: 'rgba(255,255,255,0.1)',
                textStyle: { color: '#fff', fontSize: 12 },
                formatter: function(params) {
                    const age = params[0].axisValue;
                    let content = `<div style="font-weight:bold;margin-bottom:5px;">${age}세</div>`;
                    
                    params.forEach(param => {
                        if (param.value !== null && param.value !== undefined && !isNaN(param.value)) {
                            const value = numberWithCommas(Math.round(param.value));
                            let status = '';
                            
                            if (param.seriesName === '적립 단계') {
                                status = age < retirementAge ? '적립 중' : '은퇴 시점';
                            } else if (param.seriesName === '인출 단계') {
                                status = '생활비 인출 중';
                            }
                            
                            content += `<div style="display:flex;align-items:center;margin:3px 0;">
                                <span style="display:inline-block;width:10px;height:10px;background:${param.color};margin-right:5px;border-radius:50%;"></span>
                                <span style="margin-right:5px;min-width:60px;">${param.seriesName}</span>
                                <span style="font-weight:bold;">${value}만원</span>
                                <span style="margin-left:8px;color:#aaa;">(${status})</span>
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
                textStyle: { fontSize: 12, padding: [0, 4] },
                selected: { '은퇴 시점': false }
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
                axisLine: { lineStyle: { color: '#ddd' } },
                axisLabel: {
                    formatter: function(value) {
                        return (value % 5 === 0) ? value + '세' : '';
                    },
                    fontSize: 10,
                    color: '#999',
                    interval: 'auto',
                    rotate: 0
                },
                axisTick: { show: false }
            },
            yAxis: {
                type: 'value',
                name: '자산 (만원)',
                nameTextStyle: { color: '#999', fontSize: 11, padding: [0, 0, 10, 0] },
                nameGap: 25,
                axisLine: { show: false },
                splitLine: { lineStyle: { color: '#f0f0f0' } },
                axisLabel: {
                    color: '#999',
                    margin: 14,
                    formatter: function(value) {
                        return formatKoreanCurrency(value * 10000, false);
                    },
                    fontSize: 10,
                    padding: [3, 0, 3, 0]
                }
            },
            series: [
                {
                    name: '적립 단계',
                    type: 'line',
                    data: savingsPhaseData,
                    smooth: true,
                    showSymbol: false,
                    lineStyle: { width: 3 },
                    itemStyle: { color: '#4ECDC4' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(78, 205, 196, 0.3)' },
                            { offset: 1, color: 'rgba(78, 205, 196, 0.05)' }
                        ])
                    }
                },
                {
                    name: '인출 단계',
                    type: 'line',
                    data: withdrawalPhaseData,
                    smooth: true,
                    showSymbol: false,
                    lineStyle: { width: 3, type: 'solid' },
                    itemStyle: { color: '#FF4D4D' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(255, 77, 77, 0.3)' },
                            { offset: 1, color: 'rgba(255, 77, 77, 0.05)' }
                        ])
                    }
                }
            ],
            visualMap: {
                show: false,
                type: 'piecewise',
                pieces: [
                    { gt: 0, lte: retirementAge, label: '은퇴 전' },
                    { gt: retirementAge, label: '은퇴 후' }
                ]
            }
        };
        
        option.series.push({
            name: '은퇴 시점',
            type: 'line',
            markLine: {
                silent: true,
                lineStyle: { color: '#FF4D4D', type: 'dashed', width: 2 },
                label: {
                    formatter: '은퇴 시점',
                    position: 'middle',
                    color: '#FF4D4D',
                    fontSize: 12,
                    fontWeight: 'bold'
                },
                data: [{ name: '은퇴 시점', xAxis: retirementAge }]
            },
            data: [],
            tooltip: { show: false }
        });
        
        chart.setOption(option);
        
        function resizeChart() {
            if (chart && !chart.isDisposed()) {
                chart.resize();
            }
        }
        
        setTimeout(resizeChart, 200);
        window.addEventListener('resize', resizeChart);
        window.addEventListener('orientationchange', function() {
            setTimeout(resizeChart, 200);
        });
    }
    
    function provideFinancialAdvice(age, yearsToRetirement, monthlySavingsNeeded, monthlySaving, currentProgressRate) {
        let advice = '';
        
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
