@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-8">새 게시글 작성</h1>

            <form action="{{ route('board-research.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- 카테고리 -->
                <div class="space-y-2">
                    <label for="mq_category" class="block text-sm font-medium text-gray-700">카테고리</label>
                    <select name="mq_category" 
                            id="mq_category"
                            class="w-full h-12 px-4 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all bg-white cursor-pointer @error('mq_category') border-red-500 @enderror"
                            required>
                        <option value="">카테고리를 선택하세요</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('mq_category') == $category ? 'selected' : '' }}>
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
                           value="{{ old('mq_title') }}"
                           class="w-full h-12 px-4 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('mq_title') border-red-500 @enderror"
                           placeholder="제목을 입력하세요"
                           required>
                    @error('mq_title')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 썸네일 이미지 업로드 -->
                <div class="space-y-2">
                    <label for="mq_thumbnail_image" class="block text-sm font-medium text-gray-700">썸네일 이미지</label>
                    <p class="text-sm text-gray-500 mb-3">게시글 목록에서 표시될 대표 이미지를 선택하세요 (선택사항)</p>

                    <div class="thumbnail-upload-container">
                        <div class="relative">
                            <input type="file"
                                   name="mq_thumbnail_image"
                                   id="mq_thumbnail_image"
                                   accept="image/*"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   onchange="previewThumbnail(this)">
                            <div class="w-full h-12 px-4 border border-gray-300 rounded-xl bg-white flex items-center justify-between cursor-pointer hover:border-yellow-500 transition-all @error('mq_thumbnail_image') border-red-500 @enderror">
                                <span class="thumbnail-label text-text-dark">썸네일 이미지를 선택하세요</span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- 썸네일 미리보기 -->
                        <div id="thumbnailPreview" class="hidden mt-3">
                            <div class="relative inline-block">
                                <img id="thumbnailPreviewImage" src="" alt="썸네일 미리보기" class="w-32 h-24 object-cover rounded-lg border border-gray-300">
                                <button type="button"
                                        onclick="removeThumbnailPreview()"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors text-sm">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500">권장 크기: 800x600px, 최대 2MB</p>
                    @error('mq_thumbnail_image')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 내용 (CKEditor) -->
                <div class="space-y-2">
                    <label for="editor" class="block text-sm font-medium text-gray-700">내용</label>
                    <textarea name="mq_content" id="editor">{{ old('mq_content') }}</textarea>
                    @error('mq_content')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 메인 이미지 업로드 -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-medium text-gray-700">첨부 이미지</label>
                    </div>

                    <div id="fileUploadContainer">
                        <!-- 첫 번째 파일 입력 -->
                        <div class="file-input-group relative mb-3">
                            <input type="file" 
                                   name="mq_image[]" 
                                   accept="image/*"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   onchange="updateFileLabel(this)">
                            <div class="w-full h-12 px-4 border border-gray-300 rounded-xl bg-white flex items-center justify-between cursor-pointer hover:border-yellow-500 transition-all">
                                <span class="file-label text-text-dark">이미지를 선택하세요</span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <p class="text-sm text-text-dark">최대 5개까지 업로드 가능 (파일당 2MB 이하)</p>
                        <button type="button" 
                                class="btn-image-plus px-3 py-1.5 bg-point text-cdark hover:bg-opacity-90 transition-all text-xs font-medium rounded-xl flex items-center gap-1.5" 
                                onclick="addFileInput()">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 버튼 영역 -->
                <div class="flex justify-end gap-3 pt-6">
                    <a href="{{ route('board-research.index') }}" 
                       class="inline-flex items-center justify-center px-6 h-12 border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700">
                        취소
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-6 h-12 bg-point text-cdark rounded-xl hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-dark focus:ring-offset-2 transition-all">
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
            xhr.open('POST', '{{ route('board-research.upload.image') }}', true);
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

    // 파일 입력 추가 기능
    function addFileInput() {
        const container = document.getElementById('fileUploadContainer');
        const inputs = container.getElementsByClassName('file-input-group');
        
        if(inputs.length >= 5) {
            alert('최대 5개까지 업로드 가능합니다.');
            return;
        }

        const newInput = inputs[0].cloneNode(true);
        newInput.querySelector('input').value = '';
        newInput.querySelector('.file-label').textContent = '이미지를 선택하세요';
        
        // 삭제 버튼 추가
        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'absolute -right-8 top-1/2 -translate-y-1/2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors text-sm';
        deleteButton.innerHTML = '×';
        deleteButton.onclick = function() {
            this.parentElement.remove();
        };
        
        newInput.appendChild(deleteButton);
        container.appendChild(newInput);
    }

    // 파일 라벨 업데이트
    function updateFileLabel(input) {
        const label = input.parentElement.querySelector('.file-label');
        label.textContent = input.files[0] ? input.files[0].name : '이미지를 선택하세요';
    }

    // 썸네일 미리보기
    function previewThumbnail(input) {
        const label = document.querySelector('.thumbnail-label');
        const preview = document.getElementById('thumbnailPreview');
        const previewImage = document.getElementById('thumbnailPreviewImage');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // 파일 크기 체크 (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('파일 크기는 2MB 이하로 선택해주세요.');
                input.value = '';
                label.textContent = '썸네일 이미지를 선택하세요';
                return;
            }

            // 이미지 파일 체크
            if (!file.type.match('image.*')) {
                alert('이미지 파일만 선택할 수 있습니다.');
                input.value = '';
                label.textContent = '썸네일 이미지를 선택하세요';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.classList.remove('hidden');
                label.textContent = file.name;
            };
            reader.readAsDataURL(file);
        } else {
            label.textContent = '썸네일 이미지를 선택하세요';
            preview.classList.add('hidden');
        }
    }

    // 썸네일 미리보기 제거
    function removeThumbnailPreview() {
        const input = document.getElementById('mq_thumbnail_image');
        const label = document.querySelector('.thumbnail-label');
        const preview = document.getElementById('thumbnailPreview');

        input.value = '';
        label.textContent = '썸네일 이미지를 선택하세요';
        preview.classList.add('hidden');
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

/* 버튼을 완전한 원형으로 만들 경우 */
.btn-image-plus {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 9999px;
}

.file-input-group {
    position: relative;
    margin-bottom: 1rem;
}

.file-input-group:last-child {
    margin-bottom: 0;
}

/* 삭제 버튼이 있는 입력 그룹에 여백 추가 */
.file-input-group:not(:first-child) {
    padding-right: 0;
    margin-right: 2rem;
}

/* 버튼 스타일 수정 */
.btn-image-plus {
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-image-plus:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
@endpush
@endsection 