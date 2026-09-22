{{--
    게시판 상단의 연속 접속 띠

    이 게시판은 공개 목록이 주인공이라 내 활동 지표는 목록 위 한 줄로만 얇게 깐다.
    등급은 사이트 전체에서 공통으로 쓰는 값이라 헤더 프로필 드롭다운에서만 노출하고
    여기서는 다루지 않는다.

    @param array $expSummary ExpService::summary() 결과
--}}
@php
    // 'visit.streak7' 처럼 키에 일수가 들어 있는 행동에서 연속 접속 보상 구간을 뽑는다
    $streakRewards = [];
    foreach (config('exp.actions', []) as $key => $action) {
        if (preg_match('/^visit\.streak(\d+)$/', $key, $matched)) {
            $streakRewards[(int) $matched[1]] = (int) $action['exp'];
        }
    }
    ksort($streakRewards);

    $streak = (int) $expSummary['streak'];

    // 아직 못 받은 가장 가까운 구간
    $nextReward = null;
    foreach ($streakRewards as $days => $exp) {
        if ($streak < $days) {
            $nextReward = ['days' => $days, 'exp' => $exp, 'left' => $days - $streak];
            break;
        }
    }
    $streakPercent = $nextReward ? min(100, (int) round($streak / $nextReward['days'] * 100)) : 100;
@endphp
<div class="container mx-auto px-4 mb-4 max-w-7xl animate-slideUp" style="animation-delay: 0.22s;">
    <div class="flex items-center gap-3 px-4 py-2.5 bg-white rounded-xl border border-gray-100 shadow-sm">
        <span class="flex-shrink-0 inline-flex items-center gap-1.5 text-sm font-bold text-[#2D3047]">
            <svg class="w-4 h-4 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
            </svg>
            연속 접속 {{ $streak }}일째
        </span>

        <span class="hidden sm:inline text-xs text-gray-400 font-medium flex-shrink-0">
            최장 {{ $expSummary['bestStreak'] }}일
        </span>

        @if($nextReward)
            <span class="hidden md:block flex-1 min-w-0 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <span class="block h-full rounded-full bg-[#4ECDC4] transition-all duration-700"
                      style="width: {{ $streakPercent }}%;"></span>
            </span>
            <span class="ml-auto md:ml-0 text-xs text-gray-500 font-medium flex-shrink-0">
                {{ $nextReward['days'] }}일까지 {{ $nextReward['left'] }}일
                <strong class="text-[#2AA9A0] font-bold">+{{ number_format($nextReward['exp']) }} EXP</strong>
            </span>
        @else
            <span class="ml-auto text-xs text-gray-400 font-medium flex-shrink-0">
                연속 접속 보상을 모두 받았어요
            </span>
        @endif
    </div>
</div>
