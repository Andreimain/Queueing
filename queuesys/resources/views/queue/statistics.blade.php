<x-app-layout>
    <x-slot name="title">Statistics</x-slot>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Statistics
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                View and monitor queue activity and registration statistics.
            </p>
        </div>
    </x-slot>

    @php
        $statCards = $role === 'staff' || $role === 'head'
            ? [
                ['label' => 'Total Tickets', 'value' => $totalTickets, 'color' => 'gray', 'icon' => 'ticket'],
                ['label' => 'Completed', 'value' => $completed, 'color' => 'emerald', 'icon' => 'check'],
                ['label' => 'Skipped', 'value' => $skipped, 'color' => 'amber', 'icon' => 'skip'],
                ['label' => 'Transferred', 'value' => $transferred, 'color' => 'orange', 'icon' => 'transfer'],
                ['label' => 'Students', 'value' => $students, 'color' => 'indigo', 'icon' => 'student'],
                ['label' => 'Visitors', 'value' => $visitorsCount, 'color' => 'blue', 'icon' => 'visitor'],
            ]
            : [
                ['label' => 'Registrations', 'value' => $totalRegistrations, 'color' => 'gray', 'icon' => 'register'],
                ['label' => 'Total Tickets', 'value' => $totalTickets, 'color' => 'gray', 'icon' => 'ticket'],
                ['label' => 'Completed', 'value' => $completed, 'color' => 'emerald', 'icon' => 'check'],
                ['label' => 'Skipped', 'value' => $skipped, 'color' => 'amber', 'icon' => 'skip'],
                ['label' => 'Students', 'value' => $students, 'color' => 'indigo', 'icon' => 'student'],
                ['label' => 'Visitors', 'value' => $visitorsCount, 'color' => 'blue', 'icon' => 'visitor'],
            ];

        $colorClasses = [
            'gray' => 'bg-gray-100 text-gray-700',
            'emerald' => 'bg-emerald-100 text-emerald-700',
            'amber' => 'bg-amber-100 text-amber-700',
            'orange' => 'bg-orange-100 text-orange-700',
            'indigo' => 'bg-indigo-100 text-indigo-700',
            'blue' => 'bg-blue-100 text-blue-700',
        ];

        $valueClasses = [
            'gray' => 'text-gray-900',
            'emerald' => 'text-emerald-600',
            'amber' => 'text-amber-600',
            'orange' => 'text-orange-600',
            'indigo' => 'text-indigo-600',
            'blue' => 'text-blue-600',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filters --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">
                            Report Filters
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Select the period you want to view.
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-2 text-xs font-medium text-green-700 bg-green-50 border border-green-100 rounded-full px-3 py-1.5 w-fit">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        Statistics Overview
                    </span>
                </div>

                <form method="GET" id="statisticsForm"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    <div>
                        <label for="rangeSelect" class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Report Type
                        </label>
                        <select name="range" id="rangeSelect"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="weekly" {{ $range === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $range === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>

                    <div>
                        <label for="monthSelect" class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Month
                        </label>
                        <select id="monthSelect"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                            @foreach ($months as $num => $name)
                                <option value="{{ $num }}" {{ $selectedMonthNumber == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="weekWrapper" class="{{ $range === 'monthly' ? 'hidden' : '' }}">
                        <label for="weekSelect" class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Week
                        </label>
                        <select name="week" id="weekSelect"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                            @foreach ($weeks as $weekNumber => $week)
                                <option value="{{ $weekNumber }}" {{ $selectedWeek == $weekNumber ? 'selected' : '' }}>
                                    Week {{ $weekNumber }} ({{ $week['start']->format('M j') }} – {{ $week['end']->format('M j') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="yearSelect" class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Year
                        </label>
                        <select id="yearSelect"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="month" id="combinedMonth" value="{{ $selectedMonth }}">
                </form>
            </div>

            {{-- Reporting Period --}}
            <div class="bg-gradient-to-r from-green-700 to-emerald-600 rounded-2xl p-5 sm:p-6 text-white shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-sm text-green-100 font-medium">
                            Reporting Period
                        </p>
                        <h3 class="text-xl sm:text-2xl font-bold mt-1">
                            {{ $periodStart->format('M j, Y') }}
                            <span class="text-green-200 mx-1">–</span>
                            {{ $periodEnd->format('M j, Y') }}
                        </h3>
                    </div>

                    <div class="rounded-xl bg-white/15 border border-white/20 px-4 py-3 w-fit">
                        <p class="text-xs text-green-100">Report Type</p>
                        <p class="font-semibold capitalize mt-0.5">{{ $range }} Report</p>
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($statCards as $card)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                                {{ $card['label'] }}
                            </p>

                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $colorClasses[$card['color']] }}">
                                @if ($card['icon'] === 'check')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @elseif ($card['icon'] === 'skip')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5l14 14M19 5L5 19"/>
                                    </svg>
                                @elseif ($card['icon'] === 'transfer')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h11l-3-3m3 3l-3 3M17 17H6l3 3m-3-3l3-3"/>
                                    </svg>
                                @elseif ($card['icon'] === 'student')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7m-6-10v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/>
                                    </svg>
                                @elseif ($card['icon'] === 'visitor')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m12-13a4 4 0 11-8 0 4 4 0 018 0zm2 5h4m-2-2v4"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14H4V6a1 1 0 011-1z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <p class="text-2xl sm:text-3xl font-bold mt-4 {{ $valueClasses[$card['color']] }}">
                            {{ number_format($card['value']) }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Office Statistics --}}
            @if ($role === 'admin')
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-5 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800">
                            Office Statistics
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Registration and queue activity by office.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-5 py-4 text-left font-semibold whitespace-nowrap">Office</th>
                                    <th class="px-5 py-4 text-center font-semibold whitespace-nowrap">Registrations</th>
                                    <th class="px-5 py-4 text-center font-semibold whitespace-nowrap">Tickets</th>
                                    <th class="px-5 py-4 text-center font-semibold whitespace-nowrap">Completed</th>
                                    <th class="px-5 py-4 text-center font-semibold whitespace-nowrap">Skipped</th>
                                    <th class="px-5 py-4 text-center font-semibold whitespace-nowrap">Students</th>
                                    <th class="px-5 py-4 text-center font-semibold whitespace-nowrap">Visitors</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse ($officeData as $office)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-5 py-4 font-medium text-gray-800 whitespace-nowrap">
                                            {{ $office['office'] }}

                                            @if ($office['isOther'])
                                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                                    Other
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-4 text-center font-semibold text-gray-700">
                                            {{ number_format($office['totalRegistrations']) }}
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold text-gray-700">
                                            {{ number_format($office['total']) }}
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold text-emerald-600">
                                            {{ number_format($office['completed']) }}
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold text-amber-600">
                                            {{ number_format($office['skipped']) }}
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold text-indigo-600">
                                            {{ number_format($office['students']) }}
                                        </td>
                                        <td class="px-5 py-4 text-center font-semibold text-blue-600">
                                            {{ number_format($office['visitors']) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4"/>
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-medium text-gray-700">
                                                    No statistics found
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    There is no office data for this reporting period.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Chart --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-5 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">
                        {{ $role === 'admin' ? 'Registration Trends' : 'Queue Activity Trends' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Activity for {{ $periodStart->format('M j') }} – {{ $periodEnd->format('M j, Y') }}.
                    </p>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="relative h-80 sm:h-96">
                        <canvas id="statsChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        window.statisticsData = {
            role: @json($role),
            range: @json($range),
            labels: @json($role === 'staff' ? $labels : $registrationLabels),
            counts: @json($role === 'staff' ? $counts : $registrationCounts),
            totalRegistrations: @json($role === 'admin' ? $totalRegistrations : 0),
            totalTickets: @json($totalTickets),
            completed: @json($completed),
            skipped: @json($skipped),
            transferred: @json($transferred),
            students: @json($students),
            visitors: @json($visitorsCount),
            others: @json($role === 'admin' ? $others : 0)
        };
    </script>

    @vite('resources/js/statistics.js')
</x-app-layout>
