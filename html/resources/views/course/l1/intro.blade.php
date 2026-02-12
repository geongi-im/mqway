@extends('layouts.app')

@section('content')
<style>
    /* ===== Design System & Animations (Matched to index.blade.php) ===== */


    /* Animations */
    /* Animations */
    @keyframes pulse-soft { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.02); } }
    @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }

    .animate-float { animation: float 6s ease-in-out infinite; }

    /* Custom Scrollbar for step content */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.02); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.2); }

    /* Card Hover Effects */
    .card-hover {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid rgba(0,0,0,0.04);
    }
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        border-color: rgba(78, 205, 196, 0.3); /* Mint hint on hover */
    }

    /* Checkbox Custom Style */
    .custom-checkbox {
        appearance: none;
        background-color: #fff;
        margin: 0;
        font: inherit;
        color: currentColor;
        width: 1.5em;
        height: 1.5em;
        border: 2px solid #E5E7EB;
        border-radius: 0.35em;
        display: grid;
        place-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .custom-checkbox::before {
        content: "";
        width: 0.85em;
        height: 0.85em;
        transform: scale(0);
        transition: 120ms transform ease-in-out;
        box-shadow: inset 1em 1em white;
        transform-origin: center;
        clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
    }
    .custom-checkbox:checked {
        background-color: #4ECDC4; /* Mint */
        border-color: #4ECDC4;
    }
    .custom-checkbox:checked::before {
        transform: scale(1);
    }

    /* Progress Bar Animation */
    .progress-fill {
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(90deg, #4ECDC4 0%, #26D0CE 100%);
    }
    
    /* Completed Step Style */
    .step-completed {
        background-color: #F0FDFA; /* Light Mint */
        border-color: #4ECDC4;
    }
    .step-completed .step-title { color: #2C7A7B; }
    .step-completed .step-icon { background-color: #E6FFFA; color: #38B2AC; }
</style>

<div class="main-page bg-[#F8F9FB] min-h-screen pb-12">
    
    <!-- ===== Header Section ===== -->
    <div class="bg-gradient-to-br from-[#3D4148] to-[#2D3047] pt-12 pb-32 px-4 relative overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute top-[-20%] right-[-5%] w-[500px] h-[500px] rounded-full bg-[radial-gradient(circle,rgba(78,205,196,0.1)_0%,transparent_70%)] blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[400px] h-[400px] rounded-full bg-[radial-gradient(circle,rgba(255,77,77,0.08)_0%,transparent_70%)] blur-3xl pointer-events-none"></div>
        
        <div class="container mx-auto max-w-6xl relative z-10 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 text-white/90 py-1.5 px-4 rounded-full text-sm font-medium mb-4 border border-white/10 backdrop-blur-md animate-fadeIn">
                <span>🌱</span> <span>MQ 경제 교육 코스</span>
            </div>
            <h1 class="font-outfit text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-4 tracking-tight animate-slideUp" style="animation-delay: 0.1s;">
                Level 1. <span class="text-[#4ECDC4]">씨앗</span> 심기
            </h1>
            <p class="text-white/70 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed animate-slideUp" style="animation-delay: 0.2s;">
                올바른 <span class="text-white font-semibold">경제 마인드셋</span>과 투자의 기초를 다지는 3단계 과정입니다.
            </p>
        </div>
    </div>

    <!-- ===== Main Dashboard Content ===== -->
    <div class="container mx-auto px-4 max-w-6xl -mt-20 relative z-20">
        <div class="grid lg:grid-cols-3 gap-6 lg:gap-8">
            
            <!-- Left Column: Tree & Progress (Sticky on Desktop) -->
            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <!-- Tree Visualization Card -->
                    <div class="bg-white rounded-3xl p-6 shadow-[0_20px_40px_rgba(0,0,0,0.08)] text-center animate-slideUp border border-white/50 backdrop-blur-sm" style="animation-delay: 0.3s;">
                        <h2 class="font-outfit text-xl font-bold text-gray-800 mb-2">나의 성장 나무</h2>
                        <p class="text-sm text-gray-500 mb-6">학습을 완료하면 나무가 자라나요!</p>
                        
                        <div class="bg-gradient-to-b from-[#F0F4F8] to-white rounded-2xl p-6 mb-6 relative min-h-[300px] flex items-center justify-center overflow-hidden group">
                            <!-- Glow effect behind tree -->
                            <div class="absolute inset-0 bg-gradient-to-t from-[#4ECDC4]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            <div class="relative z-10 w-full h-full transition-all duration-500 animate-float" id="tree-image">
                                <img src="{{ asset('/images/course-l1/tree_1.png') }}" 
                                     alt="나무 성장 이미지" 
                                     class="w-full h-full object-contain filter drop-shadow-xl"
                                     onerror="this.src='https://via.placeholder.com/600x500/90EE90/228B22?text=Tree+Loading...'">
                            </div>
                        </div>

                        <!-- Progress Section -->
                        <div class="text-left">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-sm font-semibold text-gray-500">진행률</span>
                                <span class="font-outfit text-2xl font-bold text-[#4ECDC4]" id="progress-text">0%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                <div id="progress-bar" class="progress-fill h-full rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Learning Steps (3 Steps) -->
            <div class="lg:col-span-2 space-y-5 animate-slideUp" style="animation-delay: 0.4s;">
                
                 <!-- Step 1 (1-1 ~ 1-4) -->
                <div class="bg-white rounded-2xl p-6 md:p-8 card-hover border-l-4 border-l-transparent group program-step relative overflow-hidden" data-step="1">
                    <div class="flex items-start gap-5">
                        <div class="pt-1">
                            <input type="checkbox"
                                   class="step-checkbox custom-checkbox"
                                   onchange="handleCheckboxChange(1)"
                                   onclick="event.stopPropagation()">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-outfit text-xl font-bold text-gray-800 group-hover:text-[#2D3047] transition-colors step-title">Step 1. 화폐와 가치, 그리고 꿈</h3>
                                <span class="step-status text-xs font-bold uppercase tracking-wider py-1 px-2.5 rounded bg-gray-100 text-gray-500">진행중</span>
                            </div>
                            <p class="text-gray-500 text-sm mb-4 leading-relaxed">돈의 역사를 배우고, 자산과 부채를 명확히 구분하며 내가 원하는 삶을 구체적으로 설계합니다.</p>
                            
                            <div class="bg-[#F8F9FB] rounded-xl p-4 step-content">
                                <ul class="grid grid-cols-1 gap-2.5 text-sm text-gray-600">
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#FF4D4D] mt-1.5 flex-shrink-0"></span>
                                        <span><strong>1-1. 화폐가치 & 돈의 역사</strong> (MQWAY 가입, 돈의 가치 이해)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#FF4D4D] mt-1.5 flex-shrink-0"></span>
                                        <span><strong>1-2. Need/Wants</strong> (소비패턴 분석, 자산과 부채의 연결)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#4ECDC4] mt-1.5 flex-shrink-0"></span>
                                        <span><strong>1-3. 원하는 삶 시각화</strong> (나만의 버킷리스트 만들기)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-1.5 flex-shrink-0"></span>
                                        <span><strong>1-4. 목표 구조화</strong> (만다라트 계획표 작성)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2 (2-1 ~ 2-4) -->
                <div class="bg-white rounded-2xl p-6 md:p-8 card-hover border-l-4 border-l-transparent group program-step relative overflow-hidden" data-step="2">
                    <div class="flex items-start gap-5">
                        <div class="pt-1">
                            <input type="checkbox"
                                   class="step-checkbox custom-checkbox"
                                   onchange="handleCheckboxChange(2)"
                                   onclick="event.stopPropagation()">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-outfit text-xl font-bold text-gray-800 group-hover:text-[#2D3047] transition-colors step-title">Step 2. 주식 시장과 투자의 원리</h3>
                                <span class="step-status text-xs font-bold uppercase tracking-wider py-1 px-2.5 rounded bg-gray-100 text-gray-500">진행중</span>
                            </div>
                            <p class="text-gray-500 text-sm mb-4 leading-relaxed">주식과 채권의 개념을 익히고, 복리의 마법을 통해 저축과 투자의 차이를 비교 분석합니다.</p>
                            
                            <div class="bg-[#F8F9FB] rounded-xl p-4 step-content">
                                <ul class="grid grid-cols-1 gap-2.5 text-sm text-gray-600">
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#FF4D4D] mt-1.5 flex-shrink-0"></span>
                                        <span><strong>2-1. 주식의 역사</strong> (주식/증권/채권 용어 완전 정복)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#FFB347] mt-1.5 flex-shrink-0"></span>
                                        <span><strong>2-2. 금융 현장 체험</strong> (증권박물관 견학 및 후기)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#4ECDC4] mt-1.5 flex-shrink-0"></span>
                                        <span><strong>2-3. 금융 개념 퀴즈</strong> (주식, 채권 게임 체험)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-1.5 flex-shrink-0"></span>
                                        <span><strong>2-4. 금리와 복리</strong> (저축 vs 투자 시나리오 비교)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 (3-1 ~ 3-4) -->
                <div class="bg-white rounded-2xl p-6 md:p-8 card-hover border-l-4 border-l-transparent group program-step relative overflow-hidden" data-step="3">
                    <div class="flex items-start gap-5">
                        <div class="pt-1">
                            <input type="checkbox"
                                   class="step-checkbox custom-checkbox"
                                   onchange="handleCheckboxChange(3)"
                                   onclick="event.stopPropagation()">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-outfit text-xl font-bold text-gray-800 group-hover:text-[#2D3047] transition-colors step-title">Step 3. 실전! 경제 흐름과 재무제표</h3>
                                <span class="step-status text-xs font-bold uppercase tracking-wider py-1 px-2.5 rounded bg-gray-100 text-gray-500">진행중</span>
                            </div>
                            <p class="text-gray-500 text-sm mb-4 leading-relaxed">Cashflow 게임과 역할 놀이를 통해 시장의 가격 형성 원리를 배우고 내 직업의 재무제표를 작성합니다.</p>
                            
                            <div class="bg-[#F8F9FB] rounded-xl p-4 step-content">
                                <ul class="grid grid-cols-1 gap-2.5 text-sm text-gray-600">
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#4ECDC4] mt-1.5 flex-shrink-0"></span>
                                        <span><strong>3-1. Cashflow 게임</strong> (기초: 경제 흐름 읽기)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#FF4D4D] mt-1.5 flex-shrink-0"></span>
                                        <span><strong>3-2. 직업 재무제표</strong> (나만의 수입/지출/자산/부채 설계)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#FFB347] mt-1.5 flex-shrink-0"></span>
                                        <span><strong>3-3. 시장의 이해</strong> (원가 분석과 유통 구조 역할놀이)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-1.5 flex-shrink-0"></span>
                                        <span><strong>3-4. Cashflow 실전</strong> (심화: 투자와 자산 증식)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const COURSE_CODE = 'l1';
const TOTAL_STEPS = 3; 

// 페이지 로드 시 DB에서 진행 상태 불러오기
async function loadProgressFromDB() {
    try {
        const response = await fetch(`/course/progress/${COURSE_CODE}`);
        const data = await response.json();

        if (data.success && data.progress) {
            Object.values(data.progress).forEach(step => {
                updateStepUI(step.step_number, step.is_completed);
            });
            updateProgress();
            updateTreeImage();
        }
    } catch (error) {
        console.error('진행 상태 로드 실패:', error);
    }
}

// 체크박스 변경 핸들러
async function handleCheckboxChange(stepNumber) {
    const checkbox = document.querySelector(`[data-step="${stepNumber}"] .step-checkbox`);
    const isChecked = checkbox.checked;

    try {
        const response = await fetch('/course/progress/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                course_code: COURSE_CODE,
                step_number: stepNumber
            })
        });

        const data = await response.json();

        if (data.success) {
            updateStepUI(stepNumber, data.is_completed);
            updateProgress();
            updateTreeImage();
        } else {
            checkbox.checked = !isChecked;
            if (data.message) alert(data.message);
        }
    } catch (error) {
        console.error('상태 업데이트 실패:', error);
        checkbox.checked = !isChecked;
        alert('상태 업데이트 중 오류가 발생했습니다.');
    }
}

// 단계 UI 업데이트
function updateStepUI(stepNumber, isCompleted) {
    const stepElement = document.querySelector(`[data-step="${stepNumber}"]`);
    if (!stepElement) return;

    const checkbox = stepElement.querySelector('.step-checkbox');
    const statusElement = stepElement.querySelector('.step-status');
    const titleElement = stepElement.querySelector('.step-title');

    if (checkbox) checkbox.checked = isCompleted;

    if (isCompleted) {
        stepElement.classList.add('step-completed');
        stepElement.classList.remove('bg-white');
        
        // border-l-4 색상 변경
        stepElement.classList.remove('border-l-transparent');
        stepElement.classList.add('border-l-[#4ECDC4]');

        if (statusElement) {
            statusElement.textContent = '완료';
            statusElement.className = 'step-status text-xs font-bold uppercase tracking-wider py-1 px-2.5 rounded bg-[#E6FFFA] text-[#38B2AC]';
        }
    } else {
        stepElement.classList.remove('step-completed');
        stepElement.classList.add('bg-white');
        
        // border-l-4 색상 복구
        stepElement.classList.add('border-l-transparent');
        stepElement.classList.remove('border-l-[#4ECDC4]');

        if (statusElement) {
            statusElement.textContent = '진행중';
            statusElement.className = 'step-status text-xs font-bold uppercase tracking-wider py-1 px-2.5 rounded bg-gray-100 text-gray-500';
        }
    }
}

function updateProgress() {
    const completedSteps = document.querySelectorAll('[data-step] .step-checkbox:checked').length;
    const progress = Math.round((completedSteps / TOTAL_STEPS) * 100);

    document.getElementById('progress-text').textContent = progress + '%';
    document.getElementById('progress-bar').style.width = progress + '%';
}

function updateTreeImage() {
    const completedSteps = document.querySelectorAll('[data-step] .step-checkbox:checked').length;
    const treeImage = document.getElementById('tree-image');
    const imageElement = treeImage?.querySelector('img');

    // 단계별로 실제 나무 이미지 변경 (3단계로 조정)
    const treeStages = [
        "/images/course-l1/tree_1.png",  // 0단계
        "/images/course-l1/tree_2.png",  // 1단계 완료
        "/images/course-l1/tree_3.png",  // 2단계 완료
        "/images/course-l1/tree_4.png"   // 3단계 완료
    ];

    if (imageElement) {
        const newImageSrc = treeStages[completedSteps] || treeStages[0];
        // 부드러운 전환
        imageElement.style.transform = 'scale(0.95)';
        imageElement.style.opacity = '0.5';
        
        setTimeout(() => {
            imageElement.src = newImageSrc;
            imageElement.style.transform = 'scale(1)';
            imageElement.style.opacity = '1';
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadProgressFromDB();
});
</script>
@endpush
@endsection
