{{--
    뉴스 스크랩 AI 분석 파트 폼 (등록 / 수정 공용)

    $scrap 이 넘어오면 수정 화면, 없으면 등록 화면으로 동작한다.
    검증 실패로 되돌아온 경우 old() 값이 모델 값보다 우선한다.

    분석 전에는 결과와 같은 모양의 스켈레톤을 깔고, 그 위 반투명 레이어 가운데에 [AI분석] 버튼을 둔다.
    분석이 끝나면 스켈레톤을 감추고 실제 입력 폼으로 바꿔 끼운다.
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
                'checked' => !empty($row['checked']),
            ];
        }
    } else {
        $terms = $scrap ? $scrap->getAiTerms() : [];
    }

    // 질문은 항상 2칸으로 맞춘다. 질문 문구는 AI 가 만든 값을 그대로 두고 사용자는 답변만 쓴다.
    $oldQuestions = old('mq_ai_questions');
    $questions = is_array($oldQuestions)
        ? array_values($oldQuestions)
        : ($scrap ? $scrap->getAiQuestions() : []);
    $questions = array_pad(array_slice($questions, 0, 2), 2, '');

    $oldAnswers = old('mq_ai_answers');
    $answers = is_array($oldAnswers)
        ? array_values($oldAnswers)
        : ($scrap ? $scrap->getAiAnswers(2) : []);
    $answers = array_pad(array_slice($answers, 0, 2), 2, '');

    $hasAiContent = trim((string) $interpretation) !== ''
        || trim((string) $outlookShort) !== ''
        || trim((string) $outlookLong) !== ''
        || count($terms) > 0
        || trim(implode('', $questions)) !== '';

    $inputClass = 'w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#4ECDC4] focus:ring-2 focus:ring-[#4ECDC4]/20 transition-all bg-gray-50 text-gray-700 placeholder-gray-400';
@endphp

<input type="hidden" name="mq_ai_model" id="mq_ai_model" value="{{ $aiModel }}">
<input type="hidden" name="mq_ai_source" id="mq_ai_source" value="{{ $aiSource }}">

<!-- ===== AI 분석 ===== -->
<div class="space-y-4">

    <div>
        <h3 class="text-base font-bold text-[#2D3047] flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-gradient-to-br from-violet-500 to-indigo-500 text-white text-xs font-bold">AI</span>
            AI 분석 결과
        </h3>
        <p class="text-xs text-gray-400 mt-1">AI가 만든 초안입니다. 자유롭게 고쳐서 저장하세요.</p>
    </div>

    <p id="aiAnalyzeStatus" class="text-xs hidden"></p>

    <!-- 분석 전: 결과와 같은 모양의 스켈레톤 위에 반투명 레이어와 [AI분석] 버튼을 겹친다 -->
    <div id="aiSkeleton" class="relative rounded-2xl overflow-hidden {{ $hasAiContent ? 'hidden' : '' }}">
        <div class="space-y-6 animate-pulse select-none" aria-hidden="true">
            <!-- 짧은 해석 -->
            <div class="space-y-2">
                <div class="h-3.5 w-32 rounded bg-gray-200"></div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-2.5">
                    <div class="h-2.5 w-full rounded bg-gray-200"></div>
                    <div class="h-2.5 w-11/12 rounded bg-gray-200"></div>
                    <div class="h-2.5 w-full rounded bg-gray-200"></div>
                    <div class="h-2.5 w-2/3 rounded bg-gray-200"></div>
                </div>
            </div>

            <!-- 경제 용어 -->
            <div class="space-y-2">
                <div class="h-3.5 w-28 rounded bg-gray-200"></div>
                <div class="space-y-3">
                    @for($i = 0; $i < 2; $i++)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 flex items-start gap-3">
                            <div class="w-4 h-4 mt-1 rounded bg-gray-200 shrink-0"></div>
                            <div class="flex-1 space-y-2.5 min-w-0">
                                <div class="h-3 w-1/3 rounded bg-gray-300"></div>
                                <div class="h-2.5 w-full rounded bg-gray-200"></div>
                                <div class="h-2.5 w-4/5 rounded bg-gray-200"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- 향후 전망 -->
            <div class="space-y-2">
                <div class="h-3.5 w-20 rounded bg-gray-200"></div>
                <div class="grid gap-4 md:grid-cols-2">
                    @for($i = 0; $i < 2; $i++)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-2.5">
                            <div class="h-2.5 w-24 rounded bg-gray-300"></div>
                            <div class="h-2.5 w-full rounded bg-gray-200"></div>
                            <div class="h-2.5 w-5/6 rounded bg-gray-200"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- 내 상황 질문 + 답변란 -->
            <div class="space-y-2">
                <div class="h-3.5 w-36 rounded bg-gray-200"></div>
                <div class="space-y-3">
                    @for($i = 0; $i < 2; $i++)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full bg-gray-200 shrink-0"></div>
                                <div class="flex-1 space-y-2 min-w-0 pt-1">
                                    <div class="h-2.5 w-full rounded bg-gray-200"></div>
                                    <div class="h-2.5 w-3/4 rounded bg-gray-200"></div>
                                </div>
                            </div>
                            <div class="h-16 rounded-lg border border-gray-200 bg-white"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 px-6 text-center bg-white/70 backdrop-blur-[2px]">
            <button type="button"
                    id="aiAnalyzeBtnSkeleton"
                    class="js-ai-analyze inline-flex items-center justify-center gap-2 h-12 px-7 rounded-xl bg-gradient-to-r from-violet-500 to-indigo-500 text-white font-bold shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all">
                <svg class="ai-btn-spinner hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                <span class="ai-btn-label">AI분석</span>
            </button>
            <p class="text-xs text-gray-500 max-w-sm">
                위에 뉴스 링크를 넣고 눌러주세요. 해석 · 경제 용어 · 향후 전망 · 내 상황 질문이 한 번에 채워집니다.
            </p>
        </div>
    </div>

    <!-- 분석 후: 실제 입력 폼 -->
    <div id="aiResultSection" class="space-y-8 {{ $hasAiContent ? '' : 'hidden' }}">

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
                몰랐던 용어는 <span class="font-semibold text-[#2AA9A0]">저장</span>에 체크해두면 상세 화면에 따로 모아서 보여줍니다
            </p>

            <div id="termsList" class="space-y-3">
                @foreach($terms as $index => $term)
                    <div class="term-row rounded-xl border border-gray-200 bg-gray-50 p-4" data-index="{{ $index }}">
                        <div class="flex items-start gap-3">
                            <label class="flex items-center gap-1.5 shrink-0 pt-2.5 cursor-pointer" title="몰랐던 용어로 저장">
                                <input type="checkbox"
                                       name="terms[{{ $index }}][checked]"
                                       value="1"
                                       {{ $term['checked'] ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-gray-300 text-[#4ECDC4] focus:ring-[#4ECDC4]">
                                <span class="text-xs text-gray-500 whitespace-nowrap">저장</span>
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
                                          placeholder="용어 설명"
                                          class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:border-[#4ECDC4] resize-y">{{ $term['definition'] }}</textarea>
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
        {{-- 질문은 AI 가 만든 문구 그대로 라벨로 보여주고(hidden 으로 되돌려 보낸다), 사용자는 답변만 쓴다 --}}
        <div class="space-y-2">
            <label class="text-sm font-semibold text-[#2D3047] block">
                내 경제상황에 맞는 질문
                <span class="text-xs font-normal text-gray-400 ml-1">답변은 선택</span>
                <span class="inline-flex items-center gap-1 ml-1 py-0.5 px-2 rounded-full bg-[#4ECDC4]/10 text-[#2AA9A0] text-xs font-bold align-middle">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    나만 보임
                </span>
            </label>
            <p class="text-xs text-gray-400 mb-3">
                정답이 있는 질문이 아닙니다. 스스로 답해보면서 이 뉴스를 내 상황으로 옮겨보세요
            </p>
            {{-- 개인 경제 상황이 담기는 칸이라 상세보기에서 작성자에게만 보여준다(show.blade.php) --}}
            <p class="text-xs text-[#2AA9A0] bg-[#4ECDC4]/10 border border-[#4ECDC4]/30 rounded-lg px-3 py-2 mb-3 leading-relaxed">
                이 항목은 전체 공개로 저장해도 질문과 답변 모두 다른 사람에게 보이지 않습니다. 나만 볼 수 있으니 편하게 적어보세요
            </p>
            <div class="space-y-3">
                @foreach($questions as $index => $question)
                    <div id="aiQuestionBlock_{{ $index }}"
                         class="ai-question-block rounded-xl border border-gray-200 bg-gray-50 p-4 {{ trim((string) $question) === '' ? 'hidden' : '' }}">
                        <input type="hidden"
                               name="mq_ai_questions[{{ $index }}]"
                               id="mq_ai_question_{{ $index }}"
                               value="{{ $question }}">
                        <div class="flex items-start gap-3">
                            <span class="shrink-0 w-7 h-7 rounded-full bg-[#2D3047] text-white text-xs font-bold flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <p id="aiQuestionText_{{ $index }}" class="flex-1 min-w-0 pt-1 text-sm font-semibold text-[#2D3047] leading-relaxed">{{ $question }}</p>
                        </div>
                        <textarea name="mq_ai_answers[{{ $index }}]"
                                  id="mq_ai_answer_{{ $index }}"
                                  rows="3"
                                  maxlength="1000"
                                  class="w-full mt-3 px-3 py-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#4ECDC4] focus:ring-2 focus:ring-[#4ECDC4]/20 transition-all resize-y"
                                  placeholder="여기에 내 생각을 적어보세요 (선택)">{{ $answers[$index] }}</textarea>
                    </div>
                @endforeach
            </div>
            @error('mq_ai_answers.*')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var MAX_TERMS = {{ \App\Models\BoardScrap::MAX_TERMS }};
    var ANALYZE_URL = '{{ route('board-scrap.ai-analyze') }}';

    // 뉴스 링크 우측 버튼과 스켈레톤 위 버튼이 같은 동작을 공유한다
    var analyzeBtns = Array.prototype.slice.call(document.querySelectorAll('.js-ai-analyze'));
    var urlInput = document.getElementById('mq_url');
    var skeleton = document.getElementById('aiSkeleton');
    var resultSection = document.getElementById('aiResultSection');
    var termsList = document.getElementById('termsList');
    var termsEmptyHint = document.getElementById('termsEmptyHint');
    var addTermBtn = document.getElementById('addTermBtn');
    var statusBox = document.getElementById('aiAnalyzeStatus');

    // 로딩 중 라벨을 "분석 중" 으로 바꾸므로 버튼별 원래 문구를 미리 담아둔다
    analyzeBtns.forEach(function (button) {
        var label = button.querySelector('.ai-btn-label');
        if (label) {
            button.setAttribute('data-idle-label', label.textContent);
        }
    });

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
        statusBox.className = 'text-xs ' + (tones[tone] || tones.info);
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
            +     '<label class="flex items-center gap-1.5 shrink-0 pt-2.5 cursor-pointer" title="몰랐던 용어로 저장">'
            +       '<input type="checkbox" name="' + name + '[checked]" value="1"' + (term.checked ? ' checked' : '')
            +         ' class="w-4 h-4 rounded border-gray-300 text-[#4ECDC4] focus:ring-[#4ECDC4]">'
            +       '<span class="text-xs text-gray-500 whitespace-nowrap">저장</span>'
            +     '</label>'
            +     '<div class="flex-1 space-y-2 min-w-0">'
            +       '<input type="text" name="' + name + '[term]" value="' + escapeHtml(term.term) + '" maxlength="100" placeholder="용어"'
            +         ' class="w-full h-10 px-3 border border-gray-200 rounded-lg bg-white text-sm font-bold text-[#2D3047] focus:outline-none focus:border-[#4ECDC4]">'
            +       '<textarea name="' + name + '[definition]" rows="2" maxlength="500" placeholder="용어 설명"'
            +         ' class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700 focus:outline-none focus:border-[#4ECDC4] resize-y">'
            +         escapeHtml(term.definition) + '</textarea>'
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

    /** 스켈레톤을 걷어내고 실제 입력 폼을 보여준다 */
    function revealResult() {
        if (skeleton) {
            skeleton.classList.add('hidden');
        }
        if (resultSection) {
            resultSection.classList.remove('hidden');
        }
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

        // 질문은 라벨로만 보여주고 hidden 에 값을 담는다.
        // 질문이 바뀌면 앞선 답변은 짝이 맞지 않으므로 함께 비운다.
        var questions = data.questions || [];
        for (var i = 0; i < 2; i += 1) {
            var question = questions[i] || '';
            var hidden = document.getElementById('mq_ai_question_' + i);
            var text = document.getElementById('aiQuestionText_' + i);
            var answer = document.getElementById('mq_ai_answer_' + i);
            var block = document.getElementById('aiQuestionBlock_' + i);

            if (hidden) {
                hidden.value = question;
            }
            if (text) {
                text.textContent = question;
            }
            if (answer) {
                answer.value = '';
            }
            if (block) {
                block.classList.toggle('hidden', question === '');
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

        revealResult();
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
    }

    function setLoading(isLoading) {
        analyzeBtns.forEach(function (button) {
            button.disabled = isLoading;
            button.classList.toggle('opacity-60', isLoading);
            button.classList.toggle('cursor-not-allowed', isLoading);

            var label = button.querySelector('.ai-btn-label');
            var spinner = button.querySelector('.ai-btn-spinner');
            if (label) {
                label.textContent = isLoading ? '분석 중' : button.getAttribute('data-idle-label');
            }
            if (spinner) {
                spinner.classList.toggle('hidden', !isLoading);
            }
        });
    }

    function runAnalyze() {
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
    }

    analyzeBtns.forEach(function (button) {
        button.addEventListener('click', runAnalyze);
    });
})();
</script>
@endpush
