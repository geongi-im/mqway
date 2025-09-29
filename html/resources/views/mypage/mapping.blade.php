@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- 페이지 헤더 (중앙 정렬) -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-point mb-2">MQ 매핑</h1>
            <p class="text-secondary">나의 현재와 미래를 연결하는 꿈의 지도를 만들어보세요</p>
            <p class="text-sm text-gray-500 mt-1">원하는 미래의 모습이나 목표를 선택하여 나만의 매핑을 완성해보세요</p>
        </div>

        <!-- 선택된 아이템 카운터 -->
        <div class="bg-white rounded-lg p-4 mb-6 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-sm text-secondary">선택된 목표:</span>
                    <span id="selected-count" class="text-lg font-bold text-point ml-2">0</span>
                    <span class="text-sm text-secondary">개</span>
                </div>
                <div class="space-x-3">
                    <button id="clear-all" class="text-sm text-gray-500 hover:text-red-500 transition-colors">전체 해제</button>
                    <button id="save-mapping" class="bg-point1 text-white px-4 py-2 rounded-md hover:bg-opacity-90 transition-colors">
                        나의 매핑 저장
                    </button>
                </div>
            </div>
        </div>

        <!-- 카테고리 필터 -->
        <div class="bg-white rounded-lg p-4 mb-6 shadow-sm">
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $key => $label)
                <button class="category-filter {{ $key === 'all' ? 'active bg-point1 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} px-4 py-2 rounded-full text-sm" data-category="{{ $key }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- 매핑 그리드 -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="mapping-grid">
                @foreach($mappingItems as $item)
                <div class="mapping-item relative group cursor-pointer" data-category="{{ $item['category'] }}" data-id="{{ $item['id'] }}">
                    <div class="aspect-square rounded-lg overflow-hidden bg-white shadow-sm border border-gray-100 relative">
                        <!-- 이미지 (전체 영역) -->
                        <img src="{{ $item['image'] }}" alt="{{ $item['description'] }}" 
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                        
                        <!-- 하단 그라데이션 오버레이 (텍스트 가독성을 위한) -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                        
                        <!-- 텍스트 오버레이 -->
                        <div class="absolute bottom-0 left-0 right-0 p-3 text-center">
                            <div class="text-sm font-medium text-white leading-tight drop-shadow-lg">{{ $item['description'] }}</div>
                        </div>
                    </div>
                    <input type="checkbox" class="mapping-checkbox absolute top-2 right-2 w-5 h-5 text-point1 bg-white border-2 border-gray-300 rounded focus:ring-point1 shadow-sm">
                    <div class="absolute inset-0 bg-point1 bg-opacity-20 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg"></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilters = document.querySelectorAll('.category-filter');
    const mappingItems = document.querySelectorAll('.mapping-item');
    const mappingCheckboxes = document.querySelectorAll('.mapping-checkbox');
    const selectedCountEl = document.getElementById('selected-count');
    const clearAllBtn = document.getElementById('clear-all');
    const saveMappingBtn = document.getElementById('save-mapping');
    
    let selectedItems = [];

    // 카테고리 필터 기능
    categoryFilters.forEach(filter => {
        filter.addEventListener('click', () => {
            const category = filter.getAttribute('data-category');
            
            // 버튼 활성화 상태 변경
            categoryFilters.forEach(btn => {
                btn.classList.remove('active', 'bg-point1', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-600');
            });
            filter.classList.add('active', 'bg-point1', 'text-white');
            filter.classList.remove('bg-gray-100', 'text-gray-600');

            // 아이템 필터링
            mappingItems.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                if (category === 'all' || category === itemCategory) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // 체크박스 선택 기능
    mappingCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const item = this.closest('.mapping-item');
            const itemId = item.getAttribute('data-id');
            
            if (this.checked) {
                selectedItems.push(itemId);
                item.classList.add('selected');
            } else {
                selectedItems = selectedItems.filter(id => id !== itemId);
                item.classList.remove('selected');
            }
            
            updateSelectedCount();
        });
    });

    // 아이템 클릭으로 체크박스 토글
    mappingItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (!e.target.classList.contains('mapping-checkbox')) {
                const checkbox = this.querySelector('.mapping-checkbox');
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });

    // 전체 해제 버튼
    clearAllBtn.addEventListener('click', () => {
        mappingCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        mappingItems.forEach(item => {
            item.classList.remove('selected');
        });
        selectedItems = [];
        updateSelectedCount();
    });

    // 매핑 저장 버튼
    saveMappingBtn.addEventListener('click', () => {
        if (selectedItems.length === 0) {
            alert('선택된 목표가 없습니다. 먼저 원하는 목표를 선택해주세요.');
            return;
        }
        
        console.log('저장될 매핑:', selectedItems);
        alert(`${selectedItems.length}개의 목표가 선택되었습니다. 나의 MQ 매핑이 저장되었습니다!`);
        
        // 실제 구현 시 AJAX로 서버에 저장
    });

    function updateSelectedCount() {
        selectedCountEl.textContent = selectedItems.length;
    }
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
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
}

.mapping-item.selected .mapping-checkbox {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

.mapping-checkbox {
    transition: all 0.2s ease;
    z-index: 10;
}

.mapping-checkbox:checked {
    background-color: #3B82F6;
    border-color: #3B82F6;
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