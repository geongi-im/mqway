@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-8">새 게시글 작성</h1>

            <form action="{{ route('board-portfolio.store') }}" method="POST" class="space-y-6" id="portfolioForm">
                @csrf

                <!-- 투자자 코드 -->
                <div class="space-y-2">
                    <label for="mq_investor_code" class="block text-sm font-medium text-gray-700">투자자 코드</label>
                    <input type="text" 
                           name="mq_investor_code" 
                           id="mq_investor_code" 
                           value="{{ old('mq_investor_code') }}"
                           class="w-full h-12 px-4 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('mq_investor_code') border-red-500 @enderror"
                           placeholder="투자자 코드를 입력하세요 (예: BRK, AAPL)"
                           required>
                    @error('mq_investor_code')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 제목 -->
                <div class="space-y-2">
                    <label for="mq_title" class="block text-sm font-medium text-gray-700">제목</label>
                    <input type="text" 
                           name="mq_title" 
                           id="mq_title" 
                           value="{{ old('mq_title') }}"
                           class="w-full h-12 px-4 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('mq_title') border-red-500 @enderror"
                           placeholder="제목을 입력하세요"
                           required>
                    @error('mq_title')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 포트폴리오 ID -->
                <div class="space-y-2">
                    <label for="mq_portfolio_idx" class="block text-sm font-medium text-gray-700">포트폴리오 ID</label>
                    <input type="number" 
                           name="mq_portfolio_idx" 
                           id="mq_portfolio_idx" 
                           value="{{ old('mq_portfolio_idx') }}"
                           class="w-full h-12 px-4 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('mq_portfolio_idx') border-red-500 @enderror"
                           placeholder="포트폴리오 ID를 입력하세요"
                           required>
                    <p class="text-sm text-gray-500 mt-1">
                        유효한 포트폴리오 ID를 입력해야 합니다. ID가 잘못된 경우 게시글이 등록되지 않습니다.
                    </p>
                    @error('mq_portfolio_idx')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 버튼 영역 -->
                <div class="flex justify-end gap-3 pt-6">
                    <a href="{{ route('board-portfolio.index') }}" 
                       class="inline-flex items-center justify-center px-6 h-12 border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700">
                        취소
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-6 h-12 bg-point text-cdark rounded-xl hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-dark focus:ring-offset-2 transition-all"
                            id="submitButton">
                        작성하기
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('portfolioForm');
        const submitButton = document.getElementById('submitButton');
        
        form.addEventListener('submit', function(e) {
            // 제출 버튼 비활성화 (중복 제출 방지)
            submitButton.disabled = true;
            submitButton.innerHTML = '제출 중...';
            submitButton.classList.add('opacity-75');
            
            // 폼 제출 (기본 동작 유지)
            return true;
        });
    });
</script>
@endpush
@endsection 