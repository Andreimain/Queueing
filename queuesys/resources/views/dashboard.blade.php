<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Dashboard</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (Auth::user()->isAdmin())
                <!-- Admin Dashboard -->
                <div class="space-y-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-green-50 p-4 rounded-lg shadow text-center">
                            <p class="text-sm text-gray-500">Total Offices</p>
                            <p id="totalOffices" class="text-2xl font-bold text-green-700">{{ $totalOffices }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg shadow text-center">
                            <p class="text-sm text-gray-500">Total Staff</p>
                            <p id="totalStaff" class="text-2xl font-bold text-green-700">{{ $totalStaff }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg shadow text-center">
                            <p class="text-sm text-gray-500">Visitors Today</p>
                            <p id="visitorsToday" class="text-2xl font-bold text-green-700">{{ $visitorsToday }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg shadow text-center">
                            <p class="text-sm text-gray-500">Active Queues</p>
                            <p id="activeQueues" class="text-2xl font-bold text-green-700">{{ $activeQueues }}</p>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6 mt-8">
                        <h3 class="text-lg font-semibold text-green-700 mb-4">Live Queue Summary</h3>
                        <table class="w-full border border-green-300 text-sm">
                            <thead class="bg-green-600 text-black">
                                <tr>
                                    <th class="px-4 py-2 border border-green-700 text-left">Office</th>
                                    <th class="px-4 py-2 border border-green-700 text-center">Waiting</th>
                                </tr>
                            </thead>
                            <tbody id="queueTableBody">
                                @foreach ($offices as $office)
                                    <tr class="hover:bg-green-100">
                                        <td class="px-4 py-2 border border-green-300">{{ $office->name }}</td>
                                        <td class="px-4 py-2 border border-green-300 text-center">{{ $office->waiting_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                <!-- Staff Dashboard -->
                @php $office = Auth::user()->office; @endphp
                <div class="bg-green-50 border border-green-200 rounded-lg shadow p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-green-700">Welcome, {{ Auth::user()->name }}</h3>

                    @if ($office)
                        <p class="font-medium text-gray-700">Office: <span class="text-green-700">{{ $office->name }}</span></p>

                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div class="bg-white border border-green-300 p-4 rounded-lg text-center shadow">
                                <p class="text-sm text-gray-500">Currently Serving</p>
                                <p id="currentServing" class="text-2xl font-bold text-green-700">—</p>
                            </div>
                            <div class="bg-white border border-green-300 p-4 rounded-lg text-center shadow">
                                <p class="text-sm text-gray-500">Waiting</p>
                                <p id="waitingCount" class="text-2xl font-bold text-green-700">0</p>
                            </div>
                            <div class="bg-white border border-green-300 p-4 rounded-lg text-center shadow">
                                <p class="text-sm text-gray-500">Skipped</p>
                                <p id="skippedCount" class="text-2xl font-bold text-green-700">0</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('office.queue', $office->id) }}"
                               class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg font-semibold shadow hover:bg-green-700">
                               Go to Queue Page
                            </a>
                        </div>
                    @else
                        <p class="text-gray-600">No office assigned.</p>
                    @endif
                </div>
            @endif

        </div>
    </div>

    @vite('resources/js/dashboard.js')
</x-app-layout>
