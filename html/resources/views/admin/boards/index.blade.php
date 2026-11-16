@extends('admin.layouts.app')

@section('title', '게시판관리')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <form method="GET" action="{{ route('admin.boards.index') }}" class="p-4 border-b border-gray-100 flex flex-wrap items-center gap-2">
        <select name="board" class="sel">
            <option value="">게시판 전체</option>
            @foreach($boards as $key => $board)
                <option value="{{ $key }}" @if($filters['board'] === $key) selected @endif>{{ $board[0] }}</option>
            @endforeach
        </select>
        <select name="status" class="sel">
            <option value="">상태 전체</option>
            <option value="1" @if($filters['status'] === '1') selected @endif>노출</option>
            <option value="0" @if($filters['status'] === '0') selected @endif>숨김</option>
        </select>
        <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-1.5 w-72 text-gray-400">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/></svg>
            <input name="q" value="{{ $filters['q'] }}" class="text-[13px] outline-none flex-1 text-ink" placeholder="제목 / 작성자 아이디">
        </div>
        <button class="text-[13px] bg-point text-white rounded-lg px-4 py-1.5">검색</button>
        @if($filters['board'] !== '' || $filters['status'] !== '' || $filters['q'] !== '')
            <a href="{{ route('admin.boards.index') }}" class="text-[13px] text-gray-400 hover:text-ink px-2">초기화</a>
        @endif
        <span class="ml-auto text-[12px] text-gray-400">숨긴 글은 사이트에서 보이지 않고, 복구하면 다시 보입니다.</span>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-[13px] whitespace-nowrap">
            <thead class="bg-surface text-gray-500 text-[12px]">
                <tr>
                    <th class="text-left py-3 pl-5">게시판</th>
                    <th class="text-left">제목</th>
                    <th class="text-left">작성자</th>
                    <th class="text-right">조회</th>
                    <th class="text-left pl-6">작성일</th>
                    <th class="text-left">상태</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($posts as $post)
                    @php $hidden = (int) $post->mq_status !== 1; @endphp
                    <tr class="hover:bg-surface {{ $hidden ? 'text-gray-400' : '' }}">
                        <td class="py-3 pl-5 text-gray-500 text-[12px] whitespace-nowrap">{{ \App\Services\AdminBoardRegistry::name($post->board) }}</td>
                        <td class="max-w-md"><div class="truncate">{{ $post->mq_title }}</div></td>
                        <td class="text-gray-500 whitespace-nowrap">{{ $post->mq_user_id }}</td>
                        <td class="text-right font-outfit">{{ number_format($post->mq_view_cnt) }}</td>
                        <td class="pl-6 text-gray-400 text-[12px] font-outfit whitespace-nowrap">{{ substr($post->mq_reg_date, 0, 16) }}</td>
                        <td class="whitespace-nowrap">
                            @if($hidden)
                                <span class="text-gray-400 text-[12px]">숨김</span>
                            @else
                                <span class="text-emerald-600 text-[12px]">노출</span>
                            @endif
                        </td>
                        <td class="pr-5 pl-3">
                            <div class="flex justify-end gap-1.5 text-[12px] whitespace-nowrap">
                                @unless($hidden)
                                    <a href="{{ \App\Services\AdminBoardRegistry::showUrl($post->board, $post->idx) }}" target="_blank" class="border border-gray-200 rounded-md px-2 py-1 hover:bg-gray-50 text-ink">보기</a>
                                @endunless
                                <form method="POST" action="{{ route('admin.boards.status', [$post->board, $post->idx]) }}"
                                      @unless($hidden) onsubmit="return confirm('이 글을 숨길까요? 사이트에서 보이지 않게 됩니다.')" @endunless>
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="mq_status" value="{{ $hidden ? 1 : 0 }}">
                                    @if($hidden)
                                        <button class="border border-gray-200 rounded-md px-2 py-1 hover:bg-gray-50 text-ink">복구</button>
                                    @else
                                        <button class="border border-red-200 text-red-500 rounded-md px-2 py-1 hover:bg-red-50">숨김</button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-12 text-center text-gray-400">조건에 맞는 게시글이 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 text-[12px] text-gray-500 border-t border-gray-100">
        <span>총 {{ number_format($posts->total()) }}건</span>
        {{ $posts->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection
