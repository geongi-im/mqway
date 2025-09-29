@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- 페이지 헤더 (중앙 정렬) -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-point">마이페이지</h1>
        </div>

        <!-- 프로필 관리 (항상 노출) -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold text-point mb-6">프로필 관리</h2>

            <!-- 프로필 이미지 섹션 -->
            <div class="mb-8 text-center">
                <div class="relative inline-block">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200 mx-auto">
                        @if($user->mq_profile_image)
                            <img src="{{ asset('storage/uploads/profile/' . $user->mq_profile_image) }}" alt="프로필 이미지" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    @if($user->mq_profile_image)
                    <form method="POST" action="{{ route('mypage.profile.image.delete') }}" class="inline-block absolute -top-2 -right-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors"
                                onclick="return confirm('프로필 이미지를 삭제하시겠습니까?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
                <p class="text-sm text-gray-600 mt-4">프로필 이미지를 업로드해보세요</p>
            </div>

            <form method="POST" action="{{ route('mypage.profile.update') }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                <!-- 프로필 이미지 업로드 -->
                <div class="mb-6">
                    <label for="mq_profile_image" class="block text-sm font-medium text-secondary mb-2">프로필 이미지</label>
                    <input type="file" id="mq_profile_image" name="mq_profile_image" accept=".png, .jpg, .jpeg, .gif" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1">
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF 파일만 업로드 가능합니다. (최대 2MB)</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mq_user_id" class="block text-sm font-medium text-secondary mb-2">사용자 ID</label>
                        <input type="text" id="mq_user_id" value="{{ $user->mq_user_id }}" disabled class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500">
                        <p class="text-xs text-gray-500 mt-1">사용자 ID는 변경할 수 없습니다.</p>
                    </div>
                    <div>
                        <label for="mq_user_name" class="block text-sm font-medium text-secondary mb-2">이름</label>
                        <input type="text" id="mq_user_name" name="mq_user_name" value="{{ $user->mq_user_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1">
                    </div>
                    <div>
                        <label for="mq_user_email" class="block text-sm font-medium text-secondary mb-2">이메일</label>
                        <input type="email" id="mq_user_email" name="mq_user_email" value="{{ $user->mq_user_email }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1">
                    </div>
                    <div>
                        <label for="mq_level" class="block text-sm font-medium text-secondary mb-2">레벨</label>
                        <input type="text" id="mq_level" value="Level {{ $user->mq_level ?? '1' }}" disabled class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-point1 text-white px-6 py-2 rounded-md hover:bg-opacity-90 transition-colors">
                        프로필 업데이트
                    </button>
                </div>
            </form>
        </div>

        <!-- 메뉴 카드 (PC: 2열, 모바일: 1열) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- 뉴스 스크랩 카드 -->
            <div class="bg-white rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-shadow" onclick="location.href='{{ route('mypage.news-scrap') }}'">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-point">뉴스 스크랩</h3>
                        <p class="text-sm text-secondary">관심있는 경제 뉴스 모음</p>
                        <span class="text-xs text-blue-600 font-medium">{{ count($newsScrap) }}개 저장됨</span>
                    </div>
                </div>
            </div>

            <!-- 버킷리스트 카드 -->
            <div class="bg-white rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-shadow" onclick="location.href='{{ route('mypage.bucket-list') }}'">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-point">버킷리스트</h3>
                        <p class="text-sm text-secondary">나의 경제 목표와 계획</p>
                        <span class="text-xs text-green-600 font-medium">{{ collect($bucketList)->where('status', 'completed')->count() }}/{{ count($bucketList) }} 완료</span>
                    </div>
                </div>
            </div>

            <!-- 좋아요 콘텐츠 카드 -->
            <div class="bg-white rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-shadow" onclick="toggleSection('liked-content')">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-point">좋아요 콘텐츠</h3>
                        <p class="text-sm text-secondary">내가 좋아한 게시물 모음</p>
                        <span class="text-xs text-red-600 font-medium">{{ $likedContent->flatten()->count() }}개</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 좋아요 콘텐츠 섹션 (토글) -->
        <div id="liked-content" class="section-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-point">좋아요한 콘텐츠</h2>
                    <button onclick="toggleSection('liked-content')" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if($likedContent->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500">아직 좋아요한 콘텐츠가 없습니다.</p>
                    <p class="text-sm text-gray-400 mt-2">관심있는 콘텐츠에 좋아요를 눌러보세요!</p>
                </div>
                @else
                @foreach($likedContent as $boardName => $items)
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-point mb-4 border-b border-gray-200 pb-2">
                        {{ $boardName === 'mq_board_content' ? '콘텐츠 게시판' :
                           ($boardName === 'mq_board_research' ? '리서치 게시판' :
                           ($boardName === 'mq_board_portfolio' ? '포트폴리오 게시판' : $boardName)) }}
                        <span class="text-sm text-gray-500 ml-2">({{ count($items) }}개)</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($items as $item)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm font-medium text-point">{{ $item->mq_board_idx }}번 게시글</span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->mq_reg_date)->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex justify-end">
                                <button class="text-point3 text-sm hover:underline">좋아요 취소</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// 섹션 토글 기능
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    const allSections = document.querySelectorAll('.section-content');

    if (section) {
        if (section.classList.contains('hidden')) {
            // 모든 섹션 숨기기
            allSections.forEach(s => s.classList.add('hidden'));
            // 선택된 섹션 보이기
            section.classList.remove('hidden');
        } else {
            // 이미 열려있는 섹션이면 닫기
            section.classList.add('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {

    // 프로필 이미지 미리보기
    const profileImageInput = document.getElementById('mq_profile_image');
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const profileImageContainer = document.querySelector('.w-32.h-32.rounded-full');
                    profileImageContainer.innerHTML = `<img src="${e.target.result}" alt="프로필 이미지 미리보기" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 알림 메시지 전환 효과 */
.alert {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.alert:hover {
    opacity: 0.95;
}
</style>
@endsection