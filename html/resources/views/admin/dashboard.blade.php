@extends('admin.layouts.app')

@section('title', '대시보드')

@section('content')
@php
    $cards = [
        ['전체 회원', $stats['members'], '명', '오늘 가입 ' . number_format($stats['joined_today']) . '명'],
        ['오늘 방문 회원', $stats['visitors_today'], '명', '어제 ' . number_format($stats['visitors_yesterday']) . '명'],
        ['이번 주 스크랩', $stats['scrap_week'], '건', '지난주 같은 시점 ' . number_format($stats['scrap_last_week']) . '건'],
        ['알림 켠 회원', $stats['push_members'], '명', '마케팅 동의 ' . number_format($stats['push_marketing_members']) . '명'],
    ];
@endphp
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
    @foreach($cards as $card)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="text-[13px] text-gray-500">{{ $card[0] }}</div>
            <div class="font-outfit text-3xl font-bold mt-2">{{ number_format($card[1]) }}<span class="text-sm font-medium text-gray-400 ml-1">{{ $card[2] }}</span></div>
            <div class="text-[12px] text-gray-400 mt-1">{{ $card[3] }}</div>
        </div>
    @endforeach
</div>

<div class="grid xl:grid-cols-2 gap-4 mt-4">
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold">최근 게시글</h3>
            <a href="{{ route('admin.boards.index') }}" class="text-[12px] text-gray-400 hover:text-ink">전체 보기</a>
        </div>
        <ul class="divide-y divide-gray-100">
            @forelse($recentPosts as $post)
                <li>
                    <a href="{{ \App\Services\AdminBoardRegistry::showUrl($post->board, $post->idx) }}" target="_blank" class="px-5 py-3 flex items-center gap-3 text-[13px] hover:bg-surface">
                        <span class="text-[11px] text-gray-400 w-24 flex-shrink-0 truncate">{{ \App\Services\AdminBoardRegistry::name($post->board) }}</span>
                        <span class="flex-1 truncate">{{ $post->mq_title }}</span>
                        <span class="text-[12px] text-gray-400">{{ $post->mq_user_id }}</span>
                    </a>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-[13px] text-gray-400">게시글이 없습니다.</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold">최근 푸시 발송</h3>
            <a href="{{ route('admin.push.logs') }}" class="text-[12px] text-gray-400 hover:text-ink">발송 이력</a>
        </div>
        <ul class="divide-y divide-gray-100">
            @forelse($recentLogs as $log)
                <li class="px-5 py-3 flex items-center gap-3 text-[13px]">
                    @include('admin.partials.push_source', ['log' => $log])
                    <span class="flex-1 truncate">{{ $log->mq_title }}</span>
                    <span class="text-[12px] text-gray-400 font-outfit">{{ optional($log->mq_reg_date)->format('m-d H:i') }}</span>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-[13px] text-gray-400">발송 이력이 없습니다.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
