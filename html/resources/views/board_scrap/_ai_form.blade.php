{{--
    뉴스 스크랩 AI 분석 파트 폼 (등록 / 수정 공용)

    $scrap 이 넘어오면 수정 화면, 없으면 등록 화면으로 동작한다.
    검증 실패로 되돌아온 경우 old() 값이 모델 값보다 우선한다.
--}}
@php
    $scrap = isset($scrap) ? $scrap : null;

    $interpretation = old('mq_ai_interpretation', $scrap ? $scrap->mq_ai_interpretation : '');
    $outlookShort = old('mq_ai_outlook_short', $scrap ? $scrap->mq_ai_outlook_short : '');
    $outlookLong = old('mq_ai_outlook_long', $scrap ? $scrap->mq_ai_outlook_long : '');
    $aiModel = old('mq_ai_model', $scrap ? $scrap->mq_ai_model : '');
    $aiSource = old('mq_ai_source', $scrap ? $scrap->mq_ai_source : '');

    // 용어 리스트: old() -> 모델 순으로 채운다
    $oldTerms = old('terms');
    if (is_array($oldTerms)) {
        $terms = [];
        foreach ($oldTerms as $row) {
            if (!is_array($row) || trim((string) (isset($row['term']) ? $row['term'] : '')) === '') {
                continue;
            }
            $terms[] = [
                'term' => $row['term'],
                'definition' => isset($row['definition']) ? $row['definition'] : '',
                'context' => isset($row['context']) ? $row['context'] : '',
                'checked' => !empty($row['checked']),
            ];
        }
    } else {
        $terms = $scrap ? $scrap->getAiTerms() : [];
    }

    // 질문은 항상 2칸으로 맞춘다
    $oldQuestions = old('mq_ai_questions');
    $questions = is_array($oldQuestions)
        ? array_values($oldQuestions)
        : ($scrap ? $scrap->getAiQuestions() : []);
    $questions = array_pad(array_slice($questions, 0, 2), 2, '');

    $hasAiContent = trim((string) $interpretation) !== ''
        || trim((string) $outlookShort) !== ''
        || trim((string) $outlookLong) !== ''
        || count($terms) > 0
        || trim(implode('', $questions)) !== '';

    $inputClass = 'w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#4ECDC4] focus:ring-2 focus:ring-[#4ECDC4]/20 transition-all bg-gray-50 text-gray-700 placeholder-gray-400';
@endphp

<input type="hidden" name="mq_ai_model" id="mq_ai_model" value="{{ $aiModel }}">
<input type="hidden" name="mq_ai_source" id="mq_ai_source" value="{{ $aiSource }}">

<!-- ===== AI 분석 결과 ===== -->
<div id="aiResultSection" class="space-y-8 {{ $hasAiContent ? '' : 'hidden' }}">

    <div class="border-t border-gray-100"></div>

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-base font-bold text-[#2D3047] flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-gradient-to-br from-violet-500 to-indigo-500 text-white text-xs font-bold">AI</span>
                AI 분석 결과
            </h3>
            <p class="text-xs text-gray-400 mt-1">AI가 만든 초안입니다. 자유롭게 고쳐서 저장하세요.</p>
        </div>
        <p id="aiMetaBadge" class="text-xs text-gray-400 {{ $aiSource ? '' : 'hidden' }}">
            @if($aiSource)
                {{ $aiModel }} · {{ $aiSource === 'url_context' ? '원문 직접 열람' : '본문 분석' }}
            @endif
        </p>
    </div>

    <!-- 4) 뉴스에 대한 짧은 해석 -->
    <div class="space-y-2">
        <label for="mq_ai_interpretation" class="text-sm font-semibold text-[#2D3047] block">
            뉴스에 대한 짧은 해석
        </label>
        <p class="text-xs text-gray-400 mb-3">무슨 일이 있었고, 왜 중요하고, 내 생활과 어떻게 닿는지 정리한 문단입니다</p>
        <textarea name="mq_ai_interpretation"
                  id="mq_ai_interpretation"
                  rows="6"
                  maxlength="5000"
                  class="{{ $inputClass }} resize-y @error('mq_ai_interpretation') border-red-500 @enderror"
                  placeholder="[AI분석] 버튼을 누르면 자동으로 채워집니다">{{ $interpretation }}</textarea>
        @error('mq_ai_interpretation')
            <p class="text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- 5) 뉴스 본문 내 경제 용어 -->
    <div class="space-y-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <label class="text-sm font-semibold text-[#2D3047] block">
                뉴스 속 경제 용어
                <span class="text-xs font-normal text-gray-400 ml-1">최대 {{ \App\Models\BoardScrap::MAX_TERMS }}개</span>
            </label>
            <button type="button"
                    id="addTermBtn"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-[#2AA9A0] border border-[#4ECDC4]/40 rounded-lg hover:bg-[#4ECDC4]/10 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                용어 추가
            </button>
        </div>
        <p class="text-xs text-gray-400 mb-3">
            <span class="font-semibold text-[#2AA9A0]">새로 알게 된 용어</span>에 체크해두면 상세 화면에 따로 모아서 보여줍니다
        </p>

        <div id="termsList" class="space-y-3">
            @foreach($terms as $index => $term)
                <div class="term-row rounded-xl border border-gray-200 bg-gray-50 p-4" data-index="{{ $index }}">
                    <div class="flex items-start gap-3">
                        <label class="flex items-center gap-2 shrink-0 pt-2.5 cursor-pointer" title="새로 알게 된 용어로 체크">
                            <input type="checkbox"
                                   name="terms[{{ $index }}][checked]"
                                   value="1"
                                   {{ $term['checked'] ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-[#4ECDC4] focus:ring-[#4ECDC4]">
                            <span class="text-xs text-gray-500 whitespace-nowrap">알게 됨</span>
                        </label>
                        <div class="flex-1 space-y-2 min-w-0">
                            <input type="text"
                                   name="terms[{{ $index }}][term]"
                                   value="{{ $term['term'] }}"
                                   maxlength="100"
                                   placeholder="용어"
                                   class="w-full h-10 px-3 border border-gray-200 rounded-lg bg-white text-sm font-bold text-[#2D3047] focus:outline-none focus:border-[#4ECDC4]">
                            <textarea name="terms[{{ $index }}][definition]"
                                      rows="2"
                                      maxlength="500"
                                      placeholder="정의"
                                      class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:border-[#4ECDC4] resize-y">{{ $term['definition'] }}</textarea>
                            <input type="text"
                                   name="terms[{{ $index }}][context]"
                                   value="{{ $term['context'] }}"
                                   maxlength="300"
                                   placeholder="이 기사에서의 쓰임"
                                   class="w-full h-9 px-3 border border-gray-200 rounded-lg bg-white text-xs text-gray-500 focus:outline-none focus:border-[#4ECDC4]">
                        </div>
                        <button type="button"
                                class="term-remove shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all"
                                title="이 용어 삭제">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <p id="termsEmptyHint" class="text-xs text-gray-400 {{ count($terms) ? 'hidden' : '' }}">
            아직 용어가 없습니다. [AI분석] 을 누르거나 직접 추가해보세요.
        </p>
        @error('terms')
            <p class="text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- 6) 향후 전망 -->
    <div class="space-y-2">
        <label class="text-sm font-semibold text-[#2D3047] block">향후 전망</label>
        <p class="text-xs text-gray-400 mb-3">한 줄에 한 항목씩 적습니다. 줄바꿈이 항목 구분입니다</p>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-1.5">
                <span class="text-xs font-bold text-[#2D3047] flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    단기 (3~6개월)
                </span>
                <textarea name="mq_ai_outlook_short"
                          id="mq_ai_outlook_short"
                          rows="6"
                          maxlength="5000"
                          class="{{ $inputClass }} text-sm resize-y @error('mq_ai_outlook_short') border-red-500 @enderror"
                          placeholder="[AI분석] 버튼을 누르면 자동으로 채워집니다">{{ $outlookShort }}</textarea>
                @error('mq_ai_outlook_short')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-1.5">
                <span class="text-xs font-bold text-[#2D3047] flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                    중장기 (1~3년)
                </span>
                <textarea name="mq_ai_outlook_long"
                          id="mq_ai_outlook_long"
                          rows="6"
                          maxlength="5000"
                          class="{{ $inputClass }} text-sm resize-y @error('mq_ai_outlook_long') border-red-500 @enderror"
                          placeholder="[AI분석] 버튼을 누르면 자동으로 채워집니다">{{ $outlookLong }}</textarea>
                @error('mq_ai_outlook_long')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- 7) 내 경제상황에 맞는 질문 -->
    <div class="space-y-2">
        <label class="text-sm font-semibold text-[#2D3047] block">내 경제상황에 맞는 질문</label>
        <p class="text-xs text-gray-400 mb-3">
            정답이 있는 질문이 아닙니다. 스스로 답해보면서 이 뉴스를 내 상황으로 옮겨보세요
        </p>
        <div class="space-y-3">
            @foreach($questions as $index => $question)
                <div class="flex items-start gap-3">
                    <span class="shrink-0 w-7 h-7 mt-1.5 rounded-full bg-[#2D3047] text-white text-xs font-bold flex items-center justify-center">
                        {{ $index + 1 }}
                    </span>
                    <textarea name="mq_ai_questions[{{ $index }}]"
                              id="mq_ai_question_{{ $index }}"
                              rows="2"
                              maxlength="500"
                              class="{{ $inputClass }} text-sm resize-y"
                              placeholder="{{ $index === 0 ? '지금 당장 확인해볼 수 있는 것에 대한 질문' : '내 1~3년 계획과 연결되는 질문' }}">{{ $question }}</textarea>
                </div>
            @endforeach
        </div>
        @error('mq_ai_questions.*')
            <p class="text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

@push('scripts')
<script>
(function () {
    var MAX_TERMS = {{ \App\Models\BoardScrap::MAX_TERMS }};
    var ANALYZE_URL = '{{ route('board-scrap.ai-analyze') }}';

    var analyzeBtn = document.getElementById('aiAnalyzeBtn');
    var urlInput = document.getElementById('mq_url');
    var resultSection = document.getElementById('aiResultSection');
    var termsList = document.getElementById('termsList');
    var termsEmptyHint = document.getElementById('termsEmptyHint');
    var addTermBtn = document.getElementById('addTermBtn');
    var statusBox = document.getElementById('aiAnalyzeStatus');
    var metaBadge = document.getElementById('aiMetaBadge');

    // 용어 행 name 인덱스. 서버는 순서만 보므로 중간이 비어도 무관하다.
    var termIndex = termsList ? termsList.querySelectorAll('.term-row').length : 0;

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
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
        var tones = {
            info: 'text-gray-500',
            error: 'text-red-500',
            success: 'text-[#2AA9A0]'
        };
        statusBox.className = 'text-xs mt-2 ' + (tones[tone] || tones.info);
        statusBox.textContent = message;
    }

    function refreshTermsHint() {
        if (!termsEmptyHint || !termsList) {
            return;
        }
        var count = termsList.querySelectorAll('.term-row').length;
        termsEmptyHint.classList.toggle('hidden', count > 0);
    }

    function termRowHtml(index, term) {
        var name = 'terms[' + index + ']';
        return ''
            + '<div class="term-row rounded-xl border border-gray-200 bg-gray-50 p-4" data-index="' + index + '">'
            +   '<div class="flex items-start gap-3">'
            +     '<label class="flex items-center gap-2 shrink-0 pt-2.5 cursor-pointer" title="새로 알게 된 용어로 체크">'
            +       '<input type="checkbox" name="' + name + '[checked]" value="1"' + (term.checked ? ' checked' : '')
            +         ' class="w-4 h-4 rounded border-gray-300 text-[#4ECDC4] focus:ring-[#4ECDC4]">'
            +       '<span class="text-xs text-gray-500 whitespace-nowrap">알게 됨</span>'
            +     '</label>'
            +     '<div class="flex-1 space-y-2 min-w-0">'
            +       '<input type="text" name="' + name + '[term]" value="' + escapeHtml(term.term) + '" maxlength="100" placeholder="용어"'
            +         ' class="w-full h-10 px-3 border border-gray-200 rounded-lg bg-white text-sm font-bold text-[#2D3047] focus:outline-none focus:border-[#4ECDC4]">'
            +       '<textarea name="' + name + '[definition]" rows="2" maxlength="500" placeholder="정의"'
            +         ' class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:border-[#4ECDC4] resize-y">'
            +         escapeHtml(term.definition) + '</textarea>'
            +       '<input type="text" name="' + name + '[context]" value="' + escapeHtml(term.context) + '" maxlength="300" placeholder="이 기사에서의 쓰임"'
            +         ' class="w-full h-9 px-3 border border-gray-200 rounded-lg bg-white text-xs text-gray-500 focus:outline-none focus:border-[#4ECDC4]">'
            +     '</div>'
            +     '<button type="button" class="term-remove shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all" title="이 용어 삭제">'
            +       '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
            +         '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>'
            +       '</svg>'
            +     '</button>'
            +   '</div>'
            + '</div>';
    }

    function appendTerm(term) {
        if (!termsList) {
            return false;
        }
        if (termsList.querySelectorAll('.term-row').length >= MAX_TERMS) {
            return false;
        }
        termsList.insertAdjacentHTML('beforeend', termRowHtml(termIndex, {
            term: term && term.term ? term.term : '',
            definition: term && term.definition ? term.definition : '',
            context: term && term.context ? term.context : '',
            checked: !!(term && term.checked)
        }));
        termIndex += 1;
        refreshTermsHint();
        return true;
    }

    if (addTermBtn) {
        addTermBtn.addEventListener('click', function () {
            if (!appendTerm({})) {
                alert('경제 용어는 최대 ' + MAX_TERMS + '개까지 추가할 수 있습니다.');
                return;
            }
            var rows = termsList.querySelectorAll('.term-row');
            var input = rows[rows.length - 1].querySelector('input[type="text"]');
            if (input) {
                input.focus();
            }
        });
    }

    if (termsList) {
        // 행이 동적으로 늘어나므로 위임으로 처리한다
        termsList.addEventListener('click', function (event) {
            var button = event.target.closest('.term-remove');
            if (!button) {
                return;
            }
            var row = button.closest('.term-row');
            if (row) {
                row.remove();
                refreshTermsHint();
            }
        });
    }

    /**
     * "이 뉴스를 선택한 이유" 본문. 파트7 질문 개인화에 쓰인다.
     * CKEditor 초기화 전이면 원본 textarea 값을 읽는다.
     */
    function getReasonText() {
        try {
            if (typeof editorInstance !== 'undefined' && editorInstance) {
                return editorInstance.getData();
            }
        } catch (error) {
            // CKEditor 미초기화 - 아래에서 textarea 로 폴백
        }
        var textarea = document.getElementById('editor');
        return textarea ? textarea.value : '';
    }

    function fillResult(data) {
        var interpretation = document.getElementById('mq_ai_interpretation');
        if (interpretation) {
            interpretation.value = data.interpretation || '';
        }

        var shortTerm = document.getElementById('mq_ai_outlook_short');
        if (shortTerm) {
            shortTerm.value = (data.outlook && data.outlook.shortTerm ? data.outlook.shortTerm : []).join('\n');
        }

        var longTerm = document.getElementById('mq_ai_outlook_long');
        if (longTerm) {
            longTerm.value = (data.outlook && data.outlook.longTerm ? data.outlook.longTerm : []).join('\n');
        }

        var questions = data.questions || [];
        for (var i = 0; i < 2; i += 1) {
            var box = document.getElementById('mq_ai_question_' + i);
            if (box) {
                box.value = questions[i] || '';
            }
        }

        if (termsList) {
            termsList.innerHTML = '';
            termIndex = 0;
            (data.terms || []).forEach(function (term) {
                appendTerm(term);
            });
            refreshTermsHint();
        }

        // 제목이 비어 있을 때만 기사 제목으로 채운다 (사용자가 쓴 제목을 덮지 않는다)
        var titleInput = document.getElementById('mq_title');
        if (titleInput && titleInput.value.trim() === '' && data.articleTitle) {
            titleInput.value = data.articleTitle;
        }

        if (resultSection) {
            resultSection.classList.remove('hidden');
        }
    }

    function setMeta(meta) {
        var modelInput = document.getElementById('mq_ai_model');
        var sourceInput = document.getElementById('mq_ai_source');

        if (modelInput) {
            modelInput.value = meta && meta.model ? meta.model : '';
        }
        if (sourceInput) {
            sourceInput.value = meta && meta.source ? meta.source : '';
        }
        if (metaBadge && meta && meta.source) {
            metaBadge.textContent = (meta.model || '') + ' · '
                + (meta.source === 'url_context' ? '원문 직접 열람' : '본문 분석');
            metaBadge.classList.remove('hidden');
        }
    }

    function setLoading(isLoading) {
        if (!analyzeBtn) {
            return;
        }
        analyzeBtn.disabled = isLoading;
        analyzeBtn.classList.toggle('opacity-60', isLoading);
        analyzeBtn.classList.toggle('cursor-not-allowed', isLoading);
        var label = analyzeBtn.querySelector('.ai-btn-label');
        var spinner = analyzeBtn.querySelector('.ai-btn-spinner');
        if (label) {
            label.textContent = isLoading ? '분석 중' : 'AI분석';
        }
        if (spinner) {
            spinner.classList.toggle('hidden', !isLoading);
        }
    }

    if (analyzeBtn) {
        analyzeBtn.addEventListener('click', function () {
            var url = urlInput ? urlInput.value.trim() : '';

            if (url === '') {
                setStatus('먼저 뉴스 링크를 입력해주세요.', 'error');
                if (urlInput) {
                    urlInput.focus();
                }
                return;
            }

            var hasContent = document.getElementById('mq_ai_interpretation');
            hasContent = hasContent && hasContent.value.trim() !== '';
            if (hasContent && !confirm('기존 AI 분석 결과를 새로 만든 결과로 덮어씁니다. 계속하시겠습니까?')) {
                return;
            }

            setLoading(true);
            setStatus('뉴스를 읽고 분석하는 중입니다. 20초 정도 걸릴 수 있습니다.', 'info');

            var body = new FormData();
            body.append('mq_url', url);
            body.append('mq_reason', getReasonText());

            fetch(ANALYZE_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: body,
                credentials: 'same-origin'
            })
                .then(function (response) {
                    return response.json().then(function (json) {
                        return { status: response.status, json: json };
                    });
                })
                .then(function (result) {
                    if (result.status === 401 || result.status === 419) {
                        setStatus('로그인이 만료되었습니다. 작성 중인 내용을 복사해두고 다시 로그인해주세요.', 'error');
                        return;
                    }
                    if (result.status === 429) {
                        setStatus('AI 분석 요청이 너무 잦습니다. 잠시 후 다시 시도해주세요.', 'error');
                        return;
                    }
                    if (!result.json || !result.json.success) {
                        setStatus((result.json && result.json.message) || 'AI 분석에 실패했습니다. 잠시 후 다시 시도해주세요.', 'error');
                        return;
                    }
                    fillResult(result.json.data);
                    setMeta(result.json.meta);
                    setStatus('분석이 끝났습니다. 아래 내용을 확인하고 자유롭게 수정하세요.', 'success');
                    if (resultSection) {
                        resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                })
                .catch(function () {
                    setStatus('AI 분석 중 오류가 발생했습니다. 네트워크 상태를 확인하고 다시 시도해주세요.', 'error');
                })
                .then(function () {
                    setLoading(false);
                });
        });
    }
})();
</script>
@endpush
