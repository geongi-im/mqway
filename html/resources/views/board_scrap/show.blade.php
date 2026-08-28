@extends('layouts.app')

@section('content')
<!-- ===== Hero Background ===== -->
<div class="relative bg-[#3D4148] pb-32 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>

    <div class="container mx-auto px-4 pt-28 pb-8 relative z-10 max-w-4xl animate-slideUp">
        <a href="{{ route('board-scrap.index') }}" class="inline-flex items-center text-gray-400 hover:text-white mb-6 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center mr-2 group-hover:bg-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            목록으로 돌아가기
        </a>
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-xs font-medium backdrop-blur-md">
                📰 News Scrap
            </span>
            @if($isOwner)
                @if($scrap->isPublic())
                <span id="visibilityBadge" class="inline-flex items-center gap-1 py-1 px-3 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-xs font-bold backdrop-blur-md">
                    공개 중
                </span>
                @else
                <span id="visibilityBadge" class="inline-flex items-center gap-1 py-1 px-3 rounded-full bg-white/10 border border-white/20 text-gray-300 text-xs font-bold backdrop-blur-md">
                    나만보기
                </span>
                @endif
            @endif
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-4 leading-tight tracking-tight">{{ $scrap->mq_title }}</h1>
        <div class="flex flex-wrap items-center text-gray-400 text-sm gap-4">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>{{ $scrap->getAuthorName() }}</span>
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ $scrap->mq_reg_date ? $scrap->mq_reg_date->format('Y.m.d H:i') : '' }}</span>
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span>조회 {{ number_format($scrap->mq_view_cnt) }}</span>
            </div>
            @if($scrap->mq_update_date)
            <div class="flex items-center text-gray-500">
                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>수정: {{ $scrap->mq_update_date->format('Y.m.d H:i') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ===== Content Section ===== -->
<div class="container mx-auto px-4 -mt-20 relative z-20 pb-20">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-4xl mx-auto animate-slideUp" style="animation-delay: 0.3s;">
        <div class="p-8 md:p-10">

            <!-- 원문 링크 -->
            <div class="mb-8 text-center">
                <a href="{{ $scrap->mq_url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    뉴스 원문 보러가기
                </a>
            </div>

            <!-- 썸네일 이미지 -->
            @if($scrap->hasThumbnail())
            <div class="mb-8">
                <img src="{{ $scrap->getThumbnailUrl() }}"
                     alt="{{ $scrap->mq_title }}"
                     class="w-full max-w-2xl mx-auto rounded-xl shadow-md"
                     onerror="this.parentElement.style.display='none';">
            </div>
            @endif

            <!-- 뉴스를 선택한 이유 -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#FF4D4D] to-[#e03e3e] flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-[#2D3047]">뉴스를 선택한 이유</h2>
                </div>
                <div class="prose max-w-none bg-gray-50 rounded-xl p-6 border border-gray-100">
                    {!! $scrap->mq_reason !!}
                </div>
            </div>

            <!-- ===== AI 분석 결과 ===== -->
            @if($scrap->hasAiAnalysis())
            @php
                $aiTerms = $scrap->getAiTerms();
                $checkedTerms = $scrap->getCheckedTerms();
                $outlookShort = $scrap->getOutlookLines('short');
                $outlookLong = $scrap->getOutlookLines('long');
                $aiQuestions = $scrap->getAiQuestions();
                $aiAnswers = $scrap->getAiAnswers(count($aiQuestions));
            @endphp
            <div class="mb-8 space-y-8">

                <!-- 뉴스에 대한 짧은 해석 -->
                @if($scrap->mq_ai_interpretation)
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-[#2D3047]">뉴스에 대한 짧은 해석</h2>
                    </div>
                    <div class="bg-violet-50/60 border border-violet-100 rounded-xl p-6">
                        <p class="text-gray-800 whitespace-pre-line leading-relaxed">{{ $scrap->mq_ai_interpretation }}</p>
                    </div>
                </div>
                @endif

                <!-- 뉴스 속 경제 용어 -->
                @if(count($aiTerms))
                <div>
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#4ECDC4] to-[#2AA9A0] flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-[#2D3047]">뉴스 속 경제 용어</h2>
                        <span class="text-xs text-gray-400">{{ count($aiTerms) }}개</span>
                        @if(count($checkedTerms))
                        <span class="inline-flex items-center gap-1 py-1 px-2.5 rounded-full bg-[#4ECDC4]/15 text-[#2AA9A0] text-xs font-bold">
                            저장한 용어 {{ count($checkedTerms) }}개
                        </span>
                        @endif
                    </div>
                    <div class="space-y-3">
                        @foreach($aiTerms as $term)
                        <div class="rounded-xl border p-5 {{ $term['checked'] ? 'border-[#4ECDC4]/50 bg-[#4ECDC4]/5' : 'border-gray-100 bg-gray-50' }}">
                            <div class="flex items-start gap-3">
                                @if($term['checked'])
                                <span class="shrink-0 w-5 h-5 mt-0.5 rounded-full bg-[#4ECDC4] flex items-center justify-center" title="저장한 용어">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                @else
                                <span class="shrink-0 w-5 h-5 mt-0.5 flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                </span>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-bold text-[#2D3047] mb-1">{{ $term['term'] }}</p>
                                    <p class="text-gray-700 leading-relaxed">{{ $term['definition'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- 향후 전망 -->
                @if(count($outlookShort) || count($outlookLong))
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-[#2D3047]">향후 전망</h2>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        @if(count($outlookShort))
                        <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-5">
                            <p class="text-sm font-bold text-amber-700 mb-3 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                단기 (3~6개월)
                            </p>
                            <ul class="space-y-2.5">
                                @foreach($outlookShort as $line)
                                <li class="flex items-start gap-2 text-gray-800 leading-relaxed">
                                    <span class="shrink-0 mt-2 w-1 h-1 rounded-full bg-amber-400"></span>
                                    <span>{{ $line }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if(count($outlookLong))
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-5">
                            <p class="text-sm font-bold text-indigo-700 mb-3 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                중장기 (1~3년)
                            </p>
                            <ul class="space-y-2.5">
                                @foreach($outlookLong as $line)
                                <li class="flex items-start gap-2 text-gray-800 leading-relaxed">
                                    <span class="shrink-0 mt-2 w-1 h-1 rounded-full bg-indigo-400"></span>
                                    <span>{{ $line }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 내 경제상황에 맞는 질문 -->
                @if(count($aiQuestions))
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#2D3047] to-[#1A1C29] flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-[#2D3047]">내 경제상황에 맞는 질문</h2>
                    </div>
                    <div class="space-y-3">
                        @foreach($aiQuestions as $index => $question)
                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                            <div class="flex items-start gap-4">
                                <span class="shrink-0 w-7 h-7 rounded-full bg-[#2D3047] text-white text-xs font-bold flex items-center justify-center">
                                    {{ $index + 1 }}
                                </span>
                                <p class="text-gray-800 font-medium leading-relaxed">{{ $question }}</p>
                            </div>
                            {{-- 답변은 선택 항목이라 쓴 경우에만 보여준다 --}}
                            @if(!empty($aiAnswers[$index]))
                            <div class="mt-3 ml-11 border-l-2 border-[#4ECDC4] pl-4">
                                <p class="text-xs font-bold text-[#2AA9A0] mb-1">내 답변</p>
                                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $aiAnswers[$index] }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- AI 분석 안내 -->
                <p class="text-xs text-gray-400 leading-relaxed border-t border-gray-100 pt-4">
                    위 네 항목은 AI가 뉴스 원문을 읽고 만든 초안을 작성자가 다듬은 내용입니다.
                    사실과 다를 수 있으니 중요한 판단은 원문과 공식 자료로 확인해주세요. 투자 권유가 아닙니다.
                    @if($scrap->mq_ai_date)
                    <br>AI 분석 {{ $scrap->mq_ai_date->format('Y.m.d H:i') }}@if($scrap->mq_ai_model) · {{ $scrap->mq_ai_model }}@endif
                    @endif
                </p>
            </div>
            @endif

            <!-- 새로 알게된 용어 (예전 형식) -->
            @if($scrap->mq_new_terms)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#4ECDC4] to-[#2AA9A0] flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-[#2D3047]">새로 알게된 용어</h2>
                </div>
                <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-6">
                    <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $scrap->mq_new_terms }}</p>
                </div>
            </div>
            @endif

            <!-- 좋아요 (공개된 스크랩만) -->
            @if($scrap->isPublic())
            <div class="flex justify-center pb-8">
                <button type="button"
                        id="likeButton"
                        data-liked="{{ $isLiked ? '1' : '0' }}"
                        class="inline-flex items-center gap-2 h-12 px-6 rounded-full border transition-all font-semibold {{ $isLiked ? 'bg-[#FF4D4D] border-[#FF4D4D] text-white' : 'bg-white border-gray-200 text-gray-500 hover:border-[#FF4D4D] hover:text-[#FF4D4D]' }}">
                    <svg class="w-5 h-5" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span id="likeCount">{{ number_format($scrap->mq_like_cnt) }}</span>
                </button>
            </div>
            @endif

            <!-- 공개 설정 (작성자 본인만) -->
            @if($isOwner)
            <div class="mb-8 p-5 rounded-xl border border-gray-100 bg-gray-50 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-[#2D3047] mb-1">공개 설정</p>
                    <p id="visibilityText" class="text-xs text-gray-500">
                        {{ $scrap->isPublic()
                            ? '이 스크랩은 뉴스 스크랩 게시판에 공개되어 있습니다.'
                            : '나만 볼 수 있는 상태입니다. 공개하면 게시판 목록에 올라갑니다.' }}
                    </p>
                </div>
                <button type="button"
                        id="visibilityButton"
                        data-public="{{ $scrap->isPublic() ? '1' : '0' }}"
                        class="inline-flex items-center justify-center h-11 px-5 rounded-xl transition-all text-sm font-bold flex-shrink-0 {{ $scrap->isPublic() ? 'border border-gray-200 bg-white text-gray-600 hover:border-gray-300' : 'bg-gradient-to-r from-[#4ECDC4] to-[#2AA9A0] text-white hover:shadow-lg hover:-translate-y-0.5' }}">
                    {{ $scrap->isPublic() ? '나만보기로 변경' : '공개 게시판에 공유' }}
                </button>
            </div>
            @endif

            <!-- 버튼 영역 -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-100">
                <!-- 좌측 버튼 -->
                <a href="{{ route('board-scrap.index', $isOwner && !$scrap->isPublic() ? ['mine' => 1] : []) }}"
                   class="inline-flex items-center justify-center h-11 px-5 border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all text-gray-600 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    목록
                </a>

                <!-- 우측 버튼 그룹 (작성자 본인만) -->
                @if($isOwner)
                <div class="flex items-center gap-2">
                    <a href="{{ route('board-scrap.edit', $scrap->idx) }}"
                       class="inline-flex items-center justify-center h-11 px-5 border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all text-gray-600 text-sm font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        수정
                    </a>
                    <form action="{{ route('board-scrap.destroy', $scrap->idx) }}"
                          method="POST"
                          onsubmit="return confirmDelete()"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center h-11 px-5 bg-gradient-to-r from-[#FF4D4D] to-[#e03e3e] text-white rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all text-sm font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            삭제
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete() {
    return confirm('정말 삭제하시겠습니까?\n삭제된 스크랩은 복구할 수 없습니다.');
}

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// 좋아요 토글
const likeButton = document.getElementById('likeButton');
if (likeButton) {
    likeButton.addEventListener('click', async () => {
        @guest
            alert('로그인이 필요한 기능입니다.');
            window.location.href = '{{ route('login') }}';
            return;
        @endguest

        likeButton.disabled = true;

        try {
            const response = await fetch('{{ route('board-scrap.like', $scrap->idx) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (!data.success) {
                alert(data.message || '좋아요 처리 중 오류가 발생했습니다.');
                return;
            }

            document.getElementById('likeCount').textContent = data.likes.toLocaleString();
            likeButton.dataset.liked = data.isLiked ? '1' : '0';

            const heart = likeButton.querySelector('svg');
            if (data.isLiked) {
                likeButton.className = 'inline-flex items-center gap-2 h-12 px-6 rounded-full border transition-all font-semibold bg-[#FF4D4D] border-[#FF4D4D] text-white';
                heart.setAttribute('fill', 'currentColor');
            } else {
                likeButton.className = 'inline-flex items-center gap-2 h-12 px-6 rounded-full border transition-all font-semibold bg-white border-gray-200 text-gray-500 hover:border-[#FF4D4D] hover:text-[#FF4D4D]';
                heart.setAttribute('fill', 'none');
            }
        } catch (error) {
            console.error(error);
            alert('좋아요 처리 중 오류가 발생했습니다.');
        } finally {
            likeButton.disabled = false;
        }
    });
}

// 공개 / 나만보기 전환
const visibilityButton = document.getElementById('visibilityButton');
if (visibilityButton) {
    visibilityButton.addEventListener('click', async () => {
        const isPublic = visibilityButton.dataset.public === '1';

        const message = isPublic
            ? '나만보기로 변경하시겠습니까?\n공개 게시판 목록에서 사라집니다.'
            : '이 스크랩을 공개 게시판에 공유하시겠습니까?\n다른 회원과 비회원도 볼 수 있게 됩니다.';

        if (!confirm(message)) {
            return;
        }

        visibilityButton.disabled = true;

        try {
            const response = await fetch('{{ route('board-scrap.visibility', $scrap->idx) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (!data.success) {
                alert(data.message || '공개 설정 변경 중 오류가 발생했습니다.');
                return;
            }

            alert(data.message);
            // 좋아요 버튼 노출 여부가 공개 상태에 따라 달라지므로 새로고침
            window.location.reload();
        } catch (error) {
            console.error(error);
            alert('공개 설정 변경 중 오류가 발생했습니다.');
        } finally {
            visibilityButton.disabled = false;
        }
    });
}
</script>

<style>
/* CKEditor 콘텐츠 스타일 */
.prose {
    color: #374151;
    line-height: 1.75;
}

.prose p {
    margin-bottom: 1em;
}

.prose h1, .prose h2, .prose h3 {
    margin-top: 1.5em;
    margin-bottom: 0.75em;
    font-weight: 600;
    color: #111827;
}

.prose h1 {
    font-size: 1.875rem;
}

.prose h2 {
    font-size: 1.5rem;
}

.prose h3 {
    font-size: 1.25rem;
}

.prose ul, .prose ol {
    margin-left: 1.5em;
    margin-bottom: 1em;
}

.prose li {
    margin-bottom: 0.5em;
}

.prose a {
    color: #2563eb;
    text-decoration: underline;
}

.prose a:hover {
    color: #1d4ed8;
}

.prose img {
    max-width: 100%;
    height: auto;
    border-radius: 0.75rem;
    margin: 1.5em auto;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.prose blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1em;
    margin-left: 0;
    margin-right: 0;
    font-style: italic;
    color: #6b7280;
}

.prose code {
    background-color: #f3f4f6;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-size: 0.875em;
    font-family: 'Courier New', monospace;
}

.prose pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1em;
    border-radius: 0.75rem;
    overflow-x: auto;
}

.prose pre code {
    background-color: transparent;
    padding: 0;
    color: inherit;
}

.prose table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5em 0;
}

.prose th, .prose td {
    border: 1px solid #e5e7eb;
    padding: 0.75em;
    text-align: left;
}

.prose th {
    background-color: #f9fafb;
    font-weight: 600;
}
</style>
@endpush
@endsection
