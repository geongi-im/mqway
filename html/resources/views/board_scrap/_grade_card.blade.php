{{--
    내 스크랩 탭 상단의 등급 카드

    왼쪽은 지금 등급과 다음 등급까지 남은 경험치, 오른쪽은 연속 접속 기록이다.
    등급은 사이트 전체 활동으로 오르기 때문에 문구도 스크랩에 한정하지 않는다.

    @param array $expSummary ExpService::summary() 결과
--}}
@php
    // 'scrap.create' 처럼 키에 점이 있어 config() 의 dot notation 으로는 못 읽는다
    $expActions = config('exp.actions', []);
@endphp
<div class="container mx-auto px-4 mb-6 max-w-7xl animate-slideUp" style="animation-delay: 0.22s;">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-5 md:gap-8">

            {{-- 등급 + 진행도 --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    @include('components.grade_badge', ['grade' => $expSummary['grade'], 'size' => 'md'])
                    <span class="text-sm font-bold text-[#2D3047]">{{ number_format($expSummary['total']) }} EXP</span>
                </div>

                <div class="h-2.5 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-700"
                         style="width: {{ $expSummary['percent'] }}%; background-color: {{ $expSummary['grade']['color'] }};"></div>
                </div>

                <p class="text-xs text-gray-500 mt-2">
                    @if($expSummary['next'])
                        <strong class="text-[#2D3047]">{{ $expSummary['next']['name'] }}</strong> 까지
                        <strong class="text-[#2D3047]">{{ number_format($expSummary['remain']) }} EXP</strong> 남았어요.
                    @else
                        최고 등급입니다. 기록을 계속 쌓아보세요.
                    @endif
                </p>
            </div>

            {{-- 연속 접속 --}}
            <div class="flex items-center gap-4 md:border-l md:border-gray-100 md:pl-8 shrink-0">
                <div>
                    <p class="text-xs text-gray-400 font-medium mb-0.5">연속 접속</p>
                    <p class="text-2xl font-bold text-[#2D3047] leading-none">
                        {{ $expSummary['streak'] }}<span class="text-sm font-semibold text-gray-400 ml-0.5">일</span>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium mb-0.5">최장 기록</p>
                    <p class="text-2xl font-bold text-gray-300 leading-none">
                        {{ $expSummary['bestStreak'] }}<span class="text-sm font-semibold ml-0.5">일</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- 스크랩으로 받을 수 있는 경험치 안내 --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-gray-400">
            <span class="font-semibold text-gray-500">스크랩으로 받는 경험치</span>
            <span>작성 +{{ $expActions['scrap.create']['exp'] }}</span>
            <span>AI 분석 첨부 +{{ $expActions['scrap.ai']['exp'] }}</span>
            <span>공개 공유 +{{ $expActions['scrap.public']['exp'] }}</span>
            <span>좋아요 받기 +{{ $expActions['scrap.liked']['exp'] }}</span>
        </div>
    </div>
</div>
