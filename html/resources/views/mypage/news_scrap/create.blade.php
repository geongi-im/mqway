@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-8">새 뉴스 스크랩 작성</h1>

            <form action="{{ route('mypage.news-scrap.store') }}" method="POST" class="space-y-6" id="scrapForm">
                @csrf

                <!-- 뉴스 제목 -->
                <div class="space-y-2">
                    <label for="mq_title" class="block text-sm font-medium text-gray-700">뉴스 제목 <span class="text-red-500">*</span></label>
                    <input type="text"
                           name="mq_title"
                           id="mq_title"
                           value="{{ old('mq_title') }}"
                           class="w-full h-12 px-4 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('mq_title') border-red-500 @enderror"
                           placeholder="뉴스 제목을 입력하세요"
                           required>
                    @error('mq_title')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 뉴스 링크 -->
                <div class="space-y-2">
                    <label for="mq_url" class="block text-sm font-medium text-gray-700">뉴스 링크 <span class="text-red-500">*</span></label>
                    <input type="url"
                           name="mq_url"
                           id="mq_url"
                           value="{{ old('mq_url') }}"
                           class="w-full h-12 px-4 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('mq_url') border-red-500 @enderror"
                           placeholder="https://example.com/news/article"
                           required>
                    @error('mq_url')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500">뉴스 원문 링크를 입력하세요. 썸네일 이미지는 자동으로 가져옵니다.</p>
                </div>

                <!-- 뉴스를 선택한 이유 (CKEditor) -->
                <div class="space-y-2">
                    <label for="editor" class="block text-sm font-medium text-gray-700">뉴스를 선택한 이유 <span class="text-red-500">*</span></label>
                    <p class="text-sm text-gray-500 mb-2">이 뉴스를 스크랩하는 이유와 느낀 점을 작성해주세요</p>
                    <textarea name="mq_reason" id="editor">{{ old('mq_reason') }}</textarea>
                    @error('mq_reason')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 새로 알게된 용어 -->
                <div class="space-y-2">
                    <label for="mq_new_terms" class="block text-sm font-medium text-gray-700">새로 알게된 용어</label>
                    <p class="text-sm text-gray-500 mb-2">이 뉴스를 통해 새롭게 알게된 경제 용어나 개념을 정리해보세요 (선택사항)</p>
                    <textarea name="mq_new_terms"
                              id="mq_new_terms"
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all resize-none @error('mq_new_terms') border-red-500 @enderror"
                              placeholder="예: GDP (국내총생산) - 한 나라의 경제 규모를 나타내는 지표">{{ old('mq_new_terms') }}</textarea>
                    @error('mq_new_terms')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 버튼 영역 -->
                <div class="flex justify-end gap-3 pt-6">
                    <a href="{{ route('mypage.news-scrap.index') }}"
                       class="inline-flex items-center justify-center px-6 h-12 border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700">
                        취소
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-6 h-12 bg-point1 text-cdark rounded-xl hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-dark focus:ring-offset-2 transition-all">
                        작성하기
                    </button>
                </div>
            </form>
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
            xhr.open('POST', '{{ route('mypage.news-scrap.upload-image') }}', true);
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

        const content = editorInstance.getData();
        if (!content || content.trim() === '') {
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
    min-height: 300px;
    max-height: 500px;
}
.ck-editor__editable:focus {
    box-shadow: none !important;
    border-color: rgb(234 179 8) !important;
}
.ck.ck-toolbar {
    border-color: #e5e7eb !important;
}
.ck.ck-toolbar .ck-toolbar__items {
    flex-wrap: wrap;
}
</style>
@endpush
@endsection
