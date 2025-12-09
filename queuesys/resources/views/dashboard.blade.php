<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (Auth::user()->isAdmin())
                <!-- Admin Dashboard -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <x-stats-card key="totalOffices" title="Total Offices" :value="$totalOffices">
                        <x-slot name="icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </x-slot>
                    </x-stats-card>
                    <x-stats-card key="totalStaff" title="Total Staff" :value="$totalStaff">
                        <x-slot name="icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </x-slot>
                    </x-stats-card>
                    <x-stats-card key="visitorsToday" title="Visitors Today" :value="$visitorsToday">
                        <x-slot name="icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </x-slot>
                    </x-stats-card>
                    <x-stats-card key="activeQueues" title="Active Queues" :value="$activeQueues">
                        <x-slot name="icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </x-slot>
                    </x-stats-card>
                </div>

                <x-card class="overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Live Queue Summary</h3>
                        <x-badge color="emerald">Live</x-badge>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-3 font-medium">Office</th>
                                    <th class="px-6 py-3 font-medium text-center">Waiting</th>
                                    <th class="px-6 py-3 font-medium text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody id="queueTableBody" class="divide-y divide-gray-100">
                                @foreach ($offices as $office)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $office->name }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $office->waiting_count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $office->waiting_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($office->waiting_count > 5)
                                                <span class="text-xs text-amber-600 font-medium">Busy</span>
                                            @elseif($office->waiting_count > 0)
                                                <span class="text-xs text-emerald-600 font-medium">Active</span>
                                            @else
                                                <span class="text-xs text-gray-400">Idle</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>

            @else
                <!-- Staff Dashboard -->
                @php $office = Auth::user()->office; @endphp
                <x-card>
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Welcome, {{ Auth::user()->name }}</h3>
                            @if ($office)
                                <p class="text-gray-500 mt-1">Assigned to: <span class="font-medium text-emerald-600">{{ $office->name }}</span></p>
                            @else
                                <p class="text-red-500 mt-1">No office assigned.</p>
                            @endif
                        </div>
                        <div class="h-12 w-12 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>

                    @if ($office)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-emerald-50 rounded-xl p-6 text-center border border-emerald-100">
                                <p class="text-sm font-medium text-emerald-600 uppercase tracking-wider">Currently Serving</p>
                                <p id="currentServing" class="mt-2 text-4xl font-bold text-emerald-700">—</p>
                            </div>
                            <div class="bg-white rounded-xl p-6 text-center border border-gray-100 shadow-sm">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Waiting</p>
                                <p id="waitingCount" class="mt-2 text-3xl font-bold text-gray-900">0</p>
                            </div>
                            <div class="bg-white rounded-xl p-6 text-center border border-gray-100 shadow-sm">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Skipped</p>
                                <p id="skippedCount" class="mt-2 text-3xl font-bold text-gray-900">0</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('office.queue', $office->id) }}"
                               class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-xl font-semibold text-white hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-emerald-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Manage Queue
                            </a>
                        </div>
                    @endif
                </x-card>
            @endif

        </div>
    </div>

    @vite('resources/js/dashboard.js')
</x-app-layout>
