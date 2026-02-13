@extends('layouts.app')

@section('content')
<!-- ===== Header Section ===== -->
<div class="relative bg-[#3D4148] py-12 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-3xl font-bold text-white mb-2">새 게시글 작성</h1>
        <p class="text-gray-400 text-sm">유용한 경제 지식과 정보를 공유해보세요.</p>
    </div>
</div>

<div class="min-h-screen bg-gray-50 -mt-8 pb-20 relative z-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 md:p-10">
                <form action="{{ route('board-content.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- 카테고리 -->
                    <div class="space-y-2">
                        <label for="mq_category" class="text-sm font-semibold text-[#2D3047] block">카테고리 <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="mq_category" 
                                    id="mq_category"
                                    class="w-full h-12 pl-4 pr-10 border border-gray-200 rounded-xl focus:outline-none focus:border-[#9F5AFF] focus:ring-2 focus:ring-[#9F5AFF]/20 transition-all bg-gray-50 cursor-pointer appearance-none font-medium text-gray-700 @error('mq_category') border-red-500 @enderror"
                                    required>
                                <option value="">카테고리를 선택하세요</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ old('mq_category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('mq_category')
                            <p class="text-sm text-red-500 flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 제목 -->
                    <div class="space-y-2">
                        <label for="mq_title" class="text-sm font-semibold text-[#2D3047] block">제목 <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="mq_title" 
                               id="mq_title" 
                               value="{{ old('mq_title') }}"
                               class="w-full h-12 px-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#9F5AFF] focus:ring-2 focus:ring-[#9F5AFF]/20 transition-all bg-gray-50 placeholder-gray-400 font-medium @error('mq_title') border-red-500 @enderror"
                               placeholder="제목을 입력하세요"
                               required>
                        @error('mq_title')
                            <p class="text-sm text-red-500 flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 썸네일 이미지 업로드 -->
                    <div class="space-y-4 p-5 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <div>
                            <label for="mq_thumbnail_image" class="text-sm font-semibold text-[#2D3047] flex items-center mb-1">
                                <svg class="w-5 h-5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                썸네일 이미지
                            </label>
                            <p class="text-xs text-gray-500">게시글 목록에 표시될 이미지입니다. (선택사항, 권장: 800x600px)</p>
                        </div>

                        <div class="thumbnail-upload-container">
                            <div class="relative group">
                                <input type="file"
                                       name="mq_thumbnail_image"
                                       id="mq_thumbnail_image"
                                       accept="image/*"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       onchange="previewThumbnail(this)">
                                <div class="w-full h-14 px-4 border border-gray-200 rounded-xl bg-white flex items-center justify-between cursor-pointer group-hover:border-[#9F5AFF] group-hover:ring-1 group-hover:ring-[#9F5AFF] transition-all shadow-sm">
                                    <span class="thumbnail-label text-gray-500 text-sm">이미지를 선택하거나 드래그하세요</span>
                                    <span class="bg-[#2D3047] text-white text-xs px-3 py-1.5 rounded-lg">파일 선택</span>
                                </div>
                            </div>

                            <!-- 썸네일 미리보기 -->
                            <div id="thumbnailPreview" class="hidden mt-4">
                                <div class="relative inline-block group">
                                    <img id="thumbnailPreviewImage" src="" alt="썸네일 미리보기" class="w-40 h-32 object-cover rounded-lg shadow-md">
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                        <button type="button"
                                                onclick="removeThumbnailPreview()"
                                                class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors transform hover:scale-110">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('mq_thumbnail_image')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 내용 (CKEditor) -->
                    <div class="space-y-2">
                        <label for="editor" class="text-sm font-semibold text-[#2D3047] block">내용</label>
                        <div class="prose max-w-none">
                            <textarea name="mq_content" id="editor">{{ old('mq_content') }}</textarea>
                        </div>
                        @error('mq_content')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 첨부 이미지 업로드 (다중) -->
                    <div class="space-y-4 pt-6 border-t border-gray-100">
                        <div class="flex justify-between items-end">
                            <div>
                                <label class="text-sm font-semibold text-[#2D3047] block mb-1">추가 이미지 첨부</label>
                                <p class="text-xs text-gray-400">최대 5개, 파일당 2MB 이하</p>
                            </div>
                            <button type="button" 
                                    class="text-sm px-3 py-1.5 bg-[#9F5AFF]/10 text-[#7B2CBF] hover:bg-[#9F5AFF]/20 rounded-lg transition-colors font-medium flex items-center gap-1" 
                                    onclick="addFileInput()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                이미지 추가
                            </button>
                        </div>

                        <div id="fileUploadContainer" class="space-y-3">
                            <!-- 첫 번째 파일 입력 -->
                            <div class="file-input-group animate-fadeIn" data-index="0">
                                <div class="relative group">
                                    <input type="file"
                                           name="mq_image[]"
                                           accept="image/*"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                           onchange="previewAttachment(this)">
                                    <div class="w-full h-12 px-4 border border-gray-200 rounded-xl bg-gray-50 flex items-center justify-between cursor-pointer group-hover:border-[#9F5AFF] transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center text-gray-500 attachment-icon">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <span class="file-label text-gray-500 text-sm">이미지를 선택하세요</span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-[#9F5AFF] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </div>
                                
                                <!-- 미리보기 -->
                                <div class="attachment-preview hidden mt-2 ml-1">
                                    <div class="relative inline-block group">
                                        <img src="" alt="미리보기" class="w-20 h-20 object-cover rounded-lg shadow-sm border border-gray-100">
                                        <button type="button" onclick="removeAttachmentPreview(this)"
                                                class="absolute -top-1 -right-1 w-5 h-5 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs hover:bg-black transition-colors shadow-sm opacity-0 group-hover:opacity-100">
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 버튼 영역 -->
                    <div class="flex items-center justify-between pt-8 border-t border-gray-100 mt-8">
                        <button type="button" 
                                onclick="if(confirm('작성 중인 내용이 저장되지 않고 사라집니다.\n목록 페이지로 돌아가시겠습니까?')) { location.href='{{ route('board-content.index') }}'; }"
                                class="inline-flex items-center justify-center px-6 h-12 text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            취소
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-8 h-12 bg-[#2D3047] text-white rounded-xl hover:bg-[#3D4148] transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-bold text-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            게시글 등록
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
            xhr.open('POST', '{{ route('board-content.upload.image') }}', true);
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

        const newIndex = inputs.length;
        const newInput = inputs[0].cloneNode(true);

        // 인덱스 업데이트
        newInput.setAttribute('data-index', newIndex);
        
        // 초기화
        newInput.querySelector('input').value = '';
        newInput.querySelector('.file-label').textContent = '이미지를 선택하세요';
        
        // 이미지 아이콘 초기화
        const iconContainer = newInput.querySelector('.attachment-icon');
        if(iconContainer) iconContainer.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
        
        const preview = newInput.querySelector('.attachment-preview');
        if(preview) preview.classList.add('hidden');

        // 삭제 버튼이 이미 있는지 확인하고 없으면 추가
        if (!newInput.querySelector('.delete-btn')) {
            const deleteWrapper = document.createElement('div');
            deleteWrapper.className = 'absolute -right-8 top-1/2 -translate-y-1/2 delete-btn';
            deleteWrapper.innerHTML = `
                <button type="button" class="w-6 h-6 bg-red-100 text-red-500 rounded-full flex items-center justify-center hover:bg-red-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            deleteWrapper.onclick = function() {
                const previewImg = this.parentElement.querySelector('.attachment-preview img');
                if (previewImg && previewImg.src.startsWith('blob:')) {
                    URL.revokeObjectURL(previewImg.src);
                }
                this.parentElement.remove();
            };
            newInput.appendChild(deleteWrapper);
        }

        container.appendChild(newInput);
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
                label.textContent = '이미지를 선택하거나 드래그하세요';
                return;
            }

            // 이미지 파일 체크
            if (!file.type.match('image.*')) {
                alert('이미지 파일만 선택할 수 있습니다.');
                input.value = '';
                label.textContent = '이미지를 선택하거나 드래그하세요';
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
            label.textContent = '이미지를 선택하거나 드래그하세요';
            preview.classList.add('hidden');
        }
    }

    // 썸네일 미리보기 제거
    function removeThumbnailPreview() {
        const input = document.getElementById('mq_thumbnail_image');
        const label = document.querySelector('.thumbnail-label');
        const preview = document.getElementById('thumbnailPreview');

        input.value = '';
        label.textContent = '이미지를 선택하거나 드래그하세요';
        preview.classList.add('hidden');
    }

    // 첨부 이미지 미리보기
    function previewAttachment(input) {
        const group = input.closest('.file-input-group');
        const label = group.querySelector('.file-label');
        const preview = group.querySelector('.attachment-preview');
        const previewImage = preview.querySelector('img');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // 파일 유효성 검사
            if (!validateImageFile(file)) {
                input.value = '';
                label.textContent = '이미지를 선택하세요';
                preview.classList.add('hidden');
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
            label.textContent = '이미지를 선택하세요';
            preview.classList.add('hidden');
        }
    }

    // 첨부 이미지 미리보기 제거
    function removeAttachmentPreview(button) {
        const group = button.closest('.file-input-group');
        const input = group.querySelector('input[type="file"]');
        const label = group.querySelector('.file-label');
        const preview = group.querySelector('.attachment-preview');

        // 메모리 정리
        const img = preview.querySelector('img');
        if (img.src.startsWith('blob:')) {
            URL.revokeObjectURL(img.src);
        }

        input.value = '';
        label.textContent = '이미지를 선택하세요';
        preview.classList.add('hidden');
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
    border-radius: 0 0 0.75rem 0.75rem !important;
}
.ck-editor__editable:focus {
    box-shadow: none !important;
    border-color: #9F5AFF !important;
}
.ck.ck-toolbar {
    border-color: #e5e7eb !important;
    border-radius: 0.75rem 0.75rem 0 0 !important;
    background: #f9fafb !important;
}
.ck.ck-toolbar .ck-toolbar__items {
    flex-wrap: wrap;
}

/* 애니메이션 */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out forwards;
}
</style>
@endpush
@endsection 