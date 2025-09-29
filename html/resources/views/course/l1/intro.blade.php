@extends('layouts.app')

<style>
/* 단계 카드 기본 레이아웃 */
.program-step {
    display: flex;
    flex-direction: column;
}

/* 단계 콘텐츠 기본 스타일 */
.step-content {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    flex: 1 1 auto;
    margin-bottom: 10px;
}

.step-button {
    display: block;
    width: 100%;
    margin-top: auto;
}

@media (max-width: 767px) {
    .step-content {
        transition: max-height 0.3s ease-in-out;
    }
}

@media (min-width: 768px) {
    .program-step-grid {
        grid-auto-rows: 1fr;
    }

    .toggle-icon {
        display: none !important;
    }

    .step-content {
        max-height: 120px;
        min-height: 120px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 8px;
    }
}
</style>

@section('content')

<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">MQ L1 코스</h1>
        <p class="text-gray-700">코스를 진행하며 알게된 내용을 아이들과 함께 공유하세요.</p>
    </div>

    <!-- 나무 이미지 영역 -->
    <div class="mb-12">
        <div class="flex justify-center">
            <div class="w-full max-w-2xl h-[500px] bg-white rounded-3xl shadow-2xl flex items-center justify-center relative overflow-hidden">
                <!-- 나무 이미지 -->
                <div class="tree-image transition-all duration-500 w-full h-full" id="tree-image">
                    <img src="{{ asset('/images/course-l1/tree_1.png') }}" 
                         alt="나무 성장 이미지" 
                         class="w-full h-full object-contain p-4"
                         onerror="this.src='https://via.placeholder.com/600x500/90EE90/228B22?text=나무+이미지+로딩+실패'">
                </div>
            </div>
        </div>
    </div>

    <!-- 메인 컨텐츠 영역 -->
    <div class="bg-white rounded-3xl shadow-2xl p-8 lg:p-12">
            <!-- 진행률 -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-lg font-semibold text-gray-800">전체 진행률</span>
                    <span class="text-lg font-semibold text-green-600" id="progress-text">25%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-500" style="width: 25%" id="progress-bar"></div>
                </div>
            </div>

            <!-- 프로그램 단계 -->
            <div class="space-y-6">
                
                <!-- 데스크탑: 2x2 그리드, 모바일: 1x4 그리드 -->
                <div class="program-step-grid grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Step 1: 마인드셋 -->
                    <div class="program-step completed bg-green-50 border-2 border-green-200 rounded-2xl p-6 transition-all duration-300 hover:shadow-lg" data-step="1">
                        <div class="step-header flex items-center justify-between mb-4 cursor-pointer md:cursor-default" onclick="toggleStep(1)">
                            <h3 class="text-lg font-bold text-green-800">1단계: 마인드셋</h3>
                            <div class="flex items-center gap-2">
                                <span class="step-status px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Complete</span>
                                <svg class="toggle-icon w-5 h-5 text-green-700 transform transition-transform duration-300 rotate-180 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-content transition-all duration-300">
                            <div class="text-green-600 space-y-2 text-sm">
                                <p>• 금융의 역사</p>
                                <p>• 경제용어 학습</p>
                                <p>• 내 아이의 원하는 삶</p>
                                <p>• 표정맵핑 완성하기</p>
                            </div>
                        </div>
                        <button class="step-button w-full px-4 py-3 bg-point2 hover:bg-point2/90 text-white rounded-xl text-base font-medium transition-colors" 
                                onclick="openWriteModal(1, '마인드셋')">
                            수정하기
                        </button>
                    </div>

                    <!-- Step 2: 개인재무제표 -->
                    <div class="program-step bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-6 transition-all duration-300 hover:shadow-lg" data-step="2">
                        <div class="step-header flex items-center justify-between mb-4 cursor-pointer md:cursor-default" onclick="toggleStep(2)">
                            <h3 class="text-lg font-bold text-yellow-800">2단계: 개인재무제표</h3>
                            <div class="flex items-center gap-2">
                                <span class="step-status px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-full">In Progress</span>
                                <svg class="toggle-icon w-5 h-5 text-yellow-700 transform transition-transform duration-300 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-content transition-all duration-300">
                            <div class="text-yellow-600 space-y-2 text-sm">
                                <p>• 원하는 삶 공유</p>
                                <p>• 현재 재무제표 작성</p>
                                <p>• 습관근육 형성</p>
                                <p>• 부자아빠 가난한아빠 1</p>
                                <p>• 경제신문 방법</p>
                                <p>• Cashflow 보드게임 설명</p>
                            </div>
                        </div>
                        <button class="step-button w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl text-base font-medium transition-colors" 
                                onclick="openWriteModal(2, '개인재무제표')">
                            작성하기
                        </button>
                    </div>

                    <!-- Step 3: Cashflow 보드게임 -->
                    <div class="program-step bg-gray-50 border-2 border-gray-200 rounded-2xl p-6 transition-all duration-300 hover:shadow-lg" data-step="3">
                        <div class="step-header flex items-center justify-between mb-4 cursor-pointer md:cursor-default" onclick="toggleStep(3)">
                            <h3 class="text-lg font-bold text-gray-800">3단계: Cashflow 보드게임</h3>
                            <div class="flex items-center gap-2">
                                <span class="step-status px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">Ready</span>
                                <svg class="toggle-icon w-5 h-5 text-gray-700 transform transition-transform duration-300 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-content transition-all duration-300">
                            <div class="text-gray-600 space-y-2 text-sm">
                                <p>• 자산, 부채 개념</p>
                                <p>• 경제신문 공유하기 1가지</p>
                                <p>• 경제뉴스에 관한 책 한권</p>
                                <p>• 부모와 Cashflow 아이 실링 내용 전달</p>
                                <p>• 부모 답부</p>
                            </div>
                        </div>
                        <button class="step-button w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl text-base font-medium transition-colors" 
                                onclick="openWriteModal(3, 'Cashflow 보드게임')">
                            작성하기
                        </button>
                    </div>

                    <!-- Step 4: MQ뿌리다지기 -->
                    <div class="program-step bg-gray-50 border-2 border-gray-200 rounded-2xl p-6 transition-all duration-300 hover:shadow-lg" data-step="4">
                        <div class="step-header flex items-center justify-between mb-4 cursor-pointer md:cursor-default" onclick="toggleStep(4)">
                            <h3 class="text-lg font-bold text-gray-800">4단계: MQ뿌리다지기</h3>
                            <div class="flex items-center gap-2">
                                <span class="step-status px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">Ready</span>
                                <svg class="toggle-icon w-5 h-5 text-gray-700 transform transition-transform duration-300 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-content transition-all duration-300">
                            <div class="text-gray-600 space-y-2 text-sm">
                                <p>• 경제신문 독서 공유하기</p>
                                <p>• 표정맵핑, 미래 재무제표 작성</p>
                                <p>• 기둥세우기 (L2 커리큘럼)</p>
                            </div>
                        </div>
                        <button class="step-button w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl text-base font-medium transition-colors" 
                                onclick="openWriteModal(4, 'MQ뿌리다지기')">
                            작성하기
                        </button>
                    </div>
                </div>
            </div>
    </div>

</div>

<!-- 글쓰기 모달 -->
<div id="writeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4" id="modalTitle">글쓰기</h3>
        <form id="writeForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">단계별 학습 내용을 작성해주세요:</label>
                <textarea id="stepContent" 
                         class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                         rows="6" 
                         placeholder="이 단계에서 학습한 내용과 느낀 점, 실천할 내용 등을 자유롭게 작성해주세요...&#10;&#10;예시:&#10;- 오늘 배운 경제 용어: 자산, 부채&#10;- 새롭게 알게 된 점: 부자아빠와 가난한아빠의 차이&#10;- 앞으로 실천하고 싶은 것: 용돈 기입장 작성하기"></textarea>
            </div>
            <div class="flex space-x-4">
                <button type="button" 
                        onclick="closeWriteModal()" 
                        class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                    취소
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                    저장
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentStep = null;

function openWriteModal(step, stepName) {
    currentStep = step;
    document.getElementById('modalTitle').textContent = stepName + ' 단계 작성';
    document.getElementById('writeModal').classList.remove('hidden');
    document.getElementById('writeModal').classList.add('flex');
    document.getElementById('stepContent').focus();
}

function closeWriteModal() {
    document.getElementById('writeModal').classList.add('hidden');
    document.getElementById('writeModal').classList.remove('flex');
    document.getElementById('stepContent').value = '';
    currentStep = null;
}

document.getElementById('writeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const content = document.getElementById('stepContent').value.trim();
    if (!content) {
        alert('내용을 입력해주세요.');
        return;
    }
    
    // 여기서 실제 서버로 데이터 전송
    // 임시로 완료 처리
    completeStep(currentStep);
    closeWriteModal();
});

// 모바일 환경 감지 함수
function isMobile() {
    return window.innerWidth < 768; // Tailwind의 md breakpoint
}

function toggleStep(step) {
    // 모바일에서만 토글 기능 작동
    if (!isMobile()) return;
    
    const stepElement = document.querySelector(`[data-step="${step}"]`);
    if (!stepElement) return;

    const stepContent = stepElement.querySelector('.step-content');
    const stepButton = stepElement.querySelector('.step-button');
    const toggleIcon = stepElement.querySelector('.toggle-icon');

    if (!stepContent) return;

    stepContent.style.overflow = 'hidden';

    const isExpanded = stepContent.style.maxHeight && stepContent.style.maxHeight !== '0px' && stepContent.style.maxHeight !== 'none';

    if (isExpanded) {
        stepContent.style.maxHeight = '0px';
        if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
        if (stepButton) stepButton.style.display = 'none';
    } else {
        const minHeight = Math.max(stepContent.scrollHeight, 160);
        stepContent.style.maxHeight = minHeight + 'px';
        if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
        if (stepButton) stepButton.style.display = 'block';
    }
}

function completeStep(step) {
    const stepElement = document.querySelector(`[data-step="${step}"]`);
    if (!stepElement) return;

    const stepContent = stepElement.querySelector('.step-content');
    const stepButton = stepElement.querySelector('.step-button');
    const statusElement = stepElement.querySelector('.step-status');
    const toggleIcon = stepElement.querySelector('.toggle-icon');

    stepElement.classList.remove('bg-yellow-50', 'border-yellow-200', 'bg-gray-50', 'border-gray-200');
    stepElement.classList.add('bg-green-50', 'border-green-200', 'completed');

    if (statusElement) {
        statusElement.textContent = 'Complete';
        statusElement.className = 'step-status px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full';
    }

    if (toggleIcon) {
        toggleIcon.classList.remove('text-yellow-700', 'text-gray-700');
        toggleIcon.classList.add('text-green-700', 'md:hidden');
        toggleIcon.style.transform = isMobile() ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    if (stepButton) {
        const stepName = stepElement.querySelector('h3').textContent.replace(/^\d+단계: /, '');
        stepButton.textContent = '수정하기';
        stepButton.className = 'step-button w-full px-4 py-3 bg-point2 text-white hover:bg-point2/90 rounded-xl text-base font-medium transition-colors';
        stepButton.disabled = false;
        stepButton.onclick = () => openWriteModal(step, stepName);
    }

    updateProgress();
    updateTreeImage();

    if (isMobile()) {
        setTimeout(() => {
            if (stepContent) {
                stepContent.style.overflow = 'hidden';
                stepContent.style.maxHeight = '0px';
            }
            if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
            if (stepButton) stepButton.style.display = 'none';
        }, 500);
    } else {
        if (stepContent) {
            stepContent.style.overflow = '';
            stepContent.style.maxHeight = '';
            stepContent.style.height = '';
        }
        if (stepButton) stepButton.style.display = 'block';
    }
}

function updateProgress() {
    const completedSteps = document.querySelectorAll('[data-step] .bg-green-100').length;
    const totalSteps = 4;
    const progress = Math.round((completedSteps / totalSteps) * 100);
    
    document.getElementById('progress-text').textContent = progress + '%';
    document.getElementById('progress-bar').style.width = progress + '%';
}

function updateTreeImage() {
    const completedSteps = document.querySelectorAll('[data-step] .bg-green-100').length;
    const treeImage = document.getElementById('tree-image');
    const imageElement = treeImage.querySelector('img');
    
    // 단계별로 실제 나무 이미지 변경
    const treeStages = [
        "/images/course-l1/tree_1.png",  // 0단계 (초기상태)
        "/images/course-l1/tree_1.png",  // 1단계 완료
        "/images/course-l1/tree_2.png",  // 2단계 완료
        "/images/course-l1/tree_3.png",  // 3단계 완료
        "/images/course-l1/tree_4.png"   // 4단계 완료 (최종 완성)
    ];
    
    if (imageElement) {
        const newImageSrc = treeStages[completedSteps] || treeStages[0];
        
        // 이미지 변경 시 부드러운 트랜지션 효과
        imageElement.style.opacity = '0.7';
        
        setTimeout(() => {
            imageElement.src = newImageSrc;
            imageElement.style.opacity = '1';
        }, 200);
        
        // 완성 시 특별 축하 효과
        if (completedSteps >= 4) {
            treeImage.classList.add('animate-pulse');
            
            // 성공 메시지 표시
            const celebration = document.createElement('div');
            celebration.className = 'absolute top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded-lg text-sm font-bold z-10 animate-bounce';
            celebration.textContent = '🎉 나무 키우기 완성! 🎉';
            treeImage.appendChild(celebration);
            
            setTimeout(() => {
                treeImage.classList.remove('animate-pulse');
                if (celebration.parentNode) {
                    celebration.remove();
                }
            }, 3000);
        }
    }
}

// 모달 외부 클릭 시 닫기
document.getElementById('writeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeWriteModal();
    }
});

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('writeModal').classList.contains('hidden')) {
        closeWriteModal();
    }
});

// 모든 단계의 초기 상태를 설정하는 함수
function initializeStepStates() {
    const allSteps = document.querySelectorAll('.program-step');
    allSteps.forEach(function(stepElement) {
        const stepContent = stepElement.querySelector('.step-content');
        const toggleIcon = stepElement.querySelector('.toggle-icon');
        const stepButton = stepElement.querySelector('.step-button');
        const isCompleted = stepElement.classList.contains('completed');
        
        if (stepContent) {
            if (isMobile()) {
                stepContent.style.overflow = 'hidden';

                if (isCompleted) {
                    // 완료된 단계: 모바일에서 닫힌 상태
                    stepContent.style.maxHeight = '0px';
                    if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
                    if (stepButton) stepButton.style.display = 'none';
                } else {
                    // 미완료 단계: 모바일에서 열린 상태
                    const minHeight = Math.max(stepContent.scrollHeight, 160);
                    stepContent.style.maxHeight = minHeight + 'px';
                    if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
                    if (stepButton) stepButton.style.display = 'block';
                }
            } else {
                stepContent.style.overflow = '';
                stepContent.style.maxHeight = '';
                stepContent.style.height = '';
                if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
                if (stepButton) stepButton.style.display = 'block';
            }
        }
    });
}

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', function() {
    initializeStepStates();
});

// 화면 크기 변경 시 상태 재조정
window.addEventListener('resize', function() {
    initializeStepStates();
});
</script>
@endpush
@endsection
