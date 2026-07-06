<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Visitor Logs
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6">

        <!-- Search & Filter -->
        <form method="GET" class="mb-4 flex flex-wrap items-center gap-2">

            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="Search ticket, name, office..."
                autocomplete="off"
                class="border rounded px-3 py-2 w-full md:w-64">

            <input type="date" name="date"
                value="{{ request('date') }}"
                class="border rounded px-3 py-2">

            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Search
            </button>

            <a href="{{ route('queue.history') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Reset
            </a>

        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Ticket</th>
                        <th class="p-3 text-left">Name</th>
                        <th class="p-3 text-left">Office</th>
                        <th class="p-3 text-left">Cashier</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $statusColors = [
                            'done' => 'bg-emerald-100 text-emerald-800',
                            'skipped' => 'bg-amber-100 text-amber-800',
                            'transferred' => 'bg-blue-100 text-blue-800',
                        ];
                    @endphp

                    @forelse($history as $h)

                        @php
                            $visitor = $h['visitor'] ?? $h;
                            $tickets = $h['tickets'] ?? [$h];
                        @endphp

                        <tr class="border-t">
                            <td class="p-3 font-semibold">
                                {{ $visitor->ticket_number }}
                            </td>

                            <td class="p-3">
                                {{ $visitor->name }}
                            </td>

                            <td class="p-3">
                                {{ $visitor->office->name }}
                            </td>

                            <td class="p-3">
                                {{ $visitor->cashier->name ?? '—' }}
                            </td>

                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize
                                    {{ $statusColors[$visitor->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $visitor->status }}
                                </span>
                            </td>

                            <td class="p-3 text-gray-600">
                                {{ $visitor->updated_at->format('M d, Y') }}
                            </td>

                            <td class="p-3">
                                <button
                                    class="infoBtn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded text-xs"
                                    data-name="{{ $visitor->name }}"
                                    data-contact="{{ $visitor->contact_number }}"
                                    data-tickets='@json($tickets)'>
                                    Info
                                </button>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7"
                                class="p-6 text-center text-gray-500">
                                No history found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- MODAL -->
        <div id="infoModal"
            class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

            <div class="bg-gray-50 rounded-xl shadow-xl w-full max-w-2xl p-6 relative">

                <h3 class="text-lg font-semibold mb-4">
                    Visitor Info
                </h3>

                <!-- Top Info -->
                <div class="space-y-1 text-sm">
                    <p><strong>Name:</strong> <span id="mName"></span></p>
                    <p><strong>Contact:</strong> <span id="mContact"></span></p>
                </div>

                <hr class="my-4">

                <h4 class="font-semibold mb-6">
                    Ticket History
                </h4>

                <!-- Timeline -->
                <div id="timeline" class="space-y-8"></div>

                <button id="closeModal"
                    class="absolute top-2 right-3 text-gray-500 hover:text-gray-700 text-xl">
                    &times;
                </button>

            </div>
        </div>

    </div>

    @vite('resources/js/history-modal.js')
</x-app-layout>
