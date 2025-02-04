@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">현실 점검하기</h1>
        <p class="text-gray-600">월별 지출 내역을 기록하고 관리하세요</p>
    </div>

    <!-- 항상 존재하는 숨겨진 추가 버튼 -->
    <button id="addExpenseButton" class="hidden">지출 추가</button>

    <!-- 상단 버튼 영역 수정 -->
    @if(count($expenses) > 0)
    <div class="mb-8 flex justify-end items-center space-x-4">
        <!-- 샘플 가져오기 버튼 -->
        <button onclick="openSampleModal()" 
                class="bg-secondary border border-gray-300 text-cdark px-4 py-2 rounded-lg transition-colors duration-200 flex items-center text-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            샘플 가져오기
        </button>
        
        <!-- 지출 추가 버튼 -->
        <button onclick="document.getElementById('addExpenseButton').click()" 
                class="bg-point text-cdark px-4 py-2 rounded-lg transition-colors duration-200 flex items-center text-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            지출 추가
        </button>
    </div>
    @endif

    <!-- PC 테이블 뷰 -->
    <div class="hidden md:block">
        @if(count($expenses) > 0)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-point">
                        <th class="px-6 py-4 text-left text-cdark">지출 항목</th>
                        <th class="px-6 py-4 text-left text-cdark">예상 금액</th>
                        <th class="px-6 py-4 text-left text-cdark">실제 금액</th>
                        <th class="px-6 py-4 text-left text-cdark">차이</th>
                        <th class="px-6 py-4 text-left text-cdark w-24">작업</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150" data-id="{{ $expense->idx }}">
                        <td class="px-6 py-4">{{ $expense->mq_category }}</td>
                        <td class="px-6 py-4">{{ number_format($expense->mq_expected_amount) }}원</td>
                        <td class="px-6 py-4">{{ number_format($expense->mq_actual_amount) }}원</td>
                        <td class="px-6 py-4 {{ ($expense->mq_expected_amount - $expense->mq_actual_amount) > 0 ? 'text-blue-600' : (($expense->mq_expected_amount - $expense->mq_actual_amount) < 0 ? 'text-red-600' : '') }}">
                            {{ number_format(abs($expense->mq_expected_amount - $expense->mq_actual_amount)) }}원
                            {{ ($expense->mq_expected_amount - $expense->mq_actual_amount) > 0 ? '절약' : (($expense->mq_expected_amount - $expense->mq_actual_amount) < 0 ? '초과' : '') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <button class="text-dark hover:text-dark/70 transition-colors duration-200 edit-expense">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-700 transition-colors duration-200 delete-expense">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="flex flex-col items-center justify-center space-y-4">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <p class="text-lg text-gray-600">아직 등록된 지출 항목이 없습니다.</p>
                <div class="flex space-x-4 mt-4">
                    <!-- 샘플 가져오기 버튼 -->
                    <button onclick="openSampleModal()" 
                            class="px-6 py-2 bg-secondary text-cdark rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        샘플 가져오기
                    </button>
                    <!-- 지출 항목 추가 버튼 -->
                    <button onclick="document.getElementById('addExpenseButton').click()" 
                            class="px-6 py-2 bg-point text-cdark rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        지출 추가하기
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- 모바일 카드 뷰 -->
    <div class="md:hidden">
        @if(count($expenses) > 0)
            <div class="space-y-4">
                @foreach($expenses as $expense)
                <div class="bg-white rounded-lg shadow-lg p-4" data-id="{{ $expense->idx }}">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-lg">{{ $expense->mq_category }}</h3>
                        <div class="flex space-x-2">
                            <button class="text-dark hover:text-dark/70 transition-colors duration-200 edit-expense">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button class="text-red-600 hover:text-red-700 transition-colors duration-200 delete-expense">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">예상 금액</span>
                            <span>{{ number_format($expense->mq_expected_amount) }}원</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">실제 금액</span>
                            <span>{{ number_format($expense->mq_actual_amount) }}원</span>
                        </div>
                        <div class="flex justify-between text-sm pt-2 border-t">
                            <span class="text-gray-600">차이</span>
                            <span class="{{ ($expense->mq_expected_amount - $expense->mq_actual_amount) > 0 ? 'text-blue-600' : (($expense->mq_expected_amount - $expense->mq_actual_amount) < 0 ? 'text-red-600' : '') }}">
                                {{ number_format(abs($expense->mq_expected_amount - $expense->mq_actual_amount)) }}원
                                {{ ($expense->mq_expected_amount - $expense->mq_actual_amount) > 0 ? '절약' : (($expense->mq_expected_amount - $expense->mq_actual_amount) < 0 ? '초과' : '') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <p class="text-base text-gray-600">아직 등록된 지출 항목이 없습니다.</p>
                    <div class="flex space-x-4 mt-4">
                        <!-- 샘플 가져오기 버튼 -->
                        <button onclick="openSampleModal()" 
                                class="px-6 py-2 bg-secondary text-dark rounded-lg hover:bg-secondary/90 transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            샘플 가져오기
                        </button>
                        <!-- 지출 항목 추가 버튼 -->
                        <button onclick="document.getElementById('addExpenseButton').click()" 
                                class="px-6 py-2 bg-point text-point rounded-lg transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            지출 항목 추가하기
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- 모달 -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-dark" id="modalTitle">지출 추가</h2>
            <button type="button" id="closeModal" class="text-text-dark hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="expenseForm">
            @csrf
            <input type="hidden" id="itemId">
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                        지출 항목 <span class="text-red-500">*</span>
                    </label>
                    <select id="category" 
                            name="category" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark mb-2"
                            required>
                        <option value="">선택하세요</option>
                        <option value="식비">식비</option>
                        <option value="주거·통신">주거·통신</option>
                        <option value="카페·간식">카페·간식</option>
                        <option value="편의점·마트·잡화">편의점·마트·잡화</option>
                        <option value="술·유흥">술·유흥</option>
                        <option value="쇼핑">쇼핑</option>
                        <option value="취미·여가">취미·여가</option>
                        <option value="의료·건강·피트니스">의료·건강·피트니스</option>
                        <option value="교통·자동차">교통·자동차</option>
                        <option value="여행·숙박">여행·숙박</option>
                        <option value="교육">교육</option>
                        <option value="저축·투자">저축·투자</option>
                        <option value="기타">기타</option>
                    </select>
                    <input type="text" 
                           id="categoryCustom" 
                           name="categoryCustom" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark hidden"
                           placeholder="지출 항목을 입력하세요">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="expected_amount">
                        예상 금액 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="expected_amount" 
                           name="expected_amount" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark"
                           required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="actual_amount">
                        실제 금액 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="actual_amount" 
                           name="actual_amount" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark"
                           required>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="cancelButton" class="px-4 py-2 text-cdark border border-gray-300 rounded-lg transition-colors duration-200">
                    취소
                </button>
                <button type="submit" id="submitButton" class="px-4 py-2 bg-point text-cdark rounded-lg transition-colors duration-200">
                    저장
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 샘플 선택 모달 -->
<div id="sampleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md mx-4 overflow-hidden">
        <!-- 모달 헤더 -->
        <div class="bg-point p-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-cdark">샘플 가져오기</h2>
                <button type="button" onclick="closeSampleModal()" class="text-cdark hover:text-secondary/80">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-2">
                <div class="flex items-center">
                    <div class="w-full bg-cdark/30 h-2 rounded-full">
                        <div id="progressBar" class="bg-cdark h-2 rounded-full transition-all duration-300" style="width: 50%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 모달 컨텐츠 -->
        <div class="p-6">
            <!-- 성별 선택 화면 -->
            <div id="genderStep" class="space-y-6">
                <h3 class="text-lg font-bold text-center mb-8">성별을 선택해주세요</h3>
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="selectGender('male')" class="aspect-square rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200 flex flex-col items-center justify-center space-y-2">
                        <svg class="w-12 h-12 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="font-medium">남자</span>
                    </button>
                    <button onclick="selectGender('female')" class="aspect-square rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200 flex flex-col items-center justify-center space-y-2">
                        <svg class="w-12 h-12 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="font-medium">여자</span>
                    </button>
                </div>
            </div>

            <!-- 연령대 선택 화면 -->
            <div id="ageStep" class="hidden space-y-6">
                <h3 class="text-lg font-bold text-center mb-8">연령대를 선택해주세요</h3>
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="selectAge('10')" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">10대</span>
                    </button>
                    <button onclick="selectAge('2030')" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">20~30대</span>
                    </button>
                    <button onclick="selectAge('3040')" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">30~40대</span>
                    </button>
                    <button onclick="selectAge('5060')" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">50~60대</span>
                    </button>
                    <button onclick="selectAge('60')" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">60대 이상</span>
                    </button>
                </div>
            </div>

            <!-- 샘플 보기 화면의 구조 수정 -->
            <div id="sampleStep" class="hidden space-y-6">
                <!-- 타이틀을 위한 고정 영역 추가 -->
                <div id="sampleTitle" class="text-center mb-4">
                    <h3 class="text-lg font-bold text-dark"></h3>
                </div>
                
                <div class="relative">
                    <!-- 슬라이더 컨테이너 -->
                    <div class="overflow-hidden">
                        <div id="sampleSlider" class="flex transition-transform duration-300 ease-in-out">
                            <!-- 샘플 데이터가 여기에 동적으로 추가됨 -->
                        </div>
                    </div>
                    
                    <!-- 이전/다음 버튼 -->
                    <button id="prevSlide" class="absolute left-0 top-1/2 -translate-y-1/2 bg-cdark text-white p-2 rounded-full transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button id="nextSlide" class="absolute right-0 top-1/2 -translate-y-1/2 bg-cdark text-white p-2 rounded-full transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <!-- 선택 버튼 -->
                <div class="flex justify-center space-x-4 mt-8">
                    <button onclick="resetSelection()" class="px-6 py-2 text-cdark border border-gray-300 rounded-lg transition-colors duration-200">
                        다시 선택하기
                    </button>
                    <button onclick="selectSample()" class="px-6 py-2 bg-point text-cdark rounded-lg transition-colors duration-200">
                        항목 불러오기
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 금액 입력 필드에 포맷팅 적용
    initNumberFormatting('#expected_amount, #actual_amount');
    
    const modal = document.getElementById('modal');
    const form = document.getElementById('expenseForm');
    const addButton = document.getElementById('addExpenseButton');
    const cancelButton = document.getElementById('cancelButton');
    const closeModal = document.getElementById('closeModal');
    const modalTitle = document.getElementById('modalTitle');
    const categorySelect = document.getElementById('category');
    const categoryCustom = document.getElementById('categoryCustom');
    let isEditing = false;

    // 모달 제어 함수
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
        categoryCustom.classList.add('hidden');
        categoryCustom.required = false;
        isEditing = false;
    }

    // 카테고리 선택 이벤트
    categorySelect.addEventListener('change', function() {
        if (this.value === '기타') {
            categoryCustom.classList.remove('hidden');
            categoryCustom.required = true;
        } else {
            categoryCustom.classList.add('hidden');
            categoryCustom.required = false;
            categoryCustom.value = '';
        }
    });

    // 폼 제출 이벤트
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            const formData = new FormData();
            
            // CSRF 토큰 추가
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            // PUT 메소드 처리를 위한 _method 추가 (수정 시)
            if (isEditing) {
                formData.append('_method', 'PUT');
            }
            
            // 금액 필드의 콤마 제거
            const expectedAmount = document.getElementById('expected_amount').value.replace(/,/g, '');
            const actualAmount = document.getElementById('actual_amount').value.replace(/,/g, '');
            
            // 필드명을 컨트롤러의 validate 규칙과 일치하도록 설정
            formData.append('mq_expected_amount', expectedAmount);
            formData.append('mq_actual_amount', actualAmount);
            
            // 카테고리 설정
            if (categorySelect.value === '기타' && categoryCustom.value.trim()) {
                formData.append('mq_category', categoryCustom.value.trim());
            } else {
                formData.append('mq_category', categorySelect.value);
            }

            LoadingManager.show();

            const url = isEditing ? 
                `/guidebook/reality-check/${document.getElementById('itemId').value}` : 
                '/guidebook/reality-check';

            const response = await fetch(url, {
                method: 'POST', // POST로 통일하고 _method로 처리
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                window.location.reload();
            } else {
                if (result.error && typeof result.error === 'object') {
                    // 유효성 검사 오류 메시지 표시
                    const errorMessages = Object.values(result.error).flat();
                    alert(errorMessages.join('\n'));
                } else {
                    throw new Error(result.message);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('저장 중 오류가 발생했습니다.');
            LoadingManager.hide();
        }
    });

    // 수정 버튼 이벤트
    document.querySelectorAll('.edit-expense').forEach(button => {
        button.addEventListener('click', () => {
            const tr = button.closest('tr') || button.closest('.bg-white');
            const categoryValue = tr.querySelector('td:first-child, h3').textContent.trim();
            
            // 카테고리 설정
            const categoryOption = Array.from(categorySelect.options)
                .find(option => option.value === categoryValue);
            
            if (categoryOption) {
                categorySelect.value = categoryValue;
                categoryCustom.classList.add('hidden');
                categoryCustom.required = false;
            } else {
                categorySelect.value = '기타';
                categoryCustom.value = categoryValue;
                categoryCustom.classList.remove('hidden');
                categoryCustom.required = true;
            }
            
            // 금액 값 설정 및 포맷팅
            const expectedAmount = tr.querySelector('td:nth-child(2), div:nth-child(2) span:last-child')
                .textContent.replace(/[^\d]/g, '');
            const actualAmount = tr.querySelector('td:nth-child(3), div:nth-child(3) span:last-child')
                .textContent.replace(/[^\d]/g, '');
            
            document.getElementById('expected_amount').value = numberWithCommas(expectedAmount);
            document.getElementById('actual_amount').value = numberWithCommas(actualAmount);
            document.getElementById('itemId').value = tr.dataset.id;
            
            modalTitle.textContent = '지출 수정';
            isEditing = true;
            openModal();
        });
    });

    // 삭제 버튼 이벤트
    document.querySelectorAll('.delete-expense').forEach(button => {
        button.addEventListener('click', async () => {
            if (!confirm('정말 삭제하시겠습니까?')) return;

            const tr = button.closest('tr') || button.closest('.bg-white');
            const id = tr.dataset.id;

            try {
                LoadingManager.show();
                const response = await fetch(`/guidebook/reality-check/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                
                if (result.status === 'success') {
                    window.location.reload();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('삭제 중 오류가 발생했습니다.');
                LoadingManager.hide();
            }
        });
    });

    // 이벤트 리스너 등록
    addButton.addEventListener('click', () => {
        modalTitle.textContent = '지출 추가';
        openModal();
    });
    cancelButton.addEventListener('click', closeModalHandler);
    closeModal.addEventListener('click', closeModalHandler);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModalHandler();
        }
    });
});

// 샘플 모달 관련 스크립트
let selectedGender = null;
let selectedAge = null;
let currentSlide = 0;
let samples = [];

function openSampleModal() {
    const modal = document.getElementById('sampleModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // 초기 상태로 리셋
    resetSampleModal();
}

function closeSampleModal() {
    const modal = document.getElementById('sampleModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
    
    // 모달이 닫힐 때도 초기화
    resetSampleModal();
}

// 모달 데이터 초기화 함수 추가
function resetSampleModal() {
    selectedGender = null;
    selectedAge = null;
    currentSlide = 0;
    samples = [];
    
    // UI 초기화
    document.getElementById('genderStep').classList.remove('hidden');
    document.getElementById('ageStep').classList.add('hidden');
    document.getElementById('sampleStep').classList.add('hidden');
    document.getElementById('progressBar').style.width = '33%';
    
    // 슬라이더 초기화
    const slider = document.getElementById('sampleSlider');
    if (slider) {
        slider.innerHTML = '';
        slider.style.transform = 'translateX(0)';
    }
    
    // 타이틀 초기화
    const sampleTitle = document.getElementById('sampleTitle').querySelector('h3');
    if (sampleTitle) {
        sampleTitle.textContent = '';
    }
}

// ESC 키 이벤트에도 초기화 추가
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !document.getElementById('sampleModal').classList.contains('hidden')) {
        closeSampleModal();
    }
});

function showGenderStep() {
    document.getElementById('genderStep').classList.remove('hidden');
    document.getElementById('ageStep').classList.add('hidden');
    document.getElementById('progressBar').style.width = '33%';
}

function showAgeStep() {
    document.getElementById('genderStep').classList.add('hidden');
    document.getElementById('ageStep').classList.remove('hidden');
    document.getElementById('progressBar').style.width = '66%';
}

function selectGender(gender) {
    selectedGender = gender;
    updateProgressBar('age');
    showAgeStep();
}

async function selectAge(age) {
    selectedAge = age;
    updateProgressBar('sample');
    await loadSamples();
    showSampleStep();
}

// 프로그레스바 업데이트 함수 수정
function updateProgressBar(step) {
    const progressBar = document.getElementById('progressBar');
    switch(step) {
        case 'gender':
            progressBar.style.width = '33%';
            break;
        case 'age':
            progressBar.style.width = '66%';
            break;
        case 'sample':
            progressBar.style.width = '100%';
            break;
    }
}

// 샘플 데이터 로드 함수
async function loadSamples() {
    LoadingManager.show();
    try {
        const response = await fetch('/guidebook/reality-check/get-samples', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                gender: selectedGender,
                age: selectedAge
            })
        });

        if (response.ok) {
            samples = await response.json();
            renderSamples();
        } else {
            throw new Error('샘플 데이터 가져오기 실패');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('샘플 데이터를 가져오는 중 오류가 발생했습니다.');
    } finally {
        LoadingManager.hide();
    }
}

// 샘플 화면 표시 함수
function showSampleStep() {
    document.getElementById('genderStep').classList.add('hidden');
    document.getElementById('ageStep').classList.add('hidden');
    document.getElementById('sampleStep').classList.remove('hidden');
}

// 샘플 렌더링 함수 수정
function renderSamples() {
    const slider = document.getElementById('sampleSlider');
    const sampleTitle = document.getElementById('sampleTitle').querySelector('h3');
    
    // 슬라이더 내용 초기화
    slider.innerHTML = '';
    
    // 타이틀 업데이트
    sampleTitle.textContent = `${getAgeRangeText(selectedAge)} ${selectedGender === 'male' ? '남성' : '여성'} (1/${samples.length})`;
    
    samples.forEach((sample, index) => {
        const slide = document.createElement('div');
        slide.className = 'w-full flex-shrink-0 px-4';
        slide.innerHTML = `
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="text-center mb-6">
                    <h4 class="text-xl font-bold text-dark mb-2">${sample.name}</h4>
                    <p class="text-gray-600">${sample.description}</p>
                </div>
                <div class="space-y-3">
                    ${Object.entries(sample.expenses).map(([category, amount]) => `
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">${category}</span>
                            <span class="font-medium">${numberWithCommas(amount)}원</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        slider.appendChild(slide);
    });
    
    updateSliderPosition();
}

// 연령대 텍스트 변환 함수 추가
function getAgeRangeText(age) {
    const ageRanges = {
        '10': '10대',
        '2030': '20~30대',
        '3040': '30~40대',
        '5060': '50~60대',
        '60': '60대 이상'
    };
    return ageRanges[age] || '';
}

// 슬라이더 위치 업데이트 함수 수정
function updateSliderPosition() {
    const slider = document.getElementById('sampleSlider');
    const sampleTitle = document.getElementById('sampleTitle').querySelector('h3');
    
    slider.style.transform = `translateX(-${currentSlide * 100}%)`;
    
    // 타이틀 업데이트
    sampleTitle.textContent = `${getAgeRangeText(selectedAge)} ${selectedGender === 'male' ? '남성' : '여성'} (${currentSlide + 1}/${samples.length})`;
    
    // 이전/다음 버튼 상태 업데이트
    const prevButton = document.getElementById('prevSlide');
    const nextButton = document.getElementById('nextSlide');
    
    if (prevButton && nextButton) {
        prevButton.style.visibility = currentSlide === 0 ? 'hidden' : 'visible';
        nextButton.style.visibility = currentSlide === samples.length - 1 ? 'hidden' : 'visible';
    }
}

// 샘플 선택 함수
async function selectSample() {
    LoadingManager.show();
    try {
        const response = await fetch('/guidebook/reality-check/apply-sample', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                sampleIndex: currentSlide,
                gender: selectedGender,
                age: selectedAge
            })
        });

        if (response.ok) {
            window.location.reload();
        } else {
            throw new Error('샘플 적용 실패');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('샘플 적용 중 오류가 발생했습니다.');
        LoadingManager.hide();
    }
}

// 다시 선택하기 함수 추가
function resetSelection() {
    selectedGender = null;
    selectedAge = null;
    currentSlide = 0;
    samples = [];
    
    // UI 초기화
    document.getElementById('genderStep').classList.remove('hidden');
    document.getElementById('ageStep').classList.add('hidden');
    document.getElementById('sampleStep').classList.add('hidden');
    
    // 프로그레스바 초기화
    document.getElementById('progressBar').style.width = '33%';
    
    // 슬라이더 초기화
    const slider = document.getElementById('sampleSlider');
    if (slider) {
        slider.innerHTML = '';
        slider.style.transform = 'translateX(0)';
    }
    
    // 타이틀 초기화
    const sampleTitle = document.getElementById('sampleTitle').querySelector('h3');
    if (sampleTitle) {
        sampleTitle.textContent = '';
    }
}

// 슬라이더 네비게이션 이벤트 리스너 추가
document.getElementById('prevSlide')?.addEventListener('click', () => {
    if (currentSlide > 0) {
        currentSlide--;
        updateSliderPosition();
    }
});

document.getElementById('nextSlide')?.addEventListener('click', () => {
    if (currentSlide < samples.length - 1) {
        currentSlide++;
        updateSliderPosition();
    }
});
</script>
@endpush
@endsection