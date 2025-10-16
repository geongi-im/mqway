@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- 페이지 헤더 (중앙 정렬) -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-point">회원가입</h1>
        </div>

        <!-- 회원가입 폼 -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- 약관 동의 섹션 -->
                <div class="border-b pb-6 mb-6">
                    <h2 class="text-xl font-semibold text-point mb-4">약관 동의</h2>

                    <!-- 전체 동의 -->
                    <div class="mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="agree_all" class="w-5 h-5 text-point1 border-gray-300 rounded focus:ring-point1">
                            <span class="ml-3 text-base font-semibold text-secondary">전체 동의</span>
                        </label>
                    </div>

                    <div class="space-y-3 ml-2">
                        <!-- 이용약관 동의 (필수) -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="agree_terms" id="agree_terms" required class="agree-checkbox w-4 h-4 text-point1 border-gray-300 rounded focus:ring-point1">
                                <span class="ml-3 text-sm text-secondary">(필수) 이용약관에 동의합니다</span>
                            </label>
                            <a href="{{ route('service') }}" target="_blank" class="text-sm text-point1 hover:underline">보기</a>
                        </div>
                        @error('agree_terms')
                            <p class="text-red-500 text-xs ml-7">{{ $message }}</p>
                        @enderror

                        <!-- 개인정보 수집 및 이용 동의 (필수) -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="agree_privacy" id="agree_privacy" required class="agree-checkbox w-4 h-4 text-point1 border-gray-300 rounded focus:ring-point1">
                                <span class="ml-3 text-sm text-secondary">(필수) 개인정보 수집 및 이용에 동의합니다</span>
                            </label>
                            <a href="{{ route('privacy') }}" target="_blank" class="text-sm text-point1 hover:underline">보기</a>
                        </div>
                        @error('agree_privacy')
                            <p class="text-red-500 text-xs ml-7">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 회원정보 입력 섹션 -->
                <div>
                    <h2 class="text-xl font-semibold text-point mb-6">회원정보 입력</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 아이디 (필수) -->
                        <div class="md:col-span-2">
                            <label for="mq_user_id" class="block text-sm font-medium text-secondary mb-2">
                                아이디 <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text"
                                       id="mq_user_id"
                                       name="mq_user_id"
                                       value="{{ old('mq_user_id') }}"
                                       required
                                       maxlength="20"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('mq_user_id') border-red-500 @enderror">
                                <button type="button"
                                        id="check_userid_btn"
                                        onclick="checkUserId()"
                                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors whitespace-nowrap">
                                    중복확인
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">영문, 숫자, 특수문자 사용 가능 4~20자</p>
                            <p id="userid_check_message" class="text-xs mt-1 hidden"></p>
                            @error('mq_user_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 비밀번호 (필수) -->
                        <div class="md:col-span-2">
                            <label for="mq_user_password" class="block text-sm font-medium text-secondary mb-2">
                                비밀번호 <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   id="mq_user_password"
                                   name="mq_user_password"
                                   required
                                   maxlength="50"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('mq_user_password') border-red-500 @enderror">
                            <p class="text-xs text-gray-500 mt-1">영문, 숫자 필수 포함, 특수문자 사용 가능 8~50자</p>
                            @error('mq_user_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 비밀번호 확인 (필수) -->
                        <div class="md:col-span-2">
                            <label for="mq_user_password_confirmation" class="block text-sm font-medium text-secondary mb-2">
                                비밀번호 재입력 <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   id="mq_user_password_confirmation"
                                   name="mq_user_password_confirmation"
                                   required
                                   maxlength="50"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1">
                            <p class="text-xs text-gray-500 mt-1">비밀번호를 한 번 더 입력해주세요</p>
                        </div>

                        <!-- 이름 (필수) -->
                        <div>
                            <label for="mq_user_name" class="block text-sm font-medium text-secondary mb-2">
                                이름 <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="mq_user_name"
                                   name="mq_user_name"
                                   value="{{ old('mq_user_name') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('mq_user_name') border-red-500 @enderror">
                            @error('mq_user_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 이메일 (필수) -->
                        <div class="md:col-span-2">
                            <label for="mq_user_email" class="block text-sm font-medium text-secondary mb-2">
                                이메일 <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   id="mq_user_email"
                                   name="mq_user_email"
                                   value="{{ old('mq_user_email') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('mq_user_email') border-red-500 @enderror">
                            <p id="email_check_message" class="text-xs mt-1 hidden"></p>
                            @error('mq_user_email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 생년월일 (선택) -->
                        <div class="md:col-span-2">
                            <label for="mq_birthday" class="block text-sm font-medium text-secondary mb-2">
                                생년월일
                            </label>
                            <input type="date"
                                   id="mq_birthday"
                                   name="mq_birthday"
                                   value="{{ old('mq_birthday') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('mq_birthday') border-red-500 @enderror">
                            @error('mq_birthday')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- 버튼 영역 -->
                <div class="flex gap-4 pt-4">
                    <button type="button"
                            onclick="location.href='{{ route('login') }}'"
                            class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-400 transition-colors font-medium">
                        취소
                    </button>
                    <button type="submit"
                            class="flex-1 bg-point1 text-white px-6 py-3 rounded-md hover:bg-opacity-90 transition-colors font-medium">
                        회원가입
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// 아이디 중복 확인 상태
let userIdChecked = false;
let checkedUserId = '';

// 이메일 중복 확인 상태
let emailChecked = false;
let checkedEmail = '';

// 전체 동의 체크박스 처리
document.getElementById('agree_all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.agree-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// 개별 체크박스 상태 변경 시 전체 동의 체크박스 업데이트
document.querySelectorAll('.agree-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.agree-checkbox');
        const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
        document.getElementById('agree_all').checked = allChecked;
    });
});

// 아이디 입력 필드 변경 시 중복확인 상태 초기화
document.getElementById('mq_user_id').addEventListener('input', function() {
    const currentValue = this.value;
    if (currentValue !== checkedUserId) {
        userIdChecked = false;
        const messageEl = document.getElementById('userid_check_message');
        messageEl.classList.add('hidden');
        messageEl.classList.remove('text-green-600', 'text-red-500');
    }
});

// 이메일 입력 필드 blur 이벤트 리스너 (자동 중복 체크)
document.getElementById('mq_user_email').addEventListener('blur', function() {
    const currentValue = this.value.trim();
    // 값이 있고 변경된 경우에만 체크
    if (currentValue && currentValue !== checkedEmail) {
        checkEmail();
    }
});

// 이메일 입력 필드 변경 시 중복확인 상태 초기화
document.getElementById('mq_user_email').addEventListener('input', function() {
    const currentValue = this.value;
    if (currentValue !== checkedEmail) {
        emailChecked = false;
        const messageEl = document.getElementById('email_check_message');
        messageEl.classList.add('hidden');
        messageEl.classList.remove('text-green-600', 'text-red-500');
    }
});

// 아이디 중복 확인 함수
async function checkUserId() {
    const userIdInput = document.getElementById('mq_user_id');
    const userId = userIdInput.value.trim();
    const messageEl = document.getElementById('userid_check_message');
    const btnEl = document.getElementById('check_userid_btn');

    // 기본 유효성 검사
    if (!userId) {
        showMessage(messageEl, '아이디를 입력해주세요.', 'error');
        return;
    }

    // 아이디 형식 검증 (4~20자)
    if (userId.length < 4 || userId.length > 20) {
        showMessage(messageEl, '아이디는 4~20자여야 합니다.', 'error');
        return;
    }

    // 로딩 상태
    btnEl.disabled = true;
    btnEl.textContent = '확인중...';

    try {
        const response = await fetch('{{ route("register.check-userid") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ mq_user_id: userId })
        });

        const data = await response.json();

        if (data.available) {
            showMessage(messageEl, data.message, 'success');
            userIdChecked = true;
            checkedUserId = userId;
        } else {
            showMessage(messageEl, data.message, 'error');
            userIdChecked = false;
            checkedUserId = '';
        }
    } catch (error) {
        showMessage(messageEl, '중복 확인 중 오류가 발생했습니다.', 'error');
        userIdChecked = false;
        checkedUserId = '';
    } finally {
        btnEl.disabled = false;
        btnEl.textContent = '중복확인';
    }
}

// 메시지 표시 함수
function showMessage(element, message, type) {
    element.textContent = message;
    element.classList.remove('hidden', 'text-green-600', 'text-red-500');
    if (type === 'success') {
        element.classList.add('text-green-600');
    } else {
        element.classList.add('text-red-500');
    }
}

// 이메일 중복 확인 함수
async function checkEmail() {
    const emailInput = document.getElementById('mq_user_email');
    const email = emailInput.value.trim();
    const messageEl = document.getElementById('email_check_message');

    // 기본 유효성 검사
    if (!email) {
        showMessage(messageEl, '이메일을 입력해주세요.', 'error');
        return;
    }

    // 이메일 형식 검증
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        showMessage(messageEl, '올바른 이메일 형식이 아닙니다.', 'error');
        return;
    }

    try {
        const response = await fetch('{{ route("register.check-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ mq_user_email: email })
        });

        const data = await response.json();

        if (data.available) {
            showMessage(messageEl, data.message, 'success');
            emailChecked = true;
            checkedEmail = email;
        } else {
            showMessage(messageEl, data.message, 'error');
            emailChecked = false;
            checkedEmail = '';
        }
    } catch (error) {
        showMessage(messageEl, '중복 확인 중 오류가 발생했습니다.', 'error');
        emailChecked = false;
        checkedEmail = '';
    }
}

// 폼 제출 시 유효성 검사
document.querySelector('form').addEventListener('submit', function(e) {
    const errors = [];

    // 아이디 중복 확인 여부
    const userId = document.getElementById('mq_user_id').value.trim();
    if (!userIdChecked || userId !== checkedUserId) {
        errors.push('아이디 중복확인을 해주세요.');
    }

    // 아이디 형식 검증
    if (userId.length < 4 || userId.length > 20) {
        errors.push('아이디는 4~20자여야 합니다.');
    }

    // 비밀번호 검증
    const password = document.getElementById('mq_user_password').value;
    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d\W_]{8,50}$/;
    if (!passwordPattern.test(password)) {
        errors.push('비밀번호는 영문과 숫자를 필수로 포함해야 합니다.');
    }

    // 비밀번호 확인
    const passwordConfirm = document.getElementById('mq_user_password_confirmation').value;
    if (password !== passwordConfirm) {
        errors.push('비밀번호가 일치하지 않습니다.');
    }

    // 이름 검증
    const userName = document.getElementById('mq_user_name').value.trim();
    if (!userName) {
        errors.push('이름을 입력해주세요.');
    }

    // 이메일 중복 확인 여부
    const email = document.getElementById('mq_user_email').value.trim();
    if (!emailChecked || email !== checkedEmail) {
        errors.push('이메일 중복확인을 해주세요.');
    }

    // 이메일 검증 (필수)
    if (!email) {
        errors.push('이메일을 입력해주세요.');
    } else {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            errors.push('올바른 이메일 형식이 아닙니다.');
        }
    }

    // 약관 동의 검증
    if (!document.getElementById('agree_terms').checked) {
        errors.push('이용약관에 동의해주세요.');
    }
    if (!document.getElementById('agree_privacy').checked) {
        errors.push('개인정보 수집 및 이용에 동의해주세요.');
    }

    // 에러가 있으면 제출 중단
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join('\n'));
        return false;
    }
});
</script>
@endpush
@endsection
