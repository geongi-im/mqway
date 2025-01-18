@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- 상단 타이틀 및 설명 -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-dark mb-2">원하는 삶 찾기</h1>
        <p class="text-gray-600">나의 삶의 목표와 꿈을 정리해보세요</p>
    </div>

    <!-- 추가 버튼 -->
    <div class="mb-8 flex justify-end">
        <button id="addButton" class="bg-dark text-secondary px-6 py-2 rounded-lg hover:bg-dark/90 transition-colors duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            추가하기
        </button>
    </div>

    @foreach($types as $type)
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6">{{ $type }}</h2>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-dark">
                            <th class="px-6 py-4 text-left text-secondary font-semibold">카테고리</th>
                            <th class="px-6 py-4 text-left text-secondary font-semibold">항목</th>
                            <th class="px-6 py-4 text-left text-secondary font-semibold">예상비용</th>
                            <th class="px-6 py-4 text-left text-secondary font-semibold">예상소요시간</th>
                            <th class="px-6 py-4 text-left text-secondary font-semibold w-24">작업</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lifeSearches[$type] as $item)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">{{ $item->mq_category }}</td>
                            <td class="px-6 py-4">{{ $item->mq_content }}</td>
                            <td class="px-6 py-4">{{ number_format($item->mq_price) }}원</td>
                            <td class="px-6 py-4">{{ $item->mq_expected_time }}</td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button class="text-dark hover:text-dark/70 transition-colors duration-200 edit-button" 
                                            data-id="{{ $item->idx }}"
                                            data-type="{{ $item->mq_type }}"
                                            data-category="{{ $item->mq_category }}"
                                            data-content="{{ $item->mq_content }}"
                                            data-price="{{ $item->mq_price }}"
                                            data-time="{{ $item->mq_expected_time }}">
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
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                데이터가 없습니다.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- 모달 -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-dark" id="modalTitle">추가하기</h2>
            <button type="button" id="closeModal" class="text-gray-500 hover:text-gray-700">
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
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="mq_type">
                        유형
                    </label>
                    <select id="mq_type" name="mq_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark">
                        <option value="want_to_do">하고 싶은 것</option>
                        <option value="want_to_go">가고 싶은 곳</option>
                        <option value="want_to_share">나누고 싶은 것</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="mq_category">
                        카테고리
                    </label>
                    <input type="text" id="mq_category" name="mq_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="mq_content">
                        항목
                    </label>
                    <input type="text" id="mq_content" name="mq_content" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="mq_price">
                        예상비용
                    </label>
                    <input type="number" id="mq_price" name="mq_price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="mq_expected_time">
                        예상소요시간
                    </label>
                    <input type="text" id="mq_expected_time" name="mq_expected_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="cancelButton" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    취소
                </button>
                <button type="submit" class="px-4 py-2 bg-dark text-secondary rounded-lg hover:bg-dark/90 transition-colors duration-200">
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
    const form = document.getElementById('lifeSearchForm');
    const addButton = document.getElementById('addButton');
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

    addButton.addEventListener('click', () => {
        modalTitle.textContent = '추가하기';
        openModal();
    });

    cancelButton.addEventListener('click', closeModalHandler);
    closeModal.addEventListener('click', closeModalHandler);

    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', () => {
            const data = button.dataset;
            
            document.getElementById('itemId').value = data.id;
            document.getElementById('mq_type').value = data.type;
            document.getElementById('mq_category').value = data.category;
            document.getElementById('mq_content').value = data.content;
            document.getElementById('mq_price').value = data.price;
            document.getElementById('mq_expected_time').value = data.time;
            
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
                const response = await fetch(`/guidebook/life-search/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('삭제 중 오류가 발생했습니다.');
            }
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const id = document.getElementById('itemId').value;
        
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
            }
        } catch (error) {
            console.error('Error:', error);
            alert('저장 중 오류가 발생했습니다.');
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