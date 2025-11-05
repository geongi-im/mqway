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
        <p class="text-gray-700">4단계 학습 과정을 통해 아이들과 함께 경제 교육을 시작하세요.</p>
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
                    <span class="text-lg font-semibold text-green-600" id="progress-text">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-500" style="width: 0%" id="progress-bar"></div>
                </div>
            </div>

            <!-- 프로그램 단계 -->
            <div class="space-y-6">
                
                <!-- 데스크탑: 2x2 그리드, 모바일: 1x4 그리드 -->
                <div class="program-step-grid grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Step 1: 마인드셋 -->
                    <div class="program-step bg-gray-50 border-2 border-gray-200 rounded-2xl p-6 transition-all duration-300 hover:shadow-lg" data-step="1">
                        <div class="step-header flex items-center justify-between mb-4 cursor-pointer md:cursor-default" onclick="toggleStep(1)">
                            <div class="flex items-center gap-3">
                                <input type="checkbox"
                                       class="step-checkbox w-5 h-5 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer"
                                       onchange="handleCheckboxChange(1)"
                                       onclick="event.stopPropagation()">
                                <h3 class="text-lg font-bold text-gray-800">1단계: 마인드셋</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="step-status px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">진행중</span>
                                <svg class="toggle-icon w-5 h-5 text-gray-700 transform transition-transform duration-300 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-content transition-all duration-300">
                            <div class="text-gray-600 space-y-2 text-sm">
                                <p>• 금융의 역사</p>
                                <p>• 경제용어 학습</p>
                                <p>• 내 아이의 원하는 삶</p>
                                <p>• 표정맵핑 완성하기</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: 개인재무제표 -->
                    <div class="program-step bg-gray-50 border-2 border-gray-200 rounded-2xl p-6 transition-all duration-300 hover:shadow-lg" data-step="2">
                        <div class="step-header flex items-center justify-between mb-4 cursor-pointer md:cursor-default" onclick="toggleStep(2)">
                            <div class="flex items-center gap-3">
                                <input type="checkbox"
                                       class="step-checkbox w-5 h-5 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer"
                                       onchange="handleCheckboxChange(2)"
                                       onclick="event.stopPropagation()">
                                <h3 class="text-lg font-bold text-gray-800">2단계: 개인재무제표</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="step-status px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">진행중</span>
                                <svg class="toggle-icon w-5 h-5 text-gray-700 transform transition-transform duration-300 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-content transition-all duration-300">
                            <div class="text-gray-600 space-y-2 text-sm">
                                <p>• 원하는 삶 공유</p>
                                <p>• 현재 재무제표 작성</p>
                                <p>• 습관근육 형성</p>
                                <p>• 부자아빠 가난한아빠 1</p>
                                <p>• 경제신문 방법</p>
                                <p>• Cashflow 보드게임 설명</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Cashflow 보드게임 -->
                    <div class="program-step bg-gray-50 border-2 border-gray-200 rounded-2xl p-6 transition-all duration-300 hover:shadow-lg" data-step="3">
                        <div class="step-header flex items-center justify-between mb-4 cursor-pointer md:cursor-default" onclick="toggleStep(3)">
                            <div class="flex items-center gap-3">
                                <input type="checkbox"
                                       class="step-checkbox w-5 h-5 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer"
                                       onchange="handleCheckboxChange(3)"
                                       onclick="event.stopPropagation()">
                                <h3 class="text-lg font-bold text-gray-800">3단계: Cashflow 보드게임</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="step-status px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">진행중</span>
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
                    </div>

                    <!-- Step 4: MQ뿌리다지기 -->
                    <div class="program-step bg-gray-50 border-2 border-gray-200 rounded-2xl p-6 transition-all duration-300 hover:shadow-lg" data-step="4">
                        <div class="step-header flex items-center justify-between mb-4 cursor-pointer md:cursor-default" onclick="toggleStep(4)">
                            <div class="flex items-center gap-3">
                                <input type="checkbox"
                                       class="step-checkbox w-5 h-5 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer"
                                       onchange="handleCheckboxChange(4)"
                                       onclick="event.stopPropagation()">
                                <h3 class="text-lg font-bold text-gray-800">4단계: MQ뿌리다지기</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="step-status px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">진행중</span>
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
                    </div>
                </div>
            </div>
    </div>

</div>

@push('scripts')
<script>
const COURSE_CODE = 'l1';
const TOTAL_STEPS = 4;

// 페이지 로드 시 DB에서 진행 상태 불러오기
async function loadProgressFromDB() {
    try {
        const response = await fetch(`/course/progress/${COURSE_CODE}`);
        const data = await response.json();

        if (data.success && data.progress) {
            // 각 단계별로 체크박스 상태 반영
            Object.values(data.progress).forEach(step => {
                updateStepUI(step.step_number, step.is_completed);
            });

            // 진행률 업데이트
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
            // UI 업데이트
            updateStepUI(stepNumber, data.is_completed);
            updateProgress();
            updateTreeImage();
        } else {
            // 실패 시 체크박스 원래대로
            checkbox.checked = !isChecked;
            if (data.message) {
                alert(data.message);
            }
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
    const stepContent = stepElement.querySelector('.step-content');
    const toggleIcon = stepElement.querySelector('.toggle-icon');

    // 체크박스 상태 설정
    if (checkbox) {
        checkbox.checked = isCompleted;
    }

    // 완료 상태에 따라 스타일 변경
    if (isCompleted) {
        // 완료 상태
        stepElement.classList.remove('bg-gray-50', 'border-gray-200');
        stepElement.classList.add('bg-green-50', 'border-green-200');

        if (statusElement) {
            statusElement.textContent = '완료';
            statusElement.className = 'step-status px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full';
        }

        if (toggleIcon) {
            toggleIcon.classList.remove('text-gray-700');
            toggleIcon.classList.add('text-green-700');
        }

        // 텍스트 색상 변경
        const contentDiv = stepContent?.querySelector('div');
        if (contentDiv) {
            contentDiv.classList.remove('text-gray-600');
            contentDiv.classList.add('text-green-600');
        }

        const h3 = stepElement.querySelector('h3');
        if (h3) {
            h3.classList.remove('text-gray-800');
            h3.classList.add('text-green-800');
        }
    } else {
        // 진행중 상태
        stepElement.classList.remove('bg-green-50', 'border-green-200');
        stepElement.classList.add('bg-gray-50', 'border-gray-200');

        if (statusElement) {
            statusElement.textContent = '진행중';
            statusElement.className = 'step-status px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full';
        }

        if (toggleIcon) {
            toggleIcon.classList.remove('text-green-700');
            toggleIcon.classList.add('text-gray-700');
        }

        // 텍스트 색상 변경
        const contentDiv = stepContent?.querySelector('div');
        if (contentDiv) {
            contentDiv.classList.remove('text-green-600');
            contentDiv.classList.add('text-gray-600');
        }

        const h3 = stepElement.querySelector('h3');
        if (h3) {
            h3.classList.remove('text-green-800');
            h3.classList.add('text-gray-800');
        }
    }
}

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
    const toggleIcon = stepElement.querySelector('.toggle-icon');

    if (!stepContent) return;

    stepContent.style.overflow = 'hidden';

    const isExpanded = stepContent.style.maxHeight && stepContent.style.maxHeight !== '0px' && stepContent.style.maxHeight !== 'none';

    if (isExpanded) {
        stepContent.style.maxHeight = '0px';
        if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
    } else {
        const minHeight = Math.max(stepContent.scrollHeight, 160);
        stepContent.style.maxHeight = minHeight + 'px';
        if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
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
    }
}

// 모든 단계의 초기 상태를 설정하는 함수
function initializeStepStates() {
    const allSteps = document.querySelectorAll('.program-step');
    allSteps.forEach(function(stepElement) {
        const stepContent = stepElement.querySelector('.step-content');
        const toggleIcon = stepElement.querySelector('.toggle-icon');
        const checkbox = stepElement.querySelector('.step-checkbox');
        const isCompleted = checkbox?.checked || false;

        if (stepContent) {
            if (isMobile()) {
                stepContent.style.overflow = 'hidden';

                if (isCompleted) {
                    // 완료된 단계: 모바일에서 닫힌 상태
                    stepContent.style.maxHeight = '0px';
                    if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
                } else {
                    // 미완료 단계: 모바일에서 열린 상태
                    const minHeight = Math.max(stepContent.scrollHeight, 160);
                    stepContent.style.maxHeight = minHeight + 'px';
                    if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
                }
            } else {
                stepContent.style.overflow = '';
                stepContent.style.maxHeight = '';
                stepContent.style.height = '';
                if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
            }
        }
    });
}

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', function() {
    // DB에서 진행 상태 로드
    loadProgressFromDB();

    // 초기 상태 설정
    initializeStepStates();
});

// 화면 크기 변경 시 상태 재조정
window.addEventListener('resize', function() {
    initializeStepStates();
});
</script>
@endpush
@endsection
