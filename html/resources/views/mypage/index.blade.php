@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- 페이지 헤더 (중앙 정렬) -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-point">마이페이지</h1>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif

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
                @if(!$user->mq_profile_image)
                <p class="text-sm text-gray-600 mt-4">프로필 이미지를 업로드해보세요</p>
                @endif
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
                        <p id="email_check_message" class="text-xs mt-1 hidden"></p>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <label for="mq_birthday" class="text-sm font-medium text-secondary">생일</label>
                            <span id="age-display" class="text-sm text-blue-600 font-medium {{ !$user->mq_birthday ? 'hidden' : '' }}">(만 <span id="calculated-age">{{ $user->mq_birthday ? \Carbon\Carbon::parse($user->mq_birthday)->age : '0' }}</span>세)</span>
                        </div>
                        <input type="date" id="mq_birthday" name="mq_birthday" value="{{ $user->mq_birthday ? $user->mq_birthday->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1">
                        <p id="birthday-help-text" class="text-xs text-gray-500 mt-1 {{ $user->mq_birthday ? 'hidden' : '' }}">생일을 입력해주세요.</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-point1 text-white px-6 py-2 rounded-md hover:bg-opacity-90 transition-colors">
                        프로필 업데이트
                    </button>
                </div>
            </form>
        </div>

        <!-- 비밀번호 변경 (일반 계정만) -->
        @if(!$user->mq_provider)
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold text-point mb-6">비밀번호 변경</h2>

            <form method="POST" action="{{ route('mypage.change-password') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="current_password" class="block text-sm font-medium text-secondary mb-2">
                        현재 비밀번호 <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('current_password') border-red-500 @enderror">
                    <p id="current_password_check_message" class="text-xs mt-1 hidden"></p>
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-secondary mb-2">
                            새 비밀번호 <span class="text-red-500">*</span>
                        </label>
                        <input type="password"
                               id="new_password"
                               name="new_password"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('new_password') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">영문, 숫자 필수 포함, 특수문자 사용 가능 8~50자</p>
                        @error('new_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-secondary mb-2">
                            새 비밀번호 확인 <span class="text-red-500">*</span>
                        </label>
                        <input type="password"
                               id="new_password_confirmation"
                               name="new_password_confirmation"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-point1 focus:border-point1 @error('new_password_confirmation') border-red-500 @enderror">
                        @error('new_password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-point1 text-white px-6 py-2 rounded-md hover:bg-opacity-90 transition-colors">
                        비밀번호 변경
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm text-blue-700">
                    {{ ucfirst($user->mq_provider) }} 로그인 계정은 비밀번호 변경이 불가능합니다.
                </p>
            </div>
        </div>
        @endif

        <!-- 메뉴 카드 (PC: 2열, 모바일: 1열) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- 뉴스 스크랩 카드 -->
            <div class="bg-white rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-shadow" onclick="location.href='{{ route('mypage.news-scrap.index') }}'">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-point">뉴스 스크랩</h3>
                        <p class="text-sm text-secondary">관심있는 경제 뉴스 모음</p>
                    </div>
                </div>
            </div>

            <!-- MQ매핑 카드 -->
            <div class="bg-white rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-shadow" onclick="location.href='{{ route('mypage.mapping') }}'">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-point">MQ 매핑</h3>
                        <p class="text-sm text-secondary">나의 꿈의 지도를 만들어보세요</p>
                    </div>
                </div>
            </div>

            <!-- 좋아요 콘텐츠 카드 -->
            <div class="bg-white rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-shadow" onclick="location.href='{{ route('mypage.liked-content') }}'">
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