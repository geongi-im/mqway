@extends('layouts.app')

@section('content')
<!-- ===== Hero Section ===== -->
<section class="relative pt-32 pb-24 overflow-hidden bg-[#3D4148]">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#FF4D4D] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10 text-center animate-slideUp">
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-sm font-medium mb-4 backdrop-blur-md">
            👤 My Page
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight tracking-tight">
            마이페이지
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            나의 프로필과 활동을 관리하세요.<br class="hidden md:block">
            맞춤형 콘텐츠와 서비스를 이용해보세요.
        </p>
    </div>
</section>

<!-- ===== Profile Section ===== -->
<div class="container mx-auto px-4 -mt-10 relative z-20 mb-10 max-w-4xl">
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 animate-slideUp" role="alert">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- 프로필 관리 카드 -->
    <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10 mb-8 animate-slideUp" style="animation-delay: 0.2s;">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4ECDC4] to-[#2AA9A0] flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-[#2D3047]">프로필 관리</h2>
        </div>

        <!-- 프로필 이미지 섹션 -->
        <div class="mb-10 text-center">
            <div class="relative inline-block">
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-100 shadow-lg mx-auto ring-4 ring-[#4ECDC4]/20">
                    @if($user->mq_profile_image)
                        <img src="{{ asset('storage/uploads/profile/' . $user->mq_profile_image) }}" alt="프로필 이미지" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
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
                    <button type="submit" class="bg-[#FF4D4D] text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-all shadow-lg hover:scale-110"
                            onclick="return confirm('프로필 이미지를 삭제하시겠습니까?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </form>
                @endif
            </div>
            @if(!$user->mq_profile_image)
            <p class="text-sm text-gray-400 mt-4 font-medium">프로필 이미지를 업로드해보세요</p>
            @endif
        </div>

        <form method="POST" action="{{ route('mypage.profile.update') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf
            <!-- 프로필 이미지 업로드 -->
            <div class="mb-6">
                <label for="mq_profile_image" class="block text-sm font-semibold text-[#2D3047] mb-2">프로필 이미지</label>
                <input type="file" id="mq_profile_image" name="mq_profile_image" accept=".png, .jpg, .jpeg, .gif" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent bg-gray-50 transition-all text-sm">
                <p class="text-xs text-gray-400 mt-2">JPG, PNG, GIF 파일만 업로드 가능합니다. (최대 2MB)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="mq_user_id" class="block text-sm font-semibold text-[#2D3047] mb-2">사용자 ID</label>
                    <input type="text" id="mq_user_id" value="{{ $user->mq_user_id }}" disabled class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-400 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-2">사용자 ID는 변경할 수 없습니다.</p>
                </div>
                <div>
                    <label for="mq_user_name" class="block text-sm font-semibold text-[#2D3047] mb-2">이름</label>
                    <input type="text" id="mq_user_name" name="mq_user_name" value="{{ $user->mq_user_name }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent transition-all">
                </div>
                <div>
                    <label for="mq_user_email" class="block text-sm font-semibold text-[#2D3047] mb-2">이메일</label>
                    <input type="email" id="mq_user_email" name="mq_user_email" value="{{ $user->mq_user_email }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent transition-all">
                    <p id="email_check_message" class="text-xs mt-2 hidden"></p>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <label for="mq_birthday" class="text-sm font-semibold text-[#2D3047]">생일</label>
                        <span id="age-display" class="text-sm text-[#4ECDC4] font-semibold {{ !$user->mq_birthday ? 'hidden' : '' }}">(만 <span id="calculated-age">{{ $user->mq_birthday ? \Carbon\Carbon::parse($user->mq_birthday)->age : '0' }}</span>세)</span>
                    </div>
                    <input type="date" id="mq_birthday" name="mq_birthday" value="{{ $user->mq_birthday ? $user->mq_birthday->format('Y-m-d') : '' }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4ECDC4] focus:border-transparent transition-all">
                    <p id="birthday-help-text" class="text-xs text-gray-400 mt-2 {{ $user->mq_birthday ? 'hidden' : '' }}">생일을 입력해주세요.</p>
                </div>
            </div>
            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-[#4ECDC4] to-[#2AA9A0] text-white rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    프로필 업데이트
                </button>
            </div>
        </form>
    </div>

    <!-- 비밀번호 변경 (일반 계정만) -->
    @if(!$user->mq_provider)
    <div class="bg-white rounded-2xl shadow-xl mb-8 animate-slideUp overflow-hidden" style="animation-delay: 0.3s;">
        <button type="button" onclick="document.getElementById('password-change-form').classList.toggle('hidden'); document.getElementById('password-change-chevron').classList.toggle('rotate-180');" class="w-full flex items-center justify-between p-8 md:p-10 focus:outline-none hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FF4D4D] to-[#e03e3e] flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[#2D3047]">비밀번호 변경</h2>
            </div>
            <svg id="password-change-chevron" class="w-6 h-6 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div id="password-change-form" class="hidden px-8 md:px-10 pb-8 md:pb-10 pt-2 border-t border-gray-100">
            <form method="POST" action="{{ route('mypage.change-password') }}" class="space-y-6">
                @csrf

            <div>
                <label for="current_password" class="block text-sm font-semibold text-[#2D3047] mb-2">
                    현재 비밀번호 <span class="text-[#FF4D4D]">*</span>
                </label>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       required
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#FF4D4D]/30 focus:border-[#FF4D4D] transition-all @error('current_password') border-red-500 @enderror">
                <p id="current_password_check_message" class="text-xs mt-2 hidden"></p>
                @error('current_password')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="new_password" class="block text-sm font-semibold text-[#2D3047] mb-2">
                        새 비밀번호 <span class="text-[#FF4D4D]">*</span>
                    </label>
                    <input type="password"
                           id="new_password"
                           name="new_password"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#FF4D4D]/30 focus:border-[#FF4D4D] transition-all @error('new_password') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-2">영문, 숫자 필수 포함, 특수문자 사용 가능 8~50자</p>
                    @error('new_password')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-semibold text-[#2D3047] mb-2">
                        새 비밀번호 확인 <span class="text-[#FF4D4D]">*</span>
                    </label>
                    <input type="password"
                           id="new_password_confirmation"
                           name="new_password_confirmation"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#FF4D4D]/30 focus:border-[#FF4D4D] transition-all @error('new_password_confirmation') border-red-500 @enderror">
                    @error('new_password_confirmation')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-[#FF4D4D] to-[#e03e3e] text-white rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    비밀번호 변경
                </button>
            </div>
        </form>
        </div>
    </div>
    @else
    <div class="bg-white/80 backdrop-blur-sm border border-blue-100 rounded-2xl shadow-sm p-6 mb-8 animate-slideUp" style="animation-delay: 0.3s;">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-sm text-blue-700 font-medium">
                {{ ucfirst($user->mq_provider) }} 로그인 계정은 비밀번호 변경이 불가능합니다.
            </p>
        </div>
    </div>
    @endif

    <!-- ===== 메뉴 카드 그리드 ===== -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pb-20 animate-slideUp" style="animation-delay: 0.4s;">
        <!-- 뉴스 스크랩 카드 -->
        <a href="{{ route('mypage.news-scrap.index') }}" class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
            <div class="p-8">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-blue-500/20">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-[#2D3047] mb-2 group-hover:text-blue-600 transition-colors">뉴스 스크랩</h3>
                <p class="text-gray-500 text-sm">관심있는 경제 뉴스 모음</p>
            </div>
            <div class="px-8 py-4 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400 font-medium">바로가기</span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <!-- MQ 맵핑 카드 -->
        <a href="{{ route('mypage.mapping') }}" class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
            <div class="p-8">
                <div class="w-14 h-14 bg-gradient-to-br from-[#4ECDC4] to-[#2AA9A0] rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-[#4ECDC4]/20">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-[#2D3047] mb-2 group-hover:text-[#4ECDC4] transition-colors">MQ 맵핑</h3>
                <p class="text-gray-500 text-sm">나의 꿈의 지도를 만들어보세요</p>
            </div>
            <div class="px-8 py-4 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400 font-medium">바로가기</span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-[#4ECDC4] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <!-- 미션 게시판 카드 -->
        <a href="{{ route('board-mission.index') }}" class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
            <div class="p-8">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-purple-500/20">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-[#2D3047] mb-2 group-hover:text-purple-600 transition-colors">미션 게시판</h3>
                <p class="text-gray-500 text-sm">나의 미션과 성취를 확인하세요</p>
            </div>
            <div class="px-8 py-4 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400 font-medium">바로가기</span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-purple-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <!-- 좋아요 콘텐츠 카드 -->
        <a href="{{ route('mypage.liked-content') }}" class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
            <div class="p-8">
                <div class="w-14 h-14 bg-gradient-to-br from-[#FF4D4D] to-[#e03e3e] rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-[#FF4D4D]/20">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-[#2D3047] mb-2 group-hover:text-[#FF4D4D] transition-colors">좋아요 콘텐츠</h3>
                <p class="text-gray-500 text-sm">내가 좋아한 게시물 모음</p>
            </div>
            <div class="px-8 py-4 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FF4D4D]/10 text-[#FF4D4D]">
                    {{ $likedContent->flatten()->count() }}개
                </span>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-[#FF4D4D] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>
    </div>
</div>

<script>

// 만 나이 계산 함수
function calculateAge(birthday) {
    if (!birthday) return null;
    
    const today = new Date();
    const birthDate = new Date(birthday);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    // 생일이 아직 지나지 않았으면 나이에서 1을 뺌
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

// 나이 표시 및 안내 메시지 업데이트 함수
function updateAgeDisplay(birthday) {
    const ageDisplay = document.getElementById('age-display');
    const calculatedAge = document.getElementById('calculated-age');
    const helpText = document.getElementById('birthday-help-text');
    
    if (!birthday) {
        // 생일이 없으면: 나이 숨김 + 안내 메시지 표시
        ageDisplay.classList.add('hidden');
        helpText.classList.remove('hidden');
        return;
    }
    
    const age = calculateAge(birthday);
    if (age >= 0) {
        // 유효한 생일: 나이 표시 + 안내 메시지 숨김
        calculatedAge.textContent = age;
        ageDisplay.classList.remove('hidden');
        helpText.classList.add('hidden');
    } else {
        // 유효하지 않은 생일: 나이 숨김 + 안내 메시지 표시
        ageDisplay.classList.add('hidden');
        helpText.classList.remove('hidden');
    }
}

// 이메일 중복 확인 상태
let emailChecked = false;
let checkedEmail = '{{ $user->mq_user_email }}'; // 초기 이메일 저장

// 이메일 중복 확인 함수
async function checkEmail() {
    const emailInput = document.getElementById('mq_user_email');
    const email = emailInput.value.trim();
    const messageEl = document.getElementById('email_check_message');
    const submitBtn = document.querySelector('button[type="submit"]');

    // 기본 유효성 검사
    if (!email) {
        showEmailMessage(messageEl, '이메일을 입력해주세요.', 'error');
        emailChecked = false;
        return;
    }

    // 이메일 형식 검증
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        showEmailMessage(messageEl, '올바른 이메일 형식이 아닙니다.', 'error');
        emailChecked = false;
        return;
    }

    try {
        const response = await fetch('{{ route("mypage.check-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ mq_user_email: email })
        });

        const data = await response.json();

        if (data.available) {
            showEmailMessage(messageEl, data.message, 'success');
            emailChecked = true;
            checkedEmail = email;
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        } else {
            showEmailMessage(messageEl, data.message, 'error');
            emailChecked = false;
            if (submitBtn) {
                submitBtn.disabled = true;
            }
        }
    } catch (error) {
        showEmailMessage(messageEl, '중복 확인 중 오류가 발생했습니다.', 'error');
        emailChecked = false;
        if (submitBtn) {
            submitBtn.disabled = true;
        }
    }
}

// 메시지 표시 함수
function showEmailMessage(element, message, type) {
    element.textContent = message;
    element.classList.remove('hidden', 'text-green-600', 'text-red-500');
    if (type === 'success') {
        element.classList.add('text-green-600');
    } else {
        element.classList.add('text-red-500');
    }
}

document.addEventListener('DOMContentLoaded', function() {

    // 이메일 입력 필드 blur 이벤트 리스너 (자동 중복 체크)
    const emailInput = document.getElementById('mq_user_email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const currentValue = this.value.trim();
            // 값이 변경된 경우에만 체크
            if (currentValue && currentValue !== checkedEmail) {
                checkEmail();
            } else if (currentValue === checkedEmail) {
                // 기존 이메일과 동일하면 메시지 숨김
                const messageEl = document.getElementById('email_check_message');
                messageEl.classList.add('hidden');
                emailChecked = true;
                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        });

        // 이메일 입력 필드 변경 시 상태 초기화
        emailInput.addEventListener('input', function() {
            const currentValue = this.value.trim();
            if (currentValue !== checkedEmail) {
                emailChecked = false;
                const messageEl = document.getElementById('email_check_message');
                messageEl.classList.add('hidden');
            }
        });
    }

    // 프로필 이미지 미리보기
    const profileImageInput = document.getElementById('mq_profile_image');
    if (profileImageInput) {
        // 파일 입력 필드 클릭 시 로딩 표시
        profileImageInput.addEventListener('click', function() {
            LoadingManager.show();
            // 파일 선택 다이얼로그가 열릴 때까지 약간의 딜레이 후 로딩 숨김
            setTimeout(() => {
                LoadingManager.hide();
            }, 300);
        });
        
        // 파일 선택 완료 시 로딩 표시 및 미리보기
        profileImageInput.addEventListener('change', function(event) {
            LoadingManager.show();
            
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const profileImageContainer = document.querySelector('.w-32.h-32.rounded-full');
                    profileImageContainer.innerHTML = `<img src="${e.target.result}" alt="프로필 이미지 미리보기" class="w-full h-full object-cover">`;
                    // 이미지 로드 완료 후 로딩 숨김
                    LoadingManager.hide();
                };
                reader.readAsDataURL(file);
            } else {
                // 파일이 선택되지 않은 경우 로딩 숨김
                LoadingManager.hide();
            }
        });
        
        // 파일 선택 다이얼로그 취소 시 로딩 숨김
        window.addEventListener('focus', function() {
            // 포커스가 돌아왔을 때 파일이 선택되지 않았으면 로딩 숨김
            setTimeout(() => {
                if (!profileImageInput.files.length) {
                    LoadingManager.hide();
                }
            }, 100);
        }, { once: true });
    }

    // 생일 입력 필드 이벤트 리스너
    const birthdayInput = document.getElementById('mq_birthday');
    if (birthdayInput) {
        // 페이지 로드 시 생일 상태에 따른 표시 설정
        updateAgeDisplay(birthdayInput.value);

        // 생일 변경 시 나이 재계산
        birthdayInput.addEventListener('change', function(event) {
            updateAgeDisplay(event.target.value);
        });

        // 실시간 입력 시에도 나이 계산 (input 이벤트)
        birthdayInput.addEventListener('input', function(event) {
            if (event.target.value.length === 10) { // YYYY-MM-DD 형식이 완성되면
                updateAgeDisplay(event.target.value);
            } else if (event.target.value.length === 0) { // 값이 지워지면
                updateAgeDisplay('');
            }
        });
    }

    // 비밀번호 변경 폼 유효성 검사
    const passwordForm = document.querySelector('form[action="{{ route('mypage.change-password') }}"]');
    if (passwordForm) {
        const currentPasswordInput = document.getElementById('current_password');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('new_password_confirmation');
        const passwordSubmitBtn = passwordForm.querySelector('button[type="submit"]');

        // 현재 비밀번호 확인 상태
        let currentPasswordValid = false;

        // 현재 비밀번호 AJAX 확인 함수
        async function checkCurrentPassword() {
            const password = currentPasswordInput.value.trim();
            const messageEl = document.getElementById('current_password_check_message');

            if (!password) {
                messageEl.classList.add('hidden');
                currentPasswordValid = false;
                if (passwordSubmitBtn) {
                    passwordSubmitBtn.disabled = true;
                }
                return;
            }

            try {
                const response = await fetch('{{ route("mypage.check-current-password") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ current_password: password })
                });

                const data = await response.json();

                if (data.valid) {
                    messageEl.textContent = data.message;
                    messageEl.classList.remove('hidden', 'text-red-500');
                    messageEl.classList.add('text-green-600');
                    currentPasswordValid = true;
                    if (passwordSubmitBtn) {
                        passwordSubmitBtn.disabled = false;
                    }
                } else {
                    messageEl.textContent = data.message;
                    messageEl.classList.remove('hidden', 'text-green-600');
                    messageEl.classList.add('text-red-500');
                    currentPasswordValid = false;
                    if (passwordSubmitBtn) {
                        passwordSubmitBtn.disabled = true;
                    }
                }
            } catch (error) {
                messageEl.textContent = '비밀번호 확인 중 오류가 발생했습니다.';
                messageEl.classList.remove('hidden', 'text-green-600');
                messageEl.classList.add('text-red-500');
                currentPasswordValid = false;
                if (passwordSubmitBtn) {
                    passwordSubmitBtn.disabled = true;
                }
            }
        }

        // 현재 비밀번호 blur 이벤트
        if (currentPasswordInput) {
            currentPasswordInput.addEventListener('blur', function() {
                checkCurrentPassword();
            });

            // 현재 비밀번호 입력 시 상태 초기화
            currentPasswordInput.addEventListener('input', function() {
                const messageEl = document.getElementById('current_password_check_message');
                messageEl.classList.add('hidden');
                currentPasswordValid = false;
                if (passwordSubmitBtn) {
                    passwordSubmitBtn.disabled = true;
                }
            });
        }

        // 비밀번호 형식 검증 함수
        function validatePasswordFormat(password) {
            // 영문+숫자 필수, 특수문자 선택, 8~50자
            const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d\W_]{8,50}$/;
            return passwordPattern.test(password);
        }

        // 실시간 새 비밀번호 형식 검증
        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', function() {
                const password = this.value;
                if (password.length > 0) {
                    if (password.length < 8) {
                        this.setCustomValidity('비밀번호는 최소 8자 이상이어야 합니다.');
                    } else if (password.length > 50) {
                        this.setCustomValidity('비밀번호는 최대 50자까지 가능합니다.');
                    } else if (!validatePasswordFormat(password)) {
                        this.setCustomValidity('비밀번호는 영문과 숫자를 필수로 포함해야 합니다.');
                    } else {
                        this.setCustomValidity('');
                    }
                } else {
                    this.setCustomValidity('');
                }
            });
        }

        // 실시간 비밀번호 확인 매칭 검증
        if (confirmPasswordInput && newPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                if (this.value && newPasswordInput.value !== this.value) {
                    this.setCustomValidity('비밀번호가 일치하지 않습니다.');
                } else {
                    this.setCustomValidity('');
                }
            });

            // 새 비밀번호 변경 시에도 확인 필드 재검증
            newPasswordInput.addEventListener('input', function() {
                if (confirmPasswordInput.value && this.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity('비밀번호가 일치하지 않습니다.');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            });
        }

        // 폼 제출 시 최종 검증
        passwordForm.addEventListener('submit', function(e) {
            const currentPassword = currentPasswordInput.value.trim();
            const newPassword = newPasswordInput.value.trim();
            const confirmPassword = confirmPasswordInput.value.trim();

            // 현재 비밀번호 확인
            if (!currentPassword) {
                e.preventDefault();
                alert('현재 비밀번호를 입력해주세요.');
                currentPasswordInput.focus();
                return false;
            }

            // 현재 비밀번호 검증 상태 확인
            if (!currentPasswordValid) {
                e.preventDefault();
                alert('현재 비밀번호를 확인해주세요.');
                currentPasswordInput.focus();
                return false;
            }

            // 새 비밀번호 확인
            if (!newPassword) {
                e.preventDefault();
                alert('새 비밀번호를 입력해주세요.');
                newPasswordInput.focus();
                return false;
            }

            // 새 비밀번호 형식 검증
            if (newPassword.length < 8) {
                e.preventDefault();
                alert('비밀번호는 최소 8자 이상이어야 합니다.');
                newPasswordInput.focus();
                return false;
            }

            if (newPassword.length > 50) {
                e.preventDefault();
                alert('비밀번호는 최대 50자까지 가능합니다.');
                newPasswordInput.focus();
                return false;
            }

            if (!validatePasswordFormat(newPassword)) {
                e.preventDefault();
                alert('비밀번호는 영문과 숫자를 필수로 포함해야 합니다.');
                newPasswordInput.focus();
                return false;
            }

            // 비밀번호 확인 매칭
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('비밀번호 확인이 일치하지 않습니다.');
                confirmPasswordInput.focus();
                return false;
            }

            // 현재 비밀번호와 새 비밀번호 동일 여부
            if (currentPassword === newPassword) {
                e.preventDefault();
                alert('새 비밀번호는 현재 비밀번호와 달라야 합니다.');
                newPasswordInput.focus();
                return false;
            }

            // 모든 검증 통과
            return true;
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