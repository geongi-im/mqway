{{-- 발송 이력의 상태 칩. @param \App\Models\PushLog $log --}}
@php
    $statusChip = [
        'sent'    => ['발송 완료', 'bg-emerald-50 text-emerald-700'],
        'failed'  => ['실패', 'bg-red-50 text-red-600'],
        'pending' => ['발송 중', 'bg-amber-50 text-amber-700'],
    ][$log->mq_status] ?? [$log->mq_status, 'bg-gray-100 text-gray-600'];
@endphp
<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold whitespace-nowrap {{ $statusChip[1] }}" @if($log->mq_error) title="{{ $log->mq_error }}" @endif>{{ $statusChip[0] }}</span>
