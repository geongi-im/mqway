@extends('layouts.app')

@section('content')
<!-- ===== Header Section ===== -->
<div class="relative bg-[#3D4148] py-12 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-3xl font-bold text-white mb-2">포트폴리오 수정</h1>
        <p class="text-gray-400 text-sm">등록된 포트폴리오 정보를 수정합니다.</p>
    </div>
</div>

<div class="min-h-screen bg-gray-50 -mt-8 pb-20 relative z-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 md:p-10">
                <form action="{{ route('board-portfolio.update', $post->idx) }}" method="POST" class="space-y-8" id="portfolioEditForm">
                    @csrf
                    @method('PUT')

                    <!-- 투자자 코드 -->
                    <div class="space-y-2">
                        <label for="mq_investor_code" class="text-sm font-semibold text-[#2D3047] block">투자자 코드 <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="mq_investor_code" 
                               id="mq_investor_code" 
                               value="{{ old('mq_investor_code', $post->mq_investor_code) }}"
                               class="w-full h-12 px-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FFD700] focus:ring-2 focus:ring-[#FFD700]/20 transition-all bg-gray-50 placeholder-gray-400 font-medium @error('mq_investor_code') border-red-500 @enderror"
                               placeholder="투자자 코드를 입력하세요 (예: BRK, AAPL)"
                               required>
                        @error('mq_investor_code')
                            <p class="text-sm text-red-500 flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 제목 -->
                    <div class="space-y-2">
                        <label for="mq_title" class="text-sm font-semibold text-[#2D3047] block">제목 <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="mq_title" 
                               id="mq_title" 
                               value="{{ old('mq_title', $post->mq_title) }}"
                               class="w-full h-12 px-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FFD700] focus:ring-2 focus:ring-[#FFD700]/20 transition-all bg-gray-50 placeholder-gray-400 font-medium @error('mq_title') border-red-500 @enderror"
                               placeholder="제목을 입력하세요"
                               required>
                        @error('mq_title')
                            <p class="text-sm text-red-500 flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 포트폴리오 ID -->
                    <div class="space-y-2">
                        <label for="mq_portfolio_idx" class="text-sm font-semibold text-[#2D3047] block">포트폴리오 ID</label>
                        <input type="number" 
                               name="mq_portfolio_idx" 
                               id="mq_portfolio_idx" 
                               value="{{ old('mq_portfolio_idx', $post->mq_portfolio_idx) }}"
                               class="w-full h-12 px-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FFD700] focus:ring-2 focus:ring-[#FFD700]/20 transition-all bg-gray-50 placeholder-gray-400 font-medium @error('mq_portfolio_idx') border-red-500 @enderror"
                               placeholder="포트폴리오 ID를 입력하세요">
                        @error('mq_portfolio_idx')
                            <p class="text-sm text-red-500 flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 버튼 영역 -->
                    <div class="flex items-center justify-between pt-8 border-t border-gray-100 mt-8">
                        <button type="button" 
                                onclick="if(confirm('수정 중인 내용이 저장되지 않고 사라집니다.\n이전 페이지로 돌아가시겠습니까?')) { location.href='{{ route('board-portfolio.show', $post->idx) }}'; }"
                                class="inline-flex items-center justify-center px-6 h-12 text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            취소
                        </button>
                        <button type="submit" 
                                id="submitButton"
                                class="inline-flex items-center justify-center px-8 h-12 bg-[#2D3047] text-white rounded-xl hover:bg-[#3D4148] transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-bold text-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            수정 완료
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('portfolioEditForm');
        const submitButton = document.getElementById('submitButton');
        
        form.addEventListener('submit', function(e) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> 수정 중...';
            submitButton.classList.add('opacity-75');
            
            if (typeof LoadingManager !== 'undefined') {
                LoadingManager.show();
            }
            return true;
        });
    });
</script>
@endpush
@endsection