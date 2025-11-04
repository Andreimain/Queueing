<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (Auth::user()->isAdmin())
                <div class="space-y-8">
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-green-50 p-4 rounded-lg shadow text-center">
                            <p class="text-sm text-gray-500">Total Offices</p>
                            <p id="totalOffices" class="text-2xl font-bold text-green-700">{{ \App\Models\Office::count() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg shadow text-center">
                            <p class="text-sm text-gray-500">Total Staff</p>
                            <p id="totalStaff" class="text-2xl font-bold text-green-700">{{ \App\Models\User::where('role', 'staff')->count() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg shadow text-center">
                            <p class="text-sm text-gray-500">Visitors Today</p>
                            <p id="visitorsToday" class="text-2xl font-bold text-green-700">{{ \App\Models\Visitor::whereDate('created_at', today())->count() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg shadow text-center">
                            <p class="text-sm text-gray-500">Active Queues</p>
                            <p id="activeQueues" class="text-2xl font-bold text-green-700">{{ \App\Models\Office::has('visitors')->count() }}</p>
                        </div>
                    </div>

                    <!-- Queue Summary -->
                    <div class="bg-white shadow rounded-lg p-6 mt-8">
                        <h3 class="text-lg font-semibold text-green-700 mb-4">Live Queue Summary</h3>
                        <table class="w-full border border-green-300 text-sm">
                            <thead class="bg-green-600 text-black">
                                <tr>
                                    <th class="px-4 py-2 border border-green-700 text-left">Office</th>
                                    <th class="px-4 py-2 border border-green-700 text-center">Currently Serving</th>
                                    <th class="px-4 py-2 border border-green-700 text-center">Waiting</th>
                                </tr>
                            </thead>
                            <tbody id="queueTableBody">
                                @foreach (\App\Models\Office::all() as $office)
                                    <tr class="hover:bg-green-100">
                                        <td class="px-4 py-2 border border-green-300">{{ $office->name }}</td>
                                        <td class="px-4 py-2 border border-green-300 text-center">{{ $office->current_serving ?? '—' }}</td>
                                        <td class="px-4 py-2 border border-green-300 text-center">{{ $office->waiting_count ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-lg shadow p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-green-700">
                        Welcome, {{ Auth::user()->name }}
                    </h3>

                    @php
                        $office = Auth::user()->office;
                    @endphp

                    @if ($office)
                        <div class="text-gray-700">
                            <p class="font-medium">Office: <span class="text-green-700">{{ $office->name }}</span></p>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div class="bg-white border border-green-300 p-4 rounded-lg text-center shadow">
                                <p class="text-sm text-gray-500">Currently Serving</p>
                                <p id="currentServing" class="text-2xl font-bold text-green-700">{{ $office->current_serving ?? '—' }}</p>
                            </div>
                            <div class="bg-white border border-green-300 p-4 rounded-lg text-center shadow">
                                <p class="text-sm text-gray-500">Waiting</p>
                                <p id="waitingCount" class="text-2xl font-bold text-green-700">{{ $office->waiting_count ?? 0 }}</p>
                            </div>
                            <div class="bg-white border border-green-300 p-4 rounded-lg text-center shadow">
                                <p class="text-sm text-gray-500">Skipped</p>
                                <p id="skippedCount" class="text-2xl font-bold text-green-700">{{ $office->skipped_count ?? 0 }}</p>
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
    <script>
        function refreshDashboard() {
            fetch("{{ route('dashboard.data') }}")
                .then(res => res.json())
                .then(data => {
                    document.getElementById('totalOffices').textContent = data.totalOffices;
                    document.getElementById('totalStaff').textContent = data.totalStaff;
                    document.getElementById('visitorsToday').textContent = data.visitorsToday;
                    document.getElementById('activeQueues').textContent = data.activeQueues;

                    const tableBody = document.getElementById('queueTableBody');
                    tableBody.innerHTML = data.offices.map(office => `
                        <tr class="hover:bg-green-100">
                            <td class="px-4 py-2 border border-green-300">${office.name}</td>
                            <td class="px-4 py-2 border border-green-300 text-center">${office.current_serving || '—'}</td>
                            <td class="px-4 py-2 border border-green-300 text-center">${office.waiting_count}</td>
                        </tr>
                    `).join('');
                })
                .catch(err => console.error('Dashboard update error:', err));
        }
        setInterval(refreshDashboard, 1000);

        @if (Auth::user()->isStaff())
            function refreshStaffDashboard() {
                fetch("{{ route('dashboard.staff.data') }}")
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) return;
                        document.getElementById('currentServing').textContent = data.current_serving;
                        document.getElementById('waitingCount').textContent = data.waiting_count;
                        document.getElementById('skippedCount').textContent = data.skipped_count;
                    })
                    .catch(err => console.error('Staff dashboard update error:', err));
            }

            setInterval(refreshStaffDashboard, 1000);
            console.log('Staff auto-refresh active');
        @endif
    </script>
</x-app-layout>
