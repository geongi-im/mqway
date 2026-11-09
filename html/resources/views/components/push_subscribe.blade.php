{{--
    앱 푸시(FCM) 설정을 브라우저에 넘기고 public/js/push.js 를 불러온다.
    .env 에 Firebase 웹 설정값과 웹 푸시 공개 키가 모두 있을 때만 동작하고,
    없으면 아무것도 출력하지 않아 헤더의 종 버튼도 숨겨진 채로 남는다.
    여기서 넘기는 값은 모두 공개 설정값과 본인 정보다. (서비스 계정 키는 서버에만 있다)

    알림은 로그인한 회원 중 마케팅 정보 수신에 동의한 회원만 받는다.
    비회원이나 미동의 회원이 종 버튼을 누르면 로그인 / 마이페이지 동의로 안내한다.
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
@endif
