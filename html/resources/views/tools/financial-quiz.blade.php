@extends('layouts.app')

@section('title', '경제 상식 퀴즈')

@push('styles')
<style>
/* 로딩 애니메이션 */
.dot-animation {
    animation: dots 1.5s infinite;
}

@keyframes dots {
    0%, 20% {
        content: ".";
    }
    40% {
        content: "..";
    }
    60%, 100% {
        content: "...";
    }
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- 헤더 섹션 -->
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">경제 상식 퀴즈</h1>
                <p class="text-lg text-gray-600 mb-8">경제 지식을 테스트하고 새로운 개념을 배워보세요!</p>
                
                <!-- 시작하기 버튼 -->
                <button id="startQuizBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-4 px-8 rounded-full transition-all duration-300 text-lg transform hover:scale-105 hover:shadow-lg">
                    퀴즈 시작하기
                </button>
            </div>
            
            <!-- 설명 섹션 -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">퀴즈 안내</h2>
                <ul class="list-disc pl-6 space-y-2 text-gray-600">
                    <li>총 10개의 문제가 출제됩니다.</li>
                    <li>각 문제는 4개의 보기 중 하나를 선택하는 객관식입니다.</li>
                    <li>퀴즈 결과와 정답 해설을 확인할 수 있습니다.</li>
                    <li>문제를 풀면서 경제 지식을 쌓아보세요!</li>
                </ul>
            </div>
            
            <!-- 기능 소개 섹션 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">다양한 문제</h3>
                    <p class="text-gray-600">경제 기본 개념부터 실생활 금융 지식까지 다양한 문제</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">즉시 채점</h3>
                    <p class="text-gray-600">문제를 풀면 바로 정답과 해설을 확인할 수 있습니다</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">학습 효과</h3>
                    <p class="text-gray-600">틀린 문제의 해설을 통해 경제 지식을 체계적으로 학습</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 퀴즈 모달 -->
<div id="quizModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-white">
        <!-- 닫기 버튼 -->
        <button id="closeQuizBtn" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- 콘텐츠 컨테이너 -->
        <div class="w-full h-full overflow-y-auto">
            <div class="max-w-3xl mx-auto px-4 py-8">
                <!-- 진행 상태 바 -->
                <div class="mb-8">
                    <div class="relative pt-1">
                        <div class="flex items-center justify-between">
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-blue-600">
                                    <span id="currentQuestionNumber">0</span>/10
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-blue-100">
                            <div id="progressBar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500" style="width: 0%"></div>
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
    
    // 팝업 열릴 때 외부 스크롤 비활성화
    function disableBodyScroll() {
        document.body.style.overflow = 'hidden';
    }
    
    // 팝업 닫힐 때 외부 스크롤 활성화
    function enableBodyScroll() {
        document.body.style.overflow = '';
    }
    
    // 퀴즈 시작 버튼 클릭 이벤트
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
                disableBodyScroll(); // 외부 스크롤 비활성화
            })
            .catch(error => {
                console.error('Error loading quiz data:', error);
                alert('퀴즈 데이터를 불러오는 중 오류가 발생했습니다.');
            });
    });
    
    // 모달 닫기 버튼 클릭 이벤트
    closeQuizBtn.addEventListener('click', function() {
        if(confirm('퀴즈를 종료하시겠습니까?')) {
            quizModal.classList.add('hidden');
            quizModal.classList.remove('flex');
            enableBodyScroll(); // 외부 스크롤 활성화
        }
    });
    
    // 진행 상태 업데이트 함수
    function updateProgress() {
        const progress = (currentQuestion / quizData.length) * 100;
        progressBar.style.width = `${progress}%`;
        currentQuestionNumber.textContent = currentQuestion;
    }
    
    // 퀴즈 문제 표시 함수
    function showQuestion() {
        if (currentQuestion < quizData.length) {
            const question = quizData[currentQuestion];
            let html = `
                <div class="max-w-2xl mx-auto">
                    <div class="text-center mb-12">
                        <div class="text-blue-500 text-3xl font-bold mb-6">Q${currentQuestion + 1}.</div>
                        <h3 class="text-2xl font-semibold text-gray-800">${question.question}</h3>
                    </div>
                    <div class="space-y-4 max-w-xl mx-auto">
                        ${question.options.map((option, index) => `
                            <button onclick="checkAnswer(${index})" 
                                    class="w-full p-4 text-center text-gray-700 bg-white border-2 border-gray-200 rounded-full hover:border-blue-500 hover:bg-blue-50 transition-all duration-200">
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
    
    // 정답 확인 함수
    window.checkAnswer = function(selectedIndex) {
        const correctAnswer = quizData[currentQuestion].correctAnswer;
        
        // 사용자의 답안 저장
        userAnswers[currentQuestion] = selectedIndex;
        
        if (selectedIndex === correctAnswer) {
            score++;
        }
        
        currentQuestion++;
        showQuestion();
    };
    
    // 결과 메시지 함수 추가
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

    // 결과 표시 함수 수정
    function showResults() {
        const resultMessage = getResultMessage(score, quizData.length);
        let html = `
            <div class="max-w-2xl mx-auto text-center">
                <div class="mb-12">
                    <div class="text-6xl mb-6">${resultMessage.emoji}</div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">테스트 완료!</h3>
                    <p class="text-2xl text-blue-600 font-semibold mb-4">당신의 점수: ${score} / ${quizData.length}</p>
                    <div class="space-y-2">
                        <p class="text-xl font-semibold text-gray-800 whitespace-pre-line">${resultMessage.title}</p>
                        <p class="text-gray-600 whitespace-pre-line">${resultMessage.message}</p>
                    </div>
                </div>
                
                <div class="flex justify-center gap-6">
                    <button onclick="showAnswers()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-full transition-all duration-300 text-lg transform hover:scale-105 hover:shadow-lg">
                        정답 확인하기
                    </button>
                    <button onclick="closeQuiz()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-8 rounded-full transition-all duration-300 text-lg transform hover:scale-105 hover:shadow-lg">
                        종료하기
                    </button>
                </div>
            </div>
        `;
        quizContent.innerHTML = html;
        progressBar.style.width = '100%';
        currentQuestionNumber.textContent = quizData.length;
    }

    // 정답 확인 화면 함수 수정
    window.showAnswers = function() {
        let html = `
            <div class="max-w-2xl mx-auto">
                <div class="space-y-6">
                    ${quizData.map((question, index) => {
                        const isCorrect = userAnswers[index] === question.correctAnswer;
                        const userAnswer = userAnswers[index];
                        
                        return `
                            <div class="p-6 rounded-lg ${isCorrect ? 'bg-green-50' : 'bg-red-50'}">
                                <div class="flex items-start gap-4">
                                    <span class="text-blue-500 text-xl font-bold">Q${index + 1}.</span>
                                    <div class="flex-1">
                                        <p class="text-lg font-semibold text-gray-800">${question.question}</p>
                                        
                                        <div class="mt-3 space-y-2">
                                            ${question.options.map((option, optionIndex) => `
                                                <div class="flex items-center">
                                                    <span class="w-6 h-6 flex items-center justify-center rounded-full mr-2 text-sm
                                                        ${optionIndex === question.correctAnswer ? 'bg-blue-500 text-white' : 
                                                          optionIndex === userAnswer ? 'bg-red-500 text-white' : 'bg-gray-200'}"
                                                    >
                                                        ${optionIndex === question.correctAnswer ? '✓' : 
                                                          optionIndex === userAnswer ? '×' : ''}
                                                    </span>
                                                    <span class="${optionIndex === question.correctAnswer ? 'font-semibold text-blue-700' : 
                                                                 optionIndex === userAnswer && !isCorrect ? 'text-red-700' : 'text-gray-700'}"
                                                    >
                                                        ${option}
                                                    </span>
                                                </div>
                                            `).join('')}
                                        </div>
                                        
                                        <div class="mt-4">
                                            <span class="inline-block px-2 py-1 rounded text-sm ${isCorrect ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'}">
                                                ${isCorrect ? '정답입니다!' : '틀렸습니다'}
                                            </span>
                                            <p class="mt-2 text-gray-600">${question.explanation}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
                <div class="mt-8 text-center">
                    <button onclick="closeQuiz()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-full transition-all duration-300 text-lg transform hover:scale-105 hover:shadow-lg">
                        완료
                    </button>
                </div>
            </div>
        `;
        quizContent.innerHTML = html;
    }
    
    // 퀴즈 종료 함수 (전역에서 접근 가능)
    window.closeQuiz = function() {
        quizModal.classList.add('hidden');
        quizModal.classList.remove('flex');
        enableBodyScroll(); // 외부 스크롤 활성화
    }
});
</script>
@endpush 