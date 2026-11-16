{{-- 발송 이력의 출처 칩. @param \App\Models\PushLog $log --}}
@php
    $sourceChip = [
        'rule'   => ['자동 규칙', 'bg-[#4ECDC4]/15 text-[#1f8f88]'],
        'api'    => ['외부 API', 'bg-indigo-50 text-indigo-600'],
        'manual' => ['수동 발송', 'bg-gray-100 text-gray-600'],
    ][$log->mq_source] ?? [$log->mq_source, 'bg-gray-100 text-gray-600'];
@endphp
<span class="px-2 py-0.5 rounded text-[11px] font-semibold whitespace-nowrap {{ $sourceChip[1] }}">{{ $sourceChip[0] }}</span>
