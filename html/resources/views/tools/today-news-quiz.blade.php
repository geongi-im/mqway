@extends('layouts.app')

@section('title', '오늘의 뉴스 퀴즈')

@section('content')
<style>
    .news-quiz-shell {
        background:
            linear-gradient(180deg, #f8f9fb 0%, #eef2f7 100%);
    }

    .news-quiz-band {
        background: #2d3047;
    }

    .news-quiz-card {
        border: 1px solid rgba(45, 48, 71, 0.08);
        box-shadow: 0 18px 45px rgba(45, 48, 71, 0.08);
    }

    .news-quiz-highlight {
        background: linear-gradient(180deg, transparent 52%, rgba(78, 205, 196, 0.38) 52%);
        color: #18202f;
        padding: 0 2px;
        border-radius: 3px;
        font-weight: 900;
    }

    .news-quiz-option {
        border: 2px solid #e5e7eb;
        transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .news-quiz-option:hover:not(:disabled) {
        border-color: #4ecdc4;
        background: rgba(78, 205, 196, 0.06);
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(78, 205, 196, 0.12);
    }

    .news-quiz-option.is-correct {
        border-color: #22c55e;
        background: #ecfdf5;
    }

    .news-quiz-option.is-wrong {
        border-color: #ff4d4d;
        background: #fff1f2;
    }

    .news-quiz-preview-image {
        aspect-ratio: 16 / 9;
        object-fit: cover;
    }

    .news-quiz-source-link {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .news-quiz-modal {
        animation: newsQuizFade 0.18s ease-out;
        z-index: 9999;
    }

    .news-quiz-panel {
        animation: newsQuizSlide 0.24s ease-out;
    }

    @keyframes newsQuizFade {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes newsQuizSlide {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

@php
    $isLoggedIn = Auth::check();
    $displayDate = $todayQuiz ? $todayQuiz['mq_news_date'] : $today;
@endphp

<div class="news-quiz-shell min-h-screen pb-16">
    <section class="news-quiz-band text-white">
        <div class="container mx-auto px-4 max-w-6xl py-10 md:py-12">
            <div class="grid gap-8 lg:grid-cols-[1.4fr_0.8fr] lg:items-end">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-sm font-semibold text-white/90">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v6h6M7 13h10M7 17h6"></path>
                        </svg>
                        오늘의 뉴스
                    </div>
                    <h1 class="mt-5 font-outfit text-3xl font-extrabold tracking-tight md:text-5xl">
                        오늘의 뉴스 <span class="text-[#4ECDC4]">퀴즈</span>
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-relaxed text-white/72 md:text-lg">
                        경제 뉴스의 핵심 키워드를 먼저 확인하고, 짧은 이지선다 퀴즈로 오늘의 흐름을 점검합니다.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/12 bg-white/10 p-5 backdrop-blur">
                    <div>
                        <div>
                            <p class="text-sm font-semibold text-white/62">뉴스 기준일</p>
                            <p class="mt-1 font-outfit text-2xl font-extrabold">{{ $displayDate }}</p>
                        </div>
                    </div>
                    @if($todayQuiz)
                        <button id="openNewsQuizBtn" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#FF4D4D] px-6 py-4 text-base font-extrabold text-white shadow-[0_12px_28px_rgba(255,77,77,0.28)] transition hover:-translate-y-0.5 hover:bg-[#ef3f3f]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-5.197-3.03A1 1 0 008 9.002v5.996a1 1 0 001.555.832l5.197-3a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            게임 시작
                        </button>
                    @else
                        <button type="button" disabled class="mt-5 flex w-full cursor-not-allowed items-center justify-center rounded-xl bg-white/20 px-6 py-4 text-base font-extrabold text-white/60">
                            퀴즈 준비중
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="relative -mt-6">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="grid gap-5 lg:grid-cols-[1fr_0.9fr]">
                <div class="news-quiz-card rounded-2xl bg-white p-6 md:p-8">
                    <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-sm font-bold text-[#4ECDC4]">랜덤 도전</p>
                            <h2 class="mt-2 text-2xl font-extrabold text-[#2D3047]">
                                @if($todayQuiz)
                                    뉴스 헤드라인을 읽고 핵심 키워드를 맞혀보세요.
                                @else
                                    등록된 뉴스 퀴즈가 없습니다.
                                @endif
                            </h2>
                        </div>
                        <div class="rounded-xl bg-[#F8F9FB] px-4 py-3 text-sm font-semibold text-gray-600">
                            1개 뉴스 · 1개 문제
                        </div>
                    </div>

                    <div class="mt-7 grid gap-3 md:grid-cols-3">
                        <div class="rounded-xl bg-[#F8F9FB] p-4">
                            <p class="text-sm font-bold text-[#2D3047]">뉴스 확인</p>
                            <p class="mt-2 text-sm leading-relaxed text-gray-500">헤드라인과 키워드를 먼저 읽습니다.</p>
                        </div>
                        <div class="rounded-xl bg-[#F8F9FB] p-4">
                            <p class="text-sm font-bold text-[#2D3047]">퀴즈 선택</p>
                            <p class="mt-2 text-sm leading-relaxed text-gray-500">두 보기 중 알맞은 답을 고릅니다.</p>
                        </div>
                        <div class="rounded-xl bg-[#F8F9FB] p-4">
                            <p class="text-sm font-bold text-[#2D3047]">결과 확인</p>
                            <p class="mt-2 text-sm leading-relaxed text-gray-500">정답과 해설을 바로 확인합니다.</p>
                        </div>
                    </div>
                </div>

                <div class="news-quiz-card rounded-2xl bg-white p-6 md:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-[#FF4D4D]">내 연속 참여</p>
                            @if($isLoggedIn)
                                <h2 class="mt-2 font-outfit text-4xl font-extrabold text-[#2D3047]"><span id="newsQuizStreakDays">{{ $streakInfo['days'] }}</span>일</h2>
                            @else
                                <h2 class="mt-2 text-2xl font-extrabold text-[#2D3047]">기록 없음</h2>
                            @endif
                        </div>
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FFB347]/18 text-[#b46c00]">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-gray-500">
                        @if($isLoggedIn)
                            퀴즈를 완료하면 연속 참여일이 반영됩니다.
                        @else
                            비회원도 퀴즈를 풀 수 있지만 참여 기록은 저장되지 않습니다.
                        @endif
                    </p>
                    @unless($isLoggedIn)
                        <a href="{{ route('login') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl border border-[#2D3047]/10 px-4 py-2.5 text-sm font-bold text-[#2D3047] transition hover:bg-[#F8F9FB]">
                            로그인
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endunless
                </div>
            </div>

            <div class="news-quiz-card mt-5 rounded-2xl bg-white p-6 md:p-8">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm font-bold text-[#FFB347]">랭킹</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-[#2D3047]">뉴스 퀴즈 랭킹</h2>
                    </div>
                    <span class="rounded-full bg-[#F8F9FB] px-4 py-2 text-sm font-semibold text-gray-500">정답 · 연속 참여일 기준</span>
                </div>

                <div class="mt-6 space-y-2">
                    @forelse($rankingItems as $item)
                        <div class="grid grid-cols-[auto_1fr_auto] items-center gap-4 rounded-xl border border-gray-100 bg-[#FBFCFE] px-4 py-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg {{ $item['rank'] === 1 ? 'bg-[#FFB347] text-white' : 'bg-white text-[#2D3047] border border-gray-100' }} font-outfit text-lg font-extrabold">
                                {{ $item['rank'] }}
                            </div>
                            <div>
                                <p class="font-bold text-[#2D3047]">{{ $item['name'] }}</p>
                                <p class="text-xs font-semibold text-gray-400">연속 {{ $item['streak'] }}일 참여</p>
                            </div>
                            <div class="rounded-full bg-[#4ECDC4]/12 px-3 py-1.5 text-sm font-extrabold text-[#198f87]">
                                {{ $item['score'] }}점
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-200 bg-[#FBFCFE] px-4 py-8 text-center text-sm font-semibold text-gray-400">
                            아직 랭킹 기록이 없습니다.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>

@if($todayQuiz)
<div id="newsQuizModal" class="news-quiz-modal fixed inset-0 z-50 hidden bg-[#F8F9FA]">
    <div class="flex h-full flex-col">
        <header class="border-b border-gray-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[#4ECDC4]">오늘의 뉴스 퀴즈</p>
                    <h2 class="truncate text-base font-extrabold text-[#2D3047] md:text-lg">{{ $displayDate }}</h2>
                </div>
                <button id="closeNewsQuizBtn" class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-600 transition hover:bg-gray-200" aria-label="퀴즈 닫기">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto">
            <div class="mx-auto flex min-h-full max-w-5xl flex-col px-4 py-5 md:py-8">
                <div class="mb-5 grid grid-cols-2 gap-2">
                    <div id="stepNewsIndicator" class="rounded-full bg-[#2D3047] px-4 py-2 text-center text-sm font-bold text-white">오늘의 뉴스</div>
                    <div id="stepQuizIndicator" class="rounded-full bg-white px-4 py-2 text-center text-sm font-bold text-gray-400">퀴즈 문제</div>
                </div>

                <section id="newsStep" class="news-quiz-panel flex-1 rounded-2xl bg-white p-5 shadow-sm md:p-8">
                    <div class="grid gap-5">
                        <section class="rounded-2xl border border-[#2D3047]/10 bg-white p-5 shadow-sm md:p-6">
                            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-[#2D3047] px-3 py-1.5 text-xs font-extrabold text-white">뉴스</span>
                                    <span class="rounded-full bg-[#4ECDC4]/12 px-3 py-1.5 text-sm font-extrabold text-[#198f87]">{{ $todayQuiz['mq_company'] }}</span>
                                    <span class="rounded-full bg-gray-100 px-3 py-1.5 text-sm font-bold text-gray-500">{{ $todayQuiz['mq_news_date'] }}</span>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-[#FF4D4D]">뉴스 헤드라인</p>
                            <h3 id="newsQuizTitle" class="mt-3 text-xl font-extrabold leading-snug text-[#2D3047] md:text-3xl"></h3>

                            <div class="mt-5 overflow-hidden rounded-2xl border border-gray-100 bg-[#F8F9FB]">
                                <div class="bg-gray-100">
                                    @if(!empty($todayQuiz['mq_preview_image_url']))
                                        <img src="{{ $todayQuiz['mq_preview_image_url'] }}" alt="뉴스 썸네일 미리보기" class="news-quiz-preview-image h-36 w-full sm:h-44 md:h-52">
                                    @else
                                        <div class="flex h-36 w-full items-center justify-center text-sm font-bold text-gray-400 sm:h-44 md:h-52">
                                            썸네일 미리보기 없음
                                        </div>
                                    @endif
                                </div>
                                <div class="border-t border-gray-100 bg-white px-4 py-3">
                                    <p class="text-xs font-bold text-gray-400">원본 뉴스 주소</p>
                                    <a href="{{ $todayQuiz['mq_source_url'] }}" target="_blank" rel="noopener noreferrer" class="news-quiz-source-link mt-1 block text-sm font-bold leading-relaxed text-[#2D3047] underline decoration-[#4ECDC4]/50 underline-offset-4">
                                        {{ $todayQuiz['mq_source_url'] }}
                                    </a>
                                </div>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-[#4ECDC4]/30 bg-[#F1FCFB] p-5 md:p-6">
                            <div class="mb-4 flex items-center gap-2">
                                <span class="rounded-full bg-[#4ECDC4] px-3 py-1.5 text-xs font-extrabold text-[#123b3c]">키워드</span>
                                <span class="text-sm font-bold text-[#198f87]">뉴스를 이해하는 핵심 단어</span>
                            </div>
                            <div class="rounded-2xl bg-white p-4 md:p-5">
                                <p class="text-sm font-bold text-gray-500">추출 키워드</p>
                                <div class="mt-3 inline-flex rounded-xl bg-[#2D3047] px-4 py-2 font-extrabold text-white">{{ $todayQuiz['mq_keyword'] }}</div>
                                <p class="mt-4 text-base font-semibold leading-relaxed text-[#2D3047] md:text-lg">
                                    {{ $todayQuiz['mq_keyword_description'] }}
                                </p>
                            </div>
                        </section>
                    </div>

                    <div class="mt-8 flex flex-col gap-3 border-t border-gray-100 pt-5 md:flex-row md:items-center md:justify-between">
                        <p class="text-sm font-semibold text-gray-500">뉴스와 키워드 설명을 확인한 뒤 문제로 이동하세요.</p>
                        <button id="goQuizStepBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#FF4D4D] px-6 py-3.5 text-base font-extrabold text-white shadow-[0_12px_28px_rgba(255,77,77,0.25)] transition hover:bg-[#ef3f3f]">
                            퀴즈 풀기
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </section>

                <section id="quizStep" class="news-quiz-panel hidden flex-1 rounded-2xl bg-white p-5 shadow-sm md:p-8">
                    <div class="mx-auto max-w-3xl">
                        <div class="mb-6 text-center">
                            <p class="text-sm font-bold text-[#4ECDC4]">문제</p>
                            <h3 id="newsQuizQuestion" class="mt-3 text-xl font-extrabold leading-relaxed text-[#2D3047] md:text-3xl"></h3>
                        </div>

                        <div id="newsQuizOptions" class="space-y-3"></div>

                        <div id="newsQuizResult" class="mt-6 hidden rounded-2xl border border-gray-100 bg-[#F8F9FB] p-5">
                            <p id="newsQuizResultTitle" class="text-lg font-extrabold text-[#2D3047]"></p>
                            <p id="newsQuizExplanation" class="mt-2 text-sm leading-relaxed text-gray-600"></p>
                            <p id="newsQuizHistoryStatus" class="mt-3 text-xs font-bold text-gray-400"></p>
                            <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:justify-end">
                                <button id="backToNewsBtn" class="rounded-xl bg-white px-5 py-3 text-sm font-bold text-[#2D3047] transition hover:bg-gray-100">뉴스 다시 보기</button>
                                <button id="finishNewsQuizBtn" class="rounded-xl bg-[#4ECDC4] px-5 py-3 text-sm font-extrabold text-[#143a3b] transition hover:bg-[#43beb6]">완료</button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const quiz = @json($todayQuiz);
    const isLoggedIn = @json($isLoggedIn);
    const storeUrl = @json(route('tools.today-news-quiz.store'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const modal = document.getElementById('newsQuizModal');
    const openBtn = document.getElementById('openNewsQuizBtn');
    const closeBtn = document.getElementById('closeNewsQuizBtn');
    const goQuizStepBtn = document.getElementById('goQuizStepBtn');
    const backToNewsBtn = document.getElementById('backToNewsBtn');
    const finishBtn = document.getElementById('finishNewsQuizBtn');
    const newsStep = document.getElementById('newsStep');
    const quizStep = document.getElementById('quizStep');
    const stepNewsIndicator = document.getElementById('stepNewsIndicator');
    const stepQuizIndicator = document.getElementById('stepQuizIndicator');
    const resultBox = document.getElementById('newsQuizResult');
    const resultTitle = document.getElementById('newsQuizResultTitle');
    const explanation = document.getElementById('newsQuizExplanation');
    const historyStatus = document.getElementById('newsQuizHistoryStatus');
    const optionWrap = document.getElementById('newsQuizOptions');

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function highlightKeyword(title, keyword) {
        const escapedTitle = escapeHtml(title);
        const escapedKeyword = escapeHtml(keyword);
        if (!escapedKeyword) {
            return escapedTitle;
        }

        return escapedTitle.replace(
            escapedKeyword,
            '<mark class="news-quiz-highlight">' + escapedKeyword + '</mark>'
        );
    }

    function setStep(step) {
        const isNews = step === 'news';
        newsStep.classList.toggle('hidden', !isNews);
        quizStep.classList.toggle('hidden', isNews);
        stepNewsIndicator.className = isNews
            ? 'rounded-full bg-[#2D3047] px-4 py-2 text-center text-sm font-bold text-white'
            : 'rounded-full bg-white px-4 py-2 text-center text-sm font-bold text-gray-400';
        stepQuizIndicator.className = !isNews
            ? 'rounded-full bg-[#2D3047] px-4 py-2 text-center text-sm font-bold text-white'
            : 'rounded-full bg-white px-4 py-2 text-center text-sm font-bold text-gray-400';
    }

    function resetQuestion() {
        resultBox.classList.add('hidden');
        resultTitle.textContent = '';
        explanation.textContent = '';
        historyStatus.textContent = '';
        Array.from(optionWrap.querySelectorAll('button')).forEach(function (button) {
            button.disabled = false;
            button.classList.remove('is-correct', 'is-wrong');
        });
    }

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setStep('news');
        resetQuestion();
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function confirmCloseModal() {
        if (confirm('퀴즈를 종료하시겠습니까?')) {
            window.location.reload();
        }
    }

    function selectAnswer(selectedKey) {
        const answerKey = quiz.mq_quiz_content.answer;
        const isCorrect = selectedKey === answerKey;

        Array.from(optionWrap.querySelectorAll('button')).forEach(function (button) {
            const optionKey = button.getAttribute('data-option-key');
            button.disabled = true;

            if (optionKey === answerKey) {
                button.classList.add('is-correct');
            } else if (optionKey === selectedKey) {
                button.classList.add('is-wrong');
            }
        });

        resultTitle.textContent = isCorrect ? '정답입니다.' : '아쉽지만 정답은 다른 보기입니다.';
        explanation.textContent = quiz.mq_quiz_content.explanation;
        historyStatus.textContent = isLoggedIn ? '참여 기록을 저장하는 중입니다.' : '비회원 참여는 기록으로 저장되지 않습니다.';
        resultBox.classList.remove('hidden');

        storeAnswer(selectedKey);
    }

    function storeAnswer(selectedKey) {
        if (!isLoggedIn) {
            return;
        }

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                quiz_idx: quiz.idx,
                selected_answer: selectedKey
            })
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (!result.ok) {
                    throw new Error(result.data.error || '참여 기록 저장에 실패했습니다.');
                }

                historyStatus.textContent = '참여 기록이 저장되었습니다.';

                if (typeof result.data.streak_days !== 'undefined') {
                    const streakDays = document.getElementById('newsQuizStreakDays');
                    if (streakDays) {
                        streakDays.textContent = result.data.streak_days;
                    }
                }
            })
            .catch(function (error) {
                historyStatus.textContent = error.message;
            });
    }

    function renderQuiz() {
        document.getElementById('newsQuizTitle').innerHTML = highlightKeyword(quiz.mq_title, quiz.mq_keyword);
        document.getElementById('newsQuizQuestion').textContent = quiz.mq_quiz_content.question;

        const options = [
            { key: 'option_a', label: 'A', text: quiz.mq_quiz_content.option_a },
            { key: 'option_b', label: 'B', text: quiz.mq_quiz_content.option_b },
        ];

        optionWrap.innerHTML = options.map(function (option) {
            return [
                '<button type="button" data-option-key="' + option.key + '" class="news-quiz-option flex w-full items-start gap-4 rounded-2xl bg-white p-4 text-left">',
                    '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#2D3047] font-outfit text-base font-extrabold text-white">' + option.label + '</span>',
                    '<span class="pt-1 text-base font-bold leading-relaxed text-[#2D3047]">' + escapeHtml(option.text) + '</span>',
                '</button>'
            ].join('');
        }).join('');

        Array.from(optionWrap.querySelectorAll('button')).forEach(function (button) {
            button.addEventListener('click', function () {
                selectAnswer(button.getAttribute('data-option-key'));
            });
        });
    }

    renderQuiz();

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', confirmCloseModal);
    finishBtn.addEventListener('click', closeModal);
    goQuizStepBtn.addEventListener('click', function () {
        setStep('quiz');
    });
    backToNewsBtn.addEventListener('click', function () {
        setStep('news');
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>
@endif
@endsection
