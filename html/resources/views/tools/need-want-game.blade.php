@extends('layouts.app')

@section('title', 'Need or Want? 게임')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Gaegu:wght@400;700&display=swap" rel="stylesheet">
<style>
/* Game Modal Animations that aren't native to Tailwind */
.nw-card-enter { animation: nwCardIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) both; }
@keyframes nwCardIn {
    0% { opacity: 0; transform: translateY(20px) scale(0.95); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.nw-emoji-pop { animation: nwEmojiPop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
@keyframes nwEmojiPop {
    0% { transform: scale(0); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endpush

@section('content')
<!-- ===== Hero Background ===== -->
<div class="relative bg-[#3D4148] pb-32 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
    </div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#FF4D4D] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>

    <div class="relative z-10 container mx-auto px-4 pt-12 pb-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 text-white/90 py-1.5 px-4 rounded-full text-sm font-medium mb-4 border border-white/10 backdrop-blur-md animate-fadeIn">
            <span>🛒</span> <span>학습 도구</span>
        </div>
        <h1 class="font-outfit text-3xl md:text-5xl font-extrabold text-white mb-4 tracking-tight animate-slideUp" style="animation-delay: 0.1s;">
            Need or <span class="text-[#FF6B6B]">Want</span><span class="text-[#4ECDC4]">?</span>
        </h1>
        <p class="text-white/70 text-base md:text-lg max-w-2xl mx-auto leading-relaxed animate-slideUp" style="animation-delay: 0.2s;">
            물건을 보고 "꼭 필요한 것"인지 "갖고 싶은 것"인지 생각해보세요!
        </p>
    </div>
</div>

<!-- ===== Main Content ===== -->
<div class="relative z-20 -mt-24 pb-16">
    <div class="container mx-auto px-4 max-w-4xl">

        <!-- 게임 소개 카드 -->
        <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6 animate-slideUp" style="animation-delay: 0.3s;">
            <h2 class="text-xl font-bold text-[#2D3047] mb-5 flex items-center gap-2">
                <span class="w-8 h-8 bg-gradient-to-br from-[#4ECDC4] to-[#26D0CE] rounded-lg flex items-center justify-center text-white text-sm">📋</span>
                게임 방법
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-7 h-7 bg-[#FF4D4D] text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                    <p class="text-gray-600 text-sm leading-relaxed">물건의 이미지와 설명을 확인하세요.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-7 h-7 bg-[#FFB347] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                    <p class="text-gray-600 text-sm leading-relaxed"><strong>Need</strong>(꼭 필요해요) 또는 <strong>Want</strong>(갖고 싶어요)를 선택하세요.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-7 h-7 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    <p class="text-gray-600 text-sm leading-relaxed">왜 그렇게 생각하는지 이유를 적어주세요.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-7 h-7 bg-[#7C5CFC] text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                    <p class="text-gray-600 text-sm leading-relaxed">총 <strong>5개</strong>의 물건을 평가하면 결과를 확인할 수 있어요!</p>
                </div>
            </div>

            <div class="text-center">
                <button id="startGameBtn" class="bg-gradient-to-r from-[#FF4D4D] to-[#FF6B6B] hover:from-[#FF3333] hover:to-[#FF4D4D] text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 text-lg shadow-[0_8px_25px_rgba(255,77,77,0.35)] hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(255,77,77,0.45)]">
                    🎮 게임 시작하기
                </button>
            </div>
        </div>

        <!-- 기능 소개 카드 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-slideUp" style="animation-delay: 0.4s;">
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center group hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-gradient-to-br from-[#4ECDC4]/20 to-[#4ECDC4]/5 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <span class="text-2xl">🤔</span>
                </div>
                <h3 class="text-base font-bold text-[#2D3047] mb-2">생각하는 힘</h3>
                <p class="text-gray-500 text-sm leading-relaxed">필요와 욕구의 차이를 직접 판단해보세요</p>
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center group hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-gradient-to-br from-[#FF4D4D]/20 to-[#FF4D4D]/5 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <span class="text-2xl">✍️</span>
                </div>
                <h3 class="text-base font-bold text-[#2D3047] mb-2">이유 말하기</h3>
                <p class="text-gray-500 text-sm leading-relaxed">나만의 이유를 적으며 논리적 사고를 키워요</p>
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center group hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-gradient-to-br from-[#FFB347]/20 to-[#FFB347]/5 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <span class="text-2xl">📊</span>
                </div>
                <h3 class="text-base font-bold text-[#2D3047] mb-2">결과 확인</h3>
                <p class="text-gray-500 text-sm leading-relaxed">내가 선택한 결과를 한눈에 확인해보세요</p>
            </div>
        </div>

    </div>
</div>

<!-- ===== 게임 모달 ===== -->
<div id="gameModal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
    <div class="absolute inset-0 bg-slate-900/75 backdrop-blur-md"></div>

    <!-- 콘텐츠 컨테이너 영역 -->
    <div class="fixed inset-0 overflow-y-auto w-full h-full pb-10" style="z-index: 5;">
        <!-- 닫기 버튼: 상단 고정 위치 고려 (우상단) -->
        <div class="relative w-full max-w-lg mx-auto pt-5 px-5 flex justify-end">
            <button id="closeGameBtn" class="bg-white/10 hover:bg-white/20 text-white rounded-full p-2 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="max-w-lg mx-auto px-5 py-4">
            <!-- 진행 상태 (모달 상단) -->
            <div class="mb-6 bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 shadow-lg" style="margin-top: -10px;">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-1.5" id="stepDots"></div>
                    <div class="flex items-center gap-3">
                        <span id="skipCounter" class="text-[0.7rem] font-bold px-2 py-1 rounded bg-white/20 text-white shadow-sm">건너뛰기 0/3</span>
                        <span class="text-sm font-bold text-white">
                            <span id="completedCount" class="text-[#4ECDC4]">0</span> / 5
                        </span>
                    </div>
                </div>
                <div class="h-2 w-full bg-white/20 rounded-full overflow-hidden">
                    <div id="gameProgressBar" class="h-full w-0 bg-gradient-to-r from-[#4ECDC4] to-[#26D0CE] transition-all duration-500 ease-out"></div>
                </div>
            </div>

            <!-- 게임 콘텐츠 영역 -->
            <div id="gameContent"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ITEMS = [
        { id: 1, emoji: '🍎', name: '건강한 음식', description: '우리 몸이 튼튼하게 자라기 위해 필요한 과일과 채소예요.' },
        { id: 2, emoji: '🎮', name: '게임기', description: '재미있는 비디오 게임을 할 수 있는 최신 게임 콘솔이에요.' },
        { id: 3, emoji: '📚', name: '교과서', description: '학교에서 공부할 때 꼭 필요한 교과서와 학습 교재예요.' },
        { id: 4, emoji: '👟', name: '운동화', description: '체육 시간이나 운동할 때 신는 편안한 신발이에요.' },
        { id: 5, emoji: '🧸', name: '인형', description: '귀여운 캐릭터가 그려진 부드러운 봉제 인형이에요.' },
        { id: 6, emoji: '💊', name: '감기약', description: '감기에 걸렸을 때 빨리 낫도록 도와주는 약이에요.' },
        { id: 7, emoji: '🍭', name: '사탕', description: '달콤하고 맛있는 간식거리예요.' },
        { id: 8, emoji: '🏠', name: '따뜻한 집', description: '비바람을 막아주고 가족이 함께 생활하는 안전한 공간이에요.' },
        { id: 9, emoji: '📱', name: '스마트폰', description: '사진도 찍고 게임도 할 수 있는 최신 휴대전화예요.' },
        { id: 10, emoji: '🧥', name: '겨울 외투', description: '추운 겨울에 몸을 따뜻하게 감싸주는 두꺼운 옷이에요.' }
    ];

    const MAX_ITEMS = 5;
    const MAX_SKIPS = 3;
    let availableItems = [];
    let currentItem = null;
    let completedItems = [];
    let skipCount = 0;
    let selectedChoice = null;

    const startGameBtn = document.getElementById('startGameBtn');
    const closeGameBtn = document.getElementById('closeGameBtn');
    const gameModal = document.getElementById('gameModal');
    const gameContent = document.getElementById('gameContent');
    const gameProgressBar = document.getElementById('gameProgressBar');
    const completedCountEl = document.getElementById('completedCount');
    const skipCounterEl = document.getElementById('skipCounter');
    const stepDotsEl = document.getElementById('stepDots');

    function shuffleArray(arr) {
        const shuffled = [...arr];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }
        return shuffled;
    }

    function disableBodyScroll() { document.body.style.overflow = 'hidden'; }
    function enableBodyScroll() { document.body.style.overflow = ''; }

    function updateProgress() {
        const progress = (completedItems.length / MAX_ITEMS) * 100;
        gameProgressBar.style.width = progress + '%';
        completedCountEl.textContent = completedItems.length;
        skipCounterEl.textContent = '건너뛰기 ' + skipCount + '/' + MAX_SKIPS;
        renderStepDots();
    }

    function renderStepDots() {
        let dots = '';
        for (let i = 0; i < MAX_ITEMS; i++) {
            let cls = 'w-2 h-2 rounded-full transition-all duration-300 ';
            if (i < completedItems.length) cls += 'bg-[#4ECDC4] w-3';
            else if (i === completedItems.length) cls += 'bg-[#FFB347] w-4 ring-2 ring-white/50 ring-offset-2 ring-offset-[#4ECDC4]/20';
            else cls += 'bg-white/30';
            dots += '<div class="' + cls + '"></div>';
        }
        stepDotsEl.innerHTML = dots;
    }

    startGameBtn.addEventListener('click', function() {
        availableItems = shuffleArray(ITEMS);
        completedItems = [];
        skipCount = 0;
        selectedChoice = null;
        showNextItem();
        
        gameModal.classList.remove('hidden');
        // 강제 리플로우를 발생시켜 CSS Transition이 동작하도록 유도
        void gameModal.offsetWidth; 
        gameModal.classList.remove('opacity-0');
        
        updateProgress();
        disableBodyScroll();
    });

    closeGameBtn.addEventListener('click', function() {
        if (confirm('게임을 종료하시겠습니까?\n진행 중인 내용은 저장되지 않습니다.')) {
            gameModal.classList.add('opacity-0');
            setTimeout(function() {
                gameModal.classList.add('hidden');
            }, 300);
            enableBodyScroll();
        }
    });

    function showNextItem() {
        selectedChoice = null;
        if (completedItems.length >= MAX_ITEMS || availableItems.length === 0) {
            showResults();
            return;
        }
        currentItem = availableItems.shift();
        renderItemCard();
    }

    function renderItemCard() {
        var canSkip = skipCount < MAX_SKIPS && availableItems.length > 0;
        var remaining = MAX_ITEMS - completedItems.length;
        var step = completedItems.length + 1;

        gameContent.innerHTML = `
            <div class="nw-card-enter bg-white rounded-3xl shadow-2xl p-6 md:p-8 relative border border-gray-100/50">
                <div class="flex justify-center mb-5">
                    <span class="text-xs font-bold text-[#FFB347] bg-[#FFB347]/10 px-3 py-1 rounded-full border border-[#FFB347]/20">STEP ${step} / ${MAX_ITEMS}</span>
                </div>
                
                <div class="text-center mb-6">
                    <div class="w-24 h-24 mx-auto bg-gradient-to-br from-[#4ECDC4]/20 to-[#4ECDC4]/5 rounded-[2rem] flex items-center justify-center mb-4 shadow-inner border border-[#4ECDC4]/10">
                        <span class="text-5xl nw-emoji-pop inline-block">${currentItem.emoji}</span>
                    </div>
                    <h3 class="text-2xl font-bold text-[#2D3047] mb-2 tracking-tight nw-font-display">${currentItem.name}</h3>
                    <p class="text-gray-500 text-sm md:text-base leading-relaxed max-w-[280px] mx-auto break-keep nw-font-body font-medium">${currentItem.description}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-3 md:gap-4 mb-5">
                    <button onclick="window.selectChoice('need')" id="needBtn" class="group relative bg-[#F8F9FB] border-2 border-transparent hover:border-[#4ECDC4] rounded-2xl p-4 md:p-5 text-center transition-all duration-300 hover:shadow-[0_8px_20px_rgba(78,205,196,0.15)] hover:-translate-y-1 focus:outline-none overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#4ECDC4]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10 w-12 h-12 md:w-14 md:h-14 mx-auto bg-white rounded-xl md:rounded-2xl flex items-center justify-center text-2xl md:text-3xl mb-3 shadow-sm group-hover:scale-110 transition-transform duration-300">✅</div>
                        <div class="relative z-10 font-bold text-[#4ECDC4] text-lg md:text-xl mb-1 tracking-tight nw-font-display">Need</div>
                        <div class="relative z-10 text-[0.7rem] md:text-xs text-gray-400 font-medium break-keep nw-font-body">꼭 필요해요</div>
                    </button>
                    <button onclick="window.selectChoice('want')" id="wantBtn" class="group relative bg-[#F8F9FB] border-2 border-transparent hover:border-[#FF6B6B] rounded-2xl p-4 md:p-5 text-center transition-all duration-300 hover:shadow-[0_8px_20px_rgba(255,107,107,0.15)] hover:-translate-y-1 focus:outline-none overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#FF6B6B]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10 w-12 h-12 md:w-14 md:h-14 mx-auto bg-white rounded-xl md:rounded-2xl flex items-center justify-center text-2xl md:text-3xl mb-3 shadow-sm group-hover:scale-110 transition-transform duration-300">💖</div>
                        <div class="relative z-10 font-bold text-[#FF6B6B] text-lg md:text-xl mb-1 tracking-tight nw-font-display">Want</div>
                        <div class="relative z-10 text-[0.7rem] md:text-xs text-gray-400 font-medium break-keep nw-font-body">갖고 싶어요</div>
                    </button>
                </div>

                <div class="text-center mb-1">
                    <button onclick="window.skipItem()" id="skipBtn" class="inline-flex items-center justify-center gap-1 text-sm font-medium text-gray-400 hover:text-gray-600 transition-colors py-2 px-4 rounded-full hover:bg-gray-100 ${!canSkip ? 'opacity-50 cursor-not-allowed' : ''}" ${!canSkip ? 'disabled' : ''}>
                        <span>⏭</span> 건너뛰기 ${canSkip ? '(' + (MAX_SKIPS - skipCount) + '회 남음)' : '(소진)'}
                    </button>
                </div>

                <div id="reasonArea" class="hidden opacity-0 transform translate-y-2 transition-all duration-300 ease-out">
                    <div class="bg-gray-50/80 rounded-2xl p-5 mt-4 border border-gray-100 relative">
                        <!-- 화살표 (말풍선 꼬리) -->
                        <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-gray-50/80 border-t border-l border-gray-100 rotate-45"></div>
                        
                        <label class="flex items-center gap-2 text-sm font-bold text-[#2D3047] mb-3 relative z-10 nw-font-body">
                            <span>💬</span> 왜 그렇게 생각했나요? <span class="text-[#FF4D4D]">*</span>
                        </label>
                        <input type="text" id="reasonInput" maxlength="100" class="w-full relative z-10 px-4 py-3 rounded-xl border border-gray-200 focus:border-[#4ECDC4] focus:ring-4 focus:ring-[#4ECDC4]/10 outline-none transition-all text-sm mb-3 bg-white shadow-sm placeholder:text-gray-300 nw-font-body" placeholder="이유를 짧게 적어주세요!">
                        
                        <div class="flex items-center justify-between relative z-10">
                            <span id="reasonCharCount" class="text-xs font-semibold text-gray-400 bg-white px-2 py-1 rounded-md border border-gray-100">0/100</span>
                            <button onclick="window.submitAnswer()" id="submitBtn" class="bg-[#2D3047] hover:bg-[#1A1C29] text-white text-sm font-bold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-[0_4px_12px_rgba(45,48,71,0.2)] hover:shadow-[0_6px_16px_rgba(45,48,71,0.3)] hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none disabled:hover:translate-y-0 nw-font-body" disabled>다음으로 →</button>
                        </div>
                    </div>
                </div>
            </div>`;

        var reasonInput = document.getElementById('reasonInput');
        var charCount = document.getElementById('reasonCharCount');
        var submitBtn = document.getElementById('submitBtn');

        if (reasonInput) {
            reasonInput.addEventListener('input', function() {
                var len = this.value.trim().length;
                charCount.textContent = this.value.length + '/100';
                submitBtn.disabled = len === 0;
            });
            reasonInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && this.value.trim().length > 0 && selectedChoice) {
                    window.submitAnswer();
                }
            });
        }
    }

    window.selectChoice = function(choice) {
        selectedChoice = choice;
        var needBtn = document.getElementById('needBtn');
        var wantBtn = document.getElementById('wantBtn');
        var reasonArea = document.getElementById('reasonArea');
        
        // 리셋
        needBtn.classList.remove('ring-4', 'ring-[#4ECDC4]/20', 'border-[#4ECDC4]', 'bg-[#F0FDFB]', 'shadow-[0_8px_20px_rgba(78,205,196,0.15)]', '-translate-y-1');
        wantBtn.classList.remove('ring-4', 'ring-[#FF6B6B]/20', 'border-[#FF6B6B]', 'bg-[#FFF5F5]', 'shadow-[0_8px_20px_rgba(255,107,107,0.15)]', '-translate-y-1');
        needBtn.classList.add('bg-[#F8F9FB]', 'border-transparent');
        wantBtn.classList.add('bg-[#F8F9FB]', 'border-transparent');
        
        // 선택 상태 적용
        if (choice === 'need') { 
            needBtn.classList.remove('bg-[#F8F9FB]', 'border-transparent');
            needBtn.classList.add('ring-4', 'ring-[#4ECDC4]/20', 'border-[#4ECDC4]', 'bg-[#F0FDFB]', 'shadow-[0_8px_20px_rgba(78,205,196,0.15)]', '-translate-y-1'); 
        }
        else { 
            wantBtn.classList.remove('bg-[#F8F9FB]', 'border-transparent');
            wantBtn.classList.add('ring-4', 'ring-[#FF6B6B]/20', 'border-[#FF6B6B]', 'bg-[#FFF5F5]', 'shadow-[0_8px_20px_rgba(255,107,107,0.15)]', '-translate-y-1'); 
        }
        
        // 영역 노출 애니메이션
        reasonArea.classList.remove('hidden');
        // 애니메이션을 부드럽게 만들기 위한 렌더링 지연
        void reasonArea.offsetWidth;
        reasonArea.classList.remove('opacity-0', 'translate-y-2');
        reasonArea.classList.add('opacity-100', 'translate-y-0');
        
        setTimeout(function() {
            var ri = document.getElementById('reasonInput');
            if (ri) ri.focus();
        }, 300);
    };

    window.skipItem = function() {
        if (skipCount >= MAX_SKIPS || availableItems.length === 0) return;
        skipCount++;
        availableItems.push(currentItem);
        updateProgress();
        showNextItem();
    };

    window.submitAnswer = function() {
        var reasonInput = document.getElementById('reasonInput');
        var reason = reasonInput ? reasonInput.value.trim() : '';
        if (!selectedChoice || reason.length === 0) return;
        completedItems.push({ item: currentItem, choice: selectedChoice, reason: reason });
        updateProgress();
        showNextItem();
    };

        function showResults() {
        var needCount = completedItems.filter(function(c) { return c.choice === 'need'; }).length;
        var wantCount = completedItems.filter(function(c) { return c.choice === 'want'; }).length;

        var resultEmoji, resultMsg;
        if (needCount > wantCount) { resultEmoji = '👏'; resultMsg = '필요한 것과 원하는 것을 잘 구분하고 있어요!'; }
        else if (wantCount > needCount) { resultEmoji = '😎'; resultMsg = '갖고 싶은 것이 많군요! 솔직해서 좋아요!'; }
        else { resultEmoji = '⚖️'; resultMsg = 'Need와 Want의 황금 밸런스네요!'; }

        var resultCardsHtml = '';
        for (var i = 0; i < completedItems.length; i++) {
            var item = completedItems[i];
            var isNeed = item.choice === 'need';
            resultCardsHtml += `
                <div class="nw-card-enter bg-white border border-gray-100 rounded-2xl p-4 mb-3 shadow-[0_4px_12px_rgba(0,0,0,0.03)] flex items-start gap-4 transition-all hover:shadow-[0_6px_16px_rgba(0,0,0,0.06)] hover:-translate-y-0.5 duration-300 group" style="animation-delay: ${i * 0.1}s;">
                    <div class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 ${isNeed ? 'bg-gradient-to-br from-[#E8FAF7] to-white' : 'bg-gradient-to-br from-[#FFF5F5] to-white'} rounded-xl flex items-center justify-center text-2xl md:text-3xl shadow-inner border border-gray-50 group-hover:scale-105 transition-transform duration-300">
                        ${item.item.emoji}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-bold text-[#2D3047] text-base md:text-lg truncate tracking-tight nw-font-display">${item.item.name}</span>
                            <span class="text-[0.65rem] font-bold px-2.5 py-0.5 rounded-full border ${isNeed ? 'bg-[#4ECDC4]/10 text-[#4ECDC4] border-[#4ECDC4]/20' : 'bg-[#FF6B6B]/10 text-[#FF6B6B] border-[#FF6B6B]/20'} shadow-sm nw-font-body">${isNeed ? 'Need' : 'Want'}</span>
                        </div>
                        <div class="flex items-start gap-2 bg-[#F8F9FB] p-3 rounded-xl border border-gray-50/80 w-full overflow-hidden">
                            <span class="text-sm pt-0.5 opacity-70 shrink-0">💬</span>
                            <p class="text-sm md:text-base font-medium text-gray-600 break-all leading-snug nw-font-body">"${item.reason}"</p>
                        </div>
                    </div>
                </div>`;
        }

        gameContent.innerHTML = `
            <div class="nw-card-enter bg-white rounded-3xl shadow-2xl p-6 md:p-8 relative border border-gray-100/50">
                <div class="text-center mb-8">
                    <div class="text-6xl mb-4 drop-shadow-md nw-emoji-pop inline-block">${resultEmoji}</div>
                    <h3 class="text-3xl font-bold text-[#2D3047] mb-2 tracking-tight nw-font-display">게임 완료!</h3>
                    <p class="text-gray-500 font-medium text-sm md:text-base nw-font-body">${resultMsg}</p>
                </div>
                
                <div class="flex justify-center gap-4 md:gap-6 mb-8">
                    <div class="flex flex-col items-center justify-center w-28 h-28 md:w-32 md:h-32 rounded-3xl bg-gradient-to-br from-[#4ECDC4]/20 to-[#4ECDC4]/5 border border-[#4ECDC4]/20 shadow-sm relative overflow-hidden group">
                        <div class="absolute inset-0 bg-[#4ECDC4] opacity-0 group-hover:opacity-5 transition-opacity duration-300"></div>
                        <span class="text-5xl font-extrabold text-[#4ECDC4] leading-none mb-1 group-hover:scale-110 transition-transform duration-300 nw-font-display">${needCount}</span>
                        <span class="text-xs font-bold text-[#4ECDC4]/70 uppercase tracking-wider relative z-10 bg-white/50 px-3 py-1 rounded-full mt-1 nw-font-body">Need</span>
                    </div>
                    <div class="flex flex-col items-center justify-center w-28 h-28 md:w-32 md:h-32 rounded-3xl bg-gradient-to-br from-[#FF6B6B]/20 to-[#FF6B6B]/5 border border-[#FF6B6B]/20 shadow-sm relative overflow-hidden group">
                        <div class="absolute inset-0 bg-[#FF6B6B] opacity-0 group-hover:opacity-5 transition-opacity duration-300"></div>
                        <span class="text-5xl font-extrabold text-[#FF6B6B] leading-none mb-1 group-hover:scale-110 transition-transform duration-300 nw-font-display">${wantCount}</span>
                        <span class="text-xs font-bold text-[#FF6B6B]/70 uppercase tracking-wider relative z-10 bg-white/50 px-3 py-1 rounded-full mt-1 nw-font-body">Want</span>
                    </div>
                </div>
                
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-4 justify-center">
                        <h4 class="font-bold text-[#2D3047] text-lg nw-font-display">나의 선택 결과</h4>
                        <span class="bg-gray-100 border border-gray-200 text-gray-500 text-xs font-bold px-2.5 py-1 rounded-full shadow-sm nw-font-body">총 5개</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        ${resultCardsHtml}
                    </div>
                </div>
                
                <div class="flex gap-3 justify-center mt-6">
                    <button onclick="window.restartGame()" class="flex-1 bg-gradient-to-r from-[#4ECDC4] to-[#26D0CE] hover:shadow-[0_8px_20px_rgba(78,205,196,0.25)] hover:-translate-y-1 transition-all duration-300 text-white font-bold py-4 px-6 rounded-2xl text-center flex items-center justify-center gap-2 text-base nw-font-body">
                        <span class="text-lg">🔄</span> 다시하기
                    </button>
                    <button onclick="window.closeGame()" class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold py-4 px-6 rounded-2xl transition-all duration-300 text-center border border-gray-200 hover:border-gray-300 hover:shadow-sm text-base nw-font-body">종료하기</button>
                </div>
            </div>`;

        gameProgressBar.style.width = '100%';
        completedCountEl.textContent = completedItems.length;
        renderStepDots();
    }

    window.restartGame = function() {
        availableItems = shuffleArray(ITEMS);
        completedItems = [];
        skipCount = 0;
        selectedChoice = null;
        updateProgress();
        showNextItem();
    };

    window.closeGame = function() {
        gameModal.classList.add('opacity-0');
        setTimeout(function() {
            gameModal.classList.add('hidden');
        }, 300);
        enableBodyScroll();
    };
});
</script>
@endpush
