<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="어린이 경제교육 플랫폼 MQWAY - 초등학생 금융교육, 용돈관리, 경제 지능 향상. 아이들의 자기주도 학습과 올바른 경제 관념 형성을 위한 부모와 함께하는 경제교육 서비스를 제공합니다.">
    

    <meta property="og:image" content="{{ asset('/images/logo/mqway_logo_og_image.jpg') }}">
    <meta property="og:title" content="{{ config('app.name', 'MQWAY') }}">
    <meta property="og:description" content="우리 아이 경제 지능을 키우는 MQWAY. 자기주도 학습과 올바른 경제 관념 형성을 위한 실전 경제교육 플랫폼입니다.">
    <meta property="og:type" content="website">
    
    <meta name="naver-site-verification" content="a3b7b869fb45b3421b38b4773bb8c172ecf12861" />

    <title>{{ config('app.name', 'MQWAY') }}</title>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-V01TWJNXEC"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-V01TWJNXEC');
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Noto+Sans+KR:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="{{ asset('js/common.js') }}"></script>

    <!-- PhotoSwipe CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/5.4.4/photoswipe.min.css">

    <!-- PhotoSwipe JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/5.4.4/umd/photoswipe.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/5.4.4/umd/photoswipe-lightbox.umd.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#F8F9FA',
                        secondary: '#3D4148',
                        cdark: '#FFFFFF',
                        cgray: '#e5e7eb',
                        point: '#3D4148',   // 헤더/사이드바/푸터 배경 (다크 그레이)
                        point1: '#FF4D4D',  // 액센트 (레드)
                        point2: '#3D4148',  // 드롭다운 배경 (다크 그레이)
                        point3: '#FF4D4D',  // 포인트 (레드)
                        // 새 디자인 확장 색상
                        'new-primary': '#FF4D4D',
                        'new-secondary': '#3D4148',
                        'new-coral': '#FF6B6B',
                        'new-mint': '#4ECDC4',
                        'new-amber': '#FFB347',
                        'new-surface': '#F8F9FA',
                        'new-ink': '#2D3047',
                    },
                    fontFamily: {
                        'outfit': ['Outfit', 'sans-serif'],
                        'noto': ['Noto Sans KR', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .menu-overlay {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }
        .menu-overlay.active {
            transform: translateX(0);
        }

        /* 알림 메시지 스타일 */
        .alert {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: auto;
            max-width: 90%;
            transition: all 0.3s ease;
            opacity: 1;
        }
        .alert-success {
            background-color: #dcfce7;
            border-left: 4px solid #22c55e;
            color: #166534;
        }
        .alert-error {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }
        .alert-info {
            background-color: #e0f2fe;
            border-left: 4px solid #38bdf8;
            color: #075985;
        }
        .alert-warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }
    </style>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7078035340110573"
    crossorigin="anonymous"></script>
</head>
<body class="bg-primary min-h-screen flex flex-col">
    <!-- Header -->
    @include('layouts.partials.header')

    <!-- 알림 메시지 -->
    @if(session('success'))
    <div class="alert alert-success" id="alert-message">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error" id="alert-message">
        {{ session('error') }}
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info" id="alert-message">
        {{ session('info') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning" id="alert-message">
        {{ session('warning') }}
    </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')

    @stack('scripts')

    <!-- 알림 메시지 자동 숨김 스크립트 -->
    <script>
        // 알림 메시지가 있으면 자동으로 숨기기
        const alertMessage = document.getElementById('alert-message');
        if (alertMessage) {
            setTimeout(() => {
                alertMessage.style.opacity = '0';
                setTimeout(() => {
                    alertMessage.style.display = 'none';
                }, 500);
            }, 3000);
        }
    </script>
</body>
</html> 