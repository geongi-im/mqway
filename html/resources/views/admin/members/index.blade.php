@extends('admin.layouts.app')

@section('title', '회원관리')

@php
    $providerLabels = ['kakao' => '카카오', 'naver' => '네이버', 'google' => '구글', 'apple' => '애플', 'email' => '이메일'];
@endphp

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <form method="GET" action="{{ route('admin.members.index') }}" class="p-4 border-b border-gray-100 flex flex-wrap items-center gap-2">
        <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-1.5 w-72 text-gray-400">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/></svg>
            <input name="q" value="{{ $filters['q'] }}" class="text-[13px] outline-none flex-1 text-ink" placeholder="아이디 / 이름 / 이메일">
        </div>
        <select name="status" class="sel">
            <option value="">상태 전체</option>
            @foreach($statusLabels as $value => $label)
                <option value="{{ $value }}" @if((string) $filters['status'] === (string) $value) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <select name="level" class="sel">
            <option value="">권한 전체</option>
            <option value="normal" @if($filters['level'] === 'normal') selected @endif>일반</option>
            <option value="manager" @if($filters['level'] === 'manager') selected @endif>매니저 (Lv.9)</option>
            <option value="admin" @if($filters['level'] === 'admin') selected @endif>최고관리자 (Lv.10)</option>
        </select>
        <button class="text-[13px] bg-point text-white rounded-lg px-4 py-1.5">검색</button>
        @if($filters['q'] !== '' || $filters['status'] !== '' || $filters['level'] !== '')
            <a href="{{ route('admin.members.index') }}" class="text-[13px] text-gray-400 hover:text-ink px-2">초기화</a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-[13px] whitespace-nowrap">
            <thead class="bg-surface text-gray-500 text-[12px]">
                <tr>
                    <th class="text-left py-3 pl-5">회원</th>
                    <th class="text-left">가입 경로</th>
                    <th class="text-left">등급</th>
                    <th class="text-left">권한</th>
                    <th class="text-left">가입일</th>
                    <th class="text-left">마지막 접속</th>
                    <th class="text-left">상태</th>
                    <th class="w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($members as $m)
                    <tr class="hover:bg-surface cursor-pointer" data-member="{{ route('admin.members.show', $m->idx) }}">
                        <td class="py-3 pl-5">
                            <div class="font-semibold">{{ $m->mq_user_name }} <span class="text-gray-400 font-normal">{{ $m->mq_user_id }}</span></div>
                            <div class="text-[11px] text-gray-400">{{ $m->mq_user_email }}</div>
                        </td>
                        <td>{{ $providerLabels[$m->mq_provider ?: 'email'] ?? $m->mq_provider }}</td>
                        <td>@include('components.grade_badge', ['grade' => $m->grade])</td>
                        <td>@include('admin.members.level_chip', ['level' => (int) $m->mq_level])</td>
                        <td class="text-gray-500 text-[12px] font-outfit whitespace-nowrap">{{ substr($m->mq_reg_date, 0, 10) }}</td>
                        <td class="text-gray-500 text-[12px] font-outfit whitespace-nowrap">{{ $m->last_seen ? substr($m->last_seen, 0, 16) : '-' }}</td>
                        <td>@include('admin.members.status_chip', ['status' => (int) $m->mq_status])</td>
                        <td class="text-gray-300"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 6 6 6-6 6"/></svg></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-12 text-center text-gray-400">조건에 맞는 회원이 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 text-[12px] text-gray-500 border-t border-gray-100">
        <span>총 {{ number_format($members->total()) }}명</span>
        {{ $members->links('vendor.pagination.tailwind') }}
    </div>
</div>

{{-- 회원 상세 서랍 --}}
<div id="drawerBg" class="fixed inset-0 bg-black/30 z-50 hidden"></div>
<aside id="drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl translate-x-full transition-transform overflow-y-auto">
    <div class="flex items-center justify-between px-6 h-16 border-b border-gray-100">
        <h3 class="font-bold">회원 상세</h3>
        <button type="button" data-close class="text-gray-400 hover:text-ink" aria-label="닫기">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </div>
    <div id="drawerBody" class="p-6 text-[13px] text-gray-400">불러오는 중...</div>
</aside>
@endsection

@push('scripts')
<script>
(function () {
    var STATUS = @json($statusLabels);
    var LEVELS = @json($levelLabels);
    var PROVIDERS = @json($providerLabels);
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;
    var drawer = document.getElementById('drawer');
    var bg = document.getElementById('drawerBg');
    var body = document.getElementById('drawerBody');

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function row(label, value) {
        return '<div class="flex py-2.5"><dt class="w-28 text-gray-400 flex-shrink-0">' + label + '</dt><dd class="text-ink break-all">' + value + '</dd></div>';
    }
    function options(map, current) {
        var html = '', found = false;
        Object.keys(map).forEach(function (k) {
            var on = Number(k) === current; if (on) found = true;
            html += '<option value="' + k + '"' + (on ? ' selected' : '') + '>' + map[k] + '</option>';
        });
        return html;
    }
    function levelOptions(current) {
        var map = {};
        Object.keys(LEVELS).forEach(function (k) { map[k] = 'Lv.' + k + ' ' + LEVELS[k]; });
        if (!map[current]) map[current] = 'Lv.' + current + ' (현재 값)';
        return options(map, current);
    }

    function open(url) {
        body.innerHTML = '불러오는 중...';
        drawer.classList.remove('translate-x-full');
        bg.classList.remove('hidden');
        fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
            .then(render)
            .catch(function () { body.innerHTML = '<div class="text-red-500">회원 정보를 불러오지 못했습니다.</div>'; });
    }
    function close() {
        drawer.classList.add('translate-x-full');
        bg.classList.add('hidden');
    }

    function render(m) {
        var g = m.grade;
        var lock = m.is_me ? ' disabled' : '';
        body.innerHTML =
            '<div class="flex items-center gap-4">' +
                '<div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-xl font-bold text-gray-500">' + esc((m.name || m.user_id).charAt(0)) + '</div>' +
                '<div><div class="text-lg font-bold text-ink">' + esc(m.name || '-') +
                    ' <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold align-middle" style="background:' + esc(g.color) + ';color:' + esc(g.text) + '">' + esc(g.name) + '</span></div>' +
                '<div class="text-[13px] text-gray-400">' + esc(m.user_id) + '</div></div>' +
            '</div>' +
            '<dl class="mt-6 divide-y divide-gray-100">' +
                row('회원번호', esc(m.idx)) +
                row('이메일', esc(m.email || '-')) +
                row('휴대폰', esc(m.phone || '-')) +
                row('가입 경로', esc(PROVIDERS[m.provider] || m.provider)) +
                row('가입일', esc((m.reg_date || '').substr(0, 16))) +
                row('마지막 접속', esc(m.last_seen ? m.last_seen.substr(0, 16) : '-')) +
                row('경험치', esc(Number(m.exp).toLocaleString('ko-KR'))) +
                row('마케팅 수신', m.marketing ? '동의' + (m.marketing_date ? ' (' + esc(m.marketing_date.substr(0, 10)) + ')' : '') : '미동의') +
                row('알림 켠 기기', m.devices ? esc(m.devices) + '대' : '없음') +
            '</dl>' +
            '<form method="POST" action="' + esc(m.update_url) + '" class="mt-6 space-y-3">' +
                '<input type="hidden" name="_token" value="' + esc(CSRF) + '"><input type="hidden" name="_method" value="PUT">' +
                '<div><div class="text-[12px] text-gray-500 mb-1">계정 상태</div><select name="mq_status" class="sel w-full"' + lock + '>' + options(STATUS, m.status) + '</select>' +
                    '<div class="text-[11px] text-gray-400 mt-1">비활성 / 해지 회원은 새로 로그인할 수 없고 푸시도 받지 않습니다.</div></div>' +
                '<div><div class="text-[12px] text-gray-500 mb-1">권한 레벨</div><select name="mq_level" class="sel w-full"' + lock + '>' + levelOptions(m.level) + '</select></div>' +
                (m.is_me
                    ? '<div class="text-[12px] text-amber-600 bg-amber-50 rounded-lg px-3 py-2">내 계정의 상태와 권한은 여기서 바꿀 수 없습니다.</div>'
                    : '<button class="w-full mt-5 bg-point text-white rounded-lg py-2.5 text-[13px] font-bold">저장</button>') +
            '</form>';
    }

    document.querySelectorAll('[data-member]').forEach(function (tr) {
        tr.addEventListener('click', function () { open(tr.dataset.member); });
    });
    bg.addEventListener('click', close);
    drawer.querySelector('[data-close]').addEventListener('click', close);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
})();
</script>
@endpush
