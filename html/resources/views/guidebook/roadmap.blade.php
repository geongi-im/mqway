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

                <!-- 월 저축액 게이지 바 -->
                <div class="mt-6 pt-6 border-t">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-base md:text-lg font-bold text-gray-800">월 저축액</h3>
                        <div class="flex items-center">
                            <input type="text" 
                                   id="monthlySavingInput"
                                   class="w-32 text-right px-2 py-1 border rounded mr-1 text-base md:text-lg font-bold text-gray-800" maxlength="9"
                                   value="500,000">
                            <span class="text-base md:text-lg font-bold text-gray-800">원</span>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="range" 
                               id="monthlySaving" 
                               min="10000" 
                               max="3000000" 
                               step="10000" 
                               value="500000"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between mt-2 text-xs md:text-sm text-gray-600">
                            <span>1만원</span>
                            <span>300만원</span>
                        </div>
                    </div>
                </div>

                <!-- 연평균 수익률 게이지 바 -->
                <div class="mt-6 pt-6 border-t">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-base md:text-lg font-bold text-gray-800">연평균 수익률</h3>
                        <div class="flex items-center">
                            <input type="text" 
                                   id="annualReturnInput"
                                   class="w-16 text-right px-2 py-1 border rounded mr-1 text-base md:text-lg font-bold text-gray-800"
                                   value="10">
                            <span class="text-base md:text-lg font-bold text-gray-800">%</span>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="range" 
                               id="annualReturn" 
                               min="0" 
                               max="50" 
                               step="0.1" 
                               value="10"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between mt-2 text-xs md:text-sm text-gray-600">
                            <span>0%</span>
                            <span>50%</span>
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
    const annualReturn = document.getElementById('annualReturn');
    const annualReturnInput = document.getElementById('annualReturnInput');

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

    // 남은 기간 계산 함수
    function calculateRemainingPeriod(targetAmount, currentAmount, monthlySaving, annualReturn) {
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

    // 게이지바 이벤트 처리 함수
    function updateMonthlySaving(value, isFromInput = false) {
        if (!isFromInput) {
            // 슬라이더나 blur 이벤트에서 호출된 경우
            value = Math.max(10000, Math.min(3000000, value));
            monthlySavingInput.value = numberWithCommas(value);
            monthlySaving.value = value;
            updateRangeBackground(monthlySaving);
        } else {
            // 직접 입력 중인 경우
            monthlySaving.value = value;
            updateRangeBackground(monthlySaving);
        }
        
        const targetAmount = {{ $data['targetAmount'] }};
        const currentAmount = {{ $data['currentAmount'] }};
        const annualReturnValue = parseFloat(annualReturn.value);
        const months = calculateRemainingPeriod(targetAmount, currentAmount, parseInt(value), annualReturnValue);
        updateRemainingPeriod(months);
    }

    // 연평균 수익률 게이지바 이벤트 처리 함수
    function updateAnnualReturn(value, isFromInput = false) {
        if (!isFromInput) {
            // 슬라이더나 blur 이벤트에서 호출된 경우
            value = Math.max(0, Math.min(50, value));
            annualReturnInput.value = value;
            annualReturn.value = value;
            updateRangeBackground(annualReturn);
        } else {
            // 직접 입력 중인 경우
            annualReturn.value = value;
            updateRangeBackground(annualReturn);
        }
        
        const targetAmount = {{ $data['targetAmount'] }};
        const currentAmount = {{ $data['currentAmount'] }};
        const monthlySavingValue = parseInt(monthlySaving.value);
        const months = calculateRemainingPeriod(targetAmount, currentAmount, monthlySavingValue, parseFloat(value));
        updateRemainingPeriod(months);
    }

    // 이벤트 리스너
    if (monthlySaving) {
        monthlySaving.addEventListener('input', (e) => {
            updateMonthlySaving(e.target.value);
        });
    }
    if (annualReturn) {
        annualReturn.addEventListener('input', (e) => {
            updateAnnualReturn(parseFloat(e.target.value));
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
            value = Math.max(10000, Math.min(3000000, value));
            updateMonthlySaving(value);
        });

        monthlySavingInput.addEventListener('focus', (e) => {
            // 포커스 시 콤마 제거
            let value = e.target.value.replace(/,/g, '');
            e.target.value = value;
        });
    }
    
    if (annualReturnInput) {
        annualReturnInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/[^\d.]/g, '');
            // 소수점 처리
            if (value.split('.').length > 2) value = value.replace(/\.+$/, '');
            e.target.value = value;
            updateAnnualReturn(parseFloat(value) || 0, true);
        });
        
        annualReturnInput.addEventListener('blur', (e) => {
            let value = parseFloat(e.target.value) || 0;
            value = Math.max(0, Math.min(50, value));
            updateAnnualReturn(value);
        });
    }

    // 초기값 설정
    updateMonthlySaving(monthlySaving ? monthlySaving.value : 500000);
    updateAnnualReturn(annualReturn ? annualReturn.value : 10);

    // 초기 게이지바 배경 설정
    [monthlySaving, annualReturn].forEach(element => {
        if (element) updateRangeBackground(element);
    });

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

@media (max-width: 768px) {
    input[type="range"]::-webkit-slider-thumb {
        height: 16px;
        width: 16px;
    }
    
    input[type="range"]::-moz-range-thumb {
        height: 16px;
        width: 16px;
    }
}
</style>
@endpush
@endsection 