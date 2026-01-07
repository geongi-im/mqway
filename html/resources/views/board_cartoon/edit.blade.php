@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-8">게시글 수정</h1>

            <form action="{{ route('board-cartoon.update', $post->idx) }}" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="showLoadingManager()">
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

                <div class="space-y-2">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-800">썸네일 자동 설정</p>
                                <p class="text-sm text-blue-600 mt-1">본문에 첨부한 첫 번째 이미지가 자동으로 썸네일로 설정됩니다.</p>
                                @if($post->hasThumbnail())
                                <p class="text-sm text-blue-600 mt-1">현재 썸네일: {{ $post->getThumbnailOriginalName() }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 내용 (CKEditor) -->
                <div class="space-y-2">
                    <label for="editor" class="block text-sm font-medium text-gray-700">내용</label>
                    <textarea name="mq_content" id="editor">{{ old('mq_content', $post->mq_content) }}</textarea>
                    @error('mq_content')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 이미지 업로드 -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">첨부 이미지</label>

                    <div id="fileUploadContainer" class="space-y-3">
                        @if($post->mq_image)
                            @foreach($post->mq_image as $index => $filename)
                                <div class="file-input-group relative mb-3">
                                    <div class="mb-2">
                                        <div class="relative inline-block">
                                            <a href="{{ asset('storage/uploads/board_cartoon/' . $filename) }}"
                                               target="_blank"
                                               class="block hover:opacity-90 transition-opacity">
                                                <img src="{{ asset('storage/uploads/board_cartoon/' . $filename) }}"
                                                     alt="현재 이미지"
                                                     class="w-32 h-24 object-cover rounded-lg shadow-lg cursor-pointer">
                                            </a>
                                            <button type="button"
                                                    onclick="confirmDeleteImage(this, '{{ $filename }}')"
                                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors text-sm">×</button>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1 current-image-name">현재 이미지: {{ $post->getImageOriginalName($index) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="file-input-group relative mb-3" data-index="0">
                                <div class="relative">
                                    <input type="file"
                                           name="mq_image[]"
                                           accept="image/*"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                           onchange="previewAttachment(this)">
                                    <div class="w-full h-12 px-4 border border-gray-300 rounded-xl bg-white flex items-center justify-between cursor-pointer hover:border-yellow-500 transition-all">
                                        <span class="file-label text-text-dark">이미지를 선택하세요</span>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endif
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
                    <a href="{{ route('board-cartoon.show', $post->idx) }}" 
                       class="inline-flex items-center justify-center px-6 h-12 border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700">
                        취소
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-6 h-12 bg-point1 text-cdark rounded-xl hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-dark focus:ring-offset-2 transition-all">
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
        xhr.open('POST', "{{ route('board-cartoon.upload.image') }}", true);
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
        language: 'ko',
        image: {
            toolbar: ['imageTextAlternative', '|', 'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight'],
            resizeOptions: [
                {
                    name: 'imageResize:original',
                    label: '원본 크기',
                    value: null
                },
                {
                    name: 'imageResize:50',
                    label: '50%',
                    value: '50'
                },
                {
                    name: 'imageResize:75',
                    label: '75%',
                    value: '75'
                }
            ]
        }
    })
    .then(editor => {
        // 이미지 클릭 시 새창으로 열기 이벤트 추가
        editor.editing.view.document.on('click', (evt, data) => {
            if (data.domTarget.tagName === 'IMG') {
                const imageUrl = data.domTarget.getAttribute('src');
                if (imageUrl) {
                    window.open(imageUrl, '_blank');
                }
            }
        });
        
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

// 파일 입력 추가 기능
function addFileInput() {
    const container = document.getElementById('fileUploadContainer');
    const inputs = container.getElementsByClassName('file-input-group');

    if(inputs.length >= 5) {
        alert('최대 5개까지 업로드 가능합니다.');
        return;
    }

    // 새로운 파일 입력 그룹 생성
    const newInput = document.createElement('div');
    newInput.className = 'file-input-group relative mb-3';
    newInput.innerHTML = `
        <div class="relative">
            <input type="file"
                   name="mq_image[]"
                   accept="image/*"
                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                   onchange="previewAttachment(this)">
            <div class="w-full h-12 px-4 border border-gray-300 rounded-xl bg-white flex items-center justify-between cursor-pointer hover:border-yellow-500 transition-all">
                <span class="file-label text-text-dark">이미지를 선택하세요</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    `;

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

// 이미지 삭제 확인
function confirmDeleteImage(button, filename) {
    if (confirm('이미지를 삭제하시겠습니까?\n삭제된 이미지는 복구할 수 없습니다.')) {
        deleteImage(button, filename);
    }
}

// 이미지 삭제 AJAX 요청
function deleteImage(button, filename) {
    fetch(`/board-cartoon/delete-image/{{ $post->idx }}/${filename}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const fileInputGroup = button.closest('.file-input-group');

            // 전체 이미지 그룹 제거
            fileInputGroup.remove();
        } else {
            alert('이미지 삭제 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('이미지 삭제 중 오류가 발생했습니다.');
    });
}




// 첨부 이미지 미리보기 (새 이미지 업로드용)
function previewAttachment(input) {
    const group = input.closest('.file-input-group');
    const label = group.querySelector('.file-label');
    let preview = group.querySelector('.attachment-preview');

    if (input.files && input.files[0]) {
        const file = input.files[0];

        // 파일 유효성 검사
        if (!validateImageFile(file)) {
            input.value = '';
            label.textContent = '이미지를 선택하세요';
            if (preview) {
                preview.remove();
            }
            return;
        }

        // 미리보기 컨테이너가 없으면 동적으로 생성
        if (!preview) {
            preview = document.createElement('div');
            preview.className = 'attachment-preview mb-2';
            preview.innerHTML = `
                <div class="relative inline-block">
                    <img src="" alt="첨부 이미지 미리보기" class="w-32 h-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                    <button type="button" onclick="removeAttachmentPreview(this)"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-sm hover:bg-red-600 transition-colors">
                        ×
                    </button>
                </div>
            `;

            // input div 앞에 삽입
            const inputDiv = group.querySelector('.relative');
            if (inputDiv) {
                group.insertBefore(preview, inputDiv);
            } else {
                // fallback: 그룹의 첫 번째 자식으로 삽입
                group.insertAdjacentElement('afterbegin', preview);
            }
        }

        const previewImage = preview.querySelector('img');
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            label.textContent = file.name;
        };
        reader.readAsDataURL(file);
    } else {
        label.textContent = '이미지를 선택하세요';
        if (preview) {
            preview.remove();
        }
    }
}

// 첨부 이미지 미리보기 제거
function removeAttachmentPreview(button) {
    const group = button.closest('.file-input-group');
    const input = group.querySelector('input[type="file"]');
    const label = group.querySelector('.file-label');
    const preview = group.querySelector('.attachment-preview');

    if (preview) {
        // 메모리 정리
        const img = preview.querySelector('img');
        if (img && img.src.startsWith('blob:')) {
            URL.revokeObjectURL(img.src);
        }

        preview.remove();
    }

    input.value = '';
    label.textContent = '이미지를 선택하세요';
}

// 파일 유효성 검사
function validateImageFile(file) {
    const maxSize = 2 * 1024 * 1024; // 2MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // 파일 크기 검사
    if (file.size > maxSize) {
        alert('파일 크기는 2MB 이하로 선택해주세요.');
        return false;
    }

    // 파일 타입 검사
    if (!allowedTypes.includes(file.type)) {
        alert('이미지 파일만 선택할 수 있습니다.');
        return false;
    }

    return true;
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

/* 파일 입력 그룹 간격 조정 */
.file-input-group {
    position: relative;
    margin-bottom: 1rem;
}

.file-input-group:last-child {
    margin-bottom: 0;
}
</style>
@endpush
@endsection