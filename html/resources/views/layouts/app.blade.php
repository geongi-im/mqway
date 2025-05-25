<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="지금 MQWAY에서 당신의 삶을 설계하세요. 경제 지능과 자기주도력을 함께 키우는 통합 교육 플랫폼입니다.">

    <meta property="og:image" content="{{ asset('/images/logo/mqway_og_image.png') }}">
    <meta property="og:title" content="{{ config('app.name', 'MQWAY') }}">
    <meta property="og:description" content="경제 지능을 함께 키우는 교육 플랫폼">
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
    <script src="{{ asset('js/common.js') }}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f6f6f6',
                        secondary: '#FFFFFF',
                        cdark: '#2b2b2b',
                        cgray: '#f5f5f5',
                        point: '#fecc41'
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
    </style>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7078035340110573"
    crossorigin="anonymous"></script>
</head>
<body class="bg-primary min-h-screen flex flex-col">
    <!-- Header -->
    @include('layouts.partials.header')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')

    @stack('scripts')
</body>
</html> 