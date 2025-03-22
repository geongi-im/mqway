<footer class="bg-point py-4 mt-auto">
    <div class="container mx-auto text-center">
        <div class="mb-4 space-x-4">
            <a href="{{ route('service') }}" class="text-cdark hover:text-cgray text-sm transition-colors duration-200">서비스이용약관</a>
            <a href="{{ route('privacy') }}" class="text-cdark hover:text-cgray text-sm transition-colors duration-200">개인정보처리방침</a>
        </div>
        <p class="text-cdark text-sm">&copy;{{ date('Y') }} {{ config('app.name', 'MQWAY') }} All rights reserved.</p>
    </div>
</footer>

<!-- 우측 하단 고정 버튼 -->
<div class="fixed-button">
    <button id="floatingBtn" class="bg-secondary hover:bg-secondary/90 text-cdark rounded-full shadow-lg flex flex-col items-center justify-center transition-all duration-300 hover:scale-105 opacity-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 11l7-7 7 7M5 19l7-7 7 7" />
        </svg>
        <span class="text-xs font-bold">TOP</span>
    </button>
</div>

<style>
    .fixed-button {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 50;
    }
    
    .fixed-button button {
        width: 3.5rem;
        height: 3.5rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    @media (max-width: 640px) {
        .fixed-button {
            bottom: 1.5rem;
            right: 1.5rem;
        }
        
        .fixed-button button {
            width: 3rem;
            height: 3rem;
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