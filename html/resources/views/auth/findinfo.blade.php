@extends('layouts.app')

@section('content')
<main class="flex-grow container mx-auto px-4 py-8 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-dark text-center mb-8">회원정보 찾기</h2>

        <!-- 탭 메뉴 -->
        <div class="flex border-b mb-6">
            <button type="button"
                    onclick="switchTab('find-id')"
                    id="tab-find-id"
                    class="flex-1 py-3 text-center font-medium border-b-2 border-point1 text-point1 transition-colors">
                아이디 찾기
            </button>
            <button type="button"
                    onclick="switchTab('reset-password')"
                    id="tab-reset-password"
                    class="flex-1 py-3 text-center font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors">
                비밀번호 찾기
            </button>
        </div>

        <!-- 아이디 찾기 탭 -->
        <div id="content-find-id" class="tab-content">
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-4">
                    회원가입 시 등록한 이메일 주소와 이름을 입력하시면<br>
                    해당 계정의 아이디를 확인하실 수 있습니다.
                </p>
            </div>

            <form id="find-id-form" class="space-y-6">
                @csrf
                <div>
                    <label for="find_id_name" class="block text-sm font-medium text-secondary mb-2">이름</label>
                    <input type="text"
                           id="find_id_name"
                           name="name"
                           required
                           maxlength="50"
                           placeholder="홍길동"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1">
                </div>

                <div>
                    <label for="find_id_email" class="block text-sm font-medium text-secondary mb-2">이메일</label>
                    <input type="email"
                           id="find_id_email"
                           name="email"
                           required
                           placeholder="example@email.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1">
                    <p id="find-id-error" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <button type="submit"
                        class="w-full bg-point text-white px-6 py-3 rounded-md hover:bg-opacity-90 transition-colors font-medium">
                    아이디 찾기
                </button>
            </form>

            <!-- 결과 표시 영역 -->
            <div id="find-id-result" class="mt-6 p-4 bg-gray-50 rounded-md hidden">
                <p class="text-sm text-gray-600 mb-2">회원님의 아이디는 다음과 같습니다:</p>
                <p class="text-lg font-bold text-point1" id="masked-user-id"></p>
            </div>
        </div>

        <!-- 비밀번호 찾기 탭 -->
        <div id="content-reset-password" class="tab-content hidden">
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-4">
                    <strong>비밀번호 찾기 절차:</strong>
                </p>
                <ol class="text-sm text-gray-600 space-y-2 list-decimal list-inside">
                    <li>아이디와 가입 시 등록한 이메일을 입력합니다.</li>
                    <li>입력한 정보가 일치하면 임시 비밀번호가 생성됩니다.</li>
                    <li>등록하신 이메일로 임시 비밀번호가 발송됩니다.</li>
                    <li>임시 비밀번호로 로그인 후 비밀번호를 변경해주세요.</li>
                </ol>
            </div>

            <form id="reset-password-form" class="space-y-6">
                @csrf
                <div>
                    <label for="reset_user_id" class="block text-sm font-medium text-secondary mb-2">아이디</label>
                    <input type="text"
                           id="reset_user_id"
                           name="user_id"
                           required
                           maxlength="20"
                           placeholder="아이디를 입력하세요"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1">
                </div>

                <div>
                    <label for="reset_email" class="block text-sm font-medium text-secondary mb-2">이메일</label>
                    <input type="email"
                           id="reset_email"
                           name="email"
                           required
                           placeholder="example@email.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1">
                    <p id="reset-password-error" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <button type="submit"
                        class="w-full bg-point text-white px-6 py-3 rounded-md hover:bg-opacity-90 transition-colors font-medium">
                    임시 비밀번호 발급
                </button>
            </form>

            <!-- 성공 메시지 영역 -->
            <div id="reset-password-success" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-md hidden">
                <p class="text-sm text-green-800" id="reset-success-message">
                    임시 비밀번호가 등록하신 이메일로 발송되었습니다.<br>
                    이메일을 확인하신 후 로그인해주세요.
                </p>
            </div>
        </div>

        <!-- 로그인으로 돌아가기 -->
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-sm text-point1 hover:underline">
                로그인으로 돌아가기
            </a>
        </div>
    </div>
</main>

@push('scripts')
<script>
// 탭 전환 함수
function switchTab(tabName) {
    // 모든 탭 버튼 비활성화
    document.getElementById('tab-find-id').classList.remove('border-point1', 'text-point1');
    document.getElementById('tab-find-id').classList.add('border-transparent', 'text-gray-500');
    document.getElementById('tab-reset-password').classList.remove('border-point1', 'text-point1');
    document.getElementById('tab-reset-password').classList.add('border-transparent', 'text-gray-500');

    // 모든 탭 컨텐츠 숨기기
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // 선택된 탭 활성화
    if (tabName === 'find-id') {
        document.getElementById('tab-find-id').classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('tab-find-id').classList.add('border-point1', 'text-point1');
        document.getElementById('content-find-id').classList.remove('hidden');

        // 결과 초기화
        document.getElementById('find-id-result').classList.add('hidden');
        document.getElementById('find-id-error').classList.add('hidden');
        document.getElementById('find-id-form').reset();
    } else if (tabName === 'reset-password') {
        document.getElementById('tab-reset-password').classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('tab-reset-password').classList.add('border-point1', 'text-point1');
        document.getElementById('content-reset-password').classList.remove('hidden');

        // 결과 초기화
        document.getElementById('reset-password-success').classList.add('hidden');
        document.getElementById('reset-password-error').classList.add('hidden');
        document.getElementById('reset-password-form').reset();
    }
}

// 아이디 찾기 폼 제출
document.getElementById('find-id-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const name = document.getElementById('find_id_name').value;
    const email = document.getElementById('find_id_email').value;
    const errorElement = document.getElementById('find-id-error');
    const resultElement = document.getElementById('find-id-result');

    // 이전 결과 숨기기
    errorElement.classList.add('hidden');
    resultElement.classList.add('hidden');

    // 로딩 표시
    LoadingManager.show();

    try {
        const response = await fetch('{{ route('findinfo.find-id') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name, email })
        });

        const data = await response.json();

        if (response.ok) {
            // 성공: 마스킹된 아이디 표시
            document.getElementById('masked-user-id').textContent = data.masked_user_id;
            resultElement.classList.remove('hidden');
        } else {
            // 실패: 에러 메시지 표시
            errorElement.textContent = data.message || '입력하신 정보와 일치하는 계정이 없습니다.';
            errorElement.classList.remove('hidden');
        }
    } catch (error) {
        errorElement.textContent = '오류가 발생했습니다. 다시 시도해주세요.';
        errorElement.classList.remove('hidden');
    } finally {
        // 로딩 숨김
        LoadingManager.hide();
    }
});

// 비밀번호 찾기 폼 제출
document.getElementById('reset-password-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const userId = document.getElementById('reset_user_id').value;
    const email = document.getElementById('reset_email').value;
    const errorElement = document.getElementById('reset-password-error');
    const successElement = document.getElementById('reset-password-success');

    // 이전 결과 숨기기
    errorElement.classList.add('hidden');
    successElement.classList.add('hidden');

    // 로딩 표시
    LoadingManager.show();

    try {
        const response = await fetch('{{ route('findinfo.reset-password') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ user_id: userId, email })
        });

        const data = await response.json();

        if (response.ok) {
            // 성공: 성공 메시지 표시
            document.getElementById('reset-success-message').textContent = data.message;
            successElement.classList.remove('hidden');
            this.reset();
        } else {
            // 실패: 에러 메시지 표시
            errorElement.textContent = data.message || '입력한 정보와 일치하는 계정이 없습니다.';
            errorElement.classList.remove('hidden');
        }
    } catch (error) {
        errorElement.textContent = '오류가 발생했습니다. 다시 시도해주세요.';
        errorElement.classList.remove('hidden');
    } finally {
        // 로딩 숨김
        LoadingManager.hide();
    }
});
</script>
@endpush
@endsection
