{{--
    캐시플로우 챗봇 모달.

    소개 페이지에서 @include('cashflow._chatbot') 로 불러 쓴다. 로직은 전부
    public/js/cashflow/chatbot.js 에 있고, 이 파일은 마크업과 설정 전달만 맡는다.
    엔드포인트는 auth 가 걸려 있으므로 로그인 사용자에게만 include 한다.
--}}

{{--
    CDN 버전을 고정하고 무결성 해시를 건다. 예전에는 marked 를 버전 없이 불러서 CDN 이
    밀어주는 메이저를 그대로 먹었고, sanitize 옵션이 marked v5 에서 제거된 뒤로는
    정화되지 않은 HTML 이 innerHTML 로 들어갔다. 지금은 DOMPurify 로 따로 정화한다.

    버전을 올릴 때는 integrity 해시도 같이 바꿔야 한다.
      curl -s <url> | openssl dgst -sha384 -binary | openssl base64 -A
--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.2/marked.min.js"
        integrity="sha384-/TQbtLCAerC3jgaim+N78RZSDYV7ryeoBCVqTuzRrFec2akfBkHS7ACQ3PQhvMVi"
        crossorigin="anonymous" referrerpolicy="no-referrer" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.1.6/purify.min.js"
        integrity="sha384-+VfUPEb0PdtChMwmBcBmykRMDd+v6D/oFmB3rZM/puCMDYcIvF968OimRh4KQY9a"
        crossorigin="anonymous" referrerpolicy="no-referrer" defer></script>

<style>
    /* Tailwind CDN 에는 typography 플러그인이 없어 마크다운 기본 여백을 직접 준다. */
    .chatbot-markdown > *:first-child { margin-top: 0; }
    .chatbot-markdown > *:last-child { margin-bottom: 0; }
    .chatbot-markdown p { margin: 0 0 0.6em; }
    .chatbot-markdown h1,
    .chatbot-markdown h2,
    .chatbot-markdown h3,
    .chatbot-markdown h4 { margin: 1em 0 0.5em; font-weight: 700; line-height: 1.35; color: #2D3047; }
    .chatbot-markdown h1 { font-size: 1.05rem; }
    .chatbot-markdown h2 { font-size: 1rem; }
    .chatbot-markdown h3,
    .chatbot-markdown h4 { font-size: 0.95rem; }
    .chatbot-markdown ul,
    .chatbot-markdown ol { margin: 0 0 0.6em; padding-left: 1.25em; }
    .chatbot-markdown ul { list-style: disc; }
    .chatbot-markdown ol { list-style: decimal; }
    .chatbot-markdown li { margin: 0.2em 0; }
    .chatbot-markdown li > ul,
    .chatbot-markdown li > ol { margin: 0.2em 0; }
    .chatbot-markdown strong { font-weight: 700; color: #2D3047; }
    .chatbot-markdown a { color: #FF4D4D; text-decoration: underline; }
    .chatbot-markdown code {
        background: #F1F3F5; border-radius: 4px; padding: 0.1em 0.35em;
        font-size: 0.9em; word-break: break-all;
    }
    .chatbot-markdown pre {
        background: #2D3047; color: #F8F9FA; border-radius: 8px;
        padding: 0.75em; overflow-x: auto; margin: 0 0 0.6em;
    }
    .chatbot-markdown pre code { background: transparent; color: inherit; padding: 0; }
    .chatbot-markdown blockquote {
        border-left: 3px solid #4ECDC4; padding-left: 0.75em;
        margin: 0 0 0.6em; color: #4B5563;
    }
    .chatbot-markdown table { width: 100%; border-collapse: collapse; margin: 0 0 0.6em; font-size: 0.85em; display: block; overflow-x: auto; }
    .chatbot-markdown th,
    .chatbot-markdown td { border: 1px solid #E5E7EB; padding: 0.35em 0.5em; text-align: left; }
    .chatbot-markdown th { background: #F8F9FA; font-weight: 700; }
    .chatbot-markdown hr { border: 0; border-top: 1px solid #E5E7EB; margin: 0.8em 0; }

    /* 레이아웃 푸터의 플로팅 버튼(.fab-wrap: TOP/상담)이 z-index 50 이라 모달 위로 떠오른다.
       모달을 60 으로 올리고, 열려 있는 동안에는 플로팅 버튼을 아예 감춘다. */
    body.chatbot-open .fab-wrap,
    body.chatbot-open .chatbot-fab { display: none; }
</style>

<div id="cashflowChatbot"
     class="fixed inset-0 bg-black/50 z-[60] hidden items-center justify-center"
     role="dialog"
     aria-modal="true"
     aria-labelledby="chatbotTitle"
     data-send-url="{{ route('cashflow.chat.send') }}"
     data-history-url="{{ route('cashflow.chat.history') }}"
     data-reset-url="{{ route('cashflow.chat.reset') }}"
     data-max-message="{{ \App\Services\CashflowChatBot::MAX_MESSAGE_CHARS }}"
     data-greeting="안녕하세요! 캐시플로우 게임 도우미입니다. 🎲&#10;규칙이 헷갈리거나 카드 사진을 보여주시면 어떻게 진행하면 되는지 알려드릴게요.">

    <div class="w-full h-full md:h-[80vh] sm:max-w-[650px] flex">
        <div class="w-full h-full bg-[#F8F9FA] sm:rounded-2xl overflow-hidden shadow-2xl flex flex-col">

            {{-- 헤더 --}}
            <div class="flex items-center justify-between gap-2 px-4 py-3 bg-[#2D3047] flex-shrink-0">
                <h2 id="chatbotTitle" class="text-base font-bold text-white truncate">캐시플로우 챗봇</h2>
                <div class="flex items-center gap-1">
                    <button type="button" id="chatbotReset"
                            class="px-2.5 py-1.5 rounded-lg text-xs text-white/80 hover:text-white hover:bg-white/10 transition-colors disabled:opacity-40"
                            title="대화를 처음부터 다시 시작합니다">
                        새 대화
                    </button>
                    <button type="button" id="chatbotClose"
                            class="p-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors"
                            aria-label="챗봇 닫기">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- 대화 영역 --}}
            {{-- custom-scrollbar 는 layouts/app.blade.php 에 정의된 공통 유틸리티다. --}}
            <div id="chatbotMessages"
                 class="flex-grow overflow-y-auto custom-scrollbar p-4 h-0"
                 aria-live="polite"
                 aria-atomic="false"></div>

            <p id="chatbotStatus" class="text-xs px-4 pt-2 text-gray-500 hidden"></p>

            {{-- 입력 --}}
            <div class="border-t border-gray-200 bg-white p-3 flex-shrink-0">
                <form id="chatbotForm" class="flex flex-col gap-2">
                    <div id="chatbotImagePreview" class="hidden relative w-fit">
                        <img id="chatbotPreviewImage" src="" alt="첨부한 이미지 미리보기" class="max-h-28 rounded-lg border border-gray-200">
                        <button type="button" id="chatbotImageRemove"
                                class="absolute -top-2 -right-2 bg-[#FF4D4D] text-white rounded-full p-1 shadow"
                                aria-label="첨부 이미지 제거">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="button" id="chatbotImageBtn"
                                class="flex-shrink-0 p-2 rounded-lg text-gray-500 hover:text-[#2D3047] hover:bg-gray-100 transition-colors disabled:opacity-40"
                                aria-label="이미지 첨부">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>
                        <input type="file" id="chatbotImageInput" accept="image/*" class="hidden">

                        <label for="chatbotInput" class="sr-only">메시지 입력</label>
                        <textarea id="chatbotInput" rows="1"
                                  placeholder="규칙을 물어보거나 카드 사진을 올려보세요"
                                  maxlength="{{ \App\Services\CashflowChatBot::MAX_MESSAGE_CHARS }}"
                                  class="flex-grow min-w-0 resize-none px-3 py-2 border border-gray-300 rounded-xl text-sm leading-relaxed focus:outline-none focus:border-[#4ECDC4] focus:ring-2 focus:ring-[#4ECDC4]/20 transition-all disabled:bg-gray-50"></textarea>

                        <button type="submit" id="chatbotSend"
                                class="flex-shrink-0 p-2.5 rounded-xl bg-[#2D3047] text-white hover:bg-[#2D3047]/90 transition-colors disabled:opacity-40"
                                aria-label="메시지 보내기">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6" />
                            </svg>
                        </button>

                        <button type="button" id="chatbotStop"
                                class="hidden flex-shrink-0 p-2.5 rounded-xl bg-[#FF4D4D] text-white hover:bg-[#FF4D4D]/90 transition-colors"
                                aria-label="응답 중단">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="7" y="7" width="10" height="10" rx="2" fill="currentColor" stroke="none" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/cashflow/chatbot.js') }}" defer></script>
