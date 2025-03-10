@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-[768px]">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">현실 점검하기</h1>
        <p class="text-gray-600">월별 수입/지출 내역을 확인하세요</p>
    </div>
    <!-- 항상 존재하는 숨겨진 추가 버튼 -->
    <button id="addExpenseButton" class="hidden">항목 추가</button>

    <!-- 상단 버튼 영역 수정 -->
    <div class="mb-8 flex justify-end items-center space-x-4">
        <!-- 샘플 가져오기 버튼 -->
        <button class="bg-secondary border border-gray-300 text-cdark px-4 py-2 rounded-lg transition-colors duration-200 flex items-center text-sm sample-modal-button">
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
            항목 추가
        </button>
    </div>

    <!-- 탭 메뉴 -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button onclick="switchTab('statistics')" 
                        class="tab-button active flex-1 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm md:text-base text-center"
                        data-tab="statistics">
                    통계
                </button>
                <button onclick="switchTab('expense')" 
                        class="tab-button flex-1 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm md:text-base text-center"
                        data-tab="expense">
                    지출
                </button>
                <button onclick="switchTab('income')" 
                        class="tab-button flex-1 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm md:text-base text-center"
                        data-tab="income">
                    수입
                </button>
            </nav>
        </div>

        <!-- 탭 컨텐츠 -->
        <div class="p-4 md:p-6">
            <!-- 통계 탭 -->
            <div id="statistics-tab" class="tab-content">
                <!-- 총 수입/지출 정보 추가 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-white rounded-lg border p-4">
                        <div class="text-center">
                            <h4 class="text-base font-semibold text-green-600">총 수입</h4>
                            <p id="totalIncome" class="text-xl font-bold mt-1">0원</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg border p-4">
                        <div class="text-center">
                            <h4 class="text-base font-semibold text-red-600">총 지출</h4>
                            <p id="totalExpense" class="text-xl font-bold mt-1">0원</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-white rounded-lg border p-4">
                        <h3 class="text-lg font-semibold mb-4">수입 분석</h3>
                        <div id="incomeChart" class="h-64"></div>
                    </div>
                    <div class="bg-white rounded-lg border p-4">
                        <h3 class="text-lg font-semibold mb-4">지출 분석</h3>
                        <div id="expenseChart" class="h-64"></div>
                    </div>
                </div>
    <!-- PC 테이블 뷰 -->
    <div class="hidden md:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">구분</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">항목</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">금액</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">작업</th>
                                </tr>
                            </thead>
                            <tbody id="statisticsTable" class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 모바일 카드 뷰 -->
                <div class="block md:hidden">
                    <div id="statisticsCards" class="space-y-4">
                        <!-- 카드가 여기에 동적으로 추가됨 -->
                    </div>
                </div>
            </div>

            <!-- 지출 탭 -->
            <div id="expense-tab" class="tab-content hidden">
                <!-- 지출 합계 정보 추가 -->
                <div class="bg-white rounded-lg border p-4 mb-6">
                    <div class="text-center">
                        <h4 class="text-base font-semibold text-red-600">총 지출</h4>
                        <p id="totalExpenseOnly" class="text-xl font-bold mt-1">0원</p>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="bg-white rounded-lg border p-4">
                        <h3 class="text-lg font-semibold mb-4">지출 분석</h3>
                        <div id="expenseOnlyChart" class="h-64"></div>
                    </div>
                </div>
                <!-- PC 테이블 뷰 -->
                <div class="hidden md:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">항목</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">금액</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">작업</th>
                                </tr>
                            </thead>
                            <tbody id="expenseTable" class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 모바일 카드 뷰 -->
                <div class="block md:hidden">
                    <div id="expenseCards" class="space-y-4">
                        <!-- 카드가 여기에 동적으로 추가됨 -->
                    </div>
                </div>
            </div>

            <!-- 수입 탭 -->
            <div id="income-tab" class="tab-content hidden">
                <!-- 수입 합계 정보 추가 -->
                <div class="bg-white rounded-lg border p-4 mb-6">
                    <div class="text-center">
                        <h4 class="text-base font-semibold text-green-600">총 수입</h4>
                        <p id="totalIncomeOnly" class="text-xl font-bold mt-1">0원</p>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="bg-white rounded-lg border p-4">
                        <h3 class="text-lg font-semibold mb-4">수입 분석</h3>
                        <div id="incomeOnlyChart" class="h-64"></div>
                    </div>
                </div>
                <!-- PC 테이블 뷰 -->
                <div class="hidden md:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">항목</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">금액</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">작업</th>
                                </tr>
                            </thead>
                            <tbody id="incomeTable" class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 모바일 카드 뷰 -->
                <div class="block md:hidden">
                    <div id="incomeCards" class="space-y-4">
                        <!-- 카드가 여기에 동적으로 추가됨 -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PC 테이블 뷰 -->
    <div class="hidden md:hidden">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden" id="pc-table-view">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-point">
                        <th class="px-6 py-4 text-left text-cdark">구분</th>
                        <th class="px-6 py-4 text-left text-cdark">항목</th>
                        <th class="px-6 py-4 text-left text-cdark">금액</th>
                        <th class="px-6 py-4 text-left text-cdark w-24">작업</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150" data-id="{{ $expense->idx }}">
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-sm {{ $expense->mq_type == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $expense->mq_type == 1 ? '수입' : '지출' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $expense->mq_category }}</td>
                        <td class="px-6 py-4">{{ number_format($expense->mq_price) }}원</td>
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
        
        <!-- PC 빈 화면 메시지 -->
        <div class="bg-white rounded-lg shadow-lg p-8 text-center hidden" id="pc-empty-view">
            <div class="flex flex-col items-center justify-center space-y-4">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <p class="text-lg text-gray-600">아직 등록된 항목이 없습니다.</p>
                <div class="flex space-x-4 mt-4">
                    <!-- 샘플 가져오기 버튼 -->
                    <button class="px-6 py-2 bg-secondary border border-gray-300 text-cdark rounded-lg transition-colors duration-200 flex items-center sample-modal-button">
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
                        항목 추가
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 모바일 카드 뷰 -->
    <div class="md:hidden hidden">
        <!-- 모바일 카드 컨테이너 -->
        <div id="mobile-cards-container">
                @foreach($expenses as $expense)
            <div class="bg-white rounded-lg shadow p-4" data-id="{{ $expense->idx }}">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="px-2 py-1 rounded-full text-sm {{ $expense->mq_type == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $expense->mq_type == 1 ? '수입' : '지출' }}
                        </span>
                        <h3 class="text-lg font-semibold text-gray-800 mt-2">{{ $expense->mq_category }}</h3>
                        </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-800">{{ number_format($expense->mq_price) }}원</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        
        <!-- 모바일 빈 화면 메시지 -->
        <div class="bg-white rounded-lg shadow-lg p-8 text-center hidden" id="mobile-empty-view">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-base text-gray-600">아직 등록된 항목이 없습니다.</p>
                    <div class="flex space-x-4 mt-4">
                        <!-- 샘플 가져오기 버튼 -->
                        <button class="px-6 py-2 bg-secondary text-cdark border border-gray-300 rounded-lg hover:bg-secondary/90 transition-colors duration-200 flex items-center sample-modal-button">
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
                            항목 추가
                        </button>
                    </div>
                </div>
            </div>
    </div>
</div>

<!-- 모달 -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-cdark" id="modalTitle">항목 추가</h2>
            <button type="button" id="closeModal" class="text-cdark hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="expenseForm">
            @csrf
            <input type="hidden" id="itemId">
            <div class="space-y-4">
                <!-- 수입/지출 구분 -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">구분</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="mq_type" value="0" class="form-radio text-point" checked>
                            <span class="ml-2">지출</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="mq_type" value="1" class="form-radio text-point">
                            <span class="ml-2">수입</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                        항목 <span class="text-red-500">*</span>
                    </label>
                    <!-- 지출 항목 선택 -->
                    <select id="expenseCategory" 
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
                    
                    <!-- 수입 항목 선택 -->
                    <select id="incomeCategory" 
                            name="category" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark mb-2 hidden"
                            required>
                        <option value="">선택하세요</option>
                        <option value="급여">급여</option>
                        <option value="상여금">상여금</option>
                        <option value="사업수입">사업수입</option>
                        <option value="아르바이트">아르바이트</option>
                        <option value="용돈">용돈</option>
                        <option value="금융수입">금융수입</option>
                        <option value="중고거래">중고거래</option>
                        <option value="부동산">부동산</option>
                        <option value="기타수입">기타수입</option>
                    </select>
                    
                    <input type="text" 
                           id="categoryCustom" 
                           name="categoryCustom" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark hidden"
                           placeholder="항목을 입력하세요">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="price">
                        금액 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="price" 
                           name="price" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark"
                           maxlength="15"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');"
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
    <div class="bg-white rounded-lg w-full max-w-md mx-4 overflow-hidden max-h-[90vh] flex flex-col">
        <!-- 모달 헤더 -->
        <div class="bg-white p-4 border-b">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-cdark">샘플 가져오기</h2>
                <button type="button" class="text-cdark close-sample-modal">
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
        <div class="p-6 overflow-y-auto flex-grow">
            <!-- 성별 선택 화면 -->
            <div id="genderStep" class="space-y-6">
                <h3 class="text-lg font-bold text-center mb-8">성별을 선택해주세요</h3>
                <div class="grid grid-cols-2 gap-4">
                    <button data-gender="male" class="aspect-square rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200 flex flex-col items-center justify-center space-y-2">
                        <svg class="w-12 h-12 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="font-medium">남자</span>
                    </button>
                    <button data-gender="female" class="aspect-square rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200 flex flex-col items-center justify-center space-y-2">
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
                    <button data-age="10" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">10대</span>
                    </button>
                    <button data-age="2030" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">20~30대</span>
                    </button>
                    <button data-age="3040" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">30~40대</span>
                    </button>
                    <button data-age="4050" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">40~50대</span>
                    </button>
                    <button data-age="5060" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">50~60대</span>
                    </button>
                    <button data-age="60" class="rounded-xl border-2 border-gray-200 p-4 hover:border-dark focus:border-dark transition-colors duration-200">
                        <span class="font-medium">60대 이상</span>
                    </button>
                </div>
            </div>

            <!-- 샘플 보기 화면의 구조 수정 -->
            <div id="sampleStep" class="hidden space-y-4">
                <!-- 타이틀을 위한 고정 영역 추가 -->
                <div id="sampleTitle" class="text-center mb-2">
                    <h3 class="text-base font-bold text-dark"></h3>
                </div>
                
                <div class="relative">
                    <!-- 슬라이더 컨테이너 -->
                    <div class="overflow-hidden">
                        <div id="sampleSlider" class="flex transition-transform duration-300 ease-in-out">
                            <!-- 샘플 데이터가 여기에 동적으로 추가됨 -->
                        </div>
                    </div>
                    
                    <!-- 이전/다음 버튼 -->
                    <button id="prevSlide" class="absolute left-0 top-1/2 -translate-y-1/2 bg-cdark text-white p-1.5 rounded-full transition-colors duration-200 z-10">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button id="nextSlide" class="absolute right-0 top-1/2 -translate-y-1/2 bg-cdark text-white p-1.5 rounded-full transition-colors duration-200 z-10">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <!-- 선택 버튼 -->
                <div class="flex justify-center space-x-4 mt-4">
                    <button class="reset-selection px-4 py-1.5 text-sm text-cdark border border-gray-300 rounded-lg transition-colors duration-200">
                        다시 선택
                    </button>
                    <button class="select-sample px-4 py-1.5 text-sm bg-point text-cdark rounded-lg transition-colors duration-200">
                        항목 불러오기
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js"></script>
<script>
let statisticsIncomeChart = null;
let statisticsExpenseChart = null;
let expenseOnlyChart = null;
let incomeOnlyChart = null;

// 수입 차트용 색상 배열 (랜덤으로 섞인 색상)
const incomeColors = [
    '#2E7D32', '#3F51B5', '#9E9E9E', '#00BCD4',  // 녹색, 파란색, 회색, 하늘색
    '#7B1FA2', '#388E3C', '#1976D2', '#B2EBF2',  // 보라색, 녹색, 파란색, 하늘색
    '#1B5E20', '#2196F3', '#757575', '#80DEEA',  // 녹색, 파란색, 회색, 하늘색
    '#9C27B0', '#4CAF50', '#0D47A1', '#616161',  // 보라색, 녹색, 파란색, 회색
    '#6A1B9A'                                   // 보라색
];

// 지출 차트용 색상 배열 (랜덤으로 섞인 색상)
const expenseColors = [
    '#FF0000', '#FF69B4', '#000080', '#DC143C', // 빨간색, 분홍색, 남색, 빨간색
    '#FFD700', '#B22222', '#191970', '#FF1493', // 노란색, 빨간색, 남색, 분홍색
    '#FF8C00', '#DB7093', '#00008B', '#FFA500'  // 노란색, 분홍색, 남색, 노란색
];

// 샘플 데이터 관련 변수
let selectedGender = null;
let selectedAge = null;
let currentSlide = 0;
let sampleData = [];

document.addEventListener('DOMContentLoaded', function() {
    // 탭 전환 함수
    window.switchTab = function(tabName) {
        // 모든 탭 컨텐츠 숨기기
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // 선택된 탭 컨텐츠 보이기
        document.getElementById(`${tabName}-tab`).classList.remove('hidden');
        
        // 탭 버튼 스타일 업데이트
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

        // 데이터 로드
        loadTabData(tabName);
    }
    
    // 초기 탭 설정 및 데이터 로드
    // 초기 탭 설정을 위해 switchTab 함수 호출
    switchTab('statistics');
    
    // 탭 데이터 로드 함수
    function loadTabData(tabName) {
        let type = 'all';
        
        if (tabName === 'expense') {
            type = '0'; // 지출
        } else if (tabName === 'income') {
            type = '1'; // 수입
        }
        
        // Ajax 요청으로 데이터 가져오기
        fetch(`/guidebook/reality-check/get-expenses?type=${type}`)
            .then(response => {
                return response.json();
            })
            .then(result => {
            
            if (result.status === 'success') {
                    const expenses = result.data;
                    
                    // 차트 및 테이블 업데이트
                    updateTabContent(tabName, expenses);
            } else {
                    console.error('데이터 로드 실패:', result);
                }
            })
            .catch(error => {
                console.error('데이터 로드 중 오류 발생:', error);
            });
    }
    
    // 탭 컨텐츠 업데이트 함수
    function updateTabContent(tabName, expenses) {
        
        // 데이터가 없는 경우 처리
        if (!expenses || expenses.length === 0) {
        }
        
        const incomeData = expenses.filter(item => item.mq_type == 1);
        const expenseData = expenses.filter(item => item.mq_type == 0);
        
        // 합계 계산
        const totalIncome = incomeData.reduce((sum, item) => sum + parseInt(item.mq_price), 0);
        const totalExpense = expenseData.reduce((sum, item) => sum + parseInt(item.mq_price), 0);
        
        // 천단위 콤마 포맷팅 함수
        const formatNumber = (num) => {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        };
        
            switch(tabName) {
                case 'statistics':
                // 통계 탭 - 수입/지출 차트 및 테이블
                updateChart('incomeChart', prepareChartData(incomeData), '수입 분석', statisticsIncomeChart);
                updateChart('expenseChart', prepareChartData(expenseData), '지출 분석', statisticsExpenseChart);
                updateTable(expenses, 'statisticsTable', true);
                
                // 합계 정보 업데이트
                document.getElementById('totalIncome').textContent = formatNumber(totalIncome) + '원';
                document.getElementById('totalExpense').textContent = formatNumber(totalExpense) + '원';
                    break;
                
                case 'expense':
                // 지출 탭 - 지출 차트 및 테이블
                updateChart('expenseOnlyChart', prepareChartData(expenseData), '지출 분석', expenseOnlyChart);
                updateTable(expenses, 'expenseTable', false);
                
                // 지출 합계 정보 업데이트
                document.getElementById('totalExpenseOnly').textContent = formatNumber(totalExpense) + '원';
                break;
                
            case 'income':
                // 수입 탭 - 수입 차트 및 테이블
                updateChart('incomeOnlyChart', prepareChartData(incomeData), '수입 분석', incomeOnlyChart);
                updateTable(expenses, 'incomeTable', false);
                
                // 수입 합계 정보 업데이트
                document.getElementById('totalIncomeOnly').textContent = formatNumber(totalIncome) + '원';
                break;
        }
    }
    
    // 차트 데이터 준비 함수
    function prepareChartData(data) {
        // 데이터가 없는 경우 빈 배열 반환
        if (!data || data.length === 0) {
            return [];
        }
        
        const categoryMap = new Map();
        data.forEach(item => {
            const current = categoryMap.get(item.mq_category) || 0;
            categoryMap.set(item.mq_category, current + parseInt(item.mq_price));
        });
        
        // 카테고리별 데이터 생성
        const isIncome = data.length > 0 && data[0].mq_type == 1;
        const colorArray = isIncome ? incomeColors : expenseColors;
        
        return Array.from(categoryMap.entries()).map(([category, amount], index) => ({
            name: category,
            value: amount,
            itemStyle: {
                color: colorArray[index % colorArray.length]
            }
        }));
    }
    
    // 차트 업데이트 함수
    function updateChart(elementId, data, title, chartInstance) {
        const chartContainer = document.getElementById(elementId);
        if (!chartContainer) return;
        
        // 차트 인스턴스가 있으면 dispose
        if (chartInstance) {
            chartInstance.dispose();
        }
        
        // 데이터가 없는 경우 빈 차트 표시
        if (!data || data.length === 0) {
            const chart = echarts.init(chartContainer);
            chart.setOption({
                title: {
                    text: title,
                    left: 'center',
                    textStyle: {
                        fontSize: 16,
                        fontWeight: 'bold'
                    }
                },
                graphic: {
                    elements: [{
                        type: 'text',
                        left: 'center',
                        top: 'middle',
                        style: {
                            text: '데이터가 없습니다',
                            fontSize: 14,
                            fill: '#999'
                        }
                    }]
                }
            });
            
            // 차트 인스턴스 저장
            switch(elementId) {
                case 'incomeChart':
                    statisticsIncomeChart = chart;
                    break;
                case 'expenseChart':
                    statisticsExpenseChart = chart;
                    break;
                case 'expenseOnlyChart':
                    expenseOnlyChart = chart;
                    break;
                case 'incomeOnlyChart':
                    incomeOnlyChart = chart;
                    break;
            }
            
            return chart;
        }
        
        // 새 차트 생성
        const chart = echarts.init(chartContainer);
        const option = {
            tooltip: {
                trigger: 'item',
                formatter: function(params) {
                    // 천단위 콤마 포맷팅
                    const formattedValue = params.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return `${params.name}: ${formattedValue}원 (${params.percent}%)`;
                }
            },
            legend: {
                orient: 'horizontal',
                bottom: 10,
                left: 'center',
                type: 'scroll',
                pageIconColor: '#666',
                pageTextStyle: {
                    color: '#666'
                }
            },
            series: [{
                name: title,
                type: 'pie',
                radius: ['40%', '70%'],
                avoidLabelOverlap: true,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: false
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: '16',
                        fontWeight: 'bold'
                    }
                },
                labelLine: {
                    show: false
                },
                data: data
            }]
        };
        
        chart.setOption(option);
        
        // 차트 인스턴스 저장
        switch(elementId) {
            case 'incomeChart':
                statisticsIncomeChart = chart;
            break;
            case 'expenseChart':
                statisticsExpenseChart = chart;
            break;
            case 'expenseOnlyChart':
                expenseOnlyChart = chart;
                break;
            case 'incomeOnlyChart':
                incomeOnlyChart = chart;
            break;
        }
        
        // 창 크기 변경 시 차트 크기 조정
        window.addEventListener('resize', function() {
            chart.resize();
        });
        
        return chart;
    }
    
    // 테이블 업데이트 함수
    function updateTable(data, tableId, showType) {
        const tbody = document.getElementById(tableId);
        if (!tbody) {
            console.error(`테이블 ID '${tableId}'를 찾을 수 없습니다.`);
            return;
        }
        
        
        // 카드 컨테이너 ID 결정
        let cardsContainerId = '';
        if (tableId === 'statisticsTable') {
            cardsContainerId = 'statisticsCards';
        } else if (tableId === 'expenseTable') {
            cardsContainerId = 'expenseCards';
        } else if (tableId === 'incomeTable') {
            cardsContainerId = 'incomeCards';
        }
        
        const cardsContainer = document.getElementById(cardsContainerId);
        if (!cardsContainer) {
            console.error(`카드 컨테이너 ID '${cardsContainerId}'를 찾을 수 없습니다.`);
        }
        
        // 테이블과 카드 컨테이너 초기화
        tbody.innerHTML = '';
        if (cardsContainer) {
            cardsContainer.innerHTML = '';
        }
        
        // 데이터가 없는 경우 메시지 표시
        if (!data || data.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="${showType ? 4 : 3}" class="px-6 py-4 text-center text-gray-500">
                    데이터가 없습니다.
                </td>
            `;
            tbody.appendChild(row);
            
            // 카드 컨테이너에도 빈 데이터 메시지 추가
            if (cardsContainer) {
                cardsContainer.innerHTML = `
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-500 font-medium">데이터가 없습니다.</p>
                        <button onclick="document.getElementById('addExpenseButton').click()" 
                                class="mt-4 px-4 py-2 bg-point text-cdark rounded-lg transition-colors duration-200 inline-flex items-center text-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            항목 추가하기
                        </button>
                    </div>
                `;
            }
            return;
        }
        
        // 테이블 ID에 따라 데이터 필터링
        let filteredData = data;
        if (tableId === 'incomeTable') {
            // 수입 테이블인 경우 수입 데이터만 표시
            filteredData = data.filter(item => item.mq_type == 1);
        } else if (tableId === 'expenseTable') {
            // 지출 테이블인 경우 지출 데이터만 표시
            filteredData = data.filter(item => item.mq_type == 0);
        }
        
        // 필터링된 데이터가 없는 경우 메시지 표시
        if (!filteredData || filteredData.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="${showType ? 4 : 3}" class="px-6 py-4 text-center text-gray-500">
                    데이터가 없습니다.
                </td>
            `;
            tbody.appendChild(row);
            
            // 카드 컨테이너에도 빈 데이터 메시지 추가
            if (cardsContainer) {
                cardsContainer.innerHTML = `
                    <div class="bg-white rounded-lg shadow p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-500 font-medium">데이터가 없습니다.</p>
                        <button onclick="document.getElementById('addExpenseButton').click()" 
                                class="mt-4 px-4 py-2 bg-point text-cdark rounded-lg transition-colors duration-200 inline-flex items-center text-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            항목 추가하기
                        </button>
                    </div>
                `;
            }
            return;
        }
        
        // 테이블 행 생성 및 카드 생성
        filteredData.forEach(item => {
            // 테이블 행 생성
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors duration-150';
            row.setAttribute('data-id', item.idx);
            
            let html = '';
            
            if (showType) {
                html += `
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-sm ${item.mq_type == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${item.mq_type == 1 ? '수입' : '지출'}
                        </span>
                    </td>
                `;
            }
            
            html += `
                <td class="px-6 py-4">${item.mq_category}</td>
                <td class="px-6 py-4">${numberWithCommas(item.mq_price)}원</td>
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
            `;
            
            row.innerHTML = html;
            tbody.appendChild(row);
            
            // 카드 생성
            if (cardsContainer) {
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg shadow p-4 border-l-4 ' + 
                    (item.mq_type == 1 ? 'border-green-500' : 'border-red-500');
                card.setAttribute('data-id', item.idx);
                
                let cardHtml = `
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                `;
                
                if (showType) {
                    cardHtml += `
                        <span class="px-2 py-1 rounded-full text-xs ${item.mq_type == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} mb-2 inline-block">
                            ${item.mq_type == 1 ? '수입' : '지출'}
                        </span>
                    `;
                }
                
                cardHtml += `
                            <h3 class="text-lg font-semibold text-gray-800">${item.mq_category}</h3>
                </div>
                        <div class="text-right ml-4">
                            <p class="text-lg font-bold ${item.mq_type == 1 ? 'text-green-600' : 'text-red-600'}">${numberWithCommas(item.mq_price)}원</p>
                        </div>
                </div>
                    <div class="flex justify-end space-x-3 mt-3 pt-2 border-t border-gray-100">
                        <button class="text-dark hover:text-dark/70 transition-colors duration-200 edit-expense p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button class="text-red-600 hover:text-red-700 transition-colors duration-200 delete-expense p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
            </div>
        `;
                
                card.innerHTML = cardHtml;
                cardsContainer.appendChild(card);
            }
        });
        
        // 이벤트 리스너 연결
        attachTableEventListeners(tableId);
        
        // 카드 이벤트 리스너 연결
        if (cardsContainer) {
            attachCardEventListeners(cardsContainerId);
        }
    }
    
    // 카드 이벤트 리스너 연결
    function attachCardEventListeners(cardsContainerId) {
        const cardsContainer = document.getElementById(cardsContainerId);
        if (!cardsContainer) return;
        
        // 수정 버튼 이벤트
        cardsContainer.querySelectorAll('.edit-expense').forEach(button => {
            button.addEventListener('click', function() {
                const card = this.closest('div[data-id]');
                const id = card.getAttribute('data-id');
                const typeSpan = card.querySelector('span');
                const type = typeSpan ? 
                    (typeSpan.textContent.trim() === '수입' ? 1 : 0) : 
                    (cardsContainerId === 'incomeCards' ? 1 : 0);
                const category = card.querySelector('h3').textContent;
                const price = card.querySelector('p').textContent.replace(/[^\d]/g, '');
                
                openExpenseModal('edit', id, category, price, type);
            });
        });
        
        // 삭제 버튼 이벤트
        cardsContainer.querySelectorAll('.delete-expense').forEach(button => {
            button.addEventListener('click', function() {
                const card = this.closest('div[data-id]');
                const id = card.getAttribute('data-id');
                
                if (confirm('정말 이 항목을 삭제하시겠습니까?')) {
                    deleteExpense(id);
                }
            });
        });
    }
    
    // 테이블 이벤트 리스너 연결
    function attachTableEventListeners(tableId) {
        const tbody = document.getElementById(tableId);
        if (!tbody) return;
        
        // 수정 버튼 이벤트
        tbody.querySelectorAll('.edit-expense').forEach(button => {
            button.addEventListener('click', function() {
                const tr = this.closest('tr');
                const id = tr.getAttribute('data-id');
                const type = tr.querySelector('td:nth-child(1) span') ? 
                    (tr.querySelector('td:nth-child(1) span').textContent.trim() === '수입' ? 1 : 0) : 
                    (tr.querySelector('td:nth-child(3)').textContent.includes('수입') ? 1 : 0);
                const category = tr.querySelector('td:nth-child(' + (tableId === 'statisticsTable' ? '2' : '1') + ')').textContent;
                const price = tr.querySelector('td:nth-child(' + (tableId === 'statisticsTable' ? '3' : '2') + ')').textContent.replace(/[^\d]/g, '');
                
                openExpenseModal('edit', id, category, price, type);
            });
        });
        
        // 삭제 버튼 이벤트
        tbody.querySelectorAll('.delete-expense').forEach(button => {
            button.addEventListener('click', function() {
                const tr = this.closest('tr');
                const id = tr.getAttribute('data-id');
                
                if (confirm('정말 이 항목을 삭제하시겠습니까?')) {
                    deleteExpense(id);
                }
            });
        });
    }
    
    // 항목 추가/수정 모달 열기
    function openExpenseModal(mode, id = null, category = null, price = null, type = null) {
        
        const modal = document.getElementById('modal');
        const titleElement = document.getElementById('modalTitle');
        const expenseCategorySelect = document.getElementById('expenseCategory');
        const incomeCategorySelect = document.getElementById('incomeCategory');
        const priceInput = document.getElementById('price');
        const itemIdInput = document.getElementById('itemId');
        const typeRadios = document.getElementsByName('mq_type');
        
        // 모달 제목 설정
        titleElement.textContent = mode === 'add' ? '항목 추가' : '항목 수정';
        
        // 폼 필드 설정
        itemIdInput.value = id || '';
        
        // 수입/지출 구분 설정
        if (type !== null) {
            for (let i = 0; i < typeRadios.length; i++) {
                if (typeRadios[i].value == type) {
                    typeRadios[i].checked = true;
                    break;
                }
            }
        } else {
            // 기본값은 지출(0)
            typeRadios[0].checked = true;
        }
        
        // 수입/지출에 따라 카테고리 선택 목록 표시
        const isIncome = type == 1;
        expenseCategorySelect.classList.toggle('hidden', isIncome);
        incomeCategorySelect.classList.toggle('hidden', !isIncome);
        expenseCategorySelect.required = !isIncome;
        incomeCategorySelect.required = isIncome;
        
        // 카테고리 설정
        if (category) {
            if (isIncome) {
                // 수입 카테고리 설정
                let optionFound = false;
                for (let i = 0; i < incomeCategorySelect.options.length; i++) {
                    if (incomeCategorySelect.options[i].value === category) {
                        incomeCategorySelect.selectedIndex = i;
                        optionFound = true;
                        break;
                    }
                }
                
                // 일치하는 옵션이 없으면 '기타수입' 선택
                if (!optionFound) {
                    for (let i = 0; i < incomeCategorySelect.options.length; i++) {
                        if (incomeCategorySelect.options[i].value === '기타수입') {
                            incomeCategorySelect.selectedIndex = i;
                            break;
                        }
                    }
                    
                    // 커스텀 카테고리 필드 표시 및 값 설정
                    const categoryCustomInput = document.getElementById('categoryCustom');
                    if (categoryCustomInput) {
                        categoryCustomInput.value = category;
                        categoryCustomInput.classList.remove('hidden');
                    }
                }
            } else {
                // 지출 카테고리 설정
                let optionFound = false;
                for (let i = 0; i < expenseCategorySelect.options.length; i++) {
                    if (expenseCategorySelect.options[i].value === category) {
                        expenseCategorySelect.selectedIndex = i;
                        optionFound = true;
                        break;
                    }
                }
                
                // 일치하는 옵션이 없으면 '기타' 선택
                if (!optionFound) {
                    for (let i = 0; i < expenseCategorySelect.options.length; i++) {
                        if (expenseCategorySelect.options[i].value === '기타') {
                            expenseCategorySelect.selectedIndex = i;
                            break;
                        }
                    }
                    
                    // 커스텀 카테고리 필드 표시 및 값 설정
                    const categoryCustomInput = document.getElementById('categoryCustom');
                    if (categoryCustomInput) {
                        categoryCustomInput.value = category;
                        categoryCustomInput.classList.remove('hidden');
                    }
                }
            }
        } else {
            // 카테고리가 없는 경우 첫 번째 옵션 선택
            expenseCategorySelect.selectedIndex = 0;
            incomeCategorySelect.selectedIndex = 0;
        }
        
        // 금액 설정 (천단위 콤마 포맷팅 적용)
        if (price !== null) {
            priceInput.value = numberWithCommas(price);
        } else {
            priceInput.value = '';
        }
        
        // 모달 표시
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // 폼 제출 이벤트 설정
        const form = document.getElementById('expenseForm');
        form.onsubmit = function(e) {
            e.preventDefault();
            
            // 폼 데이터 수집
            const formData = new FormData(form);
            
            // 현재 선택된 수입/지출 타입 가져오기
            const isIncome = document.querySelector('input[name="mq_type"]:checked').value == '1';
            
            // 카테고리 값 설정
            let categoryValue = '';
            if (isIncome) {
                // 수입 카테고리
                categoryValue = document.getElementById('incomeCategory').value;
                if (categoryValue === '기타수입') {
                    const categoryCustomInput = document.getElementById('categoryCustom');
                    if (categoryCustomInput && categoryCustomInput.value.trim() !== '') {
                        categoryValue = categoryCustomInput.value.trim();
                    }
                }
            } else {
                // 지출 카테고리
                categoryValue = document.getElementById('expenseCategory').value;
                if (categoryValue === '기타') {
                    const categoryCustomInput = document.getElementById('categoryCustom');
                    if (categoryCustomInput && categoryCustomInput.value.trim() !== '') {
                        categoryValue = categoryCustomInput.value.trim();
                    }
                }
            }
            formData.set('mq_category', categoryValue);
            
            // 금액에서 콤마 제거
            const priceWithoutCommas = priceInput.value.replace(/,/g, '');
            formData.set('mq_price', priceWithoutCommas);
            
            // 아이템 ID 설정 (수정 모드인 경우)
            if (mode === 'edit' && id) {
                formData.set('idx', id);
            }
            
            // API 엔드포인트 설정
            let url = '/guidebook/reality-check';
            let method = 'POST';
            
            if (mode === 'edit' && id) {
                url = `/guidebook/reality-check/${id}`;
                formData.append('_method', 'PUT'); // PUT 요청을 위한 _method 필드 추가
            }
            
            // 현재 활성화된 탭 가져오기
            const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
            
            // 디버깅: 전송되는 데이터 확인
            for (let pair of formData.entries()) {
            }
            
            // 데이터 전송
            fetch(url, {
                method: 'POST', // Laravel에서는 항상 POST로 보내고 _method로 구분
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                // 응답 헤더 출력
                for (let [key, value] of response.headers.entries()) {
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    closeModal();
                    loadTabData(activeTab);
                } else {
                    console.error('오류:', data.message);
                    alert('오류가 발생했습니다: ' + data.message);
                }
            })
            .catch(error => {
                console.error('오류 상세:', error);
                alert('오류가 발생했습니다. 개발자 도구의 콘솔을 확인해주세요.');
            });
        };
        
        // 취소 버튼 이벤트
        document.getElementById('cancelButton').onclick = closeModal;
        document.getElementById('closeModal').onclick = closeModal;
    }
    
    // 모달 닫기
    function closeModal() {
        const modal = document.getElementById('modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // 폼 초기화
        document.getElementById('expenseForm').reset();
        
        // 카테고리 선택 목록 초기화
        const expenseCategory = document.getElementById('expenseCategory');
        const incomeCategory = document.getElementById('incomeCategory');
        
        // 지출 카테고리 표시, 수입 카테고리 숨기기 (기본값)
        expenseCategory.classList.remove('hidden');
        incomeCategory.classList.add('hidden');
        expenseCategory.required = true;
        incomeCategory.required = false;
        expenseCategory.selectedIndex = 0;
        incomeCategory.selectedIndex = 0;
        
        // 커스텀 카테고리 필드 숨기기
        const categoryCustomInput = document.getElementById('categoryCustom');
        if (categoryCustomInput) {
            categoryCustomInput.classList.add('hidden');
            categoryCustomInput.value = '';
        }
    }
    
    // 항목 삭제 함수
    function deleteExpense(id) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch(`/guidebook/reality-check/${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                // 현재 활성화된 탭 데이터 다시 로드
                const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
                loadTabData(activeTab);
            } else {
                alert('항목 삭제 중 오류가 발생했습니다.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('항목 삭제 중 오류가 발생했습니다.');
        });
    }
    
    // 항목 추가 버튼 이벤트
    document.getElementById('addExpenseButton').addEventListener('click', function() {
        openExpenseModal('add');
    });
    
    // 수입/지출 라디오 버튼 이벤트 - 선택에 따라 카테고리 목록 변경
    document.getElementsByName('mq_type').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const expenseCategory = document.getElementById('expenseCategory');
            const incomeCategory = document.getElementById('incomeCategory');
            const categoryCustomInput = document.getElementById('categoryCustom');
            
            // 커스텀 카테고리 필드 초기화 및 숨기기
            categoryCustomInput.classList.add('hidden');
            categoryCustomInput.value = '';
            
            if (this.value == '0') { // 지출
                expenseCategory.classList.remove('hidden');
                incomeCategory.classList.add('hidden');
                expenseCategory.required = true;
                incomeCategory.required = false;
                expenseCategory.selectedIndex = 0;
            } else { // 수입
                expenseCategory.classList.add('hidden');
                incomeCategory.classList.remove('hidden');
                expenseCategory.required = false;
                incomeCategory.required = true;
                incomeCategory.selectedIndex = 0;
            }
        });
    });
    
    // 카테고리 선택 이벤트 - 기타 선택 시 커스텀 필드 표시
    document.getElementById('expenseCategory').addEventListener('change', function() {
        const categoryCustomInput = document.getElementById('categoryCustom');
        if (this.value === '기타') {
            categoryCustomInput.classList.remove('hidden');
            categoryCustomInput.focus();
        } else {
            categoryCustomInput.classList.add('hidden');
            categoryCustomInput.value = '';
        }
    });
    
    // 수입 카테고리 선택 이벤트 - 기타수입 선택 시 커스텀 필드 표시
    document.getElementById('incomeCategory').addEventListener('change', function() {
        const categoryCustomInput = document.getElementById('categoryCustom');
        if (this.value === '기타수입') {
            categoryCustomInput.classList.remove('hidden');
            categoryCustomInput.focus();
        } else {
            categoryCustomInput.classList.add('hidden');
            categoryCustomInput.value = '';
        }
    });
    
    // 금액 입력 필드에 천단위 콤마 자동 적용
    document.getElementById('price').addEventListener('input', function(e) {
        // 숫자만 추출
        let value = this.value.replace(/[^\d]/g, '');
        
        // 콤마 추가하여 다시 설정
        if (value) {
            this.value = numberWithCommas(value);
        }
    });

    // ----------------- 샘플 모달 관련 함수 -----------------
    // 연령대 텍스트 변환 함수 추가
    function getAgeRangeText(age) {
        const ageRanges = {
            '10': '10대',
            '2030': '20~30대',
            '3040': '30~40대',
            '4050': '40~50대',
            '5060': '50~60대',
            '60': '60대 이상'
        };
        return ageRanges[age] || '';
    }

    function openSampleModal() {
        const modal = document.getElementById('sampleModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        resetSampleModal();
    }

    // 샘플 모달 닫기
    function closeSampleModal() {
        const modal = document.getElementById('sampleModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        resetSampleModal();
    }

    // 초기화 함수
    function resetSampleModal() {
        selectedGender = null;
        selectedAge = null;
        currentSlide = 0;
        sampleData = [];
        
        document.getElementById('genderStep').classList.remove('hidden');
        document.getElementById('ageStep').classList.add('hidden');
        document.getElementById('sampleStep').classList.add('hidden');
        document.getElementById('progressBar').style.width = '33%';
        
        const slider = document.getElementById('sampleSlider');
        if (slider) {
            slider.innerHTML = '';
            slider.style.transform = 'translateX(0)';
        }
        
        const sampleTitle = document.getElementById('sampleTitle').querySelector('h3');
        if (sampleTitle) {
            sampleTitle.textContent = '';
        }
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

    // 슬라이더 위치 업데이트 함수 수정
    function updateSliderPosition() {
        const slider = document.getElementById('sampleSlider');
        const sampleTitle = document.getElementById('sampleTitle').querySelector('h3');
        
        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        // 타이틀 업데이트
        sampleTitle.textContent = `${getAgeRangeText(selectedAge)} ${selectedGender === 'male' ? '남성' : '여성'} (${currentSlide + 1}/${sampleData.length})`;
        
        // 이전/다음 버튼 상태 업데이트
        const prevButton = document.getElementById('prevSlide');
        const nextButton = document.getElementById('nextSlide');
        
        if (prevButton && nextButton) {
            prevButton.style.visibility = currentSlide === 0 ? 'hidden' : 'visible';
            nextButton.style.visibility = currentSlide === sampleData.length - 1 ? 'hidden' : 'visible';
        }
    }

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
    
    // 성별 선택 처리
    function selectGender(gender) {
        selectedGender = gender;
        updateProgressBar('age');
        showAgeStep();
    }

    // 연령대 선택 처리
    async function selectAge(age) {
        selectedAge = age;
        updateProgressBar('sample');
        await loadSamples();
        showSampleStep();
    }

    // 샘플 데이터 로드
    async function loadSamples() {
        try {
            // 로딩 상태 표시
            const sampleSlider = document.getElementById('sampleSlider');
            sampleSlider.innerHTML = '<div class="w-full flex items-center justify-center py-8"><div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"></div></div>';
            
            // 샘플 데이터 요청
            const response = await fetch(`/guidebook/reality-check/samples?gender=${selectedGender}&age=${selectedAge}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error('샘플 데이터를 가져오는데 실패했습니다.');
            }

            const data = await response.json();
            
            if (Array.isArray(data) && data.length > 0) {
                sampleData = data;
                currentSlide = 0;
                renderSamples();
                
                // 샘플 선택 버튼 활성화
                const selectBtn = document.querySelector('button[onclick="selectSample()"]');
                if (selectBtn) selectBtn.disabled = false;
            } else {
                sampleData = [];
                sampleSlider.innerHTML = '<div class="w-full text-center py-8 text-gray-500">해당 조건에 맞는 샘플 데이터가 없습니다.</div>';
                
                // 샘플 선택 버튼 비활성화
                const selectBtn = document.querySelector('button[onclick="selectSample()"]');
                if (selectBtn) selectBtn.disabled = true;
                
                // 타이틀 업데이트
                const sampleTitle = document.getElementById('sampleTitle').querySelector('h3');
                sampleTitle.textContent = `${getAgeRangeText(selectedAge)} ${selectedGender === 'male' ? '남성' : '여성'} (0/0)`;
            }
        } catch (error) {
            console.error('Error:', error);
            
            const sampleSlider = document.getElementById('sampleSlider');
            sampleSlider.innerHTML = '<div class="w-full text-center py-8 text-red-500">데이터를 불러오는데 실패했습니다.</div>';
            
            // 샘플 선택 버튼 비활성화
            const selectBtn = document.querySelector('button[onclick="selectSample()"]');
            if (selectBtn) selectBtn.disabled = true;
            
            // 에러 메시지 표시
            Swal.fire({
                title: '오류 발생',
                text: error.message || '샘플 데이터를 가져오는데 실패했습니다.',
                icon: 'error',
                confirmButtonText: '확인'
            });
        }
    }

    // 샘플 렌더링
    function renderSamples() {
        const slider = document.getElementById('sampleSlider');
        const sampleTitle = document.getElementById('sampleTitle').querySelector('h3');
        
        slider.innerHTML = '';
        sampleTitle.textContent = `${getAgeRangeText(selectedAge)} ${selectedGender === 'male' ? '남성' : '여성'} (${currentSlide + 1}/${sampleData.length})`;
        
        sampleData.forEach((sample, index) => {
            const slide = document.createElement('div');
            slide.className = 'w-full flex-shrink-0 px-2';
            slide.innerHTML = `
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-center mb-2">
                        <h4 class="text-base font-bold text-dark">${sample.name}</h4>
                        <p class="text-xs text-gray-600">${sample.description || ''}</p>
                    </div>
                    
                    <div class="flex flex-col max-h-[400px] overflow-y-auto">
                        <!-- 지출 항목 -->
                        <div class="mb-3">
                            <h5 class="font-bold text-red-600 text-sm mb-1">지출</h5>
                            <div class="space-y-1 text-sm">
                                ${Object.entries(sample.expenses || {}).map(([category, amount]) => `
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700">${category}</span>
                                        <span class="font-medium">${numberWithCommas(amount)}원</span>
                                    </div>
                                `).join('')}
                                ${Object.keys(sample.expenses || {}).length === 0 ? '<div class="text-gray-500 text-center py-1">데이터 없음</div>' : ''}
                            </div>
                        </div>
                        
                        <!-- 수입 항목 -->
                        <div>
                            <h5 class="font-bold text-green-600 text-sm mb-1">수입</h5>
                            <div class="space-y-1 text-sm">
                                ${Array.isArray(sample.income) && sample.income.length > 0 ? 
                                    sample.income.map(item => `
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-700">${item.category}</span>
                                            <span class="font-medium">${numberWithCommas(item.price)}원</span>
                                        </div>
                                    `).join('') : 
                                    '<div class="text-gray-500 text-center py-1">데이터 없음</div>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
            slider.appendChild(slide);
        });
        
        updateSliderPosition();
    }

    // 샘플 화면 표시 함수
    function showSampleStep() {
        document.getElementById('genderStep').classList.add('hidden');
        document.getElementById('ageStep').classList.add('hidden');
        document.getElementById('sampleStep').classList.remove('hidden');
    }

    document.querySelector('.sample-modal-button').addEventListener('click', openSampleModal);
    document.querySelector('.close-sample-modal').addEventListener('click', closeSampleModal);
    document.querySelector('.reset-selection').addEventListener('click', resetSelection);
    document.querySelector('.select-sample').addEventListener('click', selectSample);

    document.querySelectorAll('[data-gender]').forEach(button => {
        button.addEventListener('click', function() {
            selectGender(this.dataset.gender);
        });
    });

    document.querySelectorAll('[data-age]').forEach(button => {
        button.addEventListener('click', function() {
            selectAge(this.dataset.age);
        });
    });

    // 샘플 선택 초기화 함수
    function resetSelection() {
        // 샘플 단계 화면 숨기기
        document.getElementById('sampleStep').classList.add('hidden');
        // 성별 선택 화면 표시
        document.getElementById('genderStep').classList.remove('hidden');
        document.getElementById('ageStep').classList.add('hidden');
        
        // 프로그레스 바 초기화
        document.getElementById('progressBar').style.width = '33%';
        
        // 상태 변수 초기화
        selectedGender = null;
        selectedAge = null;
        currentSlide = 0;
        sampleData = [];
        
        // 샘플 슬라이더 초기화
        const sampleSlider = document.getElementById('sampleSlider');
        if (sampleSlider) {
            sampleSlider.innerHTML = '';
        }
    }

    // 샘플 선택 및 적용
    function selectSample() {
        if (currentSlide < 0 || currentSlide >= sampleData.length) {
            alert('유효하지 않은 샘플입니다.');
            return;
        }

        // LoadingManager를 사용하여 로딩 표시
        LoadingManager.show();
        
        // API 호출
        fetch('/guidebook/reality-check/apply-sample', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                gender: selectedGender,
                age: selectedAge,
                sampleIndex: currentSlide
            })
        })
        .then(response => response.json())
        .then(data => {
            // 로딩 숨기기
            LoadingManager.hide();
            
            if (data.status === 'success') {
                alert('샘플 데이터가 성공적으로 적용되었습니다.');
                // 모달 닫기
                closeSampleModal();

                //페이지 새로고침
                location.reload();
            } else {
                alert(data.message || '샘플 데이터 적용 중 오류가 발생했습니다.');
            }
        })
        .catch(error => {
            // 로딩 숨기기
            LoadingManager.hide();
            console.error('Error:', error);
            alert('네트워크 오류가 발생했습니다. 다시 시도해주세요.');
        });
    }

    // 슬라이더 이전/다음 버튼 이벤트 리스너 추가
    document.getElementById('prevSlide').addEventListener('click', function() {
        if (currentSlide > 0) {
            currentSlide--;
            updateSliderPosition();
        }
    });

    document.getElementById('nextSlide').addEventListener('click', function() {
        if (currentSlide < sampleData.length - 1) {
            currentSlide++;
            updateSliderPosition();
        }
    });

});
</script>
<style>
/* 탭 스타일링 */
.tab-button {
    color: #6B7280;
    border-color: transparent;
    transition: all 0.3s ease;
}

.tab-button:hover {
    color: #111827;
}

.tab-button.active {
    color: #111827;
    border-color: #111827;
}

.tab-content.hidden {
    display: none;
}

.tab-content {
    display: block;
}

/* 반응형 스타일링 */
@media (max-width: 768px) {
    .tab-button {
        font-size: 0.875rem;
    }
}
</style>
@endpush
@endsection