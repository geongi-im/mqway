{{--
    앱 푸시 알림 받기/끄기 종 버튼. 헤더에서 PC용(우측)과 모바일용(햄버거 옆) 두 곳에 넣는다.
    웹 푸시를 쓸 수 있는 환경에서만 public/js/push.js 가 hidden 을 떼어 보이게 하므로,
    PC/모바일 구분은 이 버튼이 아니라 감싸는 요소에서 한다.
--}}
<button type="button" data-push-toggle aria-pressed="false" title="새 소식 알림 받기"
        class="hidden p-1.5 rounded-full text-cdark hover:text-cgray hover:bg-white/10 transition-colors">
    {{-- 알림 꺼짐 --}}
    <svg data-push-icon-off class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
    </svg>
    {{-- 알림 받는 중 --}}
    <svg data-push-icon-on class="w-6 h-6 hidden text-point1" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
    </svg>
</button>
