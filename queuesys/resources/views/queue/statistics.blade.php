<x-app-layout>
    <x-slot name="title">Statistics</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Statistics</h2>
    </x-slot>

    <div class="flex justify-center mt-6 mb-8">
        <form method="GET" id="statisticsForm" class="flex flex-wrap gap-2 items-center">
            <select name="range" id="rangeSelect" class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm">
                <option value="weekly" {{ $range === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $range === 'monthly' ? 'selected' : '' }}>Monthly</option>
            </select>
            <select id="monthSelect" class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm">
                @foreach ($months as $num => $name)
                    <option value="{{ $num }}" {{ $selectedMonthNumber == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <div id="weekWrapper" class="{{ $range === 'monthly' ? 'hidden' : '' }}">
                <select name="week" id="weekSelect" class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm">
                    @foreach ($weeks as $weekNumber => $week)
                        <option value="{{ $weekNumber }}" {{ $selectedWeek == $weekNumber ? 'selected' : '' }}>Week {{ $weekNumber }} ({{ $week['start']->format('M j') }} – {{ $week['end']->format('M j') }})</option>
                    @endforeach
                </select>
            </div>
            <select id="yearSelect" class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm">
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <input type="hidden" name="month" id="combinedMonth" value="{{ $selectedMonth }}">
        </form>
    </div>

    <div class="max-w-6xl mx-auto mb-6 text-center">
        <p class="text-sm text-gray-500">Reporting Period</p>
        <p class="text-lg font-semibold text-gray-800">{{ $periodStart->format('M j, Y') }} <span class="text-gray-400 mx-1">–</span> {{ $periodEnd->format('M j, Y') }}</p>
    </div>

    @if ($role === 'staff')
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Total no. of Tickets</p>
                <p class="text-3xl font-bold mt-2">{{ $totalTickets }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Complete</p>
                <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $completed }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Skipped</p>
                <p class="text-3xl font-bold text-amber-500 mt-2">{{ $skipped }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Transferred</p>
                <p class="text-3xl font-bold text-orange-600 mt-2">{{ $transferred }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">No. of Students</p>
                <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $students }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">No. of Visitors</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $visitorsCount }}</p>
            </div>
        </div>
    @else
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Total no. Registration</p>
                <p class="text-3xl font-bold mt-2">{{ $totalRegistrations }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Total no. of Tickets</p>
                <p class="text-3xl font-bold mt-2">{{ $totalTickets }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Complete</p>
                <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $completed }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Skipped</p>
                <p class="text-3xl font-bold text-amber-500 mt-2">{{ $skipped }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Students</p>
                <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $students }}</p>
            </div>
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500 h-10 flex items-start">Visitors</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $visitorsCount }}</p>
            </div>
        </div>
    @endif

    @if ($role === 'admin')
        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-sm border overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-4 text-left">Office</th>
                            <th class="p-4 text-center">Registrations</th>
                            <th class="p-4 text-center">Tickets</th>
                            <th class="p-4 text-center">Complete</th>
                            <th class="p-4 text-center">Skipped</th>
                            <th class="p-4 text-center">Students</th>
                            <th class="p-4 text-center">Visitors</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($officeData as $office)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-4 font-medium">
                                    {{ $office['office'] }}
                                    @if ($office['isOther'])
                                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Other</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center font-semibold">{{ $office['totalRegistrations'] }}</td>
                                <td class="p-4 text-center font-semibold">{{ $office['total'] }}</td>
                                <td class="p-4 text-center text-emerald-600 font-semibold">{{ $office['completed'] }}</td>
                                <td class="p-4 text-center text-amber-600 font-semibold">{{ $office['skipped'] }}</td>                                <td class="p-4 text-center text-indigo-600 font-semibold">{{ $office['students'] }}</td>
                                <td class="p-4 text-center text-blue-600 font-semibold">{{ $office['visitors'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500">No statistics found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="max-w-6xl mx-auto py-4">
        <div class="bg-white shadow rounded-xl p-6 h-96">
            <canvas id="statsChart"></canvas>
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
