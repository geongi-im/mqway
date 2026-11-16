@extends('admin.layouts.app')

@section('title', '앱 푸시관리')

@php
    $ruleData = $rules->map(function ($r) {
        return [
            'idx'            => $r->idx,
            'mq_name'        => $r->mq_name,
            'mq_target'      => $r->mq_target,
            'mq_target_days' => $r->mq_target_days,
            'mq_frequency'   => $r->mq_frequency,
            'mq_weekdays'    => $r->weekdayList(),
            'mq_month_day'   => $r->mq_month_day,
            'mq_send_time'   => $r->mq_send_time,
            'mq_title'       => $r->mq_title,
            'mq_body'        => $r->mq_body,
            'mq_link'        => $r->mq_link,
            'mq_status'      => $r->mq_status,
            'audience'       => $r->audience,
            'update_url'     => route('admin.push.rules.update', $r->idx),
            'delete_url'     => route('admin.push.rules.destroy', $r->idx),
        ];
    })->values();

    // 저장하다 검사에 걸려 돌아왔으면 입력하던 값으로 모달을 다시 연다.
    $oldRule = null;
    if ($errors->any() && old('_rule_form')) {
        $oldRule = [
            'idx'            => old('_rule_idx') ? (int) old('_rule_idx') : null,
            'mq_name'        => old('mq_name'),
            'mq_target'      => old('mq_target'),
            'mq_target_days' => old('mq_target_days'),
            'mq_frequency'   => old('mq_frequency'),
            'mq_weekdays'    => array_map('intval', (array) old('mq_weekdays', [])),
            'mq_month_day'   => old('mq_month_day'),
            'mq_send_time'   => old('mq_send_time'),
            'mq_title'       => old('mq_title'),
            'mq_body'        => old('mq_body'),
            'mq_link'        => old('mq_link'),
            'mq_status'      => (int) old('mq_status', 0),
        ];
    }
@endphp

@section('content')
@include('admin.push.tabs', ['active' => 'rules'])

<div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-[13px] whitespace-nowrap">
        <thead class="bg-surface text-gray-500 text-[12px]">
            <tr>
                <th class="text-center w-16 py-3">사용</th>
                <th class="text-left">규칙</th>
                <th class="text-left">발송 주기</th>
                <th class="text-right">현재 대상</th>
                <th class="text-left pl-6">마지막 발송</th>
                <th class="text-left">다음 발송</th>
                <th class="w-20"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rules as $rule)
                <tr class="hover:bg-surface">
                    <td class="text-center py-4">
                        <form method="POST" action="{{ route('admin.push.rules.status', $rule->idx) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="mq_status" value="{{ $rule->isOn() ? 0 : 1 }}">
                            <button class="toggle {{ $rule->isOn() ? 'on' : '' }}" title="{{ $rule->isOn() ? '끄기' : '켜기' }}" aria-label="{{ $rule->isOn() ? '끄기' : '켜기' }}"></button>
                        </form>
                    </td>
                    <td class="py-4 whitespace-normal min-w-[200px]">
                        <div class="font-semibold">{{ $rule->mq_name }}</div>
                        <div class="text-[12px] text-gray-400 mt-0.5">{{ $rule->targetLabel() }}</div>
                    </td>
                    <td class="whitespace-nowrap">
                        <span class="inline-flex items-center gap-1 text-gray-600">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2.5 20.5 6 17 9.5M3.5 11V9.5A3.5 3.5 0 0 1 7 6h13.5M7 21.5 3.5 18 7 14.5M20.5 13v1.5A3.5 3.5 0 0 1 17 18H3.5"/></svg>
                            {{ $rule->scheduleLabel() }}
                        </span>
                    </td>
                    <td class="text-right font-outfit font-bold whitespace-nowrap">{{ number_format($rule->audience) }}<span class="text-gray-400 font-normal text-[12px] ml-0.5">명</span></td>
                    <td class="pl-6 text-[12px] text-gray-500 font-outfit whitespace-nowrap">
                        @if($rule->mq_last_run_date)
                            {{ $rule->mq_last_run_date->format('Y-m-d H:i') }}
                            @if($rule->lastLog)
                                / 기기 {{ number_format($rule->lastLog->mq_success_count) }}대
                            @endif
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="text-[12px] font-outfit whitespace-nowrap {{ $rule->next_run ? 'text-ink' : 'text-gray-300' }}">
                        {{ $rule->next_run ? $rule->next_run->format('Y-m-d H:i') : '꺼짐' }}
                    </td>
                    <td class="pr-4 text-right">
                        <button type="button" data-rule-edit="{{ $rule->idx }}" class="text-[12px] border border-gray-200 rounded-md px-2.5 py-1 hover:bg-gray-50">편집</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-12 text-center text-gray-400">아직 규칙이 없습니다. 오른쪽 위 '새 규칙' 으로 만들어 보세요.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-3 text-[12px] text-gray-400 border-t border-gray-100 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5 4 5.5v6c0 5 3.4 8.6 8 10 4.6-1.4 8-5 8-10v-6z"/><path d="m8.8 12 2.2 2.2 4.4-4.4"/></svg>
        마케팅 수신에 동의하고 알림을 켠 활성 회원에게만 보냅니다. 21:00-08:00 에는 발송 시각을 고를 수 없습니다.
    </div>
</div>

{{-- 규칙 편집 모달 --}}
<div id="ruleModal" class="fixed inset-0 bg-black/40 z-50 hidden items-start justify-center overflow-y-auto p-4 lg:p-10">
    <form id="ruleForm" method="POST" action="{{ route('admin.push.rules.store') }}" class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl">
        @csrf
        <input type="hidden" name="_method" value="POST" id="rMethod">
        <input type="hidden" name="_rule_form" value="1">
        <input type="hidden" name="_rule_idx" value="" id="rIdx">
        <input type="hidden" name="mq_target_days" value="" id="rDays">
        <input type="hidden" name="mq_status" value="1" id="rStatus">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex-1">
                <div class="text-[12px] text-gray-400">앱 푸시관리 / 자동 발송 규칙</div>
                <input name="mq_name" id="rName" maxlength="100" required class="text-lg font-bold outline-none w-full" placeholder="규칙 이름 (예: 주간 스크랩 독려)">
            </div>
            <button type="button" data-rule-close class="text-gray-400 hover:text-ink" aria-label="닫기">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>

        {{-- 검사에 걸려 모달이 다시 열렸을 때: 페이지 상단 알림은 모달 뒤에 가려지므로 여기에도 보여준다 --}}
        @if($errors->any() && old('_rule_form'))
            <div id="rErrors" class="mx-6 mt-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-[13px] px-4 py-3">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="grid lg:grid-cols-[1fr_280px]">
            <div class="p-6 space-y-6 border-r border-gray-100">
                {{-- 1. 누구에게 --}}
                <div>
                    <div class="flex items-center gap-2 mb-3"><span class="w-6 h-6 rounded-full bg-point text-white text-[12px] font-bold flex items-center justify-center">1</span><span class="font-bold">누구에게</span></div>
                    <div class="space-y-2 text-[13px]">
                        @foreach($targets as $key => $label)
                            <label class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2.5 cursor-pointer has-[:checked]:border-point" data-target-row>
                                <input type="radio" name="mq_target" value="{{ $key }}" class="accent-point1">
                                <span class="flex items-center flex-wrap">
                                    @if(strpos($label, '{N}') !== false)
                                        @php list($before, $after) = explode('{N}', $label, 2); @endphp
                                        {{ $before }}<input type="number" min="1" max="365" data-days="{{ $key }}" class="inp !w-16 !py-0.5 mx-1 text-center" value="{{ $key === 'no_scrap' ? 7 : 30 }}">{{ $after }}
                                    @else
                                        {{ $label }}
                                    @endif
                                </span>
                            </label>
                        @endforeach
                        <div class="text-[12px] text-gray-400 pt-1">마케팅 수신 동의 + 알림을 켠 활성 회원 중에서 고릅니다. 기간 조건은 가입한 지 그 기간이 지난 회원만 봅니다.</div>
                    </div>
                </div>

                {{-- 2. 언제 --}}
                <div>
                    <div class="flex items-center gap-2 mb-3"><span class="w-6 h-6 rounded-full bg-point text-white text-[12px] font-bold flex items-center justify-center">2</span><span class="font-bold">언제</span></div>
                    <div class="flex flex-wrap items-center gap-2 text-[13px]">
                        <select name="mq_frequency" id="rFreq" class="sel">
                            <option value="daily">매일</option>
                            <option value="weekly">매주</option>
                            <option value="monthly">매월</option>
                        </select>
                        <div id="rWeekdays" class="flex gap-1">
                            @foreach($weekdays as $num => $label)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="mq_weekdays[]" value="{{ $num }}" class="peer sr-only">
                                    <span class="w-8 h-8 rounded-full border border-gray-200 text-[12px] flex items-center justify-center peer-checked:bg-point peer-checked:text-white peer-checked:border-point">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div id="rMonthDay" class="items-center gap-1">
                            <select name="mq_month_day" class="sel">
                                @for($d = 1; $d <= 31; $d++)
                                    <option value="{{ $d }}">{{ $d === 31 ? '말일' : $d . '일' }}</option>
                                @endfor
                            </select>
                        </div>
                        <select name="mq_send_time" id="rTime" class="sel">
                            @foreach($sendTimes as $time)
                                <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-[12px] text-gray-400 mt-2">발송 시각이 되면 그때 대상 회원을 다시 골라서 보냅니다.</div>
                </div>

                {{-- 3. 무엇을 --}}
                <div>
                    <div class="flex items-center gap-2 mb-3"><span class="w-6 h-6 rounded-full bg-point text-white text-[12px] font-bold flex items-center justify-center">3</span><span class="font-bold">무엇을</span></div>
                    <div class="space-y-3">
                        <div><div class="text-[12px] text-gray-500 mb-1">제목 <span class="text-gray-400">(제목과 본문에 {{ $placeholder }} 을 넣으면 받는 회원 이름으로 바뀝니다)</span></div><input name="mq_title" id="rTitle" maxlength="100" required class="inp"></div>
                        <div><div class="text-[12px] text-gray-500 mb-1">본문</div><textarea name="mq_body" id="rBody" maxlength="500" class="inp h-20"></textarea></div>
                        <div><div class="text-[12px] text-gray-500 mb-1">누르면 열 주소 <span class="text-gray-400">(사이트 안의 경로, 비우면 첫 화면)</span></div><input name="mq_link" id="rLink" maxlength="500" class="inp" placeholder="/board-scrap/create"></div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-surface lg:rounded-br-2xl">
                <div class="text-[12px] text-gray-500">지금 기준 대상</div>
                <div class="font-outfit text-4xl font-extrabold mt-1"><span id="rAud">-</span><span class="text-base font-bold ml-1">명</span></div>
                <div class="text-[12px] text-gray-500 mt-6 mb-2">미리보기</div>
                <div class="bg-white rounded-2xl p-3 shadow-sm text-[12px]">
                    <div class="flex items-center gap-1.5 text-[11px] text-gray-400 mb-1"><span class="w-4 h-4 rounded bg-point text-white text-[8px] font-bold flex items-center justify-center">MQ</span>MQWAY<span class="ml-auto">지금</span></div>
                    <div class="font-bold text-ink break-all" id="pvTitle"></div>
                    <div class="text-gray-600 mt-0.5 leading-snug break-all whitespace-pre-line" id="pvBody"></div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 px-6 py-4 border-t border-gray-100">
            <label class="flex items-center gap-2 text-[13px] cursor-pointer select-none">
                <span class="toggle on" id="rToggle"></span>규칙 사용
            </label>
            <button type="submit" form="ruleDeleteForm" id="rDelete" class="ml-auto text-[13px] text-red-500 px-3 py-2">삭제</button>
            <button type="submit" class="text-[13px] bg-point text-white font-bold rounded-lg px-5 py-2">저장</button>
        </div>
    </form>
</div>
<form id="ruleDeleteForm" method="POST" action="" onsubmit="return confirm('이 규칙을 삭제할까요? 발송 이력은 남습니다.')">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
(function () {
    var RULES = @json($ruleData);
    var OLD = @json($oldRule);
    var STORE_URL = @json(route('admin.push.rules.store'));
    var AUDIENCE_URL = @json(route('admin.push.audience'));
    var PLACEHOLDER = @json($placeholder);

    var modal = document.getElementById('ruleModal');
    var form = document.getElementById('ruleForm');
    var $ = function (id) { return document.getElementById(id); };

    function targetRadios() { return form.querySelectorAll('input[name="mq_target"]'); }
    function selectedTarget() {
        var r = form.querySelector('input[name="mq_target"]:checked');
        return r ? r.value : null;
    }
    function daysInput(target) { return form.querySelector('[data-days="' + target + '"]'); }

    function syncFreq() {
        var f = $('rFreq').value;
        $('rWeekdays').style.display = f === 'weekly' ? 'flex' : 'none';
        $('rMonthDay').style.display = f === 'monthly' ? 'flex' : 'none';
    }
    function syncPreview() {
        $('pvTitle').textContent = ($('rTitle').value || '제목').split(PLACEHOLDER).join('김민지');
        $('pvBody').textContent = ($('rBody').value || '본문').split(PLACEHOLDER).join('김민지');
    }

    var audTimer = null;
    function syncAudience() {
        var target = selectedTarget();
        var d = target ? daysInput(target) : null;
        $('rDays').value = d ? d.value : '';
        clearTimeout(audTimer);
        if (!target) { $('rAud').textContent = '-'; return; }
        audTimer = setTimeout(function () {
            var url = AUDIENCE_URL + '?target=' + encodeURIComponent(target) + (d ? '&days=' + encodeURIComponent(d.value) : '');
            fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(function (j) { $('rAud').textContent = Number(j.count).toLocaleString('ko-KR'); })
                .catch(function () { $('rAud').textContent = '-'; });
        }, 250);
    }
    function setToggle(on) {
        $('rStatus').value = on ? 1 : 0;
        $('rToggle').classList.toggle('on', !!on);
    }

    function open(r) {
        var editing = !!(r && r.idx);
        r = r || {};
        form.action = editing ? (r.update_url || STORE_URL.replace(/\/rules$/, '/rules/' + r.idx)) : STORE_URL;
        $('rMethod').value = editing ? 'PUT' : 'POST';
        $('rIdx').value = editing ? r.idx : '';
        $('rName').value = r.mq_name || '';
        $('rTitle').value = r.mq_title || '';
        $('rBody').value = r.mq_body || '';
        $('rLink').value = r.mq_link || '';

        var target = r.mq_target || 'no_scrap';
        targetRadios().forEach(function (radio) { radio.checked = radio.value === target; });
        if (r.mq_target_days && daysInput(target)) daysInput(target).value = r.mq_target_days;

        $('rFreq').value = r.mq_frequency || 'weekly';
        var days = r.mq_weekdays && r.mq_weekdays.length ? r.mq_weekdays : [1];
        form.querySelectorAll('input[name="mq_weekdays[]"]').forEach(function (c) { c.checked = days.indexOf(Number(c.value)) >= 0; });
        form.querySelector('select[name="mq_month_day"]').value = r.mq_month_day || 1;
        $('rTime').value = r.mq_send_time || '09:00';
        setToggle(r.mq_status === undefined || r.mq_status === null ? 1 : Number(r.mq_status) === 1);

        var del = $('rDelete');
        del.style.display = editing ? '' : 'none';
        if (editing) document.getElementById('ruleDeleteForm').action = r.delete_url || '';

        syncFreq();
        syncPreview();
        syncAudience();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        $('rName').focus();
    }
    function close() {
        var errBox = $('rErrors'); if (errBox) errBox.remove();
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    $('rFreq').addEventListener('change', syncFreq);
    $('rTitle').addEventListener('input', syncPreview);
    $('rBody').addEventListener('input', syncPreview);
    $('rToggle').addEventListener('click', function () { setToggle($('rStatus').value !== '1'); });
    targetRadios().forEach(function (radio) { radio.addEventListener('change', syncAudience); });
    form.querySelectorAll('[data-days]').forEach(function (inp) {
        inp.addEventListener('input', syncAudience);
        // 숫자 칸을 누르면 그 줄의 대상도 함께 고른다.
        inp.addEventListener('focus', function () {
            var radio = form.querySelector('input[name="mq_target"][value="' + inp.dataset.days + '"]');
            if (radio && !radio.checked) { radio.checked = true; syncAudience(); }
        });
    });
    form.addEventListener('submit', function () { syncAudience(); });

    document.querySelectorAll('[data-rule-new]').forEach(function (b) { b.addEventListener('click', function () { open(null); }); });
    document.querySelectorAll('[data-rule-edit]').forEach(function (b) {
        b.addEventListener('click', function () {
            var idx = Number(b.dataset.ruleEdit);
            open(RULES.filter(function (r) { return r.idx === idx; })[0]);
        });
    });
    modal.querySelectorAll('[data-rule-close]').forEach(function (b) { b.addEventListener('click', close); });
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });

    if (OLD) {
        var base = OLD.idx ? RULES.filter(function (r) { return r.idx === OLD.idx; })[0] : null;
        open(Object.assign({}, base || {}, OLD));
    }
})();
</script>
@endpush
