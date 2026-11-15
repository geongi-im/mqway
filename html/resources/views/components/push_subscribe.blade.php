{{--
    앱 푸시(FCM) 설정을 브라우저에 넘기고 public/js/push.js 를 불러온다.
    .env 에 Firebase 웹 설정값과 웹 푸시 공개 키가 모두 있을 때만 동작하고,
    없으면 아무것도 출력하지 않아 프로필 메뉴의 앱 알림 버튼도 숨겨진 채로 남는다.
    여기서 넘기는 값은 모두 공개 설정값과 본인 정보다. (서비스 계정 키는 서버에만 있다)

    알림은 로그인한 회원 중 마케팅 정보 수신에 동의한 회원만 받는다.
    버튼은 로그인해야 보이는 프로필 메뉴 안에 있고, 미동의 회원이 누르면 마이페이지 동의로 안내한다.
--}}
@php
    $pushFirebase = array_filter((array) config('services.firebase.web'), function ($value) {
        return $value !== null && $value !== '';
    });
    $pushVapidKey = config('services.firebase.vapid_key');
    $pushReady = $pushVapidKey
        && !empty($pushFirebase['apiKey'])
        && !empty($pushFirebase['projectId'])
        && !empty($pushFirebase['messagingSenderId'])
        && !empty($pushFirebase['appId']);
    $pushMember = auth()->user();
    // Laravel 6 의 @json 은 여러 줄 배열을 제대로 해석하지 못해 여기서 미리 만든다.
    $pushConfig = [
        'firebase'       => $pushFirebase,
        'vapidKey'       => $pushVapidKey,
        // 한 브라우저에서 다른 회원으로 바꿔 로그인하면 토큰을 다시 등록하기 위한 값
        'memberIdx'      => $pushMember ? (int) $pushMember->idx : null,
        'marketingAgree' => $pushMember ? (bool) $pushMember->mq_marketing_agree : false,
        'loginUrl'       => route('login'),
        'agreeUrl'       => route('mypage.index') . '#agree_marketing',
    ];
@endphp
@if($pushReady)
<script>
    window.MQ_PUSH = {!! json_encode($pushConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) !!};
</script>
<script type="module" src="{{ asset('js/push.js') }}?v={{ @filemtime(public_path('js/push.js')) }}"></script>

{{--
    홈 화면 추가 안내 모달. push.js 가 상황에 맞는 패널 하나만 보여준다.
      ios-install     : 아이폰 브라우저 탭. 홈 화면에 추가해야 알림을 받을 수 있어서 방법을 안내한다.
      ios-old         : iOS 16.4 미만. 웹 알림 자체가 안 되어 업데이트를 안내한다.
      android-install : 안드로이드에서 알림을 켠 직후. 홈 화면 추가 창을 띄우는 버튼을 보여준다.
    사이트가 홈 화면 추가를 대신 할 수는 없다. 안드로이드는 버튼 한 번으로 설치 창까지, 아이폰은 안내까지만 된다.
--}}
<div id="pushGuideModal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm" role="dialog" aria-modal="true">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden" data-push-guide-dialog>
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-[#2D3047]" data-push-guide-title>앱 알림 받기</h3>
            <button type="button" data-push-guide-close
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                    title="닫기">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- 아이폰: 홈 화면에 추가하는 방법 --}}
        <div class="hidden px-5 py-5 space-y-4" data-push-guide-panel="ios-install" data-title="홈 화면에 추가하고 알림 받기">
            <p class="text-sm text-gray-600">아이폰은 MQWAY 를 홈 화면에 추가해야 앱 알림을 받을 수 있어요.</p>

            <p class="hidden text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" data-push-guide-inapp>
                카카오톡, 네이버 같은 앱 안에서는 홈 화면에 추가할 수 없어요.
                메뉴에서 'Safari로 열기' 를 먼저 눌러주세요.
            </p>

            <ol class="space-y-3 text-sm text-gray-700">
                <li class="flex gap-3">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-[#2D3047] text-white text-xs font-bold flex items-center justify-center">1</span>
                    <span>
                        브라우저의 공유 버튼
                        <svg class="inline w-4 h-4 -mt-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-label="공유">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0-12L8 7m4-4l4 4M6 11H5a1 1 0 00-1 1v8a1 1 0 001 1h14a1 1 0 001-1v-8a1 1 0 00-1-1h-1"></path>
                        </svg>
                        을 눌러주세요. 안 보이면 ... 버튼을 먼저 눌러주세요.
                    </span>
                </li>
                <li class="flex gap-3">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-[#2D3047] text-white text-xs font-bold flex items-center justify-center">2</span>
                    <span>목록에서 <strong>'홈 화면에 추가'</strong> 를 누르고 <strong>추가</strong> 를 눌러주세요.</span>
                </li>
                <li class="flex gap-3">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-[#2D3047] text-white text-xs font-bold flex items-center justify-center">3</span>
                    <span>홈 화면에 생긴 <strong>MQWAY 아이콘</strong> 으로 열고, 로그인한 뒤 프로필 메뉴의 <strong>앱 알림</strong> 을 켜주세요.</span>
                </li>
            </ol>

            <p class="text-xs text-gray-400">홈 화면 아이콘으로 열면 로그인을 한 번 더 해야 해요.</p>

            <button type="button" data-push-guide-close
                    class="w-full py-2.5 rounded-xl bg-[#2D3047] text-white text-sm font-bold hover:opacity-90 transition-opacity">
                확인
            </button>
        </div>

        {{-- 아이폰 구버전: 업데이트 안내 --}}
        <div class="hidden px-5 py-5 space-y-4" data-push-guide-panel="ios-old" data-title="iOS 업데이트가 필요해요">
            <p class="text-sm text-gray-600">
                앱 알림은 iOS 16.4 이상에서 받을 수 있어요.
                <span class="hidden" data-push-ios-version-wrap>지금 기기는 <strong data-push-ios-version></strong> 이에요.</span>
            </p>
            <p class="text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                설정 &gt; 일반 &gt; 소프트웨어 업데이트 에서 iOS 를 업데이트한 뒤 다시 시도해 주세요.
            </p>
            <button type="button" data-push-guide-close
                    class="w-full py-2.5 rounded-xl bg-[#2D3047] text-white text-sm font-bold hover:opacity-90 transition-opacity">
                확인
            </button>
        </div>

        {{-- 안드로이드: 알림을 켠 직후 홈 화면 추가 --}}
        <div class="hidden px-5 py-5 space-y-4" data-push-guide-panel="android-install" data-title="알림을 켰어요">
            <p class="text-sm text-gray-600">
                홈 화면에 MQWAY 아이콘을 추가하면 앱처럼 바로 열 수 있어요.
            </p>
            <button type="button" data-push-install
                    class="w-full py-2.5 rounded-xl bg-[#2D3047] text-white text-sm font-bold hover:opacity-90 transition-opacity">
                홈 화면에 추가
            </button>
            <button type="button" data-push-guide-close
                    class="w-full py-2 text-sm text-gray-500 hover:text-gray-700">
                나중에
            </button>
        </div>
    </div>
</div>
@endif
