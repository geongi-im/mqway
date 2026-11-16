@extends('admin.layouts.app')

@section('title', '앱 푸시관리')

@section('content')
@include('admin.push.tabs', ['active' => 'logs'])

<div class="bg-white rounded-xl border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full text-[13px] whitespace-nowrap">
            <thead class="bg-surface text-gray-500 text-[12px]">
                <tr>
                    <th class="text-left py-3 pl-5">요청 시각</th>
                    <th class="text-left">출처</th>
                    <th class="text-left">제목</th>
                    <th class="text-left">대상</th>
                    <th class="text-right">대상 기기</th>
                    <th class="text-right">성공</th>
                    <th class="text-right">실패</th>
                    <th class="text-left pl-6 pr-5">상태</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-surface">
                        <td class="py-3 pl-5 text-gray-400 text-[12px] font-outfit whitespace-nowrap">{{ optional($log->mq_reg_date)->format('Y-m-d H:i') }}</td>
                        <td>@include('admin.partials.push_source', ['log' => $log])</td>
                        <td class="max-w-xs"><div class="truncate" title="{{ $log->mq_body }}">{{ $log->mq_title }}</div></td>
                        <td class="text-gray-500 whitespace-nowrap">@include('admin.partials.push_target', ['log' => $log])</td>
                        <td class="text-right font-outfit">{{ number_format($log->mq_target_count) }}</td>
                        <td class="text-right font-outfit text-emerald-600">{{ number_format($log->mq_success_count) }}</td>
                        <td class="text-right font-outfit {{ $log->mq_failure_count ? 'text-point1' : 'text-gray-300' }}">{{ number_format($log->mq_failure_count) }}</td>
                        <td class="pl-6 pr-5">@include('admin.partials.push_status', ['log' => $log])</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-12 text-center text-gray-400">발송 이력이 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 text-[12px] text-gray-500 border-t border-gray-100">
        <span>총 {{ number_format($logs->total()) }}건</span>
        {{ $logs->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection
