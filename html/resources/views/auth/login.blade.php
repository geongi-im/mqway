@extends('layouts.app')

@section('content')
<main class="flex-grow container mx-auto px-4 py-8 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-dark text-center mb-8">로그인</h2>

        <!-- 일반 로그인 폼 -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- 아이디 입력 -->
            <div>
                <label for="mq_user_id" class="block text-sm font-medium text-secondary mb-2">아이디</label>
                <input type="text"
                       id="mq_user_id"
                       name="mq_user_id"
                       value="{{ old('mq_user_id') }}"
                       required
                       autofocus
                       maxlength="20"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('mq_user_id') border-red-500 @enderror">
                @error('mq_user_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 비밀번호 입력 -->
            <div>
                <label for="mq_user_password" class="block text-sm font-medium text-secondary mb-2">비밀번호</label>
                <input type="password"
                       id="mq_user_password"
                       name="mq_user_password"
                       required
                       maxlength="50"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('mq_user_password') border-red-500 @enderror">
                @error('mq_user_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 로그인 버튼 -->
            <button type="submit"
                    class="w-full bg-point text-white px-6 py-3 rounded-md hover:bg-opacity-90 transition-colors font-medium">
                로그인
            </button>

            <!-- 회원가입 링크 -->
            <div class="text-center">
                <a href="{{ route('register') }}" class="text-sm text-point hover:underline">
                    회원가입
                </a>
                <span class="text-gray-400 mx-2">|</span>
                <a href="{{ route('findinfo') }}" class="text-sm text-gray-600 hover:underline">
                    회원정보 찾기
                </a>
            </div>
        </form>

        <!-- 구분선 -->
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">또는</span>
            </div>
        </div>

        <!-- 소셜 로그인 버튼 -->
        <div class="space-y-3">
            <!-- Google 로그인 버튼 -->
            <button onclick="openGoogleLogin()"
                    class="flex items-center justify-center gap-2 w-full px-3 sm:px-6 py-3 text-sm sm:text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all whitespace-nowrap">
                <img src="/images/logo/login_google_logo.png" alt="Google" class="w-5 h-5">
                <span class="truncate">Google 계정으로 로그인</span>
            </button>

            <!-- Kakao 로그인 버튼 -->
            <button onclick="openKakaoLogin()"
                    class="flex items-center justify-center gap-2 w-full px-3 sm:px-6 py-3 text-sm sm:text-base font-medium text-gray-700 bg-[#FEE500] border border-[#FEE500] rounded-lg hover:bg-[#FDD835] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FEE500] transition-all whitespace-nowrap">
                <img src="/images/logo/login_kakao_logo.png" alt="Kakao" class="w-5 h-5">
                <span class="truncate">카카오 계정으로 로그인</span>
            </button>

            <!-- Naver 로그인 버튼 -->
            <button onclick="openNaverLogin()"
                    class="flex items-center justify-center gap-2 w-full px-3 sm:px-6 py-3 text-sm sm:text-base font-medium text-white bg-[#03C75A] border border-[#03C75A] rounded-lg hover:bg-[#02B350] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#03C75A] transition-all whitespace-nowrap">
                <img src="/images/logo/login_naver_logo.png" alt="Naver" class="w-5 h-5">
                <span class="truncate">네이버 계정으로 로그인</span>
            </button>
        </div>
    </div>
</main>

@push('scripts')
<script>
function openGoogleLogin() {
    // 팝업 창 크기와 위치 계산
    const width = 500;
    const height = 600;
    const left = (window.screen.width / 2) - (width / 2);
    const top = (window.screen.height / 2) - (height / 2);

    // 팝업 창 열기
    const popup = window.open(
        '{{ route('login.google') }}',
        'GoogleLogin',
        `width=${width},height=${height},left=${left},top=${top},toolbar=no,menubar=no,location=no,status=no`
    );
}

function openKakaoLogin() {
    // 팝업 창 크기와 위치 계산
    const width = 500;
    const height = 600;
    const left = (window.screen.width / 2) - (width / 2);
    const top = (window.screen.height / 2) - (height / 2);

    // 팝업 창 열기
    const popup = window.open(
        '{{ route('login.kakao') }}',
        'KakaoLogin',
        `width=${width},height=${height},left=${left},top=${top},toolbar=no,menubar=no,location=no,status=no`
    );
}

function openNaverLogin() {
    // 팝업 창 크기와 위치 계산
    const width = 500;
    const height = 600;
    const left = (window.screen.width / 2) - (width / 2);
    const top = (window.screen.height / 2) - (height / 2);

    // 팝업 창 열기
    const popup = window.open(
        '{{ route('login.naver') }}',
        'NaverLogin',
        `width=${width},height=${height},left=${left},top=${top},toolbar=no,menubar=no,location=no,status=no`
    );
}
</script>
@endpush
@endsection 