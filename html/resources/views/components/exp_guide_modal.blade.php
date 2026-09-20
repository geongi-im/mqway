{{--
    경험치 / 등급 안내 모달

    헤더 프로필 드롭다운에서 연다. 등급은 사이트 전체 활동으로 오르는 값이라
    특정 게시판 화면이 아니라 등급이 실제로 걸려 있는 헤더에서 설명한다.

    @param array $expSummary ExpService::summary() 결과
--}}
@php
    // 'scrap.create' 처럼 키에 점이 있어 config() 의 dot notation 으로는 못 읽는다
    $expActions = config('exp.actions', []);
    $expGrades = config('exp.grades', []);

    // 키 앞부분으로 묶어서 보여준다. 새 행동이 붙어도 여기만 한 줄 추가하면 된다.
    $expGroupNames = ['visit' => '접속', 'scrap' => '뉴스 스크랩'];
    $expGroups = [];
    foreach ($expActions as $key => $action) {
        $prefix = explode('.', $key)[0];
        $groupName = $expGroupNames[$prefix] ?? '기타 활동';
        $expGroups[$groupName][] = $action;
    }
@endphp
<div id="expGuideModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div id="expGuidePanel" class="bg-white w-full max-w-lg max-h-[85vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-[#2D3047]">경험치와 등급 안내</h3>
            <button type="button" id="expGuideClose"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                    title="닫기">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="px-6 py-5 overflow-y-auto space-y-6">

            {{-- 지금 내 상태 --}}
            <div class="p-4 rounded-xl bg-gray-50">
                <div class="flex items-center gap-2 mb-3">
                    @include('components.grade_badge', ['grade' => $expSummary['grade'], 'size' => 'md'])
                    <span class="text-sm font-bold text-[#2D3047]">{{ number_format($expSummary['total']) }} EXP</span>
                </div>
                <div class="h-2 w-full bg-white rounded-full overflow-hidden">
                    <div class="h-full rounded-full"
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

            {{-- 경험치를 받는 방법 --}}
            <div>
                <p class="text-sm font-bold text-[#2D3047] mb-3">이렇게 하면 경험치가 쌓여요</p>
                <div class="space-y-4">
                    @foreach($expGroups as $groupName => $actions)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 mb-2">{{ $groupName }}</p>
                        <ul class="space-y-1.5">
                            @foreach($actions as $action)
                            <li class="flex items-center justify-between gap-3 text-sm">
                                <span class="text-gray-700 min-w-0 truncate">{{ $action['label'] }}</span>
                                <span class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs text-gray-400">하루 최대 {{ $action['daily_limit'] }}회</span>
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-purple-50 text-[#9F5AFF] text-xs font-bold">
                                        +{{ $action['exp'] }}
                                    </span>
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- 등급표 --}}
            <div>
                <p class="text-sm font-bold text-[#2D3047] mb-3">등급표</p>
                <ul class="space-y-1.5">
                    @foreach($expGrades as $grade)
                    <li class="flex items-center justify-between gap-3 px-3 py-2 rounded-xl {{ $grade['code'] === $expSummary['grade']['code'] ? 'bg-gray-50 border border-gray-200' : '' }}">
                        <span class="flex items-center gap-2">
                            @include('components.grade_badge', ['grade' => $grade, 'size' => 'sm'])
                            @if($grade['code'] === $expSummary['grade']['code'])
                            <span class="text-xs font-semibold text-gray-500">지금 등급</span>
                            @endif
                        </span>
                        <span class="text-xs font-semibold text-gray-500">
                            {{ $grade['exp'] > 0 ? '누적 ' . number_format($grade['exp']) . ' EXP' : '가입하면 바로' }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <p class="text-xs text-gray-400 leading-relaxed">
                경험치는 사이트 활동 전반으로 쌓입니다.
                한 번 오른 등급은 내려가지 않고, 글을 지워도 이미 받은 경험치는 그대로 남습니다.
            </p>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            <button type="button" id="expGuideConfirm"
                    class="w-full py-2.5 rounded-xl bg-[#2D3047] text-white text-sm font-bold hover:bg-[#1A1C29] transition-colors">
                확인
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var modal = document.getElementById('expGuideModal');
    var panel = document.getElementById('expGuidePanel');
    if (!modal) {
        return;
    }

    function openGuide() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeGuide() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.getElementById('expGuideOpen').addEventListener('click', openGuide);
    document.getElementById('expGuideClose').addEventListener('click', closeGuide);
    document.getElementById('expGuideConfirm').addEventListener('click', closeGuide);

    // 패널 바깥(어두운 배경)을 누르면 닫는다
    modal.addEventListener('click', function (e) {
        if (!panel.contains(e.target)) {
            closeGuide();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeGuide();
        }
    });
})();
</script>
@endpush
