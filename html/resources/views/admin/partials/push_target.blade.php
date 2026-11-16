{{-- 발송 이력의 대상 설명. @param \App\Models\PushLog $log --}}
@if($log->mq_target === 'rule')
    {{ $log->rule ? $log->rule->mq_name : '(삭제된 규칙)' }}
@elseif($log->mq_target === 'users')
    회원 지정 ({{ count(json_decode((string) $log->mq_target_users, true) ?: []) }}명)
@elseif($log->mq_target === 'marketing_members')
    마케팅 동의 전체
@else
    {{ $log->mq_target }}
@endif
