@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">현실 점검 하기</h1>
        <p class="text-gray-600">월별 지출 내역을 기록하고 관리하세요</p>
    </div>

    <!-- 추가 버튼 -->
    <div class="mb-8 flex justify-end">
        <button id="addExpenseButton" class="bg-dark text-secondary px-4 py-2 rounded-lg hover:bg-dark/90 transition-colors duration-200 flex items-center text-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            지출 추가
        </button>
    </div>

    <!-- PC 테이블 뷰 -->
    <div class="hidden md:block">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-dark">
                        <th class="px-6 py-4 text-left text-secondary font-semibold">지출 항목</th>
                        <th class="px-6 py-4 text-left text-secondary font-semibold">예상 금액</th>
                        <th class="px-6 py-4 text-left text-secondary font-semibold">실제 금액</th>
                        <th class="px-6 py-4 text-left text-secondary font-semibold">차이</th>
                        <th class="px-6 py-4 text-left text-secondary font-semibold w-24">작업</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">{{ $expense['category'] }}</td>
                        <td class="px-6 py-4">{{ number_format($expense['expected_amount']) }}원</td>
                        <td class="px-6 py-4">{{ number_format($expense['actual_amount']) }}원</td>
                        <td class="px-6 py-4 {{ $expense['difference'] > 0 ? 'text-blue-600' : ($expense['difference'] < 0 ? 'text-red-600' : '') }}">
                            {{ number_format(abs($expense['difference'])) }}원
                            {{ $expense['difference'] > 0 ? '절약' : ($expense['difference'] < 0 ? '초과' : '') }}
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
    </div>

    <!-- 모바일 카드 뷰 -->
    <div class="md:hidden space-y-4">
        @foreach($expenses as $expense)
        <div class="bg-white rounded-lg shadow-lg p-4">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-bold text-lg">{{ $expense['category'] }}</h3>
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
                    <span>{{ number_format($expense['expected_amount']) }}원</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">실제 금액</span>
                    <span>{{ number_format($expense['actual_amount']) }}원</span>
                </div>
                <div class="flex justify-between text-sm pt-2 border-t">
                    <span class="text-gray-600">차이</span>
                    <span class="{{ $expense['difference'] > 0 ? 'text-blue-600' : ($expense['difference'] < 0 ? 'text-red-600' : '') }}">
                        {{ number_format(abs($expense['difference'])) }}원
                        {{ $expense['difference'] > 0 ? '절약' : ($expense['difference'] < 0 ? '초과' : '') }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- 모달 -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-dark" id="modalTitle">지출 추가</h2>
            <button type="button" id="closeModal" class="text-gray-500 hover:text-gray-700">
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
                    <input type="number" 
                           id="expected_amount" 
                           name="expected_amount" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark"
                           required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="actual_amount">
                        실제 금액 <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="actual_amount" 
                           name="actual_amount" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark"
                           required>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="cancelButton" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    취소
                </button>
                <button type="submit" id="submitButton" class="px-4 py-2 bg-dark text-secondary rounded-lg hover:bg-dark/90 transition-colors duration-200">
                    저장
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal');
    const form = document.getElementById('expenseForm');
    const addButton = document.getElementById('addExpenseButton');
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
        categoryCustom.classList.add('hidden');
        categoryCustom.required = false;
        isEditing = false;
    }

    addButton.addEventListener('click', () => {
        modalTitle.textContent = '지출 추가';
        openModal();
    });

    cancelButton.addEventListener('click', closeModalHandler);
    closeModal.addEventListener('click', closeModalHandler);

    // 지출 항목 선택 시 기타 입력 필드 처리
    const categorySelect = document.getElementById('category');
    const categoryCustom = document.getElementById('categoryCustom');

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
            
            document.getElementById('expected_amount').value = tr.querySelector('td:nth-child(2), div:nth-child(2) span:last-child').textContent.replace(/[^0-9]/g, '');
            document.getElementById('actual_amount').value = tr.querySelector('td:nth-child(3), div:nth-child(3) span:last-child').textContent.replace(/[^0-9]/g, '');
            
            modalTitle.textContent = '지출 수정';
            isEditing = true;
            openModal();
        });
    });

    // 삭제 버튼 이벤트
    document.querySelectorAll('.delete-expense').forEach(button => {
        button.addEventListener('click', () => {
            if (confirm('정말 삭제하시겠습니까?')) {
                // 삭제 로직 구현
                LoadingManager.show();
                // ... DB 연동 후 구현 예정
            }
        });
    });

    // 폼 제출
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // 기타 선택 시 사용자 입력값으로 카테고리 설정
        if (categorySelect.value === '기타' && categoryCustom.value.trim()) {
            const formData = new FormData(form);
            formData.set('category', categoryCustom.value.trim());
        }
        
        LoadingManager.show();
        
        try {
            // DB 연동 후 실제 API 호출 구현 예정
            window.location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert('저장 중 오류가 발생했습니다.');
            LoadingManager.hide();
        }
    });

    // ESC 키로 모달 닫기
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModalHandler();
        }
    });

    // 모달 외부 클릭 시 닫기
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModalHandler();
        }
    });
});
</script>
@endpush
@endsection