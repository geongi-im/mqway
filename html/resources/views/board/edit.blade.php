@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-8">게시글 수정</h1>

        <form action="{{ route('board.update', $post->idx) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 제목 -->
            <div>
                <label for="mq_title" class="block text-sm font-medium text-gray-700 mb-1">제목</label>
                <input type="text" 
                       name="mq_title" 
                       id="mq_title" 
                       value="{{ old('mq_title', $post->mq_title) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark @error('mq_title') border-red-500 @enderror"
                       required>
                @error('mq_title')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 카테고리 -->
            <div>
                <label for="mq_category" class="block text-sm font-medium text-gray-700 mb-1">카테고리</label>
                <select name="mq_category" 
                        id="mq_category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark @error('mq_category') border-red-500 @enderror"
                        required>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ old('mq_category', $post->mq_category) == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                @error('mq_category')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 내용 -->
            <div>
                <label for="mq_content" class="block text-sm font-medium text-gray-700 mb-1">내용</label>
                <textarea name="mq_content" 
                          id="mq_content" 
                          rows="10"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark @error('mq_content') border-red-500 @enderror"
                          required>{{ old('mq_content', $post->mq_content) }}</textarea>
                @error('mq_content')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 현재 이미지 -->
            @if($post->mq_image)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">현재 이미지</label>
                    <img src="{{ Storage::url($post->mq_image) }}" 
                         alt="현재 이미지"
                         class="w-64 h-64 object-cover rounded-lg shadow-lg">
                </div>
            @endif

            <!-- 이미지 업로드 -->
            <div>
                <label for="mq_image" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $post->mq_image ? '이미지 변경' : '이미지 업로드' }}
                </label>
                <input type="file" 
                       name="mq_image" 
                       id="mq_image"
                       accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-dark @error('mq_image') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">최대 2MB까지 업로드 가능합니다.</p>
                @error('mq_image')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 버튼 -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('board.show', $post->idx) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                    취소
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-dark text-white rounded-lg hover:bg-opacity-90 transition-all">
                    수정하기
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 