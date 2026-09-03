<x-app-layout>

    <x-slot name="title">
        Statistics
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Statistics
        </h2>
    </x-slot>

    <div class="flex justify-center mt-6 mb-8">
        <form
            method="GET"
            id="statisticsForm"
            class="flex flex-wrap gap-2 items-center"
        >
            {{-- Report Type --}}
            <select
                name="range"
                id="rangeSelect"
                class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm"
            >
                <option
                    value="weekly"
                    {{ $range === 'weekly' ? 'selected' : '' }}
                >
                    Weekly
                </option>

                <option
                    value="monthly"
                    {{ $range === 'monthly' ? 'selected' : '' }}
                >
                    Monthly
                </option>
            </select>

            {{-- Month --}}
            <select
                id="monthSelect"
                class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm"
            >
                @foreach ($months as $num => $name)
                    <option
                        value="{{ $num }}"
                        {{ $selectedMonthNumber == $num ? 'selected' : '' }}
                    >
                        {{ $name }}
                    </option>
                @endforeach
            </select>

            {{-- Week --}}
            <div
                id="weekWrapper"
                class="{{ $range === 'monthly' ? 'hidden' : '' }}"
            >
                <select
                    name="week"
                    id="weekSelect"
                    class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm"
                >
                    @foreach ($weeks as $weekNumber => $week)
                        <option
                            value="{{ $weekNumber }}"
                            {{ $selectedWeek == $weekNumber ? 'selected' : '' }}
                        >
                            Week {{ $weekNumber }}
                            ({{ $week['start']->format('M j') }} –
                            {{ $week['end']->format('M j') }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Year --}}
            <select
                id="yearSelect"
                class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm"
            >
                @foreach ($years as $year)
                    <option
                        value="{{ $year }}"
                        {{ $selectedYear == $year ? 'selected' : '' }}
                    >
                        {{ $year }}
                    </option>
                @endforeach
            </select>

            {{-- YYYY-MM --}}
            <input
                type="hidden"
                name="month"
                id="combinedMonth"
                value="{{ $selectedMonth }}"
            >
        </form>
    </div>

    {{-- Reporting Period --}}
    <div class="max-w-6xl mx-auto mb-6 text-center">
        <p class="text-sm text-gray-500">
            Reporting Period
        </p>

        <p class="text-lg font-semibold text-gray-800">
            {{ $periodStart->format('M j, Y') }}

            <span class="text-gray-400 mx-1">
                –
            </span>

            {{ $periodEnd->format('M j, Y') }}
        </p>
    </div>

    {{-- Staff Statistics --}}
    @if ($role === 'staff')
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">

            {{-- Total --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Total Tickets
                </p>

                <p class="text-3xl font-bold mt-2">
                    {{ $totalTickets }}
                </p>
            </div>

            {{-- Completed --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Completed
                </p>

                <p class="text-3xl font-bold text-emerald-600 mt-2">
                    {{ $completed }}
                </p>
            </div>

            {{-- Skipped --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Skipped
                </p>

                <p class="text-3xl font-bold text-amber-500 mt-2">
                    {{ $skipped }}
                </p>
            </div>

            {{-- Transferred --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Transferred
                </p>

                <p class="text-3xl font-bold text-orange-600 mt-2">
                    {{ $transferred }}
                </p>
            </div>

            {{-- Students --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Students
                </p>

                <p class="text-3xl font-bold text-indigo-600 mt-2">
                    {{ $students }}
                </p>
            </div>

            {{-- Visitors --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Visitors
                </p>

                <p class="text-3xl font-bold text-blue-600 mt-2">
                    {{ $visitorsCount }}
                </p>
            </div>

        </div>
    @endif

    {{-- Admin Statistics --}}
    @if ($role === 'admin')
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">

            {{-- Offices --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Offices
                </p>

                <p class="text-3xl font-bold mt-2">
                    {{ $officeData->count() }}
                </p>
            </div>

            {{-- Total --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Total Tickets
                </p>

                <p class="text-3xl font-bold mt-2">
                    {{ $totalTickets }}
                </p>
            </div>

            {{-- Completed --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Completed
                </p>

                <p class="text-3xl font-bold text-emerald-600 mt-2">
                    {{ $completed }}
                </p>
            </div>

            {{-- Skipped --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Skipped
                </p>

                <p class="text-3xl font-bold text-amber-500 mt-2">
                    {{ $skipped }}
                </p>
            </div>

            {{-- Transferred --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Transferred
                </p>

                <p class="text-3xl font-bold text-orange-600 mt-2">
                    {{ $transferred }}
                </p>
            </div>

            {{-- Students --}}
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">
                    Students
                </p>

                <p class="text-3xl font-bold text-indigo-600 mt-2">
                    {{ $students }}
                </p>
            </div>

        </div>
    @endif

    {{-- Admin Office Table --}}
    @if ($role === 'admin')
        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-sm border overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-4 text-left">
                                Office
                            </th>

                            <th class="p-4 text-center">
                                Total Tickets
                            </th>

                            <th class="p-4 text-center">
                                Completed
                            </th>

                            <th class="p-4 text-center">
                                Skipped
                            </th>

                            <th class="p-4 text-center">
                                Transferred
                            </th>

                            <th class="p-4 text-center">
                                Students
                            </th>

                            <th class="p-4 text-center">
                                Visitors
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($officeData as $office)
                            <tr class="border-t hover:bg-gray-50">

                                <td class="p-4 font-medium">
                                    {{ $office['office'] }}
                                </td>

                                <td class="p-4 text-center font-semibold">
                                    {{ $office['total'] }}
                                </td>

                                <td class="p-4 text-center text-emerald-600 font-semibold">
                                    {{ $office['completed'] }}
                                </td>

                                <td class="p-4 text-center text-amber-600 font-semibold">
                                    {{ $office['skipped'] }}
                                </td>

                                <td class="p-4 text-center text-orange-600 font-semibold">
                                    {{ $office['transferred'] }}
                                </td>

                                <td class="p-4 text-center text-indigo-600 font-semibold">
                                    {{ $office['students'] }}
                                </td>

                                <td class="p-4 text-center text-blue-600 font-semibold">
                                    {{ $office['visitors'] }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    @endif

    {{-- Chart --}}
    <div class="max-w-6xl mx-auto py-4">
        <div class="bg-white shadow rounded-xl p-6 h-96">
            <canvas id="statsChart"></canvas>
        </div>
    </div>

    {{-- Chart Data --}}
    <script>
        window.statisticsData = {
            role: @json($role),
            range: @json($range),

            labels: @json(
                $role === 'staff'
                    ? $labels
                    : $officeLabels
            ),

            counts: @json(
                $role === 'staff'
                    ? $counts
                    : $officeCounts
            ),

            totalTickets: @json($totalTickets),
            completed: @json($completed),
            skipped: @json($skipped),
            transferred: @json($transferred),
        };
    </script>

    @vite('resources/js/statistics.js')

</x-app-layout>
