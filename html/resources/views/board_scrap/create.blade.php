@extends('layouts.app')

@section('content')
<!-- ===== Header Section ===== -->
<div class="relative bg-[#3D4148] py-12 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-xs font-medium mb-3 backdrop-blur-md">
            📰 News Scrap
        </span>
        <h1 class="text-3xl font-bold text-white mb-2">새 뉴스 스크랩 작성</h1>
        <p class="text-gray-400 text-sm">관심 있는 뉴스를 스크랩하고 나만의 인사이트를 정리해보세요.</p>
    </div>
</div>

<div class="min-h-screen bg-gray-50 -mt-8 pb-20 relative z-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 md:p-10">
                <form action="{{ route('board-scrap.store') }}" method="POST" class="space-y-8" id="scrapForm">
                    @csrf

                    <!-- 비밀글 설정 (체크하지 않으면 공개) -->
                    <div class="flex justify-start">
                        <!-- 체크박스는 해제 시 값을 보내지 않으므로 hidden 으로 기본값 0(공개)을 명시한다 -->
                        <input type="hidden" name="mq_is_secret" value="0">
                        <label for="mq_is_secret" class="inline-flex items-center gap-2 cursor-pointer" title="켜면 게시판에 올리지 않고 나만 볼 수 있습니다">
                            <input type="checkbox"
                                   name="mq_is_secret"
                                   id="mq_is_secret"
                                   value="1"
                                   {{ old('mq_is_secret') ? 'checked' : '' }}
                                   class="peer sr-only">
                            <span class="relative w-4 h-4 flex-shrink-0 rounded border border-gray-300 bg-white transition-colors peer-checked:bg-[#2D3047] peer-checked:border-[#2D3047] after:absolute after:left-1 after:top-0.5 after:w-1.5 after:h-2.5 after:border-r-2 after:border-b-2 after:border-white after:rotate-45 after:opacity-0 peer-checked:after:opacity-100"></span>
                            <span class="text-xs font-semibold text-gray-600">비밀글로 저장</span>
                            <span class="text-xs text-gray-400 peer-checked:hidden">체크하지 않으면 게시판에 공개됩니다</span>
                            <span class="hidden text-xs font-semibold text-[#2D3047] peer-checked:inline">나만 볼 수 있습니다</span>
                        </label>
                    </div>

                    <!-- 뉴스 제목 -->
                    <div class="space-y-2">
                        <label for="mq_title" class="text-sm font-semibold text-[#2D3047] block">
                            뉴스 제목 <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="mq_title"
                               id="mq_title"
                               value="{{ old('mq_title') }}"
                               class="w-full h-12 px-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#4ECDC4] focus:ring-2 focus:ring-[#4ECDC4]/20 transition-all bg-gray-50 font-medium text-gray-700 placeholder-gray-400 @error('mq_title') border-red-500 @enderror"
                               placeholder="뉴스 제목을 입력하세요"
                               required>
                        @error('mq_title')
                            <p class="text-sm text-red-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 뉴스를 선택한 이유 (CKEditor) -->
                    <div class="space-y-2">
                        <label for="editor" class="text-sm font-semibold text-[#2D3047] block">
                            뉴스를 선택한 이유 <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-400 mb-3">길게 쓰지 않아도 됩니다. 이 뉴스를 고른 이유를 한두 줄로 남겨주세요</p>
                        <textarea name="mq_reason" id="editor">{{ old('mq_reason') }}</textarea>
                        @error('mq_reason')
                            <p class="text-sm text-red-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 구분선 -->
                    <div class="border-t border-gray-100"></div>

                    <!-- 뉴스 링크 -->
                    <div class="space-y-2">
                        <label for="mq_url" class="text-sm font-semibold text-[#2D3047] block">
                            뉴스 링크 <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                                <input type="url"
                                       name="mq_url"
                                       id="mq_url"
                                       value="{{ old('mq_url') }}"
                                       class="w-full h-12 pl-10 pr-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#4ECDC4] focus:ring-2 focus:ring-[#4ECDC4]/20 transition-all bg-gray-50 font-medium text-gray-700 placeholder-gray-400 @error('mq_url') border-red-500 @enderror"
                                       placeholder="https://example.com/news/article"
                                       required>
                            </div>
                            <button type="button"
                                    id="aiAnalyzeBtn"
                                    class="js-ai-analyze inline-flex items-center justify-center gap-2 shrink-0 h-12 px-6 rounded-xl bg-gradient-to-r from-violet-500 to-indigo-500 text-white font-bold hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all">
                                <svg class="ai-btn-spinner hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                                <span class="ai-btn-label">AI분석</span>
                            </button>
                        </div>
                        @error('mq_url')
                            <p class="text-sm text-red-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        @include('board_scrap._url_duplicate_check')
                        <p class="text-xs text-gray-400 flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-[#4ECDC4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>
                                링크를 넣고 <strong class="text-indigo-500">[AI분석]</strong> 을 누르면 해석 · 경제 용어 · 향후 전망 · 내 상황 질문이 자동으로 채워집니다.
                                위 <strong>뉴스를 선택한 이유</strong>를 먼저 써야 분석할 수 있으며, 질문은 이 내용을 바탕으로 만들어집니다. 썸네일 이미지는 자동으로 가져옵니다.
                            </span>
                        </p>
                    </div>

                    <!-- 구분선 -->
                    <div class="border-t border-gray-100"></div>

                    @include('board_scrap._ai_form')

                    <!-- 구분선 -->
                    <div class="border-t border-gray-100"></div>

                    <!-- 버튼 영역 -->
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button"
                                onclick="if(confirm('작성 중인 내용이 사라집니다. 정말 취소하시겠습니까?')) { location.href='{{ route('board-scrap.index') }}'; }"
                                class="inline-flex items-center justify-center px-6 h-12 border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all text-gray-600 font-medium">
                            취소
                        </button>
                        <button type="submit"
                                class="inline-flex items-center justify-center px-8 h-12 bg-gradient-to-r from-[#4ECDC4] to-[#2AA9A0] text-white rounded-xl hover:shadow-lg hover:shadow-[#4ECDC4]/30 hover:-translate-y-0.5 transition-all font-bold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            스크랩 저장
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    // CKEditor 초기화
    let editorInstance;

    class UploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                this._initRequest();
                this._initListeners(resolve, reject, file);
                this._sendRequest(file);
            }));
        }

        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('board-scrap.upload-image') }}', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.responseType = 'json';
        }

        _initListeners(resolve, reject, file) {
            const xhr = this.xhr;
            const genericErrorText = '파일을 업로드 할 수 없습니다.';

            xhr.addEventListener('error', () => reject(genericErrorText));
            xhr.addEventListener('abort', () => reject());
            xhr.addEventListener('load', () => {
                const response = xhr.response;
                if (!response || response.error) {
                    return reject(response && response.error ? response.error.message : genericErrorText);
                }

                resolve({
                    default: response.url
                });
            });
        }

        _sendRequest(file) {
            const data = new FormData();
            data.append('upload', file);
            this.xhr.send(data);
        }
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new UploadAdapter(loader);
        };
    }

    // URL 파라미터에서 제목과 URL 가져오기 (스크랩 버튼에서 전달)
    const urlParams = new URLSearchParams(window.location.search);
    const titleFromParam = urlParams.get('title');
    const urlFromParam = urlParams.get('url');

    // 파라미터가 있으면 입력 필드에 자동 입력
    if (titleFromParam) {
        document.getElementById('mq_title').value = titleFromParam;
    }
    if (urlFromParam) {
        document.getElementById('mq_url').value = urlFromParam;

        // 값을 직접 넣으면 input 이벤트가 안 나므로 중복 검사를 직접 부른다
        if (window.scrapUrlDuplicateCheck) {
            window.scrapUrlDuplicateCheck();
        }
    }

    ClassicEditor
        .create(document.querySelector('#editor'), {
            extraPlugins: [MyCustomUploadAdapterPlugin],
            toolbar: {
                items: [
                    'undo', 'redo',
                    '|', 'heading',
                    '|', 'bold', 'italic', 'strikethrough', 'underline',
                    '|', 'bulletedList', 'numberedList',
                    '|', 'alignment',
                    '|', 'link', 'uploadImage', 'blockQuote', 'insertTable',
                    '|', 'fontColor', 'fontBackgroundColor'
                ]
            },
            language: 'ko'
        })
        .then(editor => {
            editorInstance = editor;
            console.log('Editor was initialized');
        })
        .catch(error => {
            console.error('Error:', error);
        });

    // 폼 제출 시 CKEditor 내용 검증
    document.getElementById('scrapForm').addEventListener('submit', function(e) {
        if (!editorInstance) {
            return true;
        }

        // 빈 칸이어도 <p>&nbsp;</p> 가 오므로 태그를 걷어낸 글자로 판단한다
        const box = document.createElement('div');
        box.innerHTML = editorInstance.getData();
        if ((box.textContent || '').trim() === '') {
            e.preventDefault();
            alert('뉴스를 선택한 이유를 입력해주세요.');
            editorInstance.focus();
            return false;
        }

        return true;
    });
</script>

<style>
.ck-editor__editable {
    /* 부담 없이 짧게 쓰도록 기존 높이의 절반으로 줄였다 */
    min-height: 150px;
    max-height: 250px;
    border-radius: 0 0 0.75rem 0.75rem !important;
    border-color: #e5e7eb !important;
    background-color: #f9fafb !important;
}
.ck-editor__editable:focus {
    box-shadow: 0 0 0 2px rgba(78, 205, 196, 0.2) !important;
    border-color: #4ECDC4 !important;
}
.ck.ck-toolbar {
    border-color: #e5e7eb !important;
    border-radius: 0.75rem 0.75rem 0 0 !important;
    background-color: #f9fafb !important;
}
.ck.ck-toolbar .ck-toolbar__items {
    flex-wrap: wrap;
}
.ck.ck-editor__main > .ck-editor__editable {
    border-top: none !important;
}
</style>
@endpush
@endsection
