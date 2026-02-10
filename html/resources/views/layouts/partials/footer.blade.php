<footer class="bg-[#2A2D33] text-white py-10 px-4 md:py-12 md:px-8 border-t border-white/5 mt-auto">
    <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex flex-wrap justify-center gap-6 md:gap-8 text-sm text-white/60 font-medium">
            <a href="{{ route('service') }}" class="hover:text-white transition-colors">이용약관</a>
            <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">개인정보처리방침</a>
            <a href="http://pf.kakao.com/_xlEbJn/" target="_blank" class="hover:text-white transition-colors">문의하기</a>
        </div>
        <div class="text-xs text-white/30 font-light">
            &copy; {{ date('Y') }} MQWAY. All rights reserved.
        </div>
    </div>
</footer>

<!-- 우측 하단 고정 버튼 -->
<div class="fixed-button">
    <div class="flex flex-col space-y-3">
        <!-- TOP 버튼 (스크롤 시 표시) -->
        <button id="floatingBtn" class="bg-point2 hover:bg-point2/90 text-cdark rounded-full shadow-lg flex flex-col items-center justify-center transition-all duration-300 hover:scale-105 opacity-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 11l7-7 7 7M5 19l7-7 7 7" />
            </svg>
            <span class="text-sm font-bold">TOP</span>
        </button>

        <!-- 고객센터 버튼 (항상 표시) -->
        <a href="http://pf.kakao.com/_xlEbJn/" target="_blank" class="bg-point2 hover:bg-point2/90 text-cdark rounded-full shadow-lg flex flex-col items-center justify-center transition-all duration-300 hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <span class="text-sm font-bold">상담</span>
        </a>
    </div>
</div>

<style>
    .fixed-button {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 50;
    }
    
    .fixed-button button, .fixed-button a {
        width: 4rem;
        height: 4rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    @media (max-width: 640px) {
        .fixed-button {
            bottom: 1.5rem;
            right: 1.5rem;
        }
        
        .fixed-button button, .fixed-button a {
            width: 3.5rem;
            height: 3.5rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const floatingBtn = document.getElementById('floatingBtn');
        
        // 버튼 클릭 시 맨 위로 스크롤
        floatingBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // 스크롤 위치에 따라 버튼 표시/숨김 제어
        function updateButtonVisibility() {
            if (window.scrollY > 300) {
                floatingBtn.classList.remove('opacity-0', 'pointer-events-none');
                floatingBtn.classList.add('opacity-100');
            } else {
                floatingBtn.classList.add('opacity-0');
                floatingBtn.classList.remove('opacity-100');
            }
        }
        
        // 스크롤 이벤트 리스너 등록
        window.addEventListener('scroll', updateButtonVisibility);
        
        // 페이지 로드 시 즉시 버튼 상태 업데이트
        updateButtonVisibility();
    });
</script> 