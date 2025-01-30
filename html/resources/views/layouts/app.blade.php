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
                        primary: '#f4e176',
                        secondary: '#FFFFFF',
                        dark: '#34383d'
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