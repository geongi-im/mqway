{{--
    내 스크랩 탭 상단의 연속 접속 카드

    이 화면에서는 연속 접속 기록을 주인공으로 둔다. 등급과 경험치 설명은
    헤더 프로필 드롭다운의 '안내' 에서 하므로 여기서는 지금 등급만 곁들인다.

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
<div class="container mx-auto px-4 mb-6 max-w-7xl animate-slideUp" style="animation-delay: 0.22s;">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-5 md:gap-8">

            {{-- 연속 접속 기록 --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-end gap-6 mb-4">
                    <div>
                        <p class="text-xs text-gray-400 font-medium mb-1">연속 접속</p>
                        <p class="text-4xl font-bold text-[#2D3047] leading-none">
                            {{ $streak }}<span class="text-base font-semibold text-gray-400 ml-1">일째</span>
                        </p>
                    </div>
                    <div class="pb-0.5">
                        <p class="text-xs text-gray-400 font-medium mb-1">최장 기록</p>
                        <p class="text-2xl font-bold text-gray-300 leading-none">
                            {{ $expSummary['bestStreak'] }}<span class="text-sm font-semibold ml-0.5">일</span>
                        </p>
                    </div>
                </div>

                @if($nextReward)
                    <div class="h-2.5 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-[#4ECDC4] transition-all duration-700"
                             style="width: {{ $streakPercent }}%;"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <strong class="text-[#2D3047]">{{ $nextReward['days'] }}일 연속</strong> 까지
                        <strong class="text-[#2D3047]">{{ $nextReward['left'] }}일</strong> 남았어요.
                        달성하면 <strong class="text-[#2D3047]">+{{ number_format($nextReward['exp']) }} EXP</strong>
                    </p>
                @else
                    <div class="h-2.5 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full w-full rounded-full bg-[#4ECDC4]"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        연속 접속 보상을 모두 받았어요. 기록을 계속 이어가 보세요.
                    </p>
                @endif
            </div>

            {{-- 지금 등급 (설명은 헤더의 안내에서) --}}
            <div class="md:border-l md:border-gray-100 md:pl-8 shrink-0">
                <p class="text-xs text-gray-400 font-medium mb-1.5">내 등급</p>
                <div class="flex items-center gap-2">
                    @include('components.grade_badge', ['grade' => $expSummary['grade'], 'size' => 'md'])
                    <span class="text-sm font-bold text-[#2D3047]">{{ number_format($expSummary['total']) }} EXP</span>
                </div>
            </div>
        </div>
    </div>
</div>
