@extends('layouts.app')

@section('content')
<!-- ===== Hero Section ===== -->
<section class="relative pt-32 pb-24 overflow-hidden bg-[#3D4148]">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#FF4D4D] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10 text-center animate-slideUp">
        <a href="{{ route('mypage.index') }}" class="inline-flex items-center text-gray-400 hover:text-white mb-6 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center mr-2 group-hover:bg-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            마이페이지로 돌아가기
        </a>
        <br />
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-sm font-medium mb-4 backdrop-blur-md">
            🗺️ MQ Mapping
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight tracking-tight">
            MQ 매핑
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            나의 현재와 미래를 연결하는 꿈의 지도를 만들어보세요.<br class="hidden md:block">
            원하는 미래의 모습이나 목표를 선택하여 나만의 매핑을 완성해보세요.
        </p>
    </div>
</section>

<div class="container mx-auto px-4 -mt-10 relative z-20 max-w-6xl">
    <!-- 선택된 아이템 카운터 -->
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-8 animate-slideUp" style="animation-delay: 0.2s;">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4ECDC4] to-[#2AA9A0] flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-sm text-gray-400 font-medium">선택된 목표:</span>
                    <span id="selected-count" class="text-xl font-bold text-[#2D3047] ml-2">0</span>
                    <span class="text-sm text-gray-400 font-medium">개</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button id="add-custom-goal-btn" class="inline-flex items-center gap-1.5 text-sm bg-white border border-[#FF4D4D] text-[#FF4D4D] px-4 py-2.5 rounded-xl hover:bg-[#FF4D4D] hover:text-white transition-all font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>직접 추가</span>
                </button>
            </div>
        </div>

        <!-- 선택된 목표 리스트 -->
        <div id="selected-goals-list" class="hidden border-t border-gray-100 pt-4 mt-4">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-sm font-bold text-[#2D3047]">선택된 목표 목록</h3>
            </div>
            <div id="selected-goals-container" class="space-y-2 max-h-[240px] overflow-y-auto custom-scrollbar">
                <!-- 동적으로 추가될 목표 항목들 -->
            </div>
        </div>
    </div>

    <!-- 카테고리 필터 -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 animate-slideUp" style="animation-delay: 0.3s;">
        <div class="flex flex-wrap gap-2">
            @foreach($categories as $key => $label)
            <button class="category-filter {{ $key === 'all' ? 'active bg-[#FF4D4D] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} px-4 py-2 rounded-full text-sm font-medium transition-all" data-category="{{ $key }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- 매핑 그리드 -->
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 pb-20 animate-slideUp" style="animation-delay: 0.4s;">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5" id="mapping-grid">
            @foreach($mappingItems as $item)
            <div class="mapping-item relative group cursor-pointer" data-category="{{ $item['category'] }}" data-id="{{ $item['id'] }}" data-description="{{ $item['description'] }}">
                <div class="aspect-square rounded-xl overflow-hidden bg-white shadow-sm border border-gray-100 relative">
                    @if($item['image'])
                        <!-- 이미지 (전체 영역) -->
                        <img src="{{ $item['image'] }}" alt="{{ $item['description'] }}"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    @else
                        <!-- 노이미지 썸네일 -->
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif

                    <!-- 하단 그라데이션 오버레이 (텍스트 가독성을 위한) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

                    <!-- 텍스트 오버레이 -->
                    <div class="absolute bottom-0 left-0 right-0 p-3 text-center">
                        <div class="text-sm font-medium text-white leading-tight drop-shadow-lg">{{ $item['description'] }}</div>
                    </div>
                </div>
                <input type="checkbox" class="mapping-checkbox absolute top-2 right-2 w-5 h-5 text-[#FF4D4D] bg-white border-2 border-gray-300 rounded focus:ring-[#FF4D4D] shadow-sm">
                <div class="absolute inset-0 bg-[#4ECDC4] bg-opacity-20 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl"></div>
            </div>
            @endforeach
        </div>

        <!-- 무한 스크롤 로딩 인디케이터 -->
        <div id="scroll-loading" class="hidden text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#4ECDC4]"></div>
            <p class="text-sm text-gray-400 mt-2">로딩 중...</p>
        </div>

        <!-- 더 이상 로드할 항목이 없을 때 메시지 -->
        <div id="scroll-end" class="hidden text-center py-8">
            <p class="text-sm text-gray-400">모든 항목을 불러왔습니다.</p>
        </div>
    </div>
</div>

<!-- 샘플 목표 확인 팝업 -->
<div id="confirm-goal-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-[#2D3047]">목표 추가</h2>
            <button id="close-confirm-modal-btn" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="space-y-5">
            <!-- 선택된 목표 미리보기 -->
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                <div id="confirm-goal-image-container" class="w-20 h-20 rounded-xl flex-shrink-0 overflow-hidden"></div>
                <div class="flex-1">
                    <div id="confirm-goal-description-container">
                        <p id="confirm-goal-description" class="text-sm font-medium text-[#2D3047]"></p>
                    </div>
                </div>
            </div>

            <!-- 목표 연도 선택 -->
            <div>
                <label for="confirm-goal-year" class="block text-sm font-semibold text-[#2D3047] mb-2">목표 연도</label>
                <select id="confirm-goal-year" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent transition-all">
                    <!-- JavaScript로 동적 생성 -->
                </select>
            </div>

            <!-- 버튼 -->
            <div class="flex gap-3 pt-2">
                <button type="button" id="cancel-confirm-modal-btn" class="flex-1 px-4 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-all font-medium">
                    취소
                </button>
                <button type="button" id="save-confirm-goal-btn" class="flex-1 px-4 py-3 bg-gradient-to-r from-[#4ECDC4] to-[#2AA9A0] text-white rounded-xl hover:shadow-lg transition-all font-medium">
                    저장하기
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 커스텀 목표 추가/편집 팝업 -->
<div id="custom-goal-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <h2 id="custom-modal-title" class="text-xl font-bold text-[#2D3047]">나만의 목표 추가</h2>
                <!-- 삭제 버튼 (편집 모드일 때만 표시) -->
                <button type="button" id="delete-custom-goal-from-modal-btn" class="hidden text-red-500 hover:text-red-600 transition-colors" title="삭제하기">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            <button id="close-modal-btn" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="custom-goal-form" class="space-y-5">
            <!-- 이미지 업로드 (편집 시에는 숨김) -->
            <div id="custom-image-upload-section">
                <label class="block text-sm font-semibold text-[#2D3047] mb-2">목표 이미지</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-[#4ECDC4] transition-colors cursor-pointer" id="image-upload-area">
                    <input type="file" id="custom-goal-image" accept="image/*" class="hidden">
                    <div id="image-preview-area">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">클릭하여 이미지를 업로드하세요</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF (최대 5MB)</p>
                    </div>
                </div>
            </div>

            <!-- 기존 이미지 미리보기 (편집 시에만 표시) -->
            <div id="custom-image-preview-section" class="hidden">
                <label class="block text-sm font-semibold text-[#2D3047] mb-2">현재 이미지</label>
                <div id="custom-existing-image-container" class="flex justify-center items-center p-4 bg-gray-50 rounded-xl">
                    <!-- 이미지가 동적으로 추가됨 -->
                </div>
            </div>

            <!-- 목표 설명 -->
            <div>
                <label for="custom-goal-description" class="block text-sm font-semibold text-[#2D3047] mb-2">목표 설명</label>
                <input type="text" id="custom-goal-description" placeholder="예: 세계 여행, 자격증 취득, 창업 등" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent transition-all" required>
            </div>

            <!-- 목표 연도 -->
            <div>
                <label for="custom-goal-year" class="block text-sm font-semibold text-[#2D3047] mb-2">목표 연도</label>
                <select id="custom-goal-year" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent transition-all">
                    <!-- JavaScript로 동적 생성 -->
                </select>
            </div>

            <!-- 버튼 -->
            <div class="flex gap-3 pt-2">
                <button type="button" id="cancel-modal-btn" class="flex-1 px-4 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-all font-medium">
                    취소
                </button>
                <button type="submit" id="custom-goal-submit-btn" class="flex-1 px-4 py-3 bg-gradient-to-r from-[#FF4D4D] to-[#e03e3e] text-white rounded-xl hover:shadow-lg transition-all font-medium">
                    추가하기
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilters = document.querySelectorAll('.category-filter');
    const mappingGrid = document.getElementById('mapping-grid');
    const selectedCountEl = document.getElementById('selected-count');
    const selectedGoalsList = document.getElementById('selected-goals-list');
    const selectedGoalsContainer = document.getElementById('selected-goals-container');
    const scrollLoading = document.getElementById('scroll-loading');
    const scrollEnd = document.getElementById('scroll-end');

    let selectedItems = new Map(); // {id: {description, targetYear}}
    const currentYear = new Date().getFullYear();
    let customGoalCounter = 0;
    let customGoalImageData = null;

    // 서버에서 받은 선택된 아이템 초기화
    @if(isset($selectedItems) && count($selectedItems) > 0)
        const initialSelectedItems = @json($selectedItems);
        Object.entries(initialSelectedItems).forEach(([id, data]) => {
            selectedItems.set(id, {
                description: data.description,
                targetYear: data.targetYear,
                imageSrc: data.imageSrc,
                category: data.category
            });
        });
    @endif

    // 무한 스크롤 변수
    let currentOffset = 12; // 초기 12개 로드됨
    let isLoading = false;
    let hasMoreItems = true;
    let currentCategory = 'all';

    // 샘플 목표 확인 팝업 모달 관련
    const confirmGoalModal = document.getElementById('confirm-goal-modal');
    const closeConfirmModalBtn = document.getElementById('close-confirm-modal-btn');
    const cancelConfirmModalBtn = document.getElementById('cancel-confirm-modal-btn');
    const saveConfirmGoalBtn = document.getElementById('save-confirm-goal-btn');
    const confirmGoalImageContainer = document.getElementById('confirm-goal-image-container');
    const confirmGoalDescriptionContainer = document.getElementById('confirm-goal-description-container');
    const confirmGoalDescription = document.getElementById('confirm-goal-description');
    const confirmGoalYear = document.getElementById('confirm-goal-year');
    let pendingGoalData = null; // 팝업에서 확인 대기 중인 목표 데이터

    // 커스텀 목표 추가/편집 모달 관련
    const addCustomGoalBtn = document.getElementById('add-custom-goal-btn');
    const customGoalModal = document.getElementById('custom-goal-modal');
    const customModalTitle = document.getElementById('custom-modal-title');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const cancelModalBtn = document.getElementById('cancel-modal-btn');
    const customGoalForm = document.getElementById('custom-goal-form');
    const customImageUploadSection = document.getElementById('custom-image-upload-section');
    const customImagePreviewSection = document.getElementById('custom-image-preview-section');
    const customExistingImageContainer = document.getElementById('custom-existing-image-container');
    const imageUploadArea = document.getElementById('image-upload-area');
    const customGoalImageInput = document.getElementById('custom-goal-image');
    const imagePreviewArea = document.getElementById('image-preview-area');
    const customGoalDescription = document.getElementById('custom-goal-description');
    const customGoalYear = document.getElementById('custom-goal-year');
    const customGoalSubmitBtn = document.getElementById('custom-goal-submit-btn');
    const deleteCustomGoalFromModalBtn = document.getElementById('delete-custom-goal-from-modal-btn');
    const customGoalDeleteContainer = deleteCustomGoalFromModalBtn.parentElement; // 삭제 버튼 컨테이너
    let customModalMode = 'add'; // 'add' 또는 'edit'
    let customModalEditingId = null; // 편집 중인 목표 ID

    // 커스텀 목표 연도 옵션 생성
    function populateYearOptions(selectElement, defaultYear = currentYear) {
        selectElement.innerHTML = '';
        for (let i = 0; i <= 30; i++) {
            const year = currentYear + i;
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year + '년';
            if (year === defaultYear) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        }
    }

    // 페이지 로드 시 연도 옵션 생성
    populateYearOptions(customGoalYear);
    populateYearOptions(confirmGoalYear);

    // 무한 스크롤 로딩 함수
    function loadMoreItems() {
        if (isLoading || !hasMoreItems) return;

        isLoading = true;
        scrollLoading.classList.remove('hidden');
        scrollEnd.classList.add('hidden');

        fetch(`{{ route('mypage.mapping.items') }}?offset=${currentOffset}&limit=6&category=${currentCategory}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.items.length > 0) {
                    data.items.forEach(item => {
                        const itemHtml = createMappingItemHtml(item);
                        mappingGrid.insertAdjacentHTML('beforeend', itemHtml);
                    });

                    // 새로 추가된 아이템들에 이벤트 리스너 등록
                    registerNewItemsEvents();

                    // 선택된 아이템 체크 상태 복원
                    restoreSelectedStates();

                    currentOffset += data.items.length;
                    hasMoreItems = data.hasMore;

                    if (!hasMoreItems) {
                        scrollEnd.classList.remove('hidden');
                    }
                } else {
                    hasMoreItems = false;
                    scrollEnd.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading items:', error);
                alert('아이템을 불러오는 중 오류가 발생했습니다.');
            })
            .finally(() => {
                isLoading = false;
                scrollLoading.classList.add('hidden');
            });
    }

    // 매핑 아이템 HTML 생성 함수
    function createMappingItemHtml(item) {
        const imageHtml = item.image
            ? `<img src="${item.image}" alt="${item.description}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">`
            : `<div class="w-full h-full bg-gray-200 flex items-center justify-center">
                   <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                   </svg>
               </div>`;

        return `
            <div class="mapping-item relative group cursor-pointer" data-category="${item.category}" data-id="${item.id}" data-description="${item.description}">
                <div class="aspect-square rounded-lg overflow-hidden bg-white shadow-sm border border-gray-100 relative">
                    ${imageHtml}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 text-center">
                        <div class="text-sm font-medium text-white leading-tight drop-shadow-lg">${item.description}</div>
                    </div>
                </div>
                <input type="checkbox" class="mapping-checkbox absolute top-2 right-2 w-5 h-5 text-point1 bg-white border-2 border-gray-300 rounded focus:ring-point1 shadow-sm">
                <div class="absolute inset-0 bg-point1 bg-opacity-20 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg"></div>
            </div>
        `;
    }

    // 새로 추가된 아이템들에 이벤트 리스너 등록
    function registerNewItemsEvents() {
        const allMappingItems = document.querySelectorAll('.mapping-item');
        const allCheckboxes = document.querySelectorAll('.mapping-checkbox');

        allCheckboxes.forEach(checkbox => {
            if (!checkbox.dataset.listenerAdded) {
                checkbox.dataset.listenerAdded = 'true';
                checkbox.addEventListener('change', handleCheckboxChange);
            }
        });

        allMappingItems.forEach(item => {
            if (!item.dataset.listenerAdded) {
                item.dataset.listenerAdded = 'true';
                item.addEventListener('click', handleItemClick);
            }
        });
    }

    // 선택된 아이템 체크 상태 복원
    function restoreSelectedStates() {
        selectedItems.forEach((data, itemId) => {
            const mappingItem = document.querySelector(`.mapping-item[data-id="${itemId}"]`);
            if (mappingItem) {
                const checkbox = mappingItem.querySelector('.mapping-checkbox');
                if (checkbox) {
                    checkbox.checked = true;
                    mappingItem.classList.add('selected');
                }
            }
        });
    }

    // 체크박스 변경 핸들러
    function handleCheckboxChange(e) {
        e.stopPropagation();
        const checkbox = e.target;
        const item = checkbox.closest('.mapping-item');
        const itemId = item.getAttribute('data-id');
        const description = item.getAttribute('data-description');
        const category = item.getAttribute('data-category');
        const imgElement = item.querySelector('img');
        const imageSrc = imgElement ? imgElement.src : null;

        // 카테고리가 custom이거나, selectedItems에 저장된 카테고리가 custom인 경우
        let isCustomGoal = category === 'custom';
        if (!isCustomGoal) {
            const selectedItem = selectedItems.get(itemId);
            isCustomGoal = selectedItem && selectedItem.category === 'custom';
        }

        if (checkbox.checked) {
            // 체크 시 팝업 표시 (추가 모드)
            showConfirmModal(itemId, description, imageSrc, checkbox, item, isCustomGoal, 'add', category);
        } else {
            // 체크 해제 시 처리
            if (isCustomGoal) {
                // 커스텀 목표는 커스텀 모달로 편집 (selectedItems에서 정보 가져오기)
                checkbox.checked = true; // 일단 다시 체크
                const selectedItem = selectedItems.get(itemId);
                openCustomModal('edit', {
                    id: itemId,
                    description: selectedItem ? selectedItem.description : description,
                    targetYear: selectedItem ? selectedItem.targetYear : currentYear,
                    imageSrc: selectedItem ? selectedItem.imageSrc : imageSrc
                });
            } else {
                // 기본 샘플 목표는 confirm 메시지 (복구 불가 멘트 제거)
                if (confirm(`"${description}" 목표를 삭제하시겠습니까?`)) {
                    selectedItems.delete(itemId);
                    item.classList.remove('selected');
                    removeGoalFromList(itemId);
                    saveSingleGoal(itemId, description, null, 'remove');
                    updateSelectedCount();
                } else {
                    // 취소 시 체크박스 다시 체크
                    checkbox.checked = true;
                }
            }
        }
    }

    // 확인 팝업 표시 함수
    function showConfirmModal(itemId, description, imageSrc, checkbox, item, isCustomGoal, mode, category = null) {
        pendingGoalData = {
            itemId: itemId,
            description: description,
            imageSrc: imageSrc,
            checkbox: checkbox,
            item: item,
            isCustomGoal: isCustomGoal,
            mode: mode, // 'add' 또는 'edit'
            category: category || 'custom'
        };

        // 이미지 처리 (노이미지 대응)
        if (imageSrc) {
            confirmGoalImageContainer.innerHTML = `<img src="${imageSrc}" alt="${description}" class="w-20 h-20 rounded object-cover">`;
        } else {
            confirmGoalImageContainer.innerHTML = `
                <div class="w-20 h-20 rounded bg-gray-200 flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            `;
        }

        // 커스텀 목표인 경우 텍스트 수정 가능하게 input으로 변경
        if (isCustomGoal) {
            confirmGoalDescriptionContainer.innerHTML = `
                <input type="text" id="confirm-goal-description-input" value="${description}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 text-sm font-medium text-secondary"
                       placeholder="목표 설명을 입력하세요">
            `;
            // 삭제 버튼 표시
            customGoalDeleteContainer.classList.remove('hidden');
        } else {
            confirmGoalDescriptionContainer.innerHTML = `<p id="confirm-goal-description" class="text-sm font-medium text-secondary">${description}</p>`;
            // 삭제 버튼 숨김
            customGoalDeleteContainer.classList.add('hidden');
        }

        // 목표 연도 설정 (기존 값이 있으면 사용)
        const existingItem = selectedItems.get(itemId);
        populateYearOptions(confirmGoalYear, existingItem ? existingItem.targetYear : currentYear);

        confirmGoalModal.classList.remove('hidden');
        confirmGoalModal.classList.add('flex');
    }

    // 단일 목표 저장/삭제 함수
    function saveSingleGoal(itemId, description, targetYear, action, imageData = null, callback = null) {
        const payload = {
            id: itemId,
            description: description,
            targetYear: targetYear,
            action: action
        };

        // 커스텀 목표인 경우 이미지 데이터 포함
        if (itemId.startsWith('custom-')) {
            const item = selectedItems.get(itemId);
            if (item && item.imageSrc) {
                payload.imageData = item.imageSrc;
            }
        }

        fetch('{{ route("mypage.mapping.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('저장 중 오류가 발생했습니다.');
            } else if (callback) {
                callback(data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('저장 중 오류가 발생했습니다.');
        });
    }

    // 아이템 클릭 핸들러
    function handleItemClick(e) {
        if (!e.target.classList.contains('mapping-checkbox') &&
            !e.target.classList.contains('target-year-select')) {
            const item = e.currentTarget;
            const itemId = item.getAttribute('data-id');
            const category = item.getAttribute('data-category');

            // selectedItems에서 카테고리 확인
            const selectedItem = selectedItems.get(itemId);
            const isCustomGoal = category === 'custom' || (selectedItem && selectedItem.category === 'custom');

            // 커스텀 목표이면서 이미 선택된 경우 수정 모달 열기
            if (isCustomGoal && selectedItem) {
                const checkbox = item.querySelector('.mapping-checkbox');
                if (checkbox && checkbox.checked) {
                    openCustomModal('edit', {
                        id: itemId,
                        description: selectedItem.description,
                        targetYear: selectedItem.targetYear,
                        imageSrc: selectedItem.imageSrc
                    });
                    return;
                }
            }

            // 그 외의 경우 체크박스 토글
            const checkbox = item.querySelector('.mapping-checkbox');
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change'));
        }
    }

    // 무한 스크롤 이벤트 리스너
    window.addEventListener('scroll', () => {
        if (isLoading || !hasMoreItems) return;

        const scrollHeight = document.documentElement.scrollHeight;
        const scrollTop = document.documentElement.scrollTop;
        const clientHeight = document.documentElement.clientHeight;

        // 스크롤이 페이지 하단 200px 전에 도달하면 로딩
        if (scrollTop + clientHeight >= scrollHeight - 200) {
            loadMoreItems();
        }
    });

    // 카테고리 필터 기능
    categoryFilters.forEach(filter => {
        filter.addEventListener('click', () => {
            const category = filter.getAttribute('data-category');
            currentCategory = category;

            // 버튼 활성화 상태 변경
            categoryFilters.forEach(btn => {
                btn.classList.remove('active', 'bg-point1', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-600');
            });
            filter.classList.add('active', 'bg-point1', 'text-white');
            filter.classList.remove('bg-gray-100', 'text-gray-600');

            // 카테고리 변경 시 그리드 초기화 및 재로딩
            currentOffset = 0;
            hasMoreItems = true;
            mappingGrid.innerHTML = '';
            scrollEnd.classList.add('hidden');

            loadMoreItems();
        });
    });

    // 초기 로드된 아이템들에 이벤트 리스너 등록
    registerNewItemsEvents();

    // 선택된 아이템 초기화 (체크박스 체크 및 목록 추가)
    if (selectedItems.size > 0) {
        selectedItems.forEach((data, itemId) => {
            const mappingItem = document.querySelector(`.mapping-item[data-id="${itemId}"]`);
            if (mappingItem) {
                // 그리드에 있는 아이템인 경우 체크박스 체크
                const checkbox = mappingItem.querySelector('.mapping-checkbox');
                if (checkbox) {
                    checkbox.checked = true;
                    mappingItem.classList.add('selected');
                }
            }
            // 모든 선택된 아이템을 목록에 추가 (커스텀 포함)
            addGoalToList(itemId, data.description, data.imageSrc);
        });
        updateSelectedCount();
    }

    function addGoalToList(itemId, description, imageSrc = null) {
        const goalItem = document.createElement('div');
        goalItem.id = `goal-item-${itemId}`;
        goalItem.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg';

        // 저장된 목표 연도 가져오기
        const item = selectedItems.get(itemId);
        const savedYear = item ? item.targetYear : currentYear;

        // 연도 선택 옵션 생성 (저장된 연도가 없으면 현재 연도가 기본값)
        const defaultYear = savedYear || currentYear;
        let yearOptions = '';
        for (let i = 0; i <= 30; i++) {
            const year = currentYear + i;
            const selected = year === defaultYear ? 'selected' : '';
            yearOptions += `<option value="${year}" ${selected}>${year}년</option>`;
        }

        // 이미지 HTML 생성
        let imageHTML = '';
        if (imageSrc) {
            imageHTML = `<img src="${imageSrc}" alt="${description}" class="w-10 h-10 rounded object-cover">`;
        } else {
            imageHTML = `
                <div class="w-10 h-10 rounded bg-gray-300 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            `;
        }

        goalItem.innerHTML = `
            <input type="checkbox"
                   class="goal-checkbox w-4 h-4 text-point1 border-gray-300 rounded focus:ring-point1 flex-shrink-0"
                   data-id="${itemId}"
                   data-year="${defaultYear}"
                   checked>
            ${imageHTML}
            <span class="flex-1 text-sm text-secondary">${description}</span>
            <select class="target-year-select text-sm border border-gray-300 rounded px-2 py-1 focus:ring-point1 focus:border-point1 flex-shrink-0"
                    data-id="${itemId}">
                ${yearOptions}
            </select>
        `;

        // 목표 연도 기준 오름차순으로 정렬하여 삽입
        const existingGoals = Array.from(selectedGoalsContainer.children);
        let inserted = false;

        for (let i = 0; i < existingGoals.length; i++) {
            const existingCheckbox = existingGoals[i].querySelector('.goal-checkbox');
            const existingYear = parseInt(existingCheckbox.getAttribute('data-year') || currentYear);

            if (defaultYear < existingYear) {
                selectedGoalsContainer.insertBefore(goalItem, existingGoals[i]);
                inserted = true;
                break;
            }
        }

        if (!inserted) {
            selectedGoalsContainer.appendChild(goalItem);
        }

        // 목표 리스트 표시
        selectedGoalsList.classList.remove('hidden');

        // 연도 변경 이벤트
        const yearSelect = goalItem.querySelector('.target-year-select');
        yearSelect.addEventListener('change', function(e) {
            e.stopPropagation();
            const id = this.getAttribute('data-id');
            const newYear = parseInt(this.value);
            const item = selectedItems.get(id);
            if (item) {
                item.targetYear = newYear;
                // AJAX로 목표 연도 업데이트
                saveSingleGoal(id, item.description, newYear, 'update');

                // 연도 변경 시 정렬 위치 재조정
                const currentGoalItem = document.getElementById(`goal-item-${id}`);
                const checkbox = currentGoalItem.querySelector('.goal-checkbox');
                checkbox.setAttribute('data-year', newYear);

                // 현재 항목을 제거하고 정렬된 위치에 다시 삽입
                currentGoalItem.remove();
                const allGoals = Array.from(selectedGoalsContainer.children);
                let reinserted = false;

                for (let i = 0; i < allGoals.length; i++) {
                    const existingCheckbox = allGoals[i].querySelector('.goal-checkbox');
                    const existingYear = parseInt(existingCheckbox.getAttribute('data-year') || currentYear);

                    if (newYear < existingYear) {
                        selectedGoalsContainer.insertBefore(currentGoalItem, allGoals[i]);
                        reinserted = true;
                        break;
                    }
                }

                if (!reinserted) {
                    selectedGoalsContainer.appendChild(currentGoalItem);
                }
            }
        });

        // 체크박스 해제 이벤트
        const goalCheckbox = goalItem.querySelector('.goal-checkbox');
        goalCheckbox.addEventListener('change', function(e) {
            e.stopPropagation();
            const id = this.getAttribute('data-id');
            if (!this.checked) {
                const item = selectedItems.get(id);
                const description = item ? item.description : '이 목표';
                const isCustomGoal = item && item.category === 'custom';

                if (isCustomGoal) {
                    // 커스텀 목표는 커스텀 모달로 편집 (상단 목록에서 선택된 정보 활용)
                    this.checked = true; // 일단 다시 체크
                    openCustomModal('edit', {
                        id: id,
                        description: description,
                        targetYear: item.targetYear,
                        imageSrc: item.imageSrc
                    });
                } else {
                    // 기본 샘플 목표는 confirm 메시지 (복구 불가 멘트 제거)
                    if (confirm(`"${description}" 목표를 삭제하시겠습니까?`)) {
                        // 그리드의 체크박스도 해제
                        const gridItem = document.querySelector(`.mapping-item[data-id="${id}"]`);
                        if (gridItem) {
                            const gridCheckbox = gridItem.querySelector('.mapping-checkbox');
                            gridCheckbox.checked = false;
                            gridItem.classList.remove('selected');
                        }
                        selectedItems.delete(id);
                        removeGoalFromList(id);
                        saveSingleGoal(id, description, null, 'remove');
                        updateSelectedCount();
                    } else {
                        // 취소 시 체크박스 다시 체크
                        this.checked = true;
                    }
                }
            }
        });
    }

    function removeGoalFromList(itemId) {
        const goalItem = document.getElementById(`goal-item-${itemId}`);
        if (goalItem) {
            goalItem.remove();
        }

        // 선택된 목표가 없으면 리스트 숨김
        if (selectedItems.size === 0) {
            selectedGoalsList.classList.add('hidden');
        }
    }

    function updateSelectedCount() {
        selectedCountEl.textContent = selectedItems.size;
    }

    // 커스텀 모달 열기 (추가 모드)
    addCustomGoalBtn.addEventListener('click', () => {
        openCustomModal('add');
    });

    // 커스텀 모달 열기 함수
    function openCustomModal(mode, goalData = null) {
        customModalMode = mode;

        if (mode === 'add') {
            // 추가 모드
            customModalTitle.textContent = '나만의 목표 추가';
            customGoalSubmitBtn.textContent = '추가하기';
            customImageUploadSection.classList.remove('hidden');
            customImagePreviewSection.classList.add('hidden');
            deleteCustomGoalFromModalBtn.classList.add('hidden');
            customGoalDescription.value = '';
            populateYearOptions(customGoalYear);
            customModalEditingId = null;
        } else if (mode === 'edit' && goalData) {
            // 편집 모드
            customModalTitle.textContent = '목표 수정';
            customGoalSubmitBtn.textContent = '수정하기';
            customImageUploadSection.classList.add('hidden');
            customImagePreviewSection.classList.remove('hidden');
            deleteCustomGoalFromModalBtn.classList.remove('hidden');
            customGoalDescription.value = goalData.description;
            populateYearOptions(customGoalYear, goalData.targetYear);
            customModalEditingId = goalData.id;

            // 기존 이미지 표시
            if (goalData.imageSrc) {
                customExistingImageContainer.innerHTML = `<img src="${goalData.imageSrc}" alt="${goalData.description}" class="max-h-40 rounded">`;
            } else {
                customExistingImageContainer.innerHTML = `
                    <div class="w-20 h-20 rounded bg-gray-200 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                `;
            }
        }

        customGoalModal.classList.remove('hidden');
        customGoalModal.classList.add('flex');
    }

    // 모달 닫기
    function closeCustomModal() {
        customGoalModal.classList.add('hidden');
        customGoalModal.classList.remove('flex');
        customGoalForm.reset();
        customGoalImageData = null;
        customModalEditingId = null;
        imagePreviewArea.innerHTML = `
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-sm text-gray-500">클릭하여 이미지를 업로드하세요</p>
            <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF (최대 5MB)</p>
        `;
    }

    closeModalBtn.addEventListener('click', closeCustomModal);
    cancelModalBtn.addEventListener('click', closeCustomModal);

    // 확인 팝업 모달 닫기
    function closeConfirmModal() {
        if (pendingGoalData) {
            // 팝업 취소 시 체크박스 해제
            pendingGoalData.checkbox.checked = false;
            pendingGoalData.item.classList.remove('selected');
            pendingGoalData = null;
        }
        confirmGoalModal.classList.add('hidden');
        confirmGoalModal.classList.remove('flex');
    }

    closeConfirmModalBtn.addEventListener('click', closeConfirmModal);
    cancelConfirmModalBtn.addEventListener('click', closeConfirmModal);

    // 확인 팝업 배경 클릭 시 닫기
    confirmGoalModal.addEventListener('click', (e) => {
        if (e.target === confirmGoalModal) {
            closeConfirmModal();
        }
    });

    // 확인 팝업에서 저장 버튼 클릭
    saveConfirmGoalBtn.addEventListener('click', () => {
        if (!pendingGoalData) return;

        const selectedYear = parseInt(confirmGoalYear.value);
        const { itemId, imageSrc, item, isCustomGoal, mode, category } = pendingGoalData;

        // 커스텀 목표인 경우 수정된 텍스트 가져오기
        let description = pendingGoalData.description;
        if (isCustomGoal) {
            const descriptionInput = document.getElementById('confirm-goal-description-input');
            if (descriptionInput) {
                description = descriptionInput.value.trim();
                if (!description) {
                    alert('목표 설명을 입력해주세요.');
                    return;
                }
                // 그리드 아이템의 description도 업데이트
                item.setAttribute('data-description', description);
            }
        }

        // selectedItems에 추가/업데이트
        selectedItems.set(itemId, {
            description: description,
            targetYear: selectedYear,
            imageSrc: imageSrc,
            category: category || 'custom'
        });

        item.classList.add('selected');

        // edit 모드인 경우 목록의 아이템 업데이트
        if (mode === 'edit') {
            const goalItem = document.getElementById(`goal-item-${itemId}`);
            if (goalItem) {
                // 기존 아이템의 텍스트와 연도만 업데이트
                const descSpan = goalItem.querySelector('.flex-1.text-sm');
                if (descSpan) {
                    descSpan.textContent = description;
                }
                const yearSelect = goalItem.querySelector('.target-year-select');
                if (yearSelect) {
                    yearSelect.value = selectedYear;
                }
            }
            // AJAX로 업데이트
            saveSingleGoal(itemId, description, selectedYear, 'update');
        } else {
            // add 모드인 경우 목록에 추가
            addGoalToList(itemId, description, imageSrc);
            // AJAX로 저장
            saveSingleGoal(itemId, description, selectedYear, 'add');
        }

        updateSelectedCount();

        // 팝업 닫기
        pendingGoalData = null;
        confirmGoalModal.classList.add('hidden');
        confirmGoalModal.classList.remove('flex');
    });

    // 커스텀 모달 배경 클릭 시 닫기
    customGoalModal.addEventListener('click', (e) => {
        if (e.target === customGoalModal) {
            closeCustomModal();
        }
    });

    // 이미지 업로드 영역 클릭
    imageUploadArea.addEventListener('click', () => {
        customGoalImageInput.click();
    });

    // 이미지 파일 선택 시 미리보기
    customGoalImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('이미지 크기는 5MB를 초과할 수 없습니다.');
                return;
            }

            LoadingManager.show();

            const reader = new FileReader();
            reader.onload = function(event) {
                customGoalImageData = event.target.result;
                imagePreviewArea.innerHTML = `
                    <img src="${event.target.result}" alt="미리보기" class="max-h-40 mx-auto rounded">
                `;
                LoadingManager.hide();
            };
            reader.onerror = function() {
                LoadingManager.hide();
                alert('이미지를 읽는 중 오류가 발생했습니다.');
            };
            reader.readAsDataURL(file);
        }
    });

    // 커스텀 목표 추가/편집 폼 제출
    customGoalForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const description = customGoalDescription.value.trim();
        if (!description) {
            alert('목표 설명을 입력해주세요.');
            return;
        }

        const selectedYear = parseInt(customGoalYear.value);

        if (customModalMode === 'add') {
            // 추가 모드
            customGoalCounter++;
            const tempCustomId = `custom-${customGoalCounter}`;

            // selectedItems에 임시 추가 (이미지가 없으면 null)
            selectedItems.set(tempCustomId, {
                description: description,
                targetYear: selectedYear,
                imageSrc: customGoalImageData,
                category: 'custom'
            });

            // 선택된 목표 목록에 추가
            addGoalToList(tempCustomId, description, customGoalImageData);
            updateSelectedCount();

            // AJAX로 즉시 저장하고 실제 ID를 받아 처리
            saveSingleGoal(tempCustomId, description, selectedYear, 'add', null, function(data) {
                if (data.mi_idx) {
                    const realId = data.mi_idx.toString();

                    // selectedItems의 키를 임시 ID에서 실제 ID로 변경
                    const itemData = selectedItems.get(tempCustomId);
                    selectedItems.delete(tempCustomId);
                    selectedItems.set(realId, itemData);

                    // 선택 목록의 ID 업데이트
                    const goalItem = document.getElementById(`goal-item-${tempCustomId}`);
                    if (goalItem) {
                        goalItem.id = `goal-item-${realId}`;
                        const checkbox = goalItem.querySelector('.goal-checkbox');
                        if (checkbox) checkbox.setAttribute('data-id', realId);
                        const yearSelect = goalItem.querySelector('.target-year-select');
                        if (yearSelect) yearSelect.setAttribute('data-id', realId);
                    }

                    // 샘플 그리드에 추가 (custom 카테고리이므로 필터가 custom일 때만 보임)
                    if (currentCategory === 'all' || currentCategory === 'custom') {
                        const newItem = {
                            id: realId,
                            category: 'custom',
                            image: customGoalImageData,
                            description: description
                        };
                        const itemHtml = createMappingItemHtml(newItem);
                        mappingGrid.insertAdjacentHTML('beforeend', itemHtml);
                        registerNewItemsEvents();
                        restoreSelectedStates();
                    }
                }
            });
        } else if (customModalMode === 'edit') {
            // 편집 모드
            const editingId = customModalEditingId;

            // selectedItems 업데이트
            const item = selectedItems.get(editingId);
            if (item) {
                item.description = description;
                item.targetYear = selectedYear;
            }

            // 선택 목록의 아이템 업데이트
            const goalItem = document.getElementById(`goal-item-${editingId}`);
            if (goalItem) {
                const descSpan = goalItem.querySelector('.flex-1.text-sm');
                if (descSpan) {
                    descSpan.textContent = description;
                }
                const yearSelect = goalItem.querySelector('.target-year-select');
                if (yearSelect) {
                    yearSelect.value = selectedYear;
                }
            }

            // 샘플 그리드의 아이템도 업데이트
            const gridItem = document.querySelector(`.mapping-item[data-id="${editingId}"]`);
            if (gridItem) {
                gridItem.setAttribute('data-description', description);
                const descElement = gridItem.querySelector('.text-sm.font-medium.text-white');
                if (descElement) {
                    descElement.textContent = description;
                }
            }

            // AJAX로 업데이트
            saveSingleGoal(editingId, description, selectedYear, 'update');
        }

        closeCustomModal();
    });

    // 커스텀 모달에서 삭제 버튼 클릭
    deleteCustomGoalFromModalBtn.addEventListener('click', function() {
        const editingId = customModalEditingId;
        const item = selectedItems.get(editingId);
        const description = item ? item.description : '이 목표';

        if (confirm(`"${description}" 목표를 삭제하시겠습니까?\n삭제된 목표는 복구할 수 없습니다.`)) {
            // 선택 해제
            selectedItems.delete(editingId);
            removeGoalFromList(editingId);

            // 그리드의 체크박스도 해제
            const gridItem = document.querySelector(`.mapping-item[data-id="${editingId}"]`);
            if (gridItem) {
                const gridCheckbox = gridItem.querySelector('.mapping-checkbox');
                if (gridCheckbox) {
                    gridCheckbox.checked = false;
                }
                gridItem.classList.remove('selected');
            }

            // AJAX로 삭제
            saveSingleGoal(editingId, description, null, 'remove');

            updateSelectedCount();
            closeCustomModal();
        }
    });
});
</script>

<style>
.mapping-item {
    transition: all 0.3s ease;
}

.mapping-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.mapping-item.selected {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(78, 205, 196, 0.3);
}

.mapping-item.selected .mapping-checkbox {
    background-color: #FF4D4D;
    border-color: #FF4D4D;
}

.mapping-checkbox {
    transition: all 0.2s ease;
    z-index: 10;
}

.mapping-checkbox:checked {
    background-color: #FF4D4D;
    border-color: #FF4D4D;
}

.category-filter {
    transition: all 0.2s ease;
}

.aspect-square {
    aspect-ratio: 1 / 1;
}

/* 텍스트 가독성 향상 */
.mapping-item .drop-shadow-lg {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
}

/* 호버 시 텍스트 효과 */
.mapping-item:hover .drop-shadow-lg {
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
}
</style>
@endsection