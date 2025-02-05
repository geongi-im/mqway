@extends('layouts.app')

@section('content')
<main class="flex-grow container mx-auto px-4 py-8 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-dark text-center mb-8">로그인</h2>
        
        <!-- Google 로그인 버튼 -->
        <div class="flex justify-center">
            <button onclick="openGoogleLogin()" 
                    class="flex items-center justify-center gap-2 w-full px-3 sm:px-6 py-3 text-sm sm:text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all whitespace-nowrap">
                <img src="https://www.google.com/favicon.ico" alt="Google" class="w-5 h-5">
                <span class="truncate">Google 계정으로 로그인</span>
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
</script>
@endpush
@endsection 