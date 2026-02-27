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
<div class="fab-wrap">
    <!-- TOP 버튼 (스크롤 시 자동 등장) -->
    <button id="floatingBtn" class="fab-btn fab-secondary fab-top-hidden" aria-label="맨 위로 스크롤">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 4l-8 8h5v8h6v-8h5z"/>
        </svg>
        <span class="fab-text">TOP</span>
    </button>

    <!-- 상담 버튼 (항상 표시) -->
    <a href="http://pf.kakao.com/_xlEbJn/" target="_blank" class="fab-btn fab-primary" aria-label="카카오톡 상담">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 5.58 2 10c0 2.82 1.86 5.29 4.63 6.71L5.72 20.4c-.1.36.28.64.6.44l4.17-2.57c.49.05.99.08 1.51.08 5.52 0 10-3.58 10-8S17.52 2 12 2zm-2.5 9.5a1 1 0 110-2 1 1 0 010 2zm5 0a1 1 0 110-2 1 1 0 010 2z"/>
        </svg>
        <span class="fab-text">상담</span>
    </a>
</div>

<style>
    .fab-wrap {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 50;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.6rem;
    }

    .fab-btn {
        width: 3.2rem;
        height: 3.2rem;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .fab-text {
        font-size: 0.6rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        line-height: 1;
        font-family: 'Outfit', sans-serif;
    }

    /* 상담 버튼 - 코랄 레드 그래디언트 */
    .fab-primary {
        background: linear-gradient(135deg, #FF4D4D 0%, #FF6B6B 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(255, 77, 77, 0.35), 0 1px 3px rgba(0, 0, 0, 0.1);
        opacity: 0.85;
    }

    .fab-primary:hover {
        opacity: 1;
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(255, 77, 77, 0.45), 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    /* TOP 버튼 - 다크 글래스 */
    .fab-secondary {
        background: rgba(45, 48, 71, 0.8);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2), 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .fab-secondary:hover {
        background: rgba(45, 48, 71, 0.95);
        transform: scale(1.1);
        border-color: rgba(255, 77, 77, 0.3);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25), 0 0 12px rgba(255, 77, 77, 0.15);
    }

    /* TOP 버튼 숨김/표시 애니메이션 */
    .fab-top-hidden {
        opacity: 0;
        transform: translateY(10px) scale(0.8);
        pointer-events: none;
    }

    .fab-top-visible {
        opacity: 0.85;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* 모바일 */
    @media (max-width: 640px) {
        .fab-wrap {
            bottom: 1.5rem;
            right: 1.5rem;
            gap: 0.5rem;
        }

        .fab-btn {
            width: 2.8rem;
            height: 2.8rem;
        }

        .fab-btn svg { width: 14px; height: 14px; }
        .fab-text { font-size: 0.5rem; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const floatingBtn = document.getElementById('floatingBtn');

        // TOP 클릭 → 맨 위로 스크롤
        floatingBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // 스크롤 위치에 따라 TOP 버튼 자동 표시/숨김
        function updateTopButton() {
            if (window.scrollY > 300) {
                floatingBtn.classList.remove('fab-top-hidden');
                floatingBtn.classList.add('fab-top-visible');
            } else {
                floatingBtn.classList.remove('fab-top-visible');
                floatingBtn.classList.add('fab-top-hidden');
            }
        }

        window.addEventListener('scroll', updateTopButton, { passive: true });
        updateTopButton();
    });
</script> 