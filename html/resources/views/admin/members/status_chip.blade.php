{{-- 계정 상태 칩. @param int $status (1 활성, 0 비활성, 2 해지) --}}
@if($status === 1)
    <span class="inline-flex items-center gap-1 text-[12px] font-medium text-emerald-600 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>활성</span>
@elseif($status === 0)
    <span class="inline-flex items-center gap-1 text-[12px] font-medium text-amber-600 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>비활성</span>
@else
    <span class="inline-flex items-center gap-1 text-[12px] font-medium text-gray-400 whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>해지</span>
@endif
