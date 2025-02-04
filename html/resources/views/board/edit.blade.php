@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-8">게시글 수정</h1>

            <form action="{{ route('board.update', $post->idx) }}" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="showLoadingManager()">
                @csrf
                @method('PUT')

                <!-- 카테고리 -->
                <div class="space-y-2">
                    <label for="mq_category" class="block text-sm font-medium text-gray-700">카테고리</label>
                    <select name="mq_category" 
                            id="mq_category"
                            class="w-full h-12 px-4 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all bg-white cursor-pointer @error('mq_category') border-red-500 @enderror"
                            required>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('mq_category', $post->mq_category) == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    @error('mq_category')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 제목 -->
                <div class="space-y-2">
                    <label for="mq_title" class="block text-sm font-medium text-gray-700">제목</label>
                    <input type="text" 
                           name="mq_title" 
                           id="mq_title" 
                           value="{{ old('mq_title', $post->mq_title) }}"
                           class="w-full h-12 px-4 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('mq_title') border-red-500 @enderror"
                           placeholder="제목을 입력하세요"
                           required>
                    @error('mq_title')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 내용 (CKEditor) -->
                <div class="space-y-2">
                    <label for="editor" class="block text-sm font-medium text-gray-700">내용</label>
                    <textarea name="mq_content" id="editor">{{ old('mq_content', $post->mq_content) }}</textarea>
                    @error('mq_content')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 메인 이미지 업로드 -->
                <div class="space-y-2">
                    <label for="mq_image" class="block text-sm font-medium text-gray-700">메인 이미지</label>
                    @if($post->mq_image)
                        <div class="mb-4">
                            <img src="{{ Storage::url($post->mq_image) }}" 
                                 alt="현재 이미지"
                                 class="w-64 h-64 object-cover rounded-lg shadow-lg">
                        </div>
                    @endif
                    <div class="relative">
                        <input type="file" 
                               name="mq_image" 
                               id="mq_image"
                               accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               onchange="updateFileLabel(this)">
                        <div class="w-full h-12 px-4 border border-gray-300 rounded-xl bg-white flex items-center justify-between cursor-pointer hover:border-yellow-500 transition-all">
                            <span id="fileLabel" class="text-text-dark">{{ $post->mq_image ? '이미지 변경하기' : '이미지를 선택하세요' }}</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="mt-2 text-sm text-text-dark">최대 2MB까지 업로드 가능합니다.</p>
                    </div>
                    @error('mq_image')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 버튼 영역 -->
                <div class="flex justify-end gap-3 pt-6">
                    <a href="{{ route('board.show', $post->idx) }}" 
                       class="inline-flex items-center justify-center px-6 h-12 border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700">
                        취소
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-6 h-12 bg-point text-cdark rounded-xl hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-dark focus:ring-offset-2 transition-all">
                        수정하기
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
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
        xhr.open('POST', '{{ route('board.upload.image') }}', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        xhr.responseType = 'json';
    }

    _initListeners(resolve, reject, file) {
        const xhr = this.xhr;
        const loader = this.loader;
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
                '|', 'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed',
                '|', 'fontColor', 'fontBackgroundColor'
            ]
        },
        language: 'ko'
    })
    .then(editor => {
        console.log('Editor was initialized');
    })
    .catch(error => {
        console.error('Error:', error);
    });

function showLoadingManager() {
    if (typeof LoadingManager !== 'undefined') {
        LoadingManager.show();
    }
    return true;
}

// 파일 선택 시 라벨 업데이트
function updateFileLabel(input) {
    const label = document.getElementById('fileLabel');
    label.textContent = input.files[0] ? input.files[0].name : '이미지를 선택하세요';
}
</script>
<style>
.ck-editor__editable {
    min-height: 400px;
    max-height: 600px;
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