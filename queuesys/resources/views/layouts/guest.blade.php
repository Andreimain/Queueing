<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans text-gray-900 antialiased">
        <div class="{{ $containerClass ?? 'relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-gray-50 via-white to-emerald-50 px-4 py-8' }}">

            {{-- Background Decorations --}}
            <div class="fixed inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-emerald-200/30 blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-green-200/30 blur-3xl"></div>
            </div>

            {{-- Page Content --}}
            <div class="relative z-10 w-full">
                {{ $slot }}
            </div>

        </div>
    </body>
</html>
