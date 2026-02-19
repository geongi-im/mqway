@extends('layouts.app')

@section('title', '경제 상식 퀴즈')

@push('styles')
<style>
/* 로딩 애니메이션 */
.dot-animation {
    animation: dots 1.5s infinite;
}

@keyframes dots {
    0%, 20% { content: "."; }
    40% { content: ".."; }
    60%, 100% { content: "..."; }
}

/* 퀴즈 옵션 효과 */
.quiz-option-btn {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid #E5E7EB;
}
.quiz-option-btn:hover {
    border-color: #4ECDC4;
    background: rgba(78, 205, 196, 0.05);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(78, 205, 196, 0.12);
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
            <span>📝</span> <span>학습 도구</span>
        </div>
        <h1 class="font-outfit text-3xl md:text-5xl font-extrabold text-white mb-4 tracking-tight animate-slideUp" style="animation-delay: 0.1s;">
            경제 상식 <span class="text-[#4ECDC4]">퀴즈</span>
        </h1>
        <p class="text-white/70 text-base md:text-lg max-w-2xl mx-auto leading-relaxed animate-slideUp" style="animation-delay: 0.2s;">
            경제 지식을 테스트하고 새로운 개념을 배워보세요!
        </p>
    </div>
</div>

<!-- ===== Main Content ===== -->
<div class="relative z-20 -mt-24 pb-16">
    <div class="container mx-auto px-4 max-w-4xl">

        <!-- 퀴즈 안내 카드 -->
        <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6 animate-slideUp" style="animation-delay: 0.3s;">
            <h2 class="text-xl font-bold text-[#2D3047] mb-5 flex items-center gap-2">
                <span class="w-8 h-8 bg-gradient-to-br from-[#4ECDC4] to-[#26D0CE] rounded-lg flex items-center justify-center text-white text-sm">📋</span>
                퀴즈 안내
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-6 h-6 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs">✓</span>
                    <p class="text-gray-600 text-sm">총 10개의 문제가 출제됩니다.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-6 h-6 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs">✓</span>
                    <p class="text-gray-600 text-sm">4개의 보기 중 하나를 선택하는 객관식입니다.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-6 h-6 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs">✓</span>
                    <p class="text-gray-600 text-sm">퀴즈 결과와 정답 해설을 확인할 수 있습니다.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-[#F8F9FB] rounded-xl">
                    <span class="flex-shrink-0 w-6 h-6 bg-[#4ECDC4] text-white rounded-full flex items-center justify-center text-xs">✓</span>
                    <p class="text-gray-600 text-sm">문제를 풀면서 경제 지식을 쌓아보세요!</p>
                </div>
            </div>

            <div class="text-center">
                <button id="startQuizBtn" class="bg-gradient-to-r from-[#FF4D4D] to-[#FF6B6B] hover:from-[#FF3333] hover:to-[#FF4D4D] text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 text-lg shadow-[0_8px_25px_rgba(255,77,77,0.35)] hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(255,77,77,0.45)]">
                    📝 퀴즈 시작하기
                </button>
            </div>
        </div>

        <!-- 기능 소개 카드 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-slideUp" style="animation-delay: 0.4s;">
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center group hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-gradient-to-br from-[#4ECDC4]/20 to-[#4ECDC4]/5 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-[#4ECDC4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-[#2D3047] mb-2">다양한 문제</h3>
                <p class="text-gray-500 text-sm leading-relaxed">경제 기본 개념부터 실생활 금융 지식까지</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center group hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-gradient-to-br from-[#FF4D4D]/20 to-[#FF4D4D]/5 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-[#FF4D4D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-[#2D3047] mb-2">즉시 채점</h3>
                <p class="text-gray-500 text-sm leading-relaxed">정답과 해설을 바로 확인할 수 있습니다</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center group hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-gradient-to-br from-[#FFB347]/20 to-[#FFB347]/5 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-[#FFB347]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-[#2D3047] mb-2">학습 효과</h3>
                <p class="text-gray-500 text-sm leading-relaxed">해설을 통해 경제 지식을 체계적으로 학습</p>
            </div>
        </div>

    </div>
</div>

<!-- 퀴즈 모달 -->
<div id="quizModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-white">
        <!-- 닫기 버튼 -->
        <button id="closeQuizBtn" class="absolute top-5 right-5 w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-700 z-10 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- 콘텐츠 컨테이너 -->
        <div class="w-full h-full overflow-y-auto">
            <div class="max-w-3xl mx-auto px-4 py-8">
                <!-- 진행 상태 바 -->
                <div class="mb-8">
                    <div class="relative pt-1">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-right">
                                <span class="text-xs font-bold inline-block text-[#4ECDC4]">
                                    <span id="currentQuestionNumber">0</span>/10
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2.5 text-xs flex rounded-full bg-gray-100">
                            <div id="progressBar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-[#4ECDC4] to-[#26D0CE] transition-all duration-500 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <div id="quizContent" class="space-y-8">
                    <!-- 퀴즈 내용이 여기에 동적으로 로드됩니다 -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startQuizBtn = document.getElementById('startQuizBtn');
    const closeQuizBtn = document.getElementById('closeQuizBtn');
    const quizModal = document.getElementById('quizModal');
    const quizContent = document.getElementById('quizContent');
    const progressBar = document.getElementById('progressBar');
    const currentQuestionNumber = document.getElementById('currentQuestionNumber');
    
    let currentQuestion = 0;
    let score = 0;
    let quizData = [];
    let userAnswers = [];
    
    function disableBodyScroll() {
        document.body.style.overflow = 'hidden';
    }
    
    function enableBodyScroll() {
        document.body.style.overflow = '';
    }
    
    startQuizBtn.addEventListener('click', function() {
        fetch('/api/quiz')
            .then(response => response.json())
            .then(data => {
                quizData = data;
                currentQuestion = 0;
                score = 0;
                showQuestion();
                quizModal.classList.remove('hidden');
                quizModal.classList.add('flex');
                updateProgress();
                disableBodyScroll();
            })
            .catch(error => {
                console.error('Error loading quiz data:', error);
                alert('퀴즈 데이터를 불러오는 중 오류가 발생했습니다.');
            });
    });
    
    closeQuizBtn.addEventListener('click', function() {
        if(confirm('퀴즈를 종료하시겠습니까?')) {
            quizModal.classList.add('hidden');
            quizModal.classList.remove('flex');
            enableBodyScroll();
        }
    });
    
    function updateProgress() {
        const progress = (currentQuestion / quizData.length) * 100;
        progressBar.style.width = `${progress}%`;
        currentQuestionNumber.textContent = currentQuestion;
    }
    
    function showQuestion() {
        if (currentQuestion < quizData.length) {
            const question = quizData[currentQuestion];
            let html = `
                <div class="max-w-2xl mx-auto">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-[#4ECDC4]/20 to-[#4ECDC4]/5 rounded-2xl text-[#4ECDC4] text-2xl font-extrabold mb-6">Q${currentQuestion + 1}</div>
                        <h3 class="text-xl md:text-2xl font-bold text-[#2D3047] leading-relaxed">${question.question}</h3>
                    </div>
                    <div class="space-y-3 max-w-xl mx-auto">
                        ${question.options.map((option, index) => `
                            <button onclick="checkAnswer(${index})" 
                                    class="quiz-option-btn w-full p-4 text-center text-gray-700 bg-white rounded-xl font-medium text-sm">
                                ${option}
                            </button>
                        `).join('')}
                    </div>
                </div>
            `;
            quizContent.innerHTML = html;
        } else {
            showResults();
        }
        updateProgress();
    }
    
    window.checkAnswer = function(selectedIndex) {
        const correctAnswer = quizData[currentQuestion].correctAnswer;
        userAnswers[currentQuestion] = selectedIndex;
        
        if (selectedIndex === correctAnswer) {
            score++;
        }
        
        currentQuestion++;
        showQuestion();
    };
    
    function getResultMessage(score, total) {
        if (score === total) {
            return {
                emoji: '✅',
                title: '완벽해요! 🎉\n당신은 진정한 경제 상식 마스터!',
                message: '경제 흐름이 눈에 보이기 시작했어요.\n지금 바로 다음 퀴즈에도 도전해보세요!'
            };
        } else if (score >= 8) {
            return {
                emoji: '🥳',
                title: '아주 훌륭해요! 💪\n거의 다 왔어요!',
                message: '경제를 보는 눈이 남다르네요.\n아쉬운 한두 문제만 복습하면 금방 만점입니다!'
            };
        } else if (score >= 6) {
            return {
                emoji: '👍',
                title: '좋은 출발이에요! 🚀\n기본은 충분히 갖췄어요.',
                message: '이제 조금만 더 공부하면 만점도 가능해요.\n틀린 문제는 다시 한 번 체크해보는 건 어떨까요?'
            };
        } else {
            return {
                emoji: '🙈',
                title: '아직은 조금 아쉬워요... 😅\n하지만 시작이 반이에요!',
                message: '경제 상식은 누구나 처음엔 어렵지만,\n계속 풀다 보면 분명 실력이 쑥쑥 올라갈 거예요!'
            };
        }
    }

    function showResults() {
        const resultMessage = getResultMessage(score, quizData.length);
        let html = `
            <div class="max-w-2xl mx-auto text-center">
                <div class="mb-12">
                    <div class="text-6xl mb-6">${resultMessage.emoji}</div>
                    <h3 class="text-3xl font-extrabold text-[#2D3047] mb-4">테스트 완료!</h3>
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-[#4ECDC4]/10 to-[#4ECDC4]/5 text-[#4ECDC4] font-extrabold text-2xl py-3 px-8 rounded-2xl mb-4">
                        ${score} / ${quizData.length}
                    </div>
                    <div class="space-y-2 mt-4">
                        <p class="text-lg font-semibold text-[#2D3047] whitespace-pre-line">${resultMessage.title}</p>
                        <p class="text-gray-500 whitespace-pre-line">${resultMessage.message}</p>
                    </div>
                </div>
                
                <div class="flex justify-center gap-3">
                    <button onclick="showAnswers()" 
                            class="bg-gradient-to-r from-[#4ECDC4] to-[#26D0CE] text-white font-bold py-3.5 px-8 rounded-xl transition-all duration-300 shadow-[0_8px_25px_rgba(78,205,196,0.35)] hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(78,205,196,0.45)]">
                        정답 확인하기
                    </button>
                    <button onclick="closeQuiz()" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3.5 px-8 rounded-xl transition-all duration-300">
                        종료하기
                    </button>
                </div>
            </div>
        `;
        quizContent.innerHTML = html;
        progressBar.style.width = '100%';
        currentQuestionNumber.textContent = quizData.length;
    }

    window.showAnswers = function() {
        let html = `
            <div class="max-w-2xl mx-auto">
                <div class="space-y-4">
                    ${quizData.map((question, index) => {
                        const isCorrect = userAnswers[index] === question.correctAnswer;
                        const userAnswer = userAnswers[index];
                        
                        return `
                            <div class="p-5 rounded-2xl ${isCorrect ? 'bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100' : 'bg-gradient-to-r from-red-50 to-rose-50 border border-red-100'}">
                                <div class="flex items-start gap-4">
                                    <span class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-[#4ECDC4]/20 to-[#4ECDC4]/5 rounded-xl flex items-center justify-center text-[#4ECDC4] text-sm font-extrabold">Q${index + 1}</span>
                                    <div class="flex-1">
                                        <p class="text-base font-bold text-[#2D3047] mb-3">${question.question}</p>
                                        
                                        <div class="space-y-1.5">
                                            ${question.options.map((option, optionIndex) => `
                                                <div class="flex items-center p-2 rounded-lg ${optionIndex === question.correctAnswer ? 'bg-green-100/50' : optionIndex === userAnswer && !isCorrect ? 'bg-red-100/50' : ''}">
                                                    <span class="w-6 h-6 flex items-center justify-center rounded-full mr-2 text-xs flex-shrink-0
                                                        ${optionIndex === question.correctAnswer ? 'bg-[#4ECDC4] text-white' : 
                                                          optionIndex === userAnswer ? 'bg-[#FF4D4D] text-white' : 'bg-gray-200'}"
                                                    >
                                                        ${optionIndex === question.correctAnswer ? '✓' : 
                                                          optionIndex === userAnswer ? '×' : ''}
                                                    </span>
                                                    <span class="text-sm ${optionIndex === question.correctAnswer ? 'font-semibold text-[#2D3047]' : 
                                                                 optionIndex === userAnswer && !isCorrect ? 'text-red-600' : 'text-gray-600'}"
                                                    >
                                                        ${option}
                                                    </span>
                                                </div>
                                            `).join('')}
                                        </div>
                                        
                                        <div class="mt-3 pt-3 border-t border-gray-200/50">
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold ${isCorrect ? 'bg-[#4ECDC4]/10 text-[#4ECDC4]' : 'bg-[#FF4D4D]/10 text-[#FF4D4D]'}">
                                                ${isCorrect ? '✓ 정답' : '✗ 오답'}
                                            </span>
                                            <p class="mt-2 text-gray-500 text-sm leading-relaxed">${question.explanation}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
                <div class="mt-8 text-center">
                    <button onclick="closeQuiz()" 
                            class="bg-gradient-to-r from-[#4ECDC4] to-[#26D0CE] text-white font-bold py-3.5 px-8 rounded-xl transition-all duration-300 shadow-[0_8px_25px_rgba(78,205,196,0.35)] hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(78,205,196,0.45)]">
                        완료
                    </button>
                </div>
            </div>
        `;
        quizContent.innerHTML = html;
    }
    
    window.closeQuiz = function() {
        quizModal.classList.add('hidden');
        quizModal.classList.remove('flex');
        enableBodyScroll();
    }
});
</script>
@endpush