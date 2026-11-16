{{-- 권한 레벨 칩. @param int $level --}}
@if($level >= 10)
    <span class="px-1.5 py-0.5 rounded text-[11px] font-bold bg-[#FF4D4D] text-white whitespace-nowrap">Lv.{{ $level }} 최고관리자</span>
@elseif($level === 9)
    <span class="px-1.5 py-0.5 rounded text-[11px] font-bold bg-[#3D4148] text-white whitespace-nowrap">Lv.9 매니저</span>
@else
    <span class="px-1.5 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600 whitespace-nowrap">Lv.{{ $level }}</span>
@endif
