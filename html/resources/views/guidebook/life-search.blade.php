@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">원하는 삶 찾기</h1>
        <p class="text-gray-600">나의 삶의 목표와 꿈을 정리해보세요</p>
    </div>

    <div class="mb-12">
        <!-- 상단 영역 -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- 필요금액 영역 -->
                <div class="flex items-center">
                    <span class="text-lg font-medium text-gray-900">필요금액</span>
                    <span class="ml-2 text-xl font-bold text-cdark">
                        {{ number_format($lifeSearches->sum('mq_price')) }}원
                    </span>
                </div>
                
                <!-- 버튼 영역 -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button onclick="openSampleModal()" 
                            class="flex-1 sm:flex-none bg-secondary border border-gray-300 text-cdark px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        샘플 가져오기
                    </button>
                    <button class="add-button flex-1 sm:flex-none bg-point text-cdark px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        추가하기
                    </button>
                </div>
            </div>
        </div>

        <!-- PC 테이블 뷰 -->
        <div class="hidden md:block">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-point">
                                <th class="px-6 py-4 text-left text-cdark w-32">카테고리</th>
                                <th class="px-6 py-4 text-left text-cdark">목표</th>
                                <th class="px-6 py-4 text-left text-cdark">목표일자</th>
                                <th class="px-6 py-4 text-left text-cdark">필요금액</th>
                                <th class="px-6 py-4 text-left text-cdark w-24">작업</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lifeSearches as $item)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150" data-id="{{ $item->idx }}">
                                <td class="px-6 py-4">{{ $item->mq_category }}</td>
                                <td class="px-6 py-4">{{ $item->mq_content }}</td>
                                <td class="px-6 py-4">{{ $item->mq_target_date ? date('Y-m-d', strtotime($item->mq_target_date)) : '' }}</td>
                                <td class="px-6 py-4">{{ number_format($item->mq_price) }}원</td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button class="text-dark hover:text-dark/70 transition-colors duration-200 edit-button" 
                                                data-id="{{ $item->idx }}"
                                                data-category="{{ $item->mq_category }}"
                                                data-content="{{ $item->mq_content }}"
                                                data-price="{{ $item->mq_price }}"
                                                data-target-date="{{ $item->mq_target_date ? date('Y-m-d', strtotime($item->mq_target_date)) : '' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-700 transition-colors duration-200 delete-button" data-id="{{ $item->idx }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-text-dark">
                                    데이터가 없습니다.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 모바일 카드 뷰 -->
        <div class="md:hidden">
            @forelse($lifeSearches as $item)
            <div class="bg-white rounded-lg shadow-lg p-4 mb-4" data-id="{{ $item->idx }}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-sm font-medium {{ $item->getCategoryColorClass() }} mb-2">
                            {{ $item->mq_category }}
                        </span>
                        <h3 class="font-bold text-lg">{{ $item->mq_content }}</h3>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-dark hover:text-dark/70 transition-colors duration-200 edit-button"
                                data-id="{{ $item->idx }}"
                                data-category="{{ $item->mq_category }}"
                                data-content="{{ $item->mq_content }}"
                                data-price="{{ $item->mq_price }}"
                                data-target-date="{{ $item->mq_target_date ? date('Y-m-d', strtotime($item->mq_target_date)) : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button class="text-red-600 hover:text-red-700 transition-colors duration-200 delete-button" data-id="{{ $item->idx }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">목표일자</span>
                        <span>{{ $item->mq_target_date ? date('Y-m-d', strtotime($item->mq_target_date)) : '' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">필요금액</span>
                        <span>{{ number_format($item->mq_price) }}원</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-lg p-4 text-center text-text-dark">
                데이터가 없습니다.
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- 모달 -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-dark" id="modalTitle">추가하기</h2>
            <button type="button" id="closeModal" class="text-text-dark hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="lifeSearchForm">
            @csrf
            <input type="hidden" id="itemId">
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="mq_category">
                        카테고리
                    </label>
                    <input type="text" id="mq_category" name="mq_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="mq_content">
                        목표 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="mq_content" 
                           name="mq_content" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark"
                           required>
                    <p id="contentError" class="hidden text-red-500 text-xs mt-1">목표를 입력해주세요.</p>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="mq_target_date">
                        목표일자 <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="mq_target_date" 
                           name="mq_target_date" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark"
                           required>
                    <p id="targetDateError" class="hidden text-red-500 text-xs mt-1">목표일자를 선택해주세요.</p>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="mq_price">
                        필요금액 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="mq_price" 
                           name="mq_price" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark"
                           required>
                    <p id="priceError" class="hidden text-red-500 text-xs mt-1">필요금액을 입력해주세요.</p>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="cancelButton" class="px-4 py-2 bg-secondary border border-gray-300 text-cdark rounded-lg transition-colors duration-200">
                    취소
                </button>
                <button type="submit" id="submitButton" class="px-4 py-2 bg-point text-cdark rounded-lg transition-colors duration-200">
                    저장
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 샘플 모달 -->
<div id="sampleModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <!-- 모달 컨테이너 -->
    <div class="relative h-full sm:flex sm:items-center sm:justify-center">
        <!-- 모달 내용 -->
        <div class="h-full w-full sm:w-[450px] bg-white">
            <!-- 모달 헤더 -->
            <div class="sticky top-0 z-10 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between p-4">
                    <h2 class="text-xl font-bold text-cdark">샘플 가져오기</h2>
                    <button onclick="closeSampleModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <!-- 카테고리 필터 -->
                <div class="px-4 pb-4 hidden" id="categoryFilterContainer">
                    <div class="relative category-swiper">
                        <!-- Swiper 컨테이너 -->
                        <div class="swiper">
                            <div class="swiper-wrapper" id="categoryFilters">
                                <!-- 카테고리 버튼들이 동적으로 추가됨 -->
                            </div>
                        </div>
                        <!-- 네비게이션 버튼 -->
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                </div>
            </div>
            
            <!-- 모달 본문 -->
            <div class="h-[calc(100%-200px)] overflow-y-auto" id="sampleCardContainer">
                <div class="p-4 space-y-4" id="sampleCardList">
                    <!-- 카드들이 여기에 동적으로 추가됨 -->
                </div>
                <!-- 로딩 인디케이터 -->
                <div id="loadingIndicator" class="hidden p-4 text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-200 border-t-point"></div>
                </div>
                <!-- More 버튼 -->
                <div id="moreButtonContainer" class="p-4 text-center hidden">
                    <button id="loadMoreBtn" class="px-4 py-2 bg-secondary text-cdark rounded-lg hover:bg-secondary/80 transition-colors duration-200">
                        더보기
                    </button>
                </div>
            </div>

            <!-- 하단 고정 영역 -->
            <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 hidden" id="completeButtonContainer">
                <button id="completeSelectionBtn" 
                        class="w-full py-3 rounded-lg font-medium transition-colors duration-200 disabled:cursor-not-allowed disabled:opacity-50 bg-gray-200 text-gray-500"
                        disabled>
                    선택완료
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>

/* 필요하다면 스크롤바를 숨기는 옵션도 추가 가능 */
.hide-scrollbar {
  -ms-overflow-style: none; /* IE, Edge */
  scrollbar-width: none; /* Firefox */
}
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

/* Swiper 기본 구조에 맞춰서 스타일링 */
.category-swiper {
  width: 100%;
  position: relative;
}

.category-swiper .swiper {
  width: 100%;
  /* freeMode를 쓰는 경우 스크롤이 필요하다면 overflow 설정 */
  /* overflow: hidden; (필요에 따라 조절) */
}

.category-swiper .swiper-wrapper {
  /* 필요하다면 flex나 정렬 옵션 조정 가능 */
}

.category-swiper .swiper-slide {
  /* Swiper가 slidesPerView: 'auto' 일 때 알아서 width를 잡습니다 */
  /* width: auto; 등을 굳이 강제할 필요는 없습니다 */
  display: flex;
  align-items: center;
  justify-content: center;
}

.category-swiper .swiper-button-next,
.category-swiper .swiper-button-prev {
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.category-swiper .swiper-button-prev {
    left: 0;
}

.category-swiper .swiper-button-next {
    right: 0;
}

.category-swiper .swiper-button-disabled {
    opacity: 0;
    pointer-events: none;
}

/* 카테고리 버튼은 슬라이드 안에 들어가는 요소이므로
   따로 display를 지정하기보다는 padding, 색상 정도만 주시면 됩니다. */
.category-filter {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    margin: 0; /* swiper-slide로 분리되므로 margin 대신 spaceBetween 옵션 사용 */
}

/* 추가 필요 CSS */
.swiper-slide {
    width: auto !important;
}

.category-filter {
    display: inline-block;
    width: max-content;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal');
    const form = document.getElementById('lifeSearchForm');
    const cancelButton = document.getElementById('cancelButton');
    const closeModal = document.getElementById('closeModal');
    const modalTitle = document.getElementById('modalTitle');
    let isEditing = false;

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModalHandler() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        form.reset();
        isEditing = false;
    }

    cancelButton.addEventListener('click', closeModalHandler);
    closeModal.addEventListener('click', closeModalHandler);

    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', () => {
            const data = button.dataset;
            
            document.getElementById('itemId').value = data.id;
            document.getElementById('mq_category').value = data.category;
            document.getElementById('mq_content').value = data.content;
            document.getElementById('mq_price').value = numberWithCommas(data.price);
            document.getElementById('mq_target_date').value = data.targetDate;
            
            modalTitle.textContent = '수정하기';
            isEditing = true;
            openModal();
        });
    });

    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', async (e) => {
            if (!confirm('정말 삭제하시겠습니까?')) return;
            
            const id = e.target.closest('button').dataset.id;
            try {
                LoadingManager.show();
                
                const response = await fetch(`/guidebook/life-search/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('삭제에 실패했습니다.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('삭제 중 오류가 발생했습니다.');
                LoadingManager.hide();
            }
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const contentInput = document.getElementById('mq_content');
        const targetDateInput = document.getElementById('mq_target_date');
        const priceInput = document.getElementById('mq_price');
        
        const contentError = document.getElementById('contentError');
        const targetDateError = document.getElementById('targetDateError');
        const priceError = document.getElementById('priceError');
        
        let hasError = false;
        
        // 목표 유효성 검사
        if (!contentInput.value.trim()) {
            contentInput.classList.add('border-red-500');
            contentError.classList.remove('hidden');
            contentInput.focus();
            hasError = true;
        } else {
            contentInput.classList.remove('border-red-500');
            contentError.classList.add('hidden');
        }
        
        // 목표일자 유효성 검사
        if (!targetDateInput.value) {
            targetDateInput.classList.add('border-red-500');
            targetDateError.classList.remove('hidden');
            if (!hasError) {
                targetDateInput.focus();
                hasError = true;
            }
        } else {
            targetDateInput.classList.remove('border-red-500');
            targetDateError.classList.add('hidden');
        }
        
        // 필요금액 유효성 검사
        if (!priceInput.value.trim()) {
            priceInput.classList.add('border-red-500');
            priceError.classList.remove('hidden');
            if (!hasError) {
                priceInput.focus();
                hasError = true;
            }
        } else {
            priceInput.classList.remove('border-red-500');
            priceError.classList.add('hidden');
        }
        
        if (hasError) return;
        
        LoadingManager.show();
        
        const formData = new FormData(form);
        const id = document.getElementById('itemId').value;
        
        const price = formData.get('mq_price').replace(/,/g, '');
        formData.set('mq_price', price);
        
        try {
            const url = isEditing 
                ? `/guidebook/life-search/${id}`
                : '/guidebook/life-search';
                
            const method = isEditing ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });
            
            if (response.ok) {
                window.location.reload();
            } else {
                throw new Error('저장에 실패했습니다.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('저장 중 오류가 발생했습니다.');
            LoadingManager.hide();
        }
    });

    // 입력 필드 이벤트 리스너 추가
    document.getElementById('mq_content').addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('border-red-500');
            document.getElementById('contentError').classList.add('hidden');
        }
    });

    document.getElementById('mq_target_date').addEventListener('change', function() {
        if (this.value) {
            this.classList.remove('border-red-500');
            document.getElementById('targetDateError').classList.add('hidden');
        }
    });

    document.getElementById('mq_price').addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('border-red-500');
            document.getElementById('priceError').classList.add('hidden');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModalHandler();
        }
    });
    
    document.querySelector('.add-button').addEventListener('click', () => {
        modalTitle.textContent = '추가하기';
        openModal();
    });

    initNumberFormatting('#mq_price');
});

let selectedCards = new Set();
let page = 1;
let loading = false;
let hasMore = true;
let categories = [];
let currentCategory = null;
let loadedCards = new Map();
let categoryData = new Map();

async function openSampleModal() {
    const modal = document.getElementById('sampleModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // 상태 초기화
    page = 1;
    loading = false;
    hasMore = true;
    categories = [];
    currentCategory = null;
    loadedCards.clear();
    categoryData.clear();
    document.getElementById('sampleCardList').innerHTML = '';
    
    // 로딩 표시
    document.getElementById('loadingIndicator').classList.remove('hidden');
    
    // 카테고리 정보 로드
    await loadCategories();
    
    // 첫 번째 카테고리의 데이터 로드
    if (categories.length > 0) {
        currentCategory = categories[0].name;
        filterByCategory(currentCategory);
    }
}

async function loadCategories() {
    try {
        const response = await fetch('/guidebook/life-search/get-categories', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        categories = data.categories;
        
        // 카테고리 필터 업데이트
        updateCategoryFilters();
        
        // 카테고리 필터와 선택완료 버튼 표시
        document.getElementById('categoryFilterContainer').classList.remove('hidden');
        document.getElementById('completeButtonContainer').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function updateCategoryFilters() {
    const filterContainer = document.querySelector('#categoryFilters');
    filterContainer.innerHTML = '';

    // 카테고리 버튼 생성 시 각 카테고리는 반드시 swiper-slide 로 감싸기
    categories.forEach(category => {
        // 슬라이드 하나 생성
        const slideDiv = document.createElement('div');
        // Swiper가 인식하도록 클래스명 지정
        slideDiv.classList.add('swiper-slide');
        
        // 버튼 생성
        const button = document.createElement('button');
        button.className = 'category-filter bg-secondary text-cdark';
        button.textContent = `${category.name} (${category.count})`;
        button.dataset.category = category.name;
        button.onclick = () => filterByCategory(category.name);

        // 슬라이드에 버튼 삽입
        slideDiv.appendChild(button);
        // 래퍼(swiper-wrapper)에 추가
        filterContainer.appendChild(slideDiv);
    });

    // Swiper 초기화
    // 이미 존재한다면 destroy 후 재초기화하는 부분은 동일
    if (window.categorySwiper) {
        window.categorySwiper.destroy(true, true);
    }

    // 50ms 지연 추가 (렌더링 완료 대기)
    setTimeout(() => {
        window.categorySwiper = new Swiper('.category-swiper .swiper', {
            slidesPerView: 'auto',
            spaceBetween: 8, // 슬라이드 간격
            navigation: {
                nextEl: '.category-swiper .swiper-button-next',
                prevEl: '.category-swiper .swiper-button-prev',
            },
            // 필요하면 자유 스크롤 모드
            // freeMode: true,
            });
    }, 50);
}

async function filterByCategory(category) {
    // 이미 로딩 중이면 리턴
    if (loading) return;
    
    currentCategory = category;
    
    // 버튼 스타일 업데이트
    document.querySelectorAll('.category-filter').forEach(btn => {
        if (btn.dataset.category === category) {
            btn.classList.add('bg-point');
            btn.classList.remove('bg-secondary');
        } else {
            btn.classList.remove('bg-point');
            btn.classList.add('bg-secondary');
        }
    });
    
    // 이미 로드된 데이터가 있는지 확인
    if (!categoryData.has(category)) {
        // 카테고리 버튼 비활성화
        toggleCategoryButtons(true);
        
        // 카테고리 데이터 초기화
        categoryData.set(category, { page: 1, hasMore: true });
        document.getElementById('sampleCardList').innerHTML = '';
        await loadSampleCards();
        
        // 카테고리 버튼 활성화
        toggleCategoryButtons(false);
    } else {
        // 저장된 카드 표시
        displayCategoryCards(category);
    }
}

function displayCategoryCards(category) {
    const cardList = document.getElementById('sampleCardList');
    cardList.innerHTML = '';
    
    // 해당 카테고리의 모든 카드 표시
    Array.from(loadedCards.values())
        .filter(card => card.category === category)
        .forEach(card => {
            const cardElement = createCardElement(card, selectedCards.has(card.id));
            cardList.appendChild(cardElement);
        });
    
    // More 버튼 표시 여부 설정
    const categoryInfo = categoryData.get(category);
    document.getElementById('moreButtonContainer').classList.toggle('hidden', !categoryInfo.hasMore);
}

function createCardElement(card, isSelected = false) {
    const cardElement = document.createElement('div');
    cardElement.className = `bg-white rounded-lg border ${isSelected ? 'border-point border-4' : 'border-gray-200'} p-4 transition-all duration-200 cursor-pointer hover:bg-gray-50`;
    cardElement.setAttribute('data-card-id', card.id);
    
    // 카테고리별 배경색 매핑
    const categoryColors = {
        '여행': 'bg-blue-100 text-blue-800',
        '취미': 'bg-green-100 text-green-800',
        '음식': 'bg-orange-100 text-orange-800',
        '문화': 'bg-purple-100 text-purple-800',
        '학습': 'bg-yellow-100 text-yellow-800',
        '건강': 'bg-red-100 text-red-800',
        '생활': 'bg-gray-100 text-gray-800'
    };

    cardElement.innerHTML = `
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 pt-1">
                <input type="checkbox" 
                       class="w-5 h-5 rounded border-gray-300 text-point focus:ring-point"
                       onclick="event.stopPropagation()"
                       ${isSelected ? 'checked' : ''}>
            </div>
            <div class="flex-grow">
                <div class="flex items-center justify-between mb-2">
                    <div class="category-label inline-block px-2.5 py-0.5 rounded-md text-sm font-medium ${categoryColors[card.category] || 'bg-gray-100 text-gray-800'}">
                        ${card.category}
                    </div>
                    <span class="font-medium text-sm text-cdark">${numberWithCommas(card.price)}원</span>
                </div>
                <div class="font-medium text-gray-900">${card.content}</div>
            </div>
        </div>
    `;
    
    // 카드 클릭 이벤트 추가
    cardElement.addEventListener('click', () => toggleCardSelection(card.id));
    
    return cardElement;
}

function toggleCardSelection(cardId) {
    const card = document.querySelector(`[data-card-id="${cardId}"]`);
    const checkbox = card.querySelector('input[type="checkbox"]');
    
    if (selectedCards.has(cardId)) {
        selectedCards.delete(cardId);
        card.classList.remove('border-point', 'border-4');
        card.classList.add('border-gray-200', 'border');
        checkbox.checked = false;
    } else {
        selectedCards.add(cardId);
        card.classList.add('border-point', 'border-4');
        card.classList.remove('border-gray-200', 'border');
        checkbox.checked = true;
    }
    updateCompleteButton();
}

function updateCompleteButton() {
    const completeBtn = document.getElementById('completeSelectionBtn');
    const selectedCount = selectedCards.size;
    
    if (selectedCount > 0) {
        completeBtn.textContent = `선택완료(${selectedCount})`;
        completeBtn.classList.remove('bg-gray-200', 'text-gray-500');
        completeBtn.classList.add('bg-point', 'text-cdark');
        completeBtn.disabled = false;
    } else {
        completeBtn.textContent = '선택완료';
        completeBtn.classList.add('bg-gray-200', 'text-gray-500');
        completeBtn.classList.remove('bg-point', 'text-cdark');
        completeBtn.disabled = true;
    }
}

async function loadSampleCards() {
    if (loading || !hasMore) return;
    
    const categoryInfo = categoryData.get(currentCategory);
    if (!categoryInfo.hasMore) return;
    
    loading = true;
    document.getElementById('loadingIndicator').classList.remove('hidden');
    document.getElementById('loadMoreBtn').classList.add('opacity-50', 'cursor-not-allowed');
    
    // 카테고리 버튼 비활성화
    toggleCategoryButtons(true);
    
    try {
        const response = await fetch(`/guidebook/life-search/get-samples?page=${categoryInfo.page}&category=${currentCategory}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.cards.length === 0) {
            categoryInfo.hasMore = false;
            document.getElementById('moreButtonContainer').classList.add('hidden');
            return;
        }

        const cardList = document.getElementById('sampleCardList');
        data.cards.forEach(card => {
            loadedCards.set(card.id, card);
            const cardElement = createCardElement(card, selectedCards.has(card.id));
            cardList.appendChild(cardElement);
        });
        
        categoryInfo.page++;
        categoryInfo.hasMore = data.has_more;
        categoryData.set(currentCategory, categoryInfo);
        
        document.getElementById('moreButtonContainer').classList.toggle('hidden', !data.has_more);
        
    } catch (error) {
        console.error('Error loading sample cards:', error);
    } finally {
        loading = false;
        document.getElementById('loadingIndicator').classList.add('hidden');
        document.getElementById('loadMoreBtn').classList.remove('opacity-50', 'cursor-not-allowed');
        
        // 카테고리 버튼 활성화
        toggleCategoryButtons(false);
    }
}

// More 버튼 클릭 이벤트
document.getElementById('loadMoreBtn').addEventListener('click', loadSampleCards);

function closeSampleModal() {
    const modal = document.getElementById('sampleModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // 모달이 닫힐 때 상태 초기화
    selectedCards.clear();
    page = 1;
    loading = false;
    hasMore = true;
    categories = [];
    currentCategory = null;
    loadedCards.clear();
    document.getElementById('sampleCardList').innerHTML = '';
    
    // 카테고리 필터 초기화
    document.getElementById('categoryFilterContainer').classList.add('hidden');
    const categoryFilters = document.getElementById('categoryFilters');
    categoryFilters.innerHTML = ''; // 모든 카테고리 버튼 제거
    
    // Swiper 인스턴스 제거
    if (window.categorySwiper) {
        window.categorySwiper.destroy();
        window.categorySwiper = null;
    }
    
    // 선택완료 버튼 숨기기 및 초기화
    document.getElementById('completeButtonContainer').classList.add('hidden');
    updateCompleteButton();
}

// 카테고리 버튼 비활성화/활성화 함수 추가
function toggleCategoryButtons(disabled) {
    document.querySelectorAll('.category-filter').forEach(btn => {
        btn.disabled = disabled;
        if (disabled) {
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    });
}

// completeSelectionBtn 클릭 이벤트 핸들러 추가
document.getElementById('completeSelectionBtn').addEventListener('click', async () => {
    if (!confirm('선택한 샘플을 추가하시겠습니까?')) return;
    
    try {
        LoadingManager.show();
        
        const response = await fetch('/guidebook/life-search/apply-samples', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                selectedCards: Array.from(selectedCards)
            })
        });
        
        if (!response.ok) {
            throw new Error('샘플 추가에 실패했습니다.');
        }
        
        const data = await response.json();
        if (data.success) {
            window.location.reload(); // 페이지 새로고침
        } else {
            throw new Error(data.message || '샘플 추가에 실패했습니다.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert(error.message);
    } finally {
        LoadingManager.hide();
    }
});
</script>
@endpush
@endsection