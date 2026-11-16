{{-- 앱 푸시관리 탭. @param string $active rules|logs --}}
<div class="flex items-center gap-6 border-b border-gray-200 mb-5 text-[14px] text-gray-500">
    <a href="{{ route('admin.push.index') }}" class="tab pb-3 border-b-2 border-transparent -mb-px {{ $active === 'rules' ? 'active' : '' }}">자동 발송 규칙</a>
    <a href="{{ route('admin.push.logs') }}" class="tab pb-3 border-b-2 border-transparent -mb-px {{ $active === 'logs' ? 'active' : '' }}">발송 이력</a>
    @if($active === 'rules')
        <button type="button" data-rule-new class="ml-auto mb-2 flex items-center gap-1.5 bg-point1 hover:bg-[#ff3333] text-white text-[13px] font-bold px-3.5 py-2 rounded-lg">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>새 규칙
        </button>
    @endif
</div>
