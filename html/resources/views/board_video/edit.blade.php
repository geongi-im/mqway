@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-dark">쉽게 보는 경제 - 게시글 수정</h1>
    </div>

    <form action="{{ route('board-video.update', $post->idx) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
        @csrf
        @method('PUT')
        
        <!-- 유효성 검사 오류 표시 -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- 제목 입력 -->
        <div class="mb-4">
            <label for="mq_title" class="block mb-2 text-sm font-medium text-gray-700">제목</label>
            <input type="text" 
                   id="mq_title" 
                   name="mq_title" 
                   value="{{ old('mq_title', $post->mq_title) }}" 
                   class="w-full h-12 px-4 rounded-lg border border-gray-300 focus:ring-2 focus:ring-point" 
                   required>
        </div>
        
        <!-- 카테고리 선택 -->
        <div class="mb-4">
            <label for="mq_category" class="block mb-2 text-sm font-medium text-gray-700">카테고리</label>
            <select id="mq_category" 
                    name="mq_category" 
                    class="w-full h-12 px-4 rounded-lg border border-gray-300 focus:ring-2 focus:ring-point" 
                    required>
                <option value="">카테고리 선택</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ old('mq_category', $post->mq_category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- 비디오 URL 입력 -->
        <div class="mb-4">
            <label for="mq_video_url" class="block mb-2 text-sm font-medium text-gray-700">비디오 URL</label>
            <input type="url" 
                   id="mq_video_url" 
                   name="mq_video_url" 
                   value="{{ old('mq_video_url', $post->mq_video_url) }}" 
                   class="w-full h-12 px-4 rounded-lg border border-gray-300 focus:ring-2 focus:ring-point" 
                   placeholder="YouTube 또는 다른 비디오 플랫폼의 URL을 입력하세요"
                   required>
            <p class="mt-1 text-xs text-gray-500">YouTube, Vimeo 등의 비디오 URL을 입력해주세요.</p>
        </div>
        
        <!-- 내용 입력 (CKEditor) -->
        <div class="mb-4">
            <label for="mq_content" class="block mb-2 text-sm font-medium text-gray-700">내용</label>
            <textarea id="mq_content" 
                      name="mq_content" 
                      rows="10" 
                      class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-point">{{ old('mq_content', $post->mq_content) }}</textarea>
        </div>
        
        <!-- 이미지 업로드 -->
        <div class="mb-4">
            <label for="mq_image" class="block mb-2 text-sm font-medium text-gray-700">대표 이미지 (최대 5개)</label>
            <input type="file" 
                   id="mq_image" 
                   name="mq_image[]" 
                   class="w-full h-12 px-4 rounded-lg border border-gray-300 focus:ring-2 focus:ring-point" 
                   accept="image/*" 
                   multiple>
            <p class="mt-1 text-xs text-gray-500">※ 이미지를 첨부하지 않으면 기존 이미지가 유지됩니다.</p>
        </div>
        
        <!-- 현재 이미지 표시 -->
        @if(is_array($post->mq_image) && count($post->mq_image) > 0)
            <div class="mb-4">
                <h3 class="block mb-2 text-sm font-medium text-gray-700">현재 이미지</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($post->mq_image as $index => $image)
                        <div class="relative group">
                            <img src="{{ $image }}" alt="이미지 {{ $index+1 }}" class="h-32 w-full object-cover rounded">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded">
                                <button type="button" 
                                        onclick="deleteImage('{{ basename($image) }}')" 
                                        class="text-white bg-red-600 hover:bg-red-700 rounded-full p-1.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- 버튼 영역 -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('board-video.show', $post->idx) }}" 
               class="inline-flex items-center justify-center h-12 px-6 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                취소
            </a>
            <button type="submit" 
                    class="inline-flex items-center justify-center h-12 px-6 bg-point text-dark rounded-lg hover:bg-opacity-90 transition-all">
                수정하기
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#mq_content'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'],
            simpleUpload: {
                uploadUrl: '{{ route('board-video.upload.image') }}',
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        })
        .catch(error => {
            console.error(error);
        });
        
    // 이미지 삭제 함수
    function deleteImage(filename) {
        if(confirm('이 이미지를 삭제하시겠습니까?')) {
            fetch('{{ route('board-video.delete-image', ['idx' => $post->idx, 'filename' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', filename), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // 이미지 요소 제거
                    const imageElement = document.querySelector(`img[alt="이미지 ${filename}"]`);
                    if(imageElement) {
                        imageElement.closest('.relative').remove();
                    } else {
                        location.reload(); // 요소를 찾을 수 없으면 페이지 새로고침
                    }
                    alert('이미지가 삭제되었습니다.');
                } else {
                    alert(data.message || '이미지 삭제에 실패했습니다.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('이미지 삭제 중 오류가 발생했습니다.');
            });
        }
    }
</script>
@endpush
@endsection 