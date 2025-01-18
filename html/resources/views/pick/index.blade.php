@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary flex flex-col">
    @if (!$hasStarted)
    <!-- 시작 화면 -->
    <div class="flex-1 flex flex-col items-center justify-center p-4">
        <h1 class="text-4xl font-bold text-dark mb-8">나의 성향을 알아보세요</h1>
        <button id="startBtn" class="bg-dark text-white px-8 py-4 rounded-full text-xl font-medium hover:bg-opacity-90 transition-all transform hover:scale-105 active:scale-95">
            시작하기
        </button>
    </div>
    @else
    <!-- 선택 화면 -->
    <div class="flex-1 flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-4xl">
            <!-- 선택 항목 -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-4 mb-8">
                @if ($currentItem)
                    <!-- 첫 번째 선택지 -->
                    <div class="pick-up-item w-full md:w-[350px] h-[180px] bg-white rounded-xl shadow-lg p-6 cursor-pointer transition-all hover:scale-105 hover:shadow-xl flex items-center justify-center"
                         data-selected="false" 
                         data-question-id="{{ $currentItem->questionPair->questionA->id }}">
                        <h3 class="text-xl text-dark text-center font-light leading-relaxed">
                            {{ $currentItem->questionPair->questionA->question_text }}
                        </h3>
                    </div>

                    <!-- VS 배지 -->
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-dark text-white rounded-full flex items-center justify-center text-xl md:text-2xl font-bold">
                        VS
                    </div>

                    <!-- 두 번째 선택지 -->
                    <div class="pick-up-item w-full md:w-[350px] h-[180px] bg-white rounded-xl shadow-lg p-6 cursor-pointer transition-all hover:scale-105 hover:shadow-xl flex items-center justify-center"
                         data-selected="false" 
                         data-question-id="{{ $currentItem->questionPair->questionB->id }}">
                        <h3 class="text-xl text-dark text-center font-light leading-relaxed">
                            {{ $currentItem->questionPair->questionB->question_text }}
                        </h3>
                    </div>
                @endif
            </div>

            <!-- 네비게이션 버튼 -->
            <div class="flex items-center justify-center gap-6">
                <button id="prevBtn" class="px-6 py-2 bg-dark text-white rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed" 
                        {{ $currentPage == 0 ? 'disabled' : '' }}>
                    이전
                </button>
                <span class="text-dark font-medium">
                    {{ $currentPage + 1 }} / {{ $progress['total'] }}
                </span>
                <button id="nextBtn" class="px-6 py-2 bg-dark text-white rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed" 
                        disabled>
                    다음
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
let selectedQuestionId = null;
let currentResultId = {{ $currentItem->id ?? 'null' }};

function selectItem(questionId, element) {
    // 선택 항목 스타일 변경
    document.querySelectorAll('.pick-up-item').forEach(item => {
        item.classList.remove('ring-4', 'ring-dark', 'scale-105');
        item.classList.add('opacity-50');
    });
    element.classList.remove('opacity-50');
    element.classList.add('ring-4', 'ring-dark', 'scale-105');
    
    selectedQuestionId = questionId;
    document.getElementById('nextBtn').disabled = false;
}

document.addEventListener('DOMContentLoaded', function() {
    // 시작 버튼 이벤트
    const startBtn = document.getElementById('startBtn');
    if (startBtn) {
        startBtn.addEventListener('click', function() {
            fetch('{{ route("pick.start") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.reload();
                } else {
                    alert('시작에 실패했습니다.');
                }
            });
        });
    }

    // 선택 항목 이벤트
    document.querySelectorAll('.pick-up-item').forEach(item => {
        item.addEventListener('click', function() {
            selectItem(this.dataset.questionId, this);
        });
    });

    // 다음 버튼 이벤트
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (!selectedQuestionId) {
                alert('항목을 선택해��세요.');
                return;
            }

            fetch('{{ route("pick.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    result_idx: currentResultId,
                    selected_id: selectedQuestionId
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    if (result.completed) {
                        alert('모든 선택을 완료했습니다!');
                        window.location.href = '/';
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('저장에 실패했습니다.');
                }
            });
        });
    }

    // 이전 버튼 이벤트
    const prevBtn = document.getElementById('prevBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (!currentResultId) return;

            fetch('{{ route("pick.prev") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    result_idx: currentResultId
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.reload();
                } else {
                    alert('이전 페이지로 이동하는데 실패했습니다.');
                }
            });
        });
    }
});
</script>
@endpush
@endsection 