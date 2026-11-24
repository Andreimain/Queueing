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

    <body class="font-sans antialiased text-gray-800 bg-gray-50">
        <div class="min-h-screen relative overflow-x-hidden">

            {{-- Background Decoration --}}
            <div class="fixed inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-emerald-100/40 blur-3xl"></div>
                <div class="absolute top-1/3 -left-40 w-96 h-96 rounded-full bg-green-100/30 blur-3xl"></div>
            </div>

            {{-- Navigation --}}
            <div class="relative z-40">
                @include('layouts.navigation')
            </div>

            {{-- Page Heading --}}
            @isset($header)
                <header class="relative z-10">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-5 sm:px-7">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            {{-- Page Content --}}
            <main class="relative z-10">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="relative z-10 py-6 mt-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="border-t border-gray-200/70 pt-5 text-center">
                        <p class="text-xs font-medium text-gray-400">
                            Lorma Queueing System
                        </p>
                    </div>
                </div>
            </footer>

        </div>

        @stack('scripts')
    </body>
</html>
