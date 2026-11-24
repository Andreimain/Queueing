<x-app-layout>
    <x-slot name="title">
        Dashboard
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ADMIN / SUPER ADMIN DASHBOARD --}}
            @if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())

                {{-- Welcome Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            Welcome back, {{ Auth::user()->name }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Here's an overview of your queueing system today.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 self-start px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-100">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-semibold text-emerald-700">System Overview</span>
                    </div>
                </div>

                {{-- Statistics Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Offices</span>
                        </div>
                        <p class="mt-5 text-sm text-gray-500">Total Offices</p>
                        <p data-key="totalOffices" class="mt-1 text-3xl font-bold text-gray-900">
                            {{ number_format($totalOffices) }}
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div class="h-11 w-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Personnel</span>
                        </div>
                        <p class="mt-5 text-sm text-gray-500">Total Staff</p>
                        <p data-key="totalStaff" class="mt-1 text-3xl font-bold text-gray-900">
                            {{ number_format($totalStaff) }}
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Today</span>
                        </div>
                        <p class="mt-5 text-sm text-gray-500">Visitors Today</p>
                        <p data-key="visitorsToday" class="mt-1 text-3xl font-bold text-gray-900">
                            {{ number_format($visitorsToday) }}
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div class="h-11 w-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Queues</span>
                        </div>
                        <p class="mt-5 text-sm text-gray-500">Active Queues</p>
                        <p data-key="activeQueues" class="mt-1 text-3xl font-bold text-emerald-600">
                            {{ number_format($activeQueues) }}
                        </p>
                    </div>

                </div>

                {{-- Live Queue Summary --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Live Queue Summary</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Current waiting queues across all offices.
                            </p>
                        </div>

                        <span class="inline-flex items-center gap-2 self-start px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            Live Updates
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-5 sm:px-6 py-4 font-semibold">Office</th>
                                    <th class="px-5 sm:px-6 py-4 font-semibold text-center">Waiting</th>
                                    <th class="px-5 sm:px-6 py-4 font-semibold text-right">Status</th>
                                </tr>
                            </thead>

                            <tbody id="queueTableBody" class="divide-y divide-gray-100">
                                @foreach ($offices as $office)
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <td class="px-5 sm:px-6 py-4 font-medium text-gray-900">
                                            {{ $office->name }}
                                        </td>

                                        <td class="px-5 sm:px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center min-w-9 px-3 py-1 rounded-full text-sm font-semibold {{ $office->waiting_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $office->waiting_count }}
                                            </span>
                                        </td>

                                        <td class="px-5 sm:px-6 py-4 text-right">
                                            @if ($office->waiting_count > 5)
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700">
                                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                                    Busy
                                                </span>
                                            @elseif ($office->waiting_count > 0)
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400">
                                                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                                    Idle
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Quick Access --}}
                <div>
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Quick Access</h3>
                        <p class="text-sm text-gray-500 mt-1">Frequently used system pages and tools.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 {{ Auth::user()->isSuperAdmin() ? 'xl:grid-cols-3' : '' }} gap-5">

                        <a href="{{ route('queue.history') }}"
                            class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md hover:border-emerald-200 transition">
                            <div class="flex items-start justify-between">
                                <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                        </path>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <h4 class="mt-5 font-bold text-gray-900">Visitor Logs</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                View past queue records, transactions, and visitor activity.
                            </p>
                        </a>

                        <a href="{{ route('queue.statistics') }}"
                            class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md hover:border-blue-200 transition">
                            <div class="flex items-start justify-between">
                                <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19V6l-2 2m2-2l2 2m4 11V4l-2 2m2-2l2 2m2 13v-8l-2 2m2-2l2 2">
                                        </path>
                                    </svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-blue-600 group-hover:translate-x-1 transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <h4 class="mt-5 font-bold text-gray-900">Statistics</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                View visitor trends and office activity reports.
                            </p>
                        </a>

                        @if (Auth::user()->isSuperAdmin())
                            <a href="{{ route('super-admin.user-activity.index') }}"
                                class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md hover:border-violet-200 transition">
                                <div class="flex items-start justify-between">
                                    <div class="h-12 w-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-300 group-hover:text-violet-600 group-hover:translate-x-1 transition"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                <h4 class="mt-5 font-bold text-gray-900">Activity Status</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    Monitor online, idle, and offline users across the system.
                                </p>
                            </a>
                        @endif

                    </div>
                </div>

            {{-- GUARD DASHBOARD --}}
            @elseif (Auth::user()->isGuard())

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">
                                    Guard Dashboard
                                </p>
                                <h3 class="text-2xl font-bold text-gray-900 mt-2">
                                    Welcome, {{ Auth::user()->name }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-2">
                                    Manage visitor entry, exit, and monitoring.
                                </p>
                            </div>

                            <div class="hidden sm:flex h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-600 items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-9.618 3.04A12.02 12.02 0 003 12c0 5.591 3.824 10.29 9 11.622C17.176 22.29 21 17.591 21 12c0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 p-5 sm:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                            <a href="{{ route('guard.visitors') }}"
                                class="group rounded-xl border border-emerald-100 bg-emerald-50/70 p-5 hover:bg-emerald-50 hover:shadow-sm transition">
                                <div class="h-11 w-11 rounded-xl bg-white text-emerald-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 5-4.03 9-9 9s-9-4-9-9 4.03-9 9-9 9 4 9 9z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="mt-4 font-bold text-gray-900">Visitor Monitoring</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    Monitor visitors currently inside the campus.
                                </p>
                                <span class="inline-flex items-center text-sm font-semibold text-emerald-700 mt-4">
                                    Open Monitoring
                                    <span class="ml-2 group-hover:translate-x-1 transition">→</span>
                                </span>
                            </a>

                            <a href="{{ route('guard.visitors.process') }}"
                                class="group rounded-xl border border-blue-100 bg-blue-50/70 p-5 hover:bg-blue-50 hover:shadow-sm transition">
                                <div class="h-11 w-11 rounded-xl bg-white text-blue-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="mt-4 font-bold text-gray-900">Visitor IN/OUT</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    Process visitor entry and exit.
                                </p>
                                <span class="inline-flex items-center text-sm font-semibold text-blue-700 mt-4">
                                    Process Visitor
                                    <span class="ml-2 group-hover:translate-x-1 transition">→</span>
                                </span>
                            </a>

                            <a href="{{ route('guard.history') }}"
                                class="group rounded-xl border border-gray-200 bg-gray-50/70 p-5 hover:bg-gray-50 hover:shadow-sm transition">
                                <div class="h-11 w-11 rounded-xl bg-white text-gray-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="mt-4 font-bold text-gray-900">Visitor History</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    View previous visitor transactions and activity.
                                </p>
                                <span class="inline-flex items-center text-sm font-semibold text-gray-700 mt-4">
                                    View History
                                    <span class="ml-2 group-hover:translate-x-1 transition">→</span>
                                </span>
                            </a>

                        </div>
                    </div>
                </div>

            {{-- STAFF / HEAD DASHBOARD --}}
            @else
                @php $office = Auth::user()->office; @endphp

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">
                                Staff Dashboard
                            </p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-2">
                                Welcome, {{ Auth::user()->name }}
                            </h3>

                            @if ($office)
                                <p class="text-sm text-gray-500 mt-2">
                                    Assigned to:
                                    <span class="font-semibold text-emerald-700">
                                        {{ $office->name }}
                                    </span>
                                </p>
                            @else
                                <p class="text-sm text-red-500 mt-2">No office assigned.</p>
                            @endif
                        </div>

                        <div class="hidden sm:flex h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-600 items-center justify-center">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    @if ($office)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mt-8">

                            <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-5">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">
                                        Currently Serving
                                    </p>
                                    <span class="h-9 w-9 rounded-lg bg-white text-emerald-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                                <p id="currentServing" class="mt-4 text-4xl font-bold text-emerald-700">
                                    {{ $currentServing?->ticket_number ?? '—' }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-5">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Waiting
                                    </p>
                                    <span class="h-9 w-9 rounded-lg bg-white text-blue-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                                <p id="waitingCount" class="mt-4 text-4xl font-bold text-gray-900">
                                    {{ $waitingCount }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-5">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Skipped
                                    </p>
                                    <span class="h-9 w-9 rounded-lg bg-white text-amber-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 5l7 7-7 7M4 5l7 7-7 7">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                                <p id="skippedCount" class="mt-4 text-4xl font-bold text-gray-900">
                                    {{ $skippedCount }}
                                </p>
                            </div>

                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('office.queue', $office->id) }}"
                                class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                Manage Queue
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Staff Quick Access --}}
                <div>
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Quick Access</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Access your queue records and reports.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <a href="{{ route('queue.history') }}"
                            class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md hover:border-emerald-200 transition">
                            <div class="flex items-center justify-between">
                                <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                        </path>
                                    </svg>
                                </div>
                                <span class="text-gray-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition">→</span>
                            </div>
                            <h4 class="mt-4 font-bold text-gray-900">Visitor Logs</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                View past queue records and visitor transactions.
                            </p>
                        </a>

                        <a href="{{ route('queue.statistics') }}"
                            class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md hover:border-blue-200 transition">
                            <div class="flex items-center justify-between">
                                <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19V6l-2 2m2-2l2 2m4 11V4l-2 2m2-2l2 2m2 13v-8l-2 2m2-2l2 2">
                                        </path>
                                    </svg>
                                </div>
                                <span class="text-gray-300 group-hover:text-blue-600 group-hover:translate-x-1 transition">→</span>
                            </div>
                            <h4 class="mt-4 font-bold text-gray-900">Statistics</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                View visitor trends and office activity reports.
                            </p>
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>

    @vite('resources/js/dashboard.js')
</x-app-layout>
