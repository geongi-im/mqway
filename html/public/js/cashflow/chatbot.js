/**
 * 캐시플로우 챗봇 화면 로직.
 *
 * 서버(CashflowChatController)는 Gemini 원본이 아니라 아래 봉투만 흘려보낸다.
 *   data: {"type":"delta","text":"..."}
 *   data: {"type":"done"}
 *   data: {"type":"error","message":"..."}
 * 따라서 이 파일에는 모델 응답 구조를 파고드는 코드가 없다.
 *
 * 대화는 서버(회원별 테이블)에 남는다. 화면은 모달을 처음 열 때 저장된 대화를 받아 그리고,
 * 그 뒤로는 주고받은 말풍선만 덧붙인다. 브라우저에 따로 보관하는 것은 없다.
 *
 * blade 인라인이 아니라 파일로 둔 이유는 캐시가 먹고, 소개 페이지 마크업과 섞이지 않게 하기 위해서다.
 */
(function () {
    'use strict';

    var root = document.getElementById('cashflowChatbot');

    if (!root) {
        return;
    }

    var SEND_URL = root.getAttribute('data-send-url');
    var HISTORY_URL = root.getAttribute('data-history-url');
    var RESET_URL = root.getAttribute('data-reset-url');
    var MAX_MESSAGE = parseInt(root.getAttribute('data-max-message'), 10) || 2000;
    var GREETING = root.getAttribute('data-greeting') || '';

    /**
     * 이미지만 보낸 턴을 서버가 이력에 남길 때 쓰는 표식(CashflowChatHistory::IMAGE_PLACEHOLDER).
     * 복원할 때 이 문자열은 본문 대신 첨부 표시로 바꿔 그린다.
     */
    var IMAGE_PLACEHOLDER = '(이미지 첨부)';

    /** 첨부 이미지 정규화 기준. 카드 글씨가 읽힐 정도면 충분하다. */
    var IMAGE_MAX_EDGE = 1280;
    var IMAGE_QUALITY = 0.85;

    /** 스트리밍 중 마크다운 재파싱 간격(ms). 매 델타마다 파싱하면 긴 답변에서 버벅인다. */
    var RENDER_INTERVAL = 80;

    /** 입력창이 늘어날 수 있는 최대 높이(px) */
    var MAX_INPUT_HEIGHT = 140;

    // 히어로 CTA, 하단 CTA, 플로팅 버튼이 모두 같은 모달을 연다.
    var openTriggers = Array.prototype.slice.call(document.querySelectorAll('[data-chatbot-open]'));
    var closeBtn = document.getElementById('chatbotClose');
    var resetBtn = document.getElementById('chatbotReset');
    var messagesBox = document.getElementById('chatbotMessages');
    var form = document.getElementById('chatbotForm');
    var input = document.getElementById('chatbotInput');
    var sendBtn = document.getElementById('chatbotSend');
    var stopBtn = document.getElementById('chatbotStop');
    var imageBtn = document.getElementById('chatbotImageBtn');
    var imageInput = document.getElementById('chatbotImageInput');
    var imagePreview = document.getElementById('chatbotImagePreview');
    var previewImage = document.getElementById('chatbotPreviewImage');
    var removeImageBtn = document.getElementById('chatbotImageRemove');
    var statusBox = document.getElementById('chatbotStatus');

    var pendingImage = null;   // 전송 대기 중인 data URI
    var controller = null;     // 진행 중 요청의 AbortController
    var busy = false;
    var started = false;       // 이 페이지에서 대화를 한 번이라도 열었는지
    var lastFocused = null;

    /* ------------------------------------------------------------------ 유틸 */

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    /**
     * 마크다운을 안전한 HTML 로 바꾼다.
     *
     * marked 와 DOMPurify 는 CDN 에서 온다. 광고 차단기나 사내망에 막혀 로드되지 않았을 때
     * 원문을 innerHTML 로 밀어 넣으면 그대로 XSS 가 되므로, 둘 중 하나라도 없으면
     * 이스케이프한 평문으로 떨어진다.
     */
    function renderMarkdown(text) {
        if (typeof window.marked === 'undefined' || typeof window.DOMPurify === 'undefined') {
            return escapeHtml(text).replace(/\n/g, '<br>');
        }

        try {
            return window.DOMPurify.sanitize(window.marked.parse(text));
        } catch (error) {
            return escapeHtml(text).replace(/\n/g, '<br>');
        }
    }

    function nowLabel() {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    /** 사용자가 위쪽을 읽고 있으면 스크롤을 빼앗지 않는다. */
    function isNearBottom() {
        return messagesBox.scrollHeight - messagesBox.scrollTop - messagesBox.clientHeight < 120;
    }

    function scrollToBottom(force) {
        if (force || isNearBottom()) {
            messagesBox.scrollTop = messagesBox.scrollHeight;
        }
    }

    function setStatus(message, tone) {
        if (!statusBox) {
            return;
        }

        if (!message) {
            statusBox.classList.add('hidden');
            statusBox.textContent = '';
            return;
        }

        statusBox.className = 'text-xs px-4 pt-2 ' + (tone === 'error' ? 'text-red-500' : 'text-gray-500');
        statusBox.textContent = message;
    }

    /* -------------------------------------------------------------- 말풍선 */

    var BOT_AVATAR = ''
        + '<div class="w-9 h-9 rounded-full bg-[#2D3047] flex items-center justify-center flex-shrink-0 mr-3">'
        +   '<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">'
        +     '<path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />'
        +   '</svg>'
        + '</div>';

    /**
     * 사용자 말풍선.
     *
     * @param {string} text
     * @param {Object} [options]
     * @param {string} [options.imageSrc] 방금 보낸 턴의 data URI. 복원된 턴에는 없다.
     * @param {boolean} [options.hasImage] 이미지를 첨부했던 턴인지
     * @param {string} [options.time] 표시할 시각. 없으면 지금 시각
     */
    function appendUserMessage(text, options) {
        options = options || {};

        var imageSrc = options.imageSrc || null;
        var hasImage = options.hasImage || !!imageSrc;
        var time = options.time || nowLabel();
        var body = '';

        if (imageSrc) {
            body += '<img src="' + escapeHtml(imageSrc) + '" alt="첨부한 이미지" class="max-h-48 rounded-lg mb-2 block">';
        } else if (hasImage) {
            // 이미지 자체는 서버에 담지 않는다(base64 한 장이 수 MB). 복원할 때는 첨부했다는
            // 사실만 표시한다.
            body += '<span class="inline-block text-[11px] bg-white/15 rounded-md px-2 py-1 mb-2">🖼 이미지 첨부</span>';
        }

        if (text && text !== IMAGE_PLACEHOLDER) {
            body += '<p class="whitespace-pre-wrap break-words">' + escapeHtml(text) + '</p>';
        }

        // 표식만 저장된 턴에서 첨부 여부까지 없으면 말풍선이 비어 버린다. 원문을 그대로 쓴다.
        if (body === '' && text) {
            body = '<p class="whitespace-pre-wrap break-words">' + escapeHtml(text) + '</p>';
        }

        var wrapper = document.createElement('div');
        wrapper.className = 'flex justify-end mb-4';
        wrapper.innerHTML = ''
            + '<div class="bg-[#2D3047] text-white p-3 rounded-2xl rounded-br-sm shadow-sm max-w-[85%] text-sm leading-relaxed">'
            +   body
            +   '<span class="text-[11px] text-white/60 mt-1 block">' + escapeHtml(time) + '</span>'
            + '</div>';

        messagesBox.appendChild(wrapper);
        scrollToBottom(true);
    }

    /**
     * 봇 말풍선을 만들고, 내용을 갱신할 수 있는 핸들을 돌려준다.
     *
     * @param {string} initialText
     * @param {string} [timeLabel] 복원된 답변의 시각. 없으면 지금 시각
     */
    function appendBotMessage(initialText, timeLabel) {
        var wrapper = document.createElement('div');
        wrapper.className = 'flex mb-4';
        wrapper.innerHTML = ''
            + BOT_AVATAR
            + '<div class="bg-white p-3 rounded-2xl rounded-bl-sm shadow-sm max-w-[85%] min-w-0">'
            +   '<div class="chatbot-markdown text-sm text-gray-800 leading-relaxed break-words"></div>'
            +   '<div class="flex items-center gap-2 mt-1">'
            +     '<span class="text-[11px] text-gray-400">' + escapeHtml(timeLabel || nowLabel()) + '</span>'
            +     '<button type="button" class="chatbot-copy hidden text-[11px] text-gray-400 hover:text-[#2D3047] transition-colors" title="답변 복사">복사</button>'
            +   '</div>'
            + '</div>';

        messagesBox.appendChild(wrapper);

        var target = wrapper.querySelector('.chatbot-markdown');
        var copyBtn = wrapper.querySelector('.chatbot-copy');
        var raw = initialText || '';
        var lastRender = 0;

        function paint() {
            target.innerHTML = renderMarkdown(raw);
        }

        if (raw) {
            paint();
        }

        copyBtn.addEventListener('click', function () {
            var done = function () {
                copyBtn.textContent = '복사됨';
                setTimeout(function () { copyBtn.textContent = '복사'; }, 1500);
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(raw).then(done).catch(function () {});
            }
        });

        scrollToBottom(true);

        return {
            /** 스트리밍 중 호출. 렌더링 비용 때문에 간격을 둔다. */
            append: function (chunk) {
                raw += chunk;

                var now = Date.now();

                if (now - lastRender >= RENDER_INTERVAL) {
                    lastRender = now;
                    paint();
                    scrollToBottom(false);
                }
            },
            /** 스트리밍 종료. 마지막 델타까지 반영한다. */
            finish: function () {
                paint();

                if (raw.trim() !== '') {
                    copyBtn.classList.remove('hidden');
                }

                scrollToBottom(false);
            },
            setText: function (text) {
                raw = text;
                paint();
                scrollToBottom(false);
            },
            isEmpty: function () {
                return raw.trim() === '';
            }
        };
    }

    function appendTypingIndicator() {
        var wrapper = document.createElement('div');
        wrapper.className = 'flex mb-4 chatbot-typing';
        wrapper.innerHTML = ''
            + BOT_AVATAR
            + '<div class="bg-white p-3 rounded-2xl rounded-bl-sm shadow-sm">'
            +   '<span class="inline-flex gap-1 items-center">'
            +     '<span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:0ms"></span>'
            +     '<span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:150ms"></span>'
            +     '<span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:300ms"></span>'
            +   '</span>'
            + '</div>';

        messagesBox.appendChild(wrapper);
        scrollToBottom(true);

        return wrapper;
    }

    /* ---------------------------------------------------------------- 이미지 */

    /**
     * 첨부 이미지를 항상 JPEG 로 정규화한다.
     *
     * 크기를 줄이는 목적도 있지만, 더 중요한 건 형식을 확정하는 것이다. 예전 구현은
     * 2MB 이하 파일을 원본 그대로 보내면서 mime 만 image/jpeg 로 적어 형식을 속였다.
     */
    function normalizeImage(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();

            reader.onload = function (event) {
                var image = new Image();

                image.onload = function () {
                    var width = image.width;
                    var height = image.height;
                    var longest = Math.max(width, height);

                    if (longest > IMAGE_MAX_EDGE) {
                        var ratio = IMAGE_MAX_EDGE / longest;
                        width = Math.round(width * ratio);
                        height = Math.round(height * ratio);
                    }

                    var canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;

                    var ctx = canvas.getContext('2d');
                    // 투명 PNG 를 JPEG 로 바꾸면 배경이 검게 깔린다. 흰색을 먼저 칠한다.
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, width, height);
                    ctx.drawImage(image, 0, 0, width, height);

                    resolve(canvas.toDataURL('image/jpeg', IMAGE_QUALITY));
                };

                image.onerror = function () {
                    reject(new Error('이미지를 열 수 없습니다.'));
                };

                image.src = event.target.result;
            };

            reader.onerror = function () {
                reject(new Error('파일을 읽을 수 없습니다.'));
            };

            reader.readAsDataURL(file);
        });
    }

    function clearImage() {
        pendingImage = null;
        imageInput.value = '';
        // src='' 로 두면 일부 브라우저가 현재 URL 을 다시 요청한다. 속성 자체를 지운다.
        previewImage.removeAttribute('src');
        imagePreview.classList.add('hidden');
    }

    /* ------------------------------------------------------------------ SSE */

    /**
     * 응답 본문을 줄 단위로 읽어 이벤트를 넘긴다.
     *
     * 청크 경계가 줄 중간을 가를 수 있으므로 버퍼를 두고 완결된 줄만 처리한다.
     * 예전 구현은 청크를 그대로 split 해서 경계에 걸친 이벤트를 잃었다.
     */
    function readEventStream(response, onEvent) {
        var reader = response.body.getReader();
        var decoder = new TextDecoder();
        var buffer = '';

        function handleLine(line) {
            if (line.indexOf('data:') !== 0) {
                return;
            }

            var payload = line.slice(5).trim();

            if (payload === '') {
                return;
            }

            try {
                onEvent(JSON.parse(payload));
            } catch (error) {
                // 서버가 항상 한 줄 JSON 을 보내므로 여기 오면 전송 중 손상이다. 조용히 넘긴다.
            }
        }

        function drain(final) {
            var index;

            while ((index = buffer.indexOf('\n')) !== -1) {
                handleLine(buffer.slice(0, index).replace(/\r$/, ''));
                buffer = buffer.slice(index + 1);
            }

            if (final && buffer.trim() !== '') {
                handleLine(buffer.trim());
                buffer = '';
            }
        }

        function pump() {
            return reader.read().then(function (result) {
                if (result.done) {
                    buffer += decoder.decode();
                    drain(true);
                    return;
                }

                buffer += decoder.decode(result.value, { stream: true });
                drain(false);

                return pump();
            });
        }

        return pump();
    }

    /* ----------------------------------------------------------------- 전송 */

    function setBusy(value) {
        busy = value;
        sendBtn.disabled = value;
        imageBtn.disabled = value;
        input.disabled = value;
        resetBtn.disabled = value;
        sendBtn.classList.toggle('hidden', value);
        stopBtn.classList.toggle('hidden', !value);
    }

    function send() {
        if (busy) {
            return;
        }

        var text = input.value.trim();

        if (text === '' && !pendingImage) {
            return;
        }

        if (text.length > MAX_MESSAGE) {
            setStatus('메시지가 너무 깁니다. ' + MAX_MESSAGE + '자 이내로 입력해주세요.', 'error');
            return;
        }

        setStatus('');

        var body = { message: text };

        if (pendingImage) {
            body.image = pendingImage;
        }

        appendUserMessage(text, { imageSrc: pendingImage });

        input.value = '';
        autoGrow();
        // 미리보기를 비워도 방금 만든 말풍선이 data URI 를 들고 있으므로 이미지는 화면에 남는다.
        clearImage();

        var typing = appendTypingIndicator();
        var bubble = null;
        var failed = false;

        controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
        setBusy(true);

        function ensureBubble() {
            if (typing.parentNode) {
                typing.remove();
            }

            if (!bubble) {
                bubble = appendBotMessage('');
            }

            return bubble;
        }

        fetch(SEND_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'text/event-stream',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(body),
            signal: controller ? controller.signal : undefined
        }).then(function (response) {
            // 검증 실패·인증 만료·과다 호출은 SSE 가 아니라 JSON 으로 온다.
            if (!response.ok) {
                return response.json().catch(function () {
                    return {};
                }).then(function (data) {
                    var message = data.message;

                    if (!message) {
                        if (response.status === 401 || response.status === 419) {
                            message = '로그인이 필요합니다. 페이지를 새로고침한 뒤 다시 시도해주세요.';
                        } else if (response.status === 429) {
                            message = '요청이 너무 잦습니다. 잠시 후 다시 시도해주세요.';
                        } else {
                            message = '요청을 처리하지 못했습니다. 잠시 후 다시 시도해주세요.';
                        }
                    }

                    failed = true;
                    ensureBubble().setText(message);
                });
            }

            return readEventStream(response, function (event) {
                if (event.type === 'delta') {
                    ensureBubble().append(event.text || '');
                    return;
                }

                if (event.type === 'error') {
                    failed = true;
                    ensureBubble().setText(event.message || '답변 생성 중 문제가 발생했습니다.');
                }
            });
        }).catch(function (error) {
            if (error && error.name === 'AbortError') {
                return;
            }

            failed = true;
            ensureBubble().setText('네트워크 오류로 답변을 받지 못했습니다. 잠시 후 다시 시도해주세요.');
        }).then(function () {
            if (typing.parentNode) {
                typing.remove();
            }

            if (bubble) {
                if (bubble.isEmpty() && !failed) {
                    bubble.setText('답변이 중단되었습니다.');
                }

                bubble.finish();
            }

            controller = null;
            setBusy(false);
            input.focus();
        });
    }

    function stop() {
        if (controller) {
            controller.abort();
        }
    }

    /* ---------------------------------------------------------------- 대화 관리 */

    function showGreeting() {
        if (GREETING) {
            appendBotMessage(GREETING).finish();
        }
    }

    /**
     * 저장된 대화를 말풍선으로 되돌린다.
     *
     * 첨부 이미지는 서버에 없으므로 사용자 턴은 첨부 표시만 되살아난다.
     */
    function renderTranscript(messages) {
        messages.forEach(function (item) {
            if (!item || !item.text) {
                return;
            }

            if (item.role === 'user') {
                appendUserMessage(item.text, { hasImage: !!item.has_image, time: item.time });
                return;
            }

            appendBotMessage(item.text, item.time).finish();
        });

        scrollToBottom(true);
    }

    /**
     * 서버에 남은 활성 대화를 불러와 화면을 맞춘다.
     *
     * 대화가 없으면 인사말로 시작한다. 불러오지 못했을 때도 대화 자체를 막지는 않는다.
     * 새 질문은 그대로 보낼 수 있고, 서버는 이전 맥락을 계속 기억하고 있다.
     */
    function loadHistory() {
        return fetch(HISTORY_URL, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('history request failed');
            }

            return response.json();
        }).then(function (data) {
            var messages = (data && data.messages) || [];

            messagesBox.innerHTML = '';

            if (messages.length === 0) {
                showGreeting();
                return;
            }

            renderTranscript(messages);
        }).catch(function () {
            messagesBox.innerHTML = '';
            showGreeting();
            setStatus('이전 대화를 불러오지 못했습니다. 이어서 물어보셔도 됩니다.');
        });
    }

    /**
     * 서버에 저장된 대화를 지운다.
     *
     * @return {Promise<boolean>} 지워졌는지
     */
    function resetConversation() {
        return fetch(RESET_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        }).then(function (response) {
            return response.ok;
        }).catch(function () {
            return false;
        });
    }

    function startNewConversation() {
        if (busy) {
            return;
        }

        messagesBox.innerHTML = '';
        clearImage();
        setStatus('');

        resetConversation().then(function (cleared) {
            showGreeting();

            // 대화가 서버에 남아 있으므로 초기화 실패를 조용히 넘기면 안 된다. 화면만 비워진
            // 상태로 질문하면 모델은 지워졌다고 생각한 이전 맥락을 계속 물고 답한다.
            if (!cleared) {
                setStatus('이전 대화를 지우지 못했습니다. 잠시 후 다시 시도해주세요.', 'error');
            }
        });
    }

    /* ------------------------------------------------------------------ 모달 */

    function openModal() {
        lastFocused = document.activeElement;

        root.classList.remove('hidden');
        root.classList.add('flex');
        document.body.style.overflow = 'hidden';
        // 푸터의 TOP/상담 플로팅 버튼을 가린다 (_chatbot.blade.php 의 CSS)
        document.body.classList.add('chatbot-open');

        // 페이지를 새로 연 뒤 첫 열기라면 저장된 대화를 끌어와 화면과 맞춘다.
        // 예전에는 여기서 서버 이력을 지웠고(resetConversation), 그래서 새로고침 한 번에
        // 대화가 사라졌다.
        if (!started) {
            started = true;
            messagesBox.innerHTML = '';
            loadHistory();
        }

        // 숨겨져 있는 동안에는 scrollHeight 가 0 이라 높이를 잴 수 없다.
        // 화면에 올라온 뒤에 맞춘다.
        autoGrow();
        input.focus();
    }

    function closeModal() {
        // 대화는 그대로 둔다. 닫았다 다시 열면 이어서 물어볼 수 있다.
        root.classList.add('hidden');
        root.classList.remove('flex');
        document.body.style.overflow = '';
        document.body.classList.remove('chatbot-open');

        if (lastFocused && lastFocused.focus) {
            lastFocused.focus();
        }
    }

    /**
     * 입력창 높이를 내용에 맞춘다.
     *
     * height 를 scrollHeight 로 맞춰도 테두리 두께만큼 모자라 한 줄일 때도 스크롤바가
     * 그려진다. 상한에 닿기 전까지는 overflow 를 꺼서 그 자국을 없앤다.
     */
    function autoGrow() {
        input.style.height = 'auto';

        var needed = input.scrollHeight;

        input.style.height = Math.min(needed, MAX_INPUT_HEIGHT) + 'px';
        input.style.overflowY = needed > MAX_INPUT_HEIGHT ? 'auto' : 'hidden';
    }

    /* ------------------------------------------------------------------ 바인딩 */

    openTriggers.forEach(function (trigger) {
        trigger.addEventListener('click', openModal);
    });

    closeBtn.addEventListener('click', closeModal);
    resetBtn.addEventListener('click', startNewConversation);
    stopBtn.addEventListener('click', stop);

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        send();
    });

    input.addEventListener('input', autoGrow);

    input.addEventListener('keydown', function (event) {
        // Enter 는 전송, Shift+Enter 는 줄바꿈. 한글 조합 중 Enter 는 확정용이므로 흘려보낸다.
        if (event.key !== 'Enter' || event.shiftKey || event.isComposing) {
            return;
        }

        event.preventDefault();
        send();
    });

    imageBtn.addEventListener('click', function () {
        imageInput.click();
    });

    imageInput.addEventListener('change', function () {
        if (!this.files || !this.files[0]) {
            return;
        }

        var file = this.files[0];

        if (file.type.indexOf('image/') !== 0) {
            setStatus('이미지 파일만 첨부할 수 있습니다.', 'error');
            clearImage();
            return;
        }

        setStatus('이미지를 준비하는 중입니다...');

        normalizeImage(file).then(function (dataUri) {
            pendingImage = dataUri;
            previewImage.src = dataUri;
            imagePreview.classList.remove('hidden');
            setStatus('');
        }).catch(function () {
            setStatus('이미지를 처리하지 못했습니다. 다른 파일로 시도해주세요.', 'error');
            clearImage();
        });
    });

    removeImageBtn.addEventListener('click', function () {
        clearImage();
        setStatus('');
    });

    // 배경(모달 바깥) 클릭으로 닫기
    root.addEventListener('mousedown', function (event) {
        if (event.target === root) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape' || root.classList.contains('hidden')) {
            return;
        }

        if (busy) {
            stop();
            return;
        }

        closeModal();
    });

    if (typeof window.marked !== 'undefined') {
        window.marked.setOptions({ gfm: true, breaks: true });
    }
}());
