{{--
    회원 등급 칩

    화면에 보이는 성장 지표는 이 등급 하나다.
    mq_member.mq_level 은 권한 값이므로 사용자에게 노출하지 않는다.

    @param array $grade config/exp.php 의 grades 한 줄
    @param string $size 'sm' | 'md'
--}}
@php
    $size = $size ?? 'sm';
    $padding = $size === 'md' ? 'px-3 py-1 text-sm' : 'px-2 py-0.5 text-xs';
@endphp
<span class="inline-flex items-center gap-1 rounded-full font-bold whitespace-nowrap {{ $padding }}"
      style="background-color: {{ $grade['color'] }}; color: {{ $grade['text'] }};">
    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.161c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.363 1.118l1.286 3.958c.3.921-.755 1.688-1.539 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.366 2.446c-.784.57-1.838-.197-1.539-1.118l1.286-3.958a1 1 0 00-.363-1.118L2.063 9.385c-.783-.57-.38-1.81.588-1.81h4.16a1 1 0 00.951-.69l1.287-3.958z"></path>
    </svg>
    {{ $grade['name'] }}
</span>
