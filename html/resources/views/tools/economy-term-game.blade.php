@extends('layouts.app')

@section('title', '알쏭달쏭 경제용어, 짝꿍을 찾아라!')

@push('styles')
<style>
    .card-flip {
        transition: transform 0.6s;
        transform-style: preserve-3d;
    }
    .card-flip.flipped {
        transform: rotateY(180deg);
    }
    .card-front, .card-back {
        backface-visibility: hidden;
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 12px;
    }
    .card-back {
        transform: rotateY(180deg);
    }
    .pulse-correct {
        animation: pulseCorrect 0.5s ease-in-out;
    }
    .pulse-wrong {
        animation: pulseWrong 0.5s ease-in-out;
    }
    @keyframes pulseCorrect {
        0%, 100% { transform: scale(1); background-color: rgb(34, 197, 94); }
        50% { transform: scale(1.05); background-color: rgb(22, 163, 74); }
    }
    @keyframes pulseWrong {
        0%, 100% { transform: scale(1); background-color: rgb(239, 68, 68); }
        50% { transform: scale(1.05); background-color: rgb(220, 38, 38); }
    }

    /* 랭킹 아이템 호버 효과 */
    .ranking-item {
        transition: all 0.2s ease;
    }
    .ranking-item:hover {
        transform: translateX(4px);
    }

    /* 옵션 버튼 호버 */
    .option-btn {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid rgba(255,255,255,0.15);
        backdrop-filter: blur(8px);
    }
    .option-btn:hover {
        border-color: #4ECDC4;
        background: rgba(78, 205, 196, 0.08);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(78, 205, 196, 0.15);
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
            <span>🃏</span> <span>학습 도구</span>
        </div>
        <h1 class="font-outfit text-3xl md:text-5xl font-extrabold text-white mb-4 tracking-tight animate-slideUp" style="animation-delay: 0.1s;">
            알쏭달쏭 경제용어, <span class="text-[#4ECDC4]">짝꿍</span>을 찾아라!
        </h1>
        <p class="text-white/70 text-base md:text-lg max-w-2xl mx-auto leading-relaxed animate-slideUp" style="animation-delay: 0.2s;">
            재미있는 카드 게임으로 경제 용어를 배워보세요!
        </p>
    </div>
</div>

<!-- ===== Main Content ===== -->
<div class="relative z-20 -mt-24 pb-16">
    <div class="container mx-auto px-4 max-w-4xl">

        <!-- 메인 화면 -->
        <div id="mainScreen">
            <!-- 게임 방법 카드 -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6 animate-slideUp" style="animation-delay: 0.3s;">
                <h2 class="text-xl font-bold text-[#2D3047] mb-5 flex items-center gap-2">
                    <span class="w-8 h-8 bg-gradient-to-br from-[#4ECDC4] to-[#26D0CE] rounded-lg flex items-center justify-center text-white text-sm">📖</span>
                    게임 방법
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                        <span class="flex-shrink-0 w-7 h-7 bg-[#FF4D4D] text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        <p class="text-gray-600 text-sm leading-relaxed">경제 용어 카드와 설명 카드 중 알맞은 짝을 찾으세요.</p>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                        <span class="flex-shrink-0 w-7 h-7 bg-[#FFB347] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        <p class="text-gray-600 text-sm leading-relaxed">정답을 맞히면 점수를 얻고, 다음 문제로 넘어갑니다.</p>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                        <span class="flex-shrink-0 w-7 h-7 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                        <p class="text-gray-600 text-sm leading-relaxed">모든 문제를 풀면 게임이 종료되고, 점수와 시간이 기록됩니다.</p>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                        <span class="flex-shrink-0 w-7 h-7 bg-[#7C5CFC] text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                        <p class="text-gray-600 text-sm leading-relaxed">가장 높은 점수와 빠른 시간으로 랭킹에 도전하세요!</p>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <button id="startBtn" class="bg-gradient-to-r from-[#FF4D4D] to-[#FF6B6B] hover:from-[#FF3333] hover:to-[#FF4D4D] text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 text-lg shadow-[0_8px_25px_rgba(255,77,77,0.35)] hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(255,77,77,0.45)]">
                        🎮 게임 시작하기
                    </button>
                </div>
            </div>

            <!-- 랭킹 카드 -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 animate-slideUp" style="animation-delay: 0.4s;">
                <h3 class="text-xl font-bold text-[#2D3047] mb-5 flex items-center gap-2">
                    <span class="w-8 h-8 bg-gradient-to-br from-[#FFB347] to-[#FFCC33] rounded-lg flex items-center justify-center text-white text-sm">🏆</span>
                    베스트 랭킹
                </h3>
                <div id="rankingList" class="space-y-2">
                    <!-- 랭킹 데이터가 여기에 표시됩니다 -->
                </div>
            </div>
        </div>

        <!-- 게임 화면 -->
        <div id="gameScreen" class="hidden">
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8">
                <!-- 게임 헤더 -->
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-500">문제</span>
                        <span class="text-2xl font-bold text-[#2D3047]"><span id="currentQuestion">1</span> / <span id="totalQuestions">15</span></span>
                    </div>
                    <div class="bg-gradient-to-r from-[#2D3047] to-[#3D4148] text-white px-5 py-2.5 rounded-xl shadow-lg font-mono text-lg font-bold tracking-wider">
                        <span id="gameTimer">00:00</span>
                    </div>
                </div>

                <!-- 진행바 -->
                <div class="w-full bg-gray-100 rounded-full h-2 mb-8 overflow-hidden">
                    <div id="gameProgressBar" class="h-full rounded-full bg-gradient-to-r from-[#4ECDC4] to-[#26D0CE] transition-all duration-500" style="width: 0%"></div>
                </div>

                <!-- 현재 문제 카드 -->
                <div class="flex justify-center items-center mb-8">
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-400 mb-3 uppercase tracking-wider">현재 문제</div>
                        <div id="currentCard" class="w-52 h-36 bg-gradient-to-br from-[#2D3047] to-[#3D4148] rounded-2xl shadow-[0_12px_32px_rgba(45,48,71,0.3)] flex items-center justify-center border border-white/10 p-4">
                            <div id="currentTerm" class="text-2xl font-extrabold text-white text-center leading-tight"></div>
                        </div>
                    </div>
                </div>

                <!-- 선택지들 -->
                <div class="max-w-3xl mx-auto">
                    <h3 class="text-base font-semibold text-center text-gray-500 mb-5">
                        위 용어에 맞는 설명을 골라주세요!
                    </h3>
                    <div id="optionsContainer" class="grid md:grid-cols-2 gap-3 mb-8">
                        <!-- 선택지들이 여기에 생성됩니다 -->
                    </div>

                    <!-- PASS 버튼 및 메인으로 버튼 -->
                    <div class="text-center flex justify-center gap-3">
                        <button id="passBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold text-sm px-6 py-3 rounded-xl transition-all duration-300" style="display: none;">
                            ⏭ PASS
                        </button>
                        <button id="goToMainFromGameBtn" class="bg-[#FF4D4D]/10 hover:bg-[#FF4D4D]/20 text-[#FF4D4D] font-semibold text-sm px-6 py-3 rounded-xl transition-all duration-300">
                            포기하기
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 결과 화면 -->
        <div id="resultScreen" class="hidden">
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-10">
                <div class="text-center mb-8">
                    <div class="text-5xl mb-4">🎉</div>
                    <h2 class="text-3xl font-extrabold text-[#2D3047] mb-6">게임 완료!</h2>

                    <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto mb-6">
                        <div class="bg-gradient-to-br from-[#4ECDC4]/10 to-[#4ECDC4]/5 rounded-2xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">정답</div>
                            <div class="text-3xl font-extrabold text-[#4ECDC4]"><span id="finalScore">0</span>점</div>
                        </div>
                        <div class="bg-gradient-to-br from-[#FF4D4D]/10 to-[#FF4D4D]/5 rounded-2xl p-5 text-center">
                            <div class="text-sm text-gray-500 mb-1">시간</div>
                            <div class="text-3xl font-extrabold text-[#FF4D4D] font-mono"><span id="finalTime">00:00</span></div>
                        </div>
                    </div>
                    <div id="rankMessage" class="text-base font-medium text-gray-500"></div>
                </div>

                <!-- 최종 랭킹 -->
                <div class="bg-[#F8F9FB] rounded-2xl p-6 mb-8">
                    <h3 class="text-lg font-bold text-[#2D3047] mb-4 flex items-center gap-2">
                        <span>🏆</span> 최고 랭킹
                    </h3>
                    <div id="finalRanking" class="space-y-2">
                        <!-- 업데이트된 랭킹이 표시됩니다 -->
                    </div>
                </div>

                <div class="flex justify-center gap-3">
                    <button id="playAgainBtn" class="bg-gradient-to-r from-[#4ECDC4] to-[#26D0CE] text-white font-bold text-base px-8 py-3.5 rounded-xl shadow-[0_8px_25px_rgba(78,205,196,0.35)] hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(78,205,196,0.45)] transition-all duration-300">
                        🔄 다시 플레이
                    </button>
                    <button id="backToMainBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-base px-8 py-3.5 rounded-xl transition-all duration-300">
                        메인으로
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // 로그인 상태를 JavaScript 변수로 전달
    const IS_USER_LOGGED_IN = {{ Auth::check() ? 'true' : 'false' }};

    const ECONOMY_TERMS = @json($economyTerms);

    class EconomyCardGame {
        constructor() {
            this.questions = JSON.parse(JSON.stringify(ECONOMY_TERMS));
            this.currentQuestionIndex = 0;
            this.score = 0;
            this.startTime = null;
            this.gameTime = 0;
            this.timerInterval = null;
            this.isGameActive = false;
            
            this.shuffleArray(this.questions);
            this.initializeEventListeners();
            this.fetchAndDisplayRanking();
        }

        initializeEventListeners() {
            document.getElementById('startBtn').addEventListener('click', () => {
                if (!IS_USER_LOGGED_IN) {
                    alert('로그인이 필요합니다.');
                    return;
                }
                this.startGame();
            });
            document.getElementById('passBtn').addEventListener('click', () => this.passQuestion());
            document.getElementById('playAgainBtn').addEventListener('click', () => this.resetGame());
            document.getElementById('backToMainBtn').addEventListener('click', () => this.goToMain());
            document.getElementById('goToMainFromGameBtn').addEventListener('click', () => this.abandonGameAndGoToMain());
        }

        shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
        }

        startGame() {
            this.isGameActive = true;
            this.startTime = Date.now();
            this.currentQuestionIndex = 0;
            this.score = 0;
            
            this.hideScreen('mainScreen');
            this.showScreen('gameScreen');
            
            this.startTimer();
            this.showQuestion();
        }

        startTimer() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
            }
            this.timerInterval = setInterval(() => {
                if (!this.isGameActive) return;
                
                const elapsed = Date.now() - this.startTime;
                this.gameTime = Math.floor(elapsed / 1000);
                const minutes = Math.floor(this.gameTime / 60);
                const seconds = this.gameTime % 60;
                
                document.getElementById('gameTimer').textContent = 
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        stopTimer() {
            this.isGameActive = false;
            clearInterval(this.timerInterval);
        }

        showQuestion() {
            if (this.currentQuestionIndex >= this.questions.length) {
                this.endGame();
                return;
            }

            const question = this.questions[this.currentQuestionIndex];
            
            document.getElementById('currentQuestion').textContent = this.currentQuestionIndex + 1;
            document.getElementById('totalQuestions').textContent = this.questions.length;
            document.getElementById('currentTerm').textContent = question.term;
            
            // 진행바 업데이트
            const progress = ((this.currentQuestionIndex) / this.questions.length) * 100;
            const progressBar = document.getElementById('gameProgressBar');
            if (progressBar) progressBar.style.width = progress + '%';

            this.createOptions(question);
        }

        createOptions(question) {
            const container = document.getElementById('optionsContainer');
            container.innerHTML = '';
            
            const options = [question.description];
            const wrongAnswers = this.getRandomWrongAnswers(question.description, 3);
            options.push(...wrongAnswers);
            this.shuffleArray(options);
            
            options.forEach((option) => {
                const button = document.createElement('button');
                button.className = 'option-btn bg-white/80 text-gray-700 font-medium text-sm px-5 py-4 rounded-xl shadow-sm text-left leading-relaxed';
                button.textContent = option;

                button.addEventListener('click', () => {
                    this.selectAnswer(option, question.description, button);
                });
                container.appendChild(button);
            });
        }

        getRandomWrongAnswers(correctAnswer, count) {
            const allDescriptions = this.questions
                .map(q => q.description)
                .filter(desc => desc !== correctAnswer);
            
            this.shuffleArray(allDescriptions);
            return allDescriptions.slice(0, count);
        }

        selectAnswer(selectedAnswer, correctAnswer, buttonElement) {
            const buttons = document.querySelectorAll('#optionsContainer button');
            buttons.forEach(btn => btn.disabled = true);
            
            if (selectedAnswer === correctAnswer) {
                this.score++;
                buttonElement.classList.remove('bg-white/80');
                buttonElement.classList.add('bg-green-500', 'text-white', 'pulse-correct', 'border-green-500');
                this.showFeedback('정답이에요! 🎉', 'success');
            } else {
                buttonElement.classList.remove('bg-white/80');
                buttonElement.classList.add('bg-red-500', 'text-white', 'pulse-wrong', 'border-red-500');
                
                buttons.forEach(btn => {
                    if (btn.textContent === correctAnswer) {
                        btn.classList.remove('bg-white/80');
                        btn.classList.add('bg-green-500', 'text-white', 'border-green-500');
                    }
                });
                this.showFeedback('아쉬워요! 다음에는 맞출 수 있어요 💪', 'error');
            }
            
            setTimeout(() => this.nextQuestion(), 2000);
        }

        showFeedback(message, type) {
            const feedback = document.createElement('div');
            feedback.className = `fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 text-xl font-bold px-8 py-4 rounded-2xl shadow-2xl backdrop-blur-md ${
                type === 'success' ? 'bg-green-500/90 text-white' : 'bg-red-500/90 text-white'
            }`;
            feedback.textContent = message;
            document.body.appendChild(feedback);
            
            setTimeout(() => {
                feedback.remove();
            }, 1500);
        }

        passQuestion() {
            this.showFeedback('문제를 건너뛰었습니다! ⏭️', 'info');
            setTimeout(() => this.nextQuestion(), 1000);
        }

        nextQuestion() {
            this.currentQuestionIndex++;
            this.showQuestion();
        }

        async fetchAndDisplayRanking(targetElementId = 'rankingList') {
            try {
                const response = await fetch('{{ route("tools.economy-term-game.ranking") }}');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const rankings = await response.json();
                
                const container = document.getElementById(targetElementId);
                if (rankings.length === 0) {
                    container.innerHTML = '<p class="text-gray-400 text-center py-4">아직 기록이 없습니다.</p>';
                    return;
                }
                
                container.innerHTML = rankings.map((record, index) => {
                    const medals = ['🥇', '🥈', '🥉'];
                    const medal = index < 3 ? medals[index] : `${index + 1}`;
                    const bgClass = index < 3 ? 'bg-gradient-to-r from-[#FFB347]/10 to-[#FFCC33]/5 border border-[#FFB347]/20' : 'bg-[#F8F9FB]';

                    return `
                        <div class="ranking-item flex items-center p-3 ${bgClass} rounded-xl w-full text-sm">
                            <span class="font-bold w-[12%] text-center text-base">${medal}</span>
                            <span class="w-[38%] truncate pr-2 font-medium text-[#2D3047]">${record.userName || '익명'}</span>
                            <span class="w-[25%] text-right pr-2 text-[#4ECDC4] font-bold">${record.score}점</span>
                            <span class="font-mono w-[25%] text-right text-gray-500">${record.time_formatted}</span>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                console.error("Ranking fetch error:", error);
                const container = document.getElementById(targetElementId);
                container.innerHTML = '<p class="text-red-400 text-center py-4">랭킹을 불러오는데 실패했습니다.</p>';
            }
        }

        async endGame() {
            this.stopTimer();
            this.hideScreen('gameScreen');
            this.showScreen('resultScreen');
            
            document.getElementById('finalScore').textContent = this.score;
            const minutes = Math.floor(this.gameTime / 60);
            const seconds = this.gameTime % 60;
            document.getElementById('finalTime').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            let rankMsgText = '결과를 처리 중입니다...';
            let shouldFetchRanking = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('{{ route("tools.economy-term-game.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        total_questions: this.questions.length,
                        score: this.score,
                        time: this.gameTime
                    })
                });

                const isRedirectToLogin = response.redirected && new URL(response.url).pathname === '/login';

                if (isRedirectToLogin || response.status === 401) {
                    alert('로그인이 필요합니다.');
                    rankMsgText = '로그인이 필요하여 결과를 저장할 수 없습니다.';
                    shouldFetchRanking = false; 
                } else if (!response.ok) {
                    let errorDetail = `서버 응답 오류 (코드: ${response.status})`; 
                    try {
                        const errorData = await response.json();
                        if (errorData && errorData.message) {
                            errorDetail = errorData.message;
                        }
                    } catch (e) {
                        console.warn("에러 응답의 JSON 파싱에 실패했습니다:", e);
                    }

                    if (response.status >= 400 && response.status < 500) {
                        alert(`저장 오류: ${errorDetail}`);
                    } else {
                        console.error("게임 결과 저장 중 서버 에러:", errorDetail);
                    }
                    rankMsgText = `게임 결과 저장 실패: ${errorDetail}`;
                } else {
                    const result = await response.json(); 
                    console.log('Game result saved:', result);
                    rankMsgText = '게임 결과가 저장되었습니다!';
                }
            } catch (networkOrParsingError) {
                console.error("게임 결과 저장 중 네트워크 또는 파싱 에러:", networkOrParsingError);
                rankMsgText = '결과 저장 중 통신 또는 데이터 처리 오류가 발생했습니다.';
            } finally {
                document.getElementById('rankMessage').textContent = rankMsgText;
                if (shouldFetchRanking) {
                    await this.fetchAndDisplayRanking('finalRanking');
                }
            }
        }

        resetGame() {
            this.stopTimer();
            this.questions = JSON.parse(JSON.stringify(ECONOMY_TERMS));
            this.shuffleArray(this.questions);
            this.currentQuestionIndex = 0;
            this.score = 0;
            this.gameTime = 0;
            document.getElementById('gameTimer').textContent = '00:00';
            
            this.hideScreen('resultScreen');
            this.startGame();
        }

        goToMain() {
            this.hideScreen('resultScreen');
            this.showScreen('mainScreen');
            this.fetchAndDisplayRanking();
        }

        showScreen(screenId) {
            document.getElementById(screenId).classList.remove('hidden');
        }

        hideScreen(screenId) {
            document.getElementById(screenId).classList.add('hidden');
        }

        abandonGameAndGoToMain() {
            if (confirm('정말로 게임을 포기하고 메인 화면으로 돌아가시겠습니까?')) {
                this.stopTimer();
                this.isGameActive = false;
                this.startTime = null;
                this.gameTime = 0;
                this.currentQuestionIndex = 0;
                this.score = 0;
                
                document.getElementById('gameTimer').textContent = '00:00';
                
                this.hideScreen('gameScreen');
                this.showScreen('mainScreen');
                
                this.fetchAndDisplayRanking();
            }
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        new EconomyCardGame();
    });
</script>
@endpush