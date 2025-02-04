@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">로드맵 작성하기</h1>
        <p class="text-gray-700">목표 달성을 위한 재무 계획을 확인하세요</p>
    </div>

    <!-- PC 버전 -->
    <div class="hidden md:block">
        <div class="grid grid-cols-2 gap-8 mb-8">
            <!-- 왼쪽: 목표 금액 및 진행 상황 -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">목표 금액</h2>
                    <div class="flex items-end mb-2">
                        <span class="text-4xl font-bold text-gray-800">{{ number_format($data['targetAmount']) }}</span>
                        <span class="text-xl text-gray-600 ml-2 mb-1">원</span>
                    </div>
                    <!-- 진행 게이지 바 (PC) -->
                    <div class="relative mt-6">
                        <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-gray-800 transition-all duration-500 relative"
                                 style="width: {{ ($data['currentAmount'] / $data['targetAmount']) * 100 }}%">
                                <span class="absolute -right-2 -top-8 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                                    {{ number_format(($data['currentAmount'] / $data['targetAmount']) * 100, 1) }}%
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between mt-2 text-sm text-gray-600">
                            <span>현재: {{ number_format($data['currentAmount']) }}원</span>
                            <span>목표: {{ number_format($data['targetAmount']) }}원</span>
                        </div>
                    </div>
                </div>

                <!-- 월 저축액 게이지 바 -->
                <div class="mb-6 pt-6 border-t">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-bold text-gray-800">월 저축액</h3>
                        <span class="text-lg font-bold text-gray-800" id="monthlySavingDisplay">2,200,000원</span>
                    </div>
                    <div class="relative">
                        <input type="range" 
                               id="monthlySaving" 
                               min="100000" 
                               max="10000000" 
                               step="100000" 
                               value="2200000"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between mt-2 text-sm text-gray-600">
                            <span>10만원</span>
                            <span>1,000만원</span>
                        </div>
                    </div>
                </div>

                <!-- 연평균 수익률 게이지 바 -->
                <div class="pt-6 border-t">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-bold text-gray-800">연평균 수익률</h3>
                        <span class="text-lg font-bold text-gray-800" id="annualReturnDisplay">10%</span>
                    </div>
                    <div class="relative">
                        <input type="range" 
                               id="annualReturn" 
                               min="0" 
                               max="30" 
                               step="0.1" 
                               value="10"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between mt-2 text-sm text-gray-600">
                            <span>0%</span>
                            <span>30%</span>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mt-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">남은 기간</h3>
                            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $data['remainingMonths'] }}<span class="text-base text-gray-600 ml-2">개월</span></p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-xl font-bold text-gray-800">월 필요 저축액</h3>
                            <p class="text-3xl font-bold text-gray-800 mt-2" id="requiredSavingDisplay">
                                {{ number_format(($data['targetAmount'] - $data['currentAmount']) / $data['remainingMonths']) }}<span class="text-base text-gray-600 ml-2">원</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 오른쪽: 월간 지출 분석 -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-dark mb-6">월간 지출 분석</h2>
                <div id="expenseChartPC" class="w-full" style="height: 380px;"></div>
            </div>
        </div>
    </div>

    <!-- 모바일 버전 -->
    <div class="md:hidden space-y-6">
        <!-- 목표 금액 및 진행 상황 -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">목표 금액</h2>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                    {{ number_format($data['targetAmount']) }}원
                </p>
            </div>

            <!-- 진행 게이지 바 (모바일) -->
            <div class="relative pt-1">
                <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gray-800 transition-all duration-500 relative"
                         style="width: {{ ($data['currentAmount'] / $data['targetAmount']) * 100 }}%">
                    </div>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs font-semibold text-gray-600">
                        현재: {{ number_format($data['currentAmount']) }}원
                    </span>
                    <span class="text-xs font-semibold text-gray-800">
                        {{ number_format(($data['currentAmount'] / $data['targetAmount']) * 100, 1) }}%
                    </span>
                </div>
            </div>

            <!-- 월 저축액 게이지 바 (모바일) -->
            <div class="mt-6 pt-6 border-t">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-base font-bold text-gray-800">월 저축액</h3>
                    <span class="text-base font-bold text-gray-800" id="monthlySavingDisplayMobile">2,200,000원</span>
                </div>
                <div class="relative">
                    <input type="range" 
                           id="monthlySavingMobile" 
                           min="100000" 
                           max="10000000" 
                           step="100000" 
                           value="2200000"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    <div class="flex justify-between mt-2 text-xs text-gray-600">
                        <span>10만원</span>
                        <span>1,000만원</span>
                    </div>
                </div>
            </div>

            <!-- 연평균 수익률 게이지 바 (모바일) -->
            <div class="mt-6 pt-6 border-t">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-base font-bold text-gray-800">연평균 수익률</h3>
                    <span class="text-base font-bold text-gray-800" id="annualReturnDisplayMobile">10%</span>
                </div>
                <div class="relative">
                    <input type="range" 
                           id="annualReturnMobile" 
                           min="0" 
                           max="30" 
                           step="0.1" 
                           value="10"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    <div class="flex justify-between mt-2 text-xs text-gray-600">
                        <span>0%</span>
                        <span>30%</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t">
                <div class="text-center">
                    <h3 class="text-sm font-bold text-gray-600">남은 기간</h3>
                    <p class="text-xl font-bold text-gray-800 mt-1">{{ $data['remainingMonths'] }}개월</p>
                </div>
                <div class="text-center">
                    <h3 class="text-sm font-bold text-gray-600">월 필요 저축액</h3>
                    <p class="text-xl font-bold text-gray-800 mt-1" id="requiredSavingDisplayMobile">
                        {{ number_format(($data['targetAmount'] - $data['currentAmount']) / $data['remainingMonths']) }}원
                    </p>
                </div>
            </div>
        </div>

        <!-- 월간 지출 분석 -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-dark mb-4">월간 지출 분석</h2>
            <div id="expenseChartMobile" class="w-full" style="height: 360px;"></div>
        </div>
    </div>
</div>

@push('scripts')
<!-- eCharts CDN 추가 -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const expenses = @json($data['monthlyExpenses']);
    const colors = [
        '#2C3E50', '#E67E22', '#27AE60', '#2980B9', 
        '#8E44AD', '#C0392B', '#16A085', '#D35400',
        '#2ECC71', '#3498DB', '#9B59B6', '#E74C3C'
    ];

    // 게이지바 관련 요소들
    const monthlySaving = document.getElementById('monthlySaving');
    const monthlySavingDisplay = document.getElementById('monthlySavingDisplay');
    const annualReturn = document.getElementById('annualReturn');
    const annualReturnDisplay = document.getElementById('annualReturnDisplay');
    const requiredSavingDisplay = document.getElementById('requiredSavingDisplay');

    const monthlySavingMobile = document.getElementById('monthlySavingMobile');
    const monthlySavingDisplayMobile = document.getElementById('monthlySavingDisplayMobile');
    const annualReturnMobile = document.getElementById('annualReturnMobile');
    const annualReturnDisplayMobile = document.getElementById('annualReturnDisplayMobile');
    const requiredSavingDisplayMobile = document.getElementById('requiredSavingDisplayMobile');

    // 게이지바 배경 업데이트 함수
    function updateRangeBackground(element) {
        if (!element) return;
        const min = element.min || 0;
        const max = element.max || 100;
        const value = element.value;
        const percentage = ((value - min) / (max - min)) * 100;
        element.style.backgroundSize = `${percentage}% 100%`;
    }

    // 월 저축액 게이지바 이벤트 처리
    function updateMonthlySaving(value) {
        const formattedValue = numberWithCommas(value) + '원';
        if (monthlySavingDisplay) monthlySavingDisplay.textContent = formattedValue;
        if (monthlySavingDisplayMobile) monthlySavingDisplayMobile.textContent = formattedValue;
        updateRequiredSaving();
        
        // 게이지바 배경 업데이트
        updateRangeBackground(monthlySaving);
        updateRangeBackground(monthlySavingMobile);
    }

    // 연평균 수익률 게이지바 이벤트 처리
    function updateAnnualReturn(value) {
        const formattedValue = Math.round(value) + '%';
        if (annualReturnDisplay) annualReturnDisplay.textContent = formattedValue;
        if (annualReturnDisplayMobile) annualReturnDisplayMobile.textContent = formattedValue;
        updateRequiredSaving();
        
        // 게이지바 배경 업데이트
        updateRangeBackground(annualReturn);
        updateRangeBackground(annualReturnMobile);
    }

    // 월 필요 저축액 계산 및 업데이트
    function updateRequiredSaving() {
        const targetAmount = {{ $data['targetAmount'] }};
        const currentAmount = {{ $data['currentAmount'] }};
        const remainingMonths = {{ $data['remainingMonths'] }};
        const monthlyAmount = (targetAmount - currentAmount) / remainingMonths;
        
        const formattedValue = numberWithCommas(Math.round(monthlyAmount)) + '원';
        if (requiredSavingDisplay) {
            requiredSavingDisplay.innerHTML = `${formattedValue}<span class="text-base text-gray-600 ml-2">원</span>`;
        }
        if (requiredSavingDisplayMobile) {
            requiredSavingDisplayMobile.textContent = formattedValue;
        }
    }

    // PC 버전 이벤트 리스너
    if (monthlySaving) {
        monthlySaving.addEventListener('input', (e) => {
            updateMonthlySaving(e.target.value);
            updateRangeBackground(e.target);
        });
    }
    if (annualReturn) {
        annualReturn.addEventListener('input', (e) => {
            updateAnnualReturn(e.target.value);
            updateRangeBackground(e.target);
        });
    }

    // 모바일 버전 이벤트 리스너
    if (monthlySavingMobile) {
        monthlySavingMobile.addEventListener('input', (e) => {
            updateMonthlySaving(e.target.value);
            updateRangeBackground(e.target);
        });
    }
    if (annualReturnMobile) {
        annualReturnMobile.addEventListener('input', (e) => {
            updateAnnualReturn(e.target.value);
            updateRangeBackground(e.target);
        });
    }

    // 초기값 설정
    updateMonthlySaving(monthlySaving ? monthlySaving.value : 2200000);
    updateAnnualReturn(annualReturn ? annualReturn.value : 10);

    // 초기 게이지바 배경 설정
    [monthlySaving, monthlySavingMobile, annualReturn, annualReturnMobile].forEach(element => {
        if (element) updateRangeBackground(element);
    });

    // PC 차트 설정
    if (document.getElementById('expenseChartPC')) {
        const pcChart = echarts.init(document.getElementById('expenseChartPC'));
        const pcOption = {
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
                itemWidth: 14,
                itemHeight: 14,
                textStyle: { 
                    color: '#2C3E50',
                    fontSize: 12
                },
                type: 'scroll',
                pageTextStyle: {
                    color: '#2C3E50'
                }
            },
            series: [{
                name: '월간 지출',
                type: 'pie',
                radius: ['44%', '77%'],
                center: ['50%', '45%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 6,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: false,
                    position: 'center',
                    color: '#2C3E50'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: '20',
                        fontWeight: 'bold',
                        color: '#2C3E50'
                    }
                },
                labelLine: {
                    show: true,
                    length: 10,
                    length2: 10,
                    lineStyle: {
                        color: '#2C3E50'
                    }
                },
                data: expenses
            }]
        };
        pcChart.setOption(pcOption);
        window.addEventListener('resize', () => pcChart.resize());
    }

    // 모바일 차트 설정
    if (document.getElementById('expenseChartMobile')) {
        const mobileChart = echarts.init(document.getElementById('expenseChartMobile'));
        const mobileOption = {
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
                itemWidth: 10,
                itemHeight: 10,
                textStyle: { 
                    color: '#2C3E50',
                    fontSize: 11
                },
                type: 'scroll',
                pageTextStyle: {
                    color: '#2C3E50'
                }
            },
            series: [{
                name: '월간 지출',
                type: 'pie',
                radius: ['44%', '66%'],
                center: ['50%', '55%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 4,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: false,
                    position: 'center',
                    color: '#2C3E50'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: '20',
                        fontWeight: 'bold',
                        color: '#2C3E50'
                    }
                },
                labelLine: {
                    show: true,
                    length: 10,
                    length2: 10,
                    lineStyle: {
                        color: '#2C3E50'
                    }
                },
                data: expenses
            }]
        };
        mobileChart.setOption(mobileOption);
        window.addEventListener('resize', () => mobileChart.resize());
    }
});

// 숫자 포맷팅 함수
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
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
    transition: background .3s ease-in-out;
}

input[type="range"]::-webkit-slider-runnable-track {
    -webkit-appearance: none;
    box-shadow: none;
    border: none;
    background: transparent;
}

input[type="range"]::-webkit-slider-thumb:hover {
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px #1f2937;
}

/* Firefox 지원 */
input[type="range"]::-moz-range-thumb {
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #1f2937;
    cursor: pointer;
    box-shadow: 0 0 2px 0 #555;
    transition: background .3s ease-in-out;
    border: none;
}

input[type="range"]::-moz-range-track {
    background: #e5e7eb;
    border-radius: 5px;
    height: 8px;
    border: none;
}

input[type="range"]::-moz-range-progress {
    background-color: #1f2937;
    border-radius: 5px;
    height: 8px;
}
</style>
@endpush
@endsection 