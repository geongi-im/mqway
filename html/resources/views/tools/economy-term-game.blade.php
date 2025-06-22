@extends('layouts.app')

@section('title', '알쏭달쏭 경제용어, 짝꿍을 찾아라!')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Noto Sans KR', sans-serif;
    }
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
    .timer {
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }
    /* TailwindCSS가 이미 로드되어 있다고 가정하고, 필요한 경우 추가 스타일만 여기에 작성합니다. */
    /* 예를 들어, body의 기본 배경색이 layouts.app에서 지정된 것과 다를 경우 여기서 오버라이드 할 수 있습니다. */
    /* body {
        background-color: #f0f4f8; /* 예시 배경색 */
    /* } */
</style>
@endpush

@section('content')
<div class="min-h-screen">
    <!-- 메인 화면 -->
    <div id="mainScreen" class="container mx-auto px-4 py-8">
        <div class="text-center">
            <h1 class="text-3xl md:text-3xl font-bold text-purple-800 mb-4">
                🎯 알쏭달쏭 경제용어, 짝꿍을 찾아라! 💰
            </h1>
            <p class="text-xl text-gray-700 mb-8">
                재미있는 카드 게임으로 경제 용어를 배워보세요!
            </p>
            
            <div class="max-w-2xl mx-auto bg-white rounded-2xl p-8 shadow-lg mb-8">
                <h2 class="text-2xl font-bold text-blue-700 mb-4">🎮 게임 방법</h2>
                <div class="text-left space-y-3">
                    <p class="text-lg">📚 1. 경제 용어 카드와 설명 카드 중 알맞은 짝을 찾으세요.</p>
                    <p class="text-lg">💡 2. 정답을 맞히면 점수를 얻고, 다음 문제로 넘어갑니다.</p>
                    <p class="text-lg">⏱️ 3. 모든 문제를 풀면 게임이 종료되고, 점수와 시간이 기록됩니다.</p>
                    <p class="text-lg">🏆 4. 가장 높은 점수와 빠른 시간으로 랭킹에 도전하세요!</p>
                </div>
            </div>

            <div class="space-y-4 mb-8">
                <button id="startBtn" class="bg-gradient-to-r from-purple-500 to-blue-500 text-white font-bold text-2xl px-12 py-4 rounded-2xl shadow-lg hover:from-purple-600 hover:to-blue-600 transition-all duration-300 transform hover:scale-105">
                    🎯 게임 시작하기
                </button>
            </div>

            <!-- 랭킹 표시 -->
            <div id="rankingDisplay" class="max-w-md mx-auto bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="text-xl font-bold text-orange-600 mb-4">🏆 베스트 랭킹</h3>
                <div id="rankingList" class="space-y-2">
                    <!-- 랭킹 데이터가 여기에 표시됩니다 -->
                </div>
            </div>
        </div>
    </div>

    <!-- 게임 화면 -->
    <div id="gameScreen" class="container mx-auto px-4 py-8 hidden">
        <!-- 게임 헤더 -->
        <div class="flex justify-between items-center mb-6">
            <div class="text-2xl font-bold text-purple-800">
                문제 <span id="currentQuestion">1</span> / <span id="totalQuestions">15</span>
            </div>
            <div class="timer text-2xl text-blue-700 bg-white px-4 py-2 rounded-lg shadow">
                ⏱️ <span id="gameTimer">00:00</span>
            </div>
        </div>

        <!-- 현재 문제 카드 (카드 덱 및 화살표 제거, 중앙 정렬) -->
        <div class="flex justify-center items-center mb-8">
            <div class="text-center">
                <div class="text-lg font-bold mb-2">🎯 현재 문제</div>
                <div id="currentCard" class="w-48 h-72 bg-gradient-to-br from-yellow-200 to-orange-200 rounded-xl shadow-lg flex items-center justify-center border-4 border-orange-300">
                    <div id="currentTerm" class="text-3xl font-bold text-orange-800"></div>
                </div>
            </div>
        </div>

        <!-- 선택지들 -->
        <div class="max-w-4xl mx-auto">
            <h3 class="text-xl font-bold text-center text-blue-700 mb-4">
                위 용어에 맞는 설명을 골라주세요! 🤔
            </h3>
            <div id="optionsContainer" class="grid md:grid-cols-2 gap-4 mb-6">
                <!-- 선택지들이 여기에 생성됩니다 -->
            </div>

            <!-- PASS 버튼 및 메인으로 버튼 -->
            <div class="text-center space-x-4">
                <button id="passBtn" class="bg-gray-500 text-white font-bold text-xl px-8 py-3 rounded-xl shadow-lg hover:bg-gray-600 transition-all duration-300" style="display: none;">
                    ⏭️ PASS (건너뛰기)
                </button>
                <button id="goToMainFromGameBtn" class="bg-red-500 text-white font-bold text-xl px-8 py-3 rounded-xl shadow-lg hover:bg-red-600 transition-all duration-300">
                    ❌ 포기하기
                </button>
            </div>
        </div>
    </div>

    <!-- 결과 화면 -->
    <div id="resultScreen" class="container mx-auto px-4 py-8 hidden">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-4xl font-bold text-purple-800 mb-6">🎉 게임 완료!</h2>
            
            <div class="bg-white rounded-2xl p-8 shadow-lg mb-6">
                <div class="text-6xl mb-4">🏆</div>
                <div class="space-y-4">
                    <div class="text-2xl">
                        <span class="font-bold text-green-600">정답:</span> 
                        <span id="finalScore" class="text-3xl font-bold">0</span>점
                    </div>
                    <div class="text-2xl">
                        <span class="font-bold text-blue-600">시간:</span> 
                        <span id="finalTime" class="text-3xl font-bold timer">00:00</span>
                    </div>
                    <div id="rankMessage" class="text-xl font-bold text-orange-600"></div>
                </div>
            </div>

            <!-- 업데이트된 랭킹 -->
            <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
                <h3 class="text-xl font-bold text-orange-600 mb-4">🏆 최고 랭킹</h3>
                <div id="finalRanking" class="space-y-2">
                    <!-- 업데이트된 랭킹이 표시됩니다 -->
                </div>
            </div>

            <div class="space-x-4">
                <button id="playAgainBtn" class="bg-gradient-to-r from-purple-500 to-blue-500 text-white font-bold text-xl px-8 py-3 rounded-xl shadow-lg hover:from-purple-600 hover:to-blue-600 transition-all duration-300">
                    🔄 다시 플레이
                </button>
                <button id="backToMainBtn" class="bg-gradient-to-r from-green-500 to-teal-500 text-white font-bold text-xl px-8 py-3 rounded-xl shadow-lg hover:from-green-600 hover:to-teal-600 transition-all duration-300">
                    🏠 메인으로
                </button>
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
                    return; // 로그인 안되어 있으면 게임 시작 중단
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
                button.className = 'bg-white hover:bg-blue-50 border-2 border-blue-200 text-gray-800 font-medium text-lg px-6 py-4 rounded-xl shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105 text-left';
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
                buttonElement.className = buttonElement.className.replace('bg-white', 'bg-green-500');
                buttonElement.classList.add('text-white', 'pulse-correct');
                this.showFeedback('정답이에요! 🎉', 'success');
            } else {
                buttonElement.className = buttonElement.className.replace('bg-white', 'bg-red-500');
                buttonElement.classList.add('text-white', 'pulse-wrong');
                
                buttons.forEach(btn => {
                    if (btn.textContent === correctAnswer) {
                        btn.className = btn.className.replace('bg-white', 'bg-green-500');
                        btn.classList.add('text-white');
                    }
                });
                this.showFeedback('아쉬워요! 다음에는 맞출 수 있어요 💪', 'error');
            }
            
            setTimeout(() => this.nextQuestion(), 2000);
        }

        showFeedback(message, type) {
            const feedback = document.createElement('div');
            feedback.className = `fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 text-2xl font-bold px-8 py-4 rounded-2xl shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
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
                    container.innerHTML = '<p class="text-gray-500">아직 기록이 없습니다.</p>';
                    return;
                }
                
                container.innerHTML = rankings.map((record, index) => {
                    const isTopThree = index < 3;
                    const rowBgClass = isTopThree ? 'bg-yellow-100' : 'bg-gray-50';

                    return `
                        <div class="flex items-center p-2 ${rowBgClass} rounded-lg w-full text-sm">
                            <span class="font-bold w-[15%] pr-1">${index + 1}위</span>
                            <span class="w-[40%] truncate pr-2">${record.userName || '익명'}</span>
                            <span class="w-[20%] text-right pr-2">${record.score}점</span>
                            <span class="timer w-[25%] text-right">${record.time_formatted}</span>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                console.error("Ranking fetch error:", error);
                const container = document.getElementById(targetElementId);
                container.innerHTML = '<p class="text-red-500">랭킹을 불러오는데 실패했습니다.</p>';
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
            
            let rankMsgText = '결과를 처리 중입니다...'; // 초기 메시지
            let shouldFetchRanking = true; // 기본적으로 랭킹을 가져오도록 설정

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
                    // 401 이외의 HTTP 에러 (400, 402+, 5xx 등)
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
                    // 성공적으로 응답을 받은 경우 (response.ok === true 및 로그인 리디렉션 아님)
                    const result = await response.json(); 
                    console.log('Game result saved:', result);
                    rankMsgText = '게임 결과가 저장되었습니다!';
                }
            } catch (networkOrParsingError) {
                // 네트워크 에러 (fetch 실패) 또는 성공 응답의 JSON 파싱 실패 시
                console.error("게임 결과 저장 중 네트워크 또는 파싱 에러:", networkOrParsingError);
                rankMsgText = '결과 저장 중 통신 또는 데이터 처리 오류가 발생했습니다.';
            } finally {
                // 항상 실행되는 블록
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
                // 타이머 중지 및 초기화
                this.stopTimer();
                this.isGameActive = false;
                this.startTime = null;
                this.gameTime = 0;
                this.currentQuestionIndex = 0;
                this.score = 0;
                
                // 타이머 표시 초기화
                document.getElementById('gameTimer').textContent = '00:00';
                
                // 화면 전환
                this.hideScreen('gameScreen');
                this.showScreen('mainScreen');
                
                // 랭킹 갱신
                this.fetchAndDisplayRanking();
            }
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        new EconomyCardGame();
    });
</script>
@endpush 