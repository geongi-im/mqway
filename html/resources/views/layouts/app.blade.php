<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MQWAY') }}</title>

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