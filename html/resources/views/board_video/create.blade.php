@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-dark">쉽게 보는 경제 - 게시글 작성</h1>
    </div>

    <form action="{{ route('board-video.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
        @csrf
        
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
                   value="{{ old('mq_title') }}" 
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
                    <option value="{{ $category }}" {{ old('mq_category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- 비디오 URL 입력 -->
        <div class="mb-4">
            <label for="mq_video_url" class="block mb-2 text-sm font-medium text-gray-700">비디오 URL</label>
            <input type="url" 
                   id="mq_video_url" 
                   name="mq_video_url" 
                   value="{{ old('mq_video_url') }}" 
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
                      class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-point">{{ old('mq_content') }}</textarea>
        </div>
        
        <!-- 이미지 업로드 -->
        <div class="mb-4">
            <label for="mq_image" class="block mb-2 text-sm font-medium text-gray-700">대표 이미지</label>
            <input type="file" 
                   id="mq_image" 
                   name="mq_image[]" 
                   class="w-full h-12 px-4 rounded-lg border border-gray-300 focus:ring-2 focus:ring-point" 
                   accept="image/*" 
                   multiple>
            <p class="mt-1 text-xs text-gray-500">※ 이미지를 첨부하지 않으면 비디오 썸네일이 대신 사용됩니다.</p>
        </div>
        
        <!-- 버튼 영역 -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('board-video.index') }}" 
               class="inline-flex items-center justify-center h-12 px-6 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                취소
            </a>
            <button type="submit" 
                    class="inline-flex items-center justify-center h-12 px-6 bg-point text-dark rounded-lg hover:bg-opacity-90 transition-all">
                등록하기
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
</script>
@endpush
@endsection 