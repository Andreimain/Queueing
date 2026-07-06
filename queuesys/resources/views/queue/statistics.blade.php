<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Statistics
        </h2>
    </x-slot>

    <div class="flex justify-center mt-6 mb-8 gap-2">
        <form method="GET" id="statisticsForm" class="flex gap-2 items-center">
            <select name="range" onchange="this.form.submit()"
                class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm
                       focus:ring-emerald-500 focus:border-emerald-500">
                <option value="weekly" {{ $range === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $range === 'monthly' ? 'selected' : '' }}>Monthly</option>
            </select>

            @php
                $currentYear = now('Asia/Manila')->year;
                $startYear = $currentYear - 5;
                $endYear = $currentYear + 1;

                $selectedMonthValue = $selectedMonth ?? now()->format('Y-m');
                [$selectedYear, $selectedMonthNumber] = explode('-', $selectedMonthValue);

                $months = [
                    '01' => 'January',
                    '02' => 'February',
                    '03' => 'March',
                    '04' => 'April',
                    '05' => 'May',
                    '06' => 'June',
                    '07' => 'July',
                    '08' => 'August',
                    '09' => 'September',
                    '10' => 'October',
                    '11' => 'November',
                    '12' => 'December',
                ];
            @endphp

            <select id="monthSelect"
                class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm
                       focus:ring-emerald-500 focus:border-emerald-500">
                @foreach ($months as $num => $name)
                    <option value="{{ $num }}"
                        {{ $selectedMonthNumber == $num ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>

            <select id="yearSelect"
                class="px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm shadow-sm
                       focus:ring-emerald-500 focus:border-emerald-500">
                @for ($year = $startYear; $year <= $endYear; $year++)
                    <option value="{{ $year }}"
                        {{ $selectedYear == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endfor
            </select>

            <input type="hidden" name="month" id="combinedMonth" value="{{ $selectedMonthValue }}">
        </form>
    </div>

    @if ($role === 'staff' && $range === 'monthly')
        <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            @foreach ($weekCounts as $week => $count)
                <div class="bg-white border rounded-xl p-4 text-center shadow-sm">
                    <p class="text-sm text-gray-500">Week {{ $week }}</p>
                    <p class="text-xs text-gray-400">
                        ({{ $weekRanges[$week]['start'] }} – {{ $weekRanges[$week]['end'] }})
                    </p>
                    <p class="text-2xl font-bold text-emerald-600 mt-2">{{ $count }}</p>
                </div>
            @endforeach
        </div>
    @endif

    @if ($role === 'admin')
        <div class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-emerald-600 text-white rounded-xl p-5 shadow">
                <p class="text-sm opacity-80">Overall Visitors</p>
                <p class="text-3xl font-bold mt-2">{{ $officeCounts->sum() }}</p>
            </div>

            @foreach ($officeData as $office)
                <div class="bg-white border rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">{{ $office->office }}</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-2">{{ $office->total }}</p>
                    <p class="text-xs text-gray-400 mt-1">Total Visitors</p>
                </div>
            @endforeach
        </div>
    @endif

    <div class="max-w-5xl mx-auto py-4">
        <div class="bg-white shadow rounded-xl p-6 h-96">
            <canvas id="statsChart"></canvas>
        </div>
    </div>

    <script>
        window.statisticsData = {
            role: @json($role),
            labels: @json($role === 'staff' ? $labels : $officeLabels),
            counts: @json($role === 'staff' ? $counts : $officeCounts)
        };
    </script>

    @vite('resources/js/statistics.js')
</x-app-layout>
