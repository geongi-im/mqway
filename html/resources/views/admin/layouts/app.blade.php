<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') - MQWAY 관리자</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: {
            colors: { point: '#3D4148', point1: '#FF4D4D', mint: '#4ECDC4', ink: '#2D3047', surface: '#F8F9FA' },
            fontFamily: { outfit: ['Outfit', 'sans-serif'], noto: ['Noto Sans KR', 'sans-serif'] }
        } } };
    </script>
    <style>
        body { font-family: 'Noto Sans KR', sans-serif; }
        .nav-item.active { background: rgba(255,255,255,.08); color: #fff; }
        .nav-item.active::before { content: ''; position: absolute; left: 0; top: 8px; bottom: 8px; width: 3px; border-radius: 0 3px 3px 0; background: #FF4D4D; }
        .tab.active { color: #2D3047; border-color: #FF4D4D; font-weight: 700; }
        .toggle { width: 36px; height: 20px; border-radius: 999px; background: #d1d5db; position: relative; transition: .2s; cursor: pointer; flex-shrink: 0; display: inline-block; border: 0; }
        .toggle::after { content: ''; position: absolute; top: 2px; left: 2px; width: 16px; height: 16px; border-radius: 50%; background: #fff; transition: .2s; box-shadow: 0 1px 2px rgba(0,0,0,.2); }
        .toggle.on { background: #4ECDC4; }
        .toggle.on::after { left: 18px; }
        .sel { border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px 28px 6px 10px; font-size: 13px; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2.5'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 8px center; appearance: none; }
        .inp { border: 1px solid #e5e7eb; border-radius: 8px; padding: 7px 10px; font-size: 13px; width: 100%; }
        .inp:focus, .sel:focus { outline: none; border-color: #3D4148; }
        th { font-weight: 600; }
        [x-cloak] { display: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-surface text-ink">
@php
    $adminMenus = [
        ['route' => 'admin.dashboard',     'match' => 'admin.dashboard', 'label' => '대시보드',   'icon' => 'M3 10.5 12 3l9 7.5M5 9.5V21h14V9.5'],
        ['route' => 'admin.members.index', 'match' => 'admin.members.*', 'label' => '회원관리',   'icon' => 'M9 11.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM2.5 20c.6-3.6 3.3-5.5 6.5-5.5s5.9 1.9 6.5 5.5M16 4.6a3.5 3.5 0 0 1 0 6.8M18.5 14.8c1.7.8 2.8 2.6 3 5.2'],
        ['route' => 'admin.boards.index',  'match' => 'admin.boards.*',  'label' => '게시판관리', 'icon' => 'M6 3.5h12A2.5 2.5 0 0 1 20.5 6v12a2.5 2.5 0 0 1-2.5 2.5H6A2.5 2.5 0 0 1 3.5 18V6A2.5 2.5 0 0 1 6 3.5zM7.5 8.5h9M7.5 12h9M7.5 15.5h5'],
        ['route' => 'admin.push.index',    'match' => 'admin.push.*',    'label' => '앱 푸시관리', 'icon' => 'M6 16V11a6 6 0 1 1 12 0v5l1.5 2h-15zM10 20.5a2 2 0 0 0 4 0'],
    ];
    $me = auth()->user();
@endphp

<div class="flex min-h-screen">
    {{-- 사이드바 --}}
    <aside id="adminSidebar" class="w-60 bg-point text-white/70 flex-shrink-0 flex flex-col fixed inset-y-0 left-0 z-40 -translate-x-full lg:translate-x-0 transition-transform">
        <a href="{{ route('admin.dashboard') }}" class="h-16 flex items-center gap-2 px-6 border-b border-white/10">
            <span class="font-outfit text-xl font-extrabold text-white tracking-tight">MQWAY</span>
            <span class="font-outfit text-[10px] font-bold bg-point1 text-white px-1.5 py-0.5 rounded">ADMIN</span>
        </a>
        <nav class="flex-1 py-4 px-3 space-y-1 text-[14px]">
            @foreach($adminMenus as $menu)
                <a href="{{ route($menu['route']) }}"
                   class="nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/5 hover:text-white {{ request()->routeIs($menu['match']) ? 'active' : '' }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $menu['icon'] }}"/></svg>
                    {{ $menu['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="p-4 border-t border-white/10 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-point1 text-white font-bold flex items-center justify-center text-sm">{{ mb_substr($me->mq_user_name ?: $me->mq_user_id, 0, 1) }}</div>
            <div class="flex-1 min-w-0">
                <div class="text-white text-[13px] font-semibold truncate">{{ $me->mq_user_name ?: $me->mq_user_id }} ({{ $me->mq_user_id }})</div>
                <div class="text-[11px] text-white/40">Lv.{{ $me->mq_level }} 최고관리자</div>
            </div>
            <a href="{{ url('/') }}" class="text-white/40 hover:text-white" title="사이트로 돌아가기">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 4h4.5v16H15M10 16.5 14.5 12 10 7.5M14.5 12H3.5"/></svg>
            </a>
        </div>
    </aside>

    {{-- 본문 --}}
    <div class="flex-1 lg:ml-60 min-w-0">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center gap-4 px-4 lg:px-8 sticky top-0 z-30">
            <button type="button" class="lg:hidden" onclick="document.getElementById('adminSidebar').classList.toggle('-translate-x-full')" aria-label="메뉴">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6.5h16M4 12h16M4 17.5h16"/></svg>
            </button>
            <div>
                <div class="text-[11px] text-gray-400">관리자 / @yield('title')</div>
                <h1 class="text-lg font-bold leading-tight">@yield('title')</h1>
            </div>
            <a href="{{ url('/') }}" class="ml-auto text-[13px] text-gray-500 hover:text-ink">사이트 보기</a>
        </header>

        <main class="p-4 lg:p-8">
            @if(session('admin_message'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] px-4 py-3">{{ session('admin_message') }}</div>
            @endif
            @if(session('admin_error'))
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-[13px] px-4 py-3">{{ session('admin_error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-[13px] px-4 py-3">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
