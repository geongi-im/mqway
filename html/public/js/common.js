// 전역 로딩 관리자
const LoadingManager = {
    init() { 
        // 로딩 컨테이너가 없으면 생성
        if (!document.getElementById('globalLoadingContainer')) {
            const loadingHTML = `
                <div id="globalLoadingContainer" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
                    <div class="bg-white rounded-lg p-4">
                        <svg class="animate-spin h-10 w-10 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', loadingHTML);
        }
    },

    show() {
        this.init();
        const loader = document.getElementById('globalLoadingContainer');
        loader.classList.remove('hidden');
        loader.classList.add('flex');
        document.body.style.overflow = 'hidden';
    },

    hide() {
        const loader = document.getElementById('globalLoadingContainer');
        if (loader) {
            loader.classList.add('hidden');
            loader.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }
};

// 전역으로 사용할 수 있도록 window 객체에 추가
window.LoadingManager = LoadingManager;
