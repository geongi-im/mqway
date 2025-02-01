@extends('layouts.app')

@section('content')
<main class="flex-grow container mx-auto px-4 py-8 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-dark text-center mb-8">로그인</h2>
        
        <!-- Google 로그인 버튼 -->
        <div class="flex justify-center">
            <div id="g_id_onload"
                 data-client_id="{{ config('services.google.client_id') }}"
                 data-callback="handleCredentialResponse"
                 data-auto_prompt="false"
                 data-auto_select="false"
                 data-prompt_parent_id="g_id_onload">
            </div>
            <div class="g_id_signin"
                 data-type="standard"
                 data-size="large"
                 data-theme="outline"
                 data-text="sign_in_with"
                 data-shape="rectangular"
                 data-logo_alignment="left">
            </div>
        </div>
    </div>
</main>

<!-- Google Identity Services 라이브러리 -->
<script src="https://accounts.google.com/gsi/client" async defer></script>

@push('scripts')
<script>
function handleCredentialResponse(response) {
    // 로딩 시작
    LoadingManager.show();
    
    // 서버로 토큰 전송
    fetch('{{ config('services.google.redirect') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            credential: response.credential
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ url('/') }}';
        } else {
            LoadingManager.hide(); // 에러 발생 시 로딩 숨김
            alert('로그인 처리 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        LoadingManager.hide(); // 에러 발생 시 로딩 숨김
        console.error('Error:', error);
        alert('로그인 처리 중 오류가 발생했습니다.');
    });
}

// 콘솔 에러 메시지 숨기기
const originalError = console.error;
console.error = function(...args) {
    if (args[0]?.includes('Cross-Origin-Opener-Policy')) {
        return;
    }
    originalError.apply(console, args);
};
</script>
@endpush
@endsection 