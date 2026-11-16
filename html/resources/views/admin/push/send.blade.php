@extends('admin.layouts.app')

@section('title', '앱 푸시관리')

@section('content')
@include('admin.push.tabs', ['active' => 'send'])

@unless($canSendNow)
    <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-[13px] px-4 py-3">
        지금은 보낼 수 없는 시간입니다. 알림은 {{ $sendWindow }} 사이에만 보낼 수 있습니다.
    </div>
@endunless

<form id="sendForm" method="POST" action="{{ route('admin.push.send') }}" class="bg-white rounded-xl border border-gray-200">
    @csrf
    <input type="hidden" name="send_key" value="{{ $sendKey }}">
    <div id="sHidden"></div>

    <div class="grid lg:grid-cols-[1fr_300px]">
        <div class="p-6 space-y-6 lg:border-r border-gray-100">
            {{-- 1. 누구에게 --}}
            <div>
                <div class="flex items-center gap-2 mb-3"><span class="w-6 h-6 rounded-full bg-point text-white text-[12px] font-bold flex items-center justify-center">1</span><span class="font-bold">누구에게</span></div>
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input id="sSearch" type="text" autocomplete="off" class="inp !pl-9" placeholder="아이디 / 이름 / 이메일로 회원 찾기">
                    <div id="sResults" class="hidden absolute z-10 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-72 overflow-y-auto text-[13px]"></div>
                </div>
                <div class="mt-3 flex items-center justify-between text-[12px] text-gray-500">
                    <span>고른 회원 <b id="sCount" class="text-ink">0</b>명 (최대 {{ number_format($maxUsers) }}명)</span>
                    <button type="button" id="sClear" class="text-gray-400 hover:text-ink">모두 빼기</button>
                </div>
                <div id="sSelected" class="mt-2 flex flex-wrap gap-1.5 min-h-[40px] rounded-lg border border-dashed border-gray-200 p-2"></div>
                <div class="text-[12px] text-gray-400 mt-2">마케팅 수신에 동의하고 알림을 켠 활성 회원만 받습니다. 받을 수 없는 회원은 회색으로 보이고 발송에서 빠집니다.</div>
            </div>

            {{-- 2. 무엇을 --}}
            <div>
                <div class="flex items-center gap-2 mb-3"><span class="w-6 h-6 rounded-full bg-point text-white text-[12px] font-bold flex items-center justify-center">2</span><span class="font-bold">무엇을</span></div>
                <div class="space-y-3">
                    <div><div class="text-[12px] text-gray-500 mb-1">제목</div><input name="mq_title" id="sTitle" maxlength="100" required class="inp" value="{{ old('mq_title') }}"></div>
                    <div><div class="text-[12px] text-gray-500 mb-1">본문 <span class="text-gray-400">({{ $placeholder }} 을 넣으면 받는 회원 이름으로 바뀝니다)</span></div><textarea name="mq_body" id="sBody" maxlength="500" class="inp h-20">{{ old('mq_body') }}</textarea></div>
                    <div><div class="text-[12px] text-gray-500 mb-1">누르면 열 주소 <span class="text-gray-400">(사이트 안의 경로, 비우면 첫 화면)</span></div><input name="mq_link" id="sLink" maxlength="500" class="inp" placeholder="/board-scrap/create" value="{{ old('mq_link') }}"></div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-surface rounded-b-xl lg:rounded-bl-none lg:rounded-r-xl">
            <div class="text-[12px] text-gray-500">받을 수 있는 회원</div>
            <div class="font-outfit text-4xl font-extrabold mt-1"><span id="sReach">0</span><span class="text-base font-bold ml-1">명</span></div>
            <div class="text-[12px] text-gray-400 mt-1">기기 <span id="sDevices">0</span>대</div>
            <div class="text-[12px] text-gray-500 mt-6 mb-2">미리보기</div>
            <div class="bg-white rounded-2xl p-3 shadow-sm text-[12px]">
                <div class="flex items-center gap-1.5 text-[11px] text-gray-400 mb-1"><span class="w-4 h-4 rounded bg-point text-white text-[8px] font-bold flex items-center justify-center">MQ</span>MQWAY<span class="ml-auto">지금</span></div>
                <div class="font-bold text-ink break-all" id="pvTitle"></div>
                <div class="text-gray-600 mt-0.5 leading-snug break-all whitespace-pre-line" id="pvBody"></div>
            </div>
            <button type="submit" id="sSubmit" class="w-full mt-6 bg-point1 hover:bg-[#ff3333] disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-lg py-2.5 text-[13px]" @unless($canSendNow) disabled @endunless>지금 보내기</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var PRESET = @json($selected);
    var SEARCH_URL = @json(route('admin.push.recipients'));
    var PLACEHOLDER = @json($placeholder);
    var MAX = @json($maxUsers);
    var CAN_SEND = @json($canSendNow);
    var $ = function (id) { return document.getElementById(id); };

    var picked = [];   // [{user_id, name, email, devices, reachable, reason}]

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function has(id) { return picked.some(function (m) { return m.user_id.toLowerCase() === id.toLowerCase(); }); }

    function render() {
        $('sSelected').innerHTML = picked.length ? picked.map(function (m, i) {
            var cls = m.reachable ? 'bg-[#4ECDC4]/15 text-ink' : 'bg-gray-100 text-gray-400 line-through decoration-gray-300';
            return '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[12px] ' + cls + '"' + (m.reason ? ' title="' + esc(m.reason) + '"' : '') + '>' +
                esc(m.name || m.user_id) + ' <span class="text-gray-400">' + esc(m.user_id) + '</span>' +
                (m.reason ? ' <span class="text-[11px]">(' + esc(m.reason) + ')</span>' : '') +
                '<button type="button" data-remove="' + i + '" class="ml-0.5 text-gray-400 hover:text-red-500" aria-label="빼기">&times;</button></span>';
        }).join('') : '<span class="text-[12px] text-gray-300 px-1 py-1">위에서 회원을 찾아 고르세요</span>';

        $('sHidden').innerHTML = picked.map(function (m) {
            return '<input type="hidden" name="user_ids[]" value="' + esc(m.user_id) + '">';
        }).join('');

        var reach = picked.filter(function (m) { return m.reachable; });
        $('sCount').textContent = picked.length;
        $('sReach').textContent = reach.length;
        $('sDevices').textContent = reach.reduce(function (n, m) { return n + m.devices; }, 0);
        $('sSubmit').disabled = !CAN_SEND || reach.length === 0;
    }
    function preview() {
        $('pvTitle').textContent = ($('sTitle').value || '제목').split(PLACEHOLDER).join('김민지');
        $('pvBody').textContent = ($('sBody').value || '본문').split(PLACEHOLDER).join('김민지');
    }

    function add(m) {
        if (has(m.user_id)) return;
        if (picked.length >= MAX) { alert('한 번에 ' + MAX + '명까지 보낼 수 있습니다.'); return; }
        picked.push(m);
        render();
    }

    var timer = null, results = [];
    function search() {
        var q = $('sSearch').value.trim();
        clearTimeout(timer);
        if (!q) { $('sResults').classList.add('hidden'); return; }
        timer = setTimeout(function () {
            fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(function (j) {
                    results = j.members;
                    $('sResults').innerHTML = results.length ? results.map(function (m, i) {
                        var already = has(m.user_id);
                        return '<button type="button" data-pick="' + i + '" class="w-full text-left px-3 py-2 hover:bg-surface flex items-center gap-2' + (already ? ' opacity-50' : '') + '">' +
                            '<span class="font-semibold text-ink">' + esc(m.name || '-') + '</span>' +
                            '<span class="text-gray-400">' + esc(m.user_id) + '</span>' +
                            '<span class="text-gray-400 truncate hidden sm:inline">' + esc(m.email || '') + '</span>' +
                            '<span class="ml-auto text-[11px] whitespace-nowrap ' + (m.reachable ? 'text-emerald-600' : 'text-gray-400') + '">' +
                                (already ? '고름' : (m.reachable ? '기기 ' + m.devices + '대' : esc(m.reason))) + '</span>' +
                        '</button>';
                    }).join('') : '<div class="px-3 py-3 text-gray-400">찾는 회원이 없습니다.</div>';
                    $('sResults').classList.remove('hidden');
                })
                .catch(function () { $('sResults').innerHTML = '<div class="px-3 py-3 text-red-500">검색하지 못했습니다.</div>'; $('sResults').classList.remove('hidden'); });
        }, 250);
    }

    $('sSearch').addEventListener('input', search);
    $('sSearch').addEventListener('keydown', function (e) {
        // 엔터로 폼이 제출되지 않게 하고, 결과가 하나면 바로 고른다.
        if (e.key === 'Enter') { e.preventDefault(); if (results.length === 1) { add(results[0]); $('sSearch').value = ''; $('sResults').classList.add('hidden'); } }
    });
    $('sResults').addEventListener('click', function (e) {
        var b = e.target.closest('[data-pick]');
        if (!b) return;
        add(results[Number(b.dataset.pick)]);
        $('sSearch').value = '';
        $('sResults').classList.add('hidden');
        $('sSearch').focus();
    });
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#sResults') && e.target !== $('sSearch')) $('sResults').classList.add('hidden');
    });
    $('sSelected').addEventListener('click', function (e) {
        var b = e.target.closest('[data-remove]');
        if (!b) return;
        picked.splice(Number(b.dataset.remove), 1);
        render();
    });
    $('sClear').addEventListener('click', function () { picked = []; render(); });
    $('sTitle').addEventListener('input', preview);
    $('sBody').addEventListener('input', preview);

    $('sendForm').addEventListener('submit', function (e) {
        var reach = picked.filter(function (m) { return m.reachable; }).length;
        if (!confirm('회원 ' + reach + '명에게 지금 알림을 보낼까요? 보낸 알림은 취소할 수 없습니다.')) { e.preventDefault(); return; }
        $('sSubmit').disabled = true;
        $('sSubmit').textContent = '보내는 중...';
    });

    PRESET.forEach(add);
    render();
    preview();
})();
</script>
@endpush
