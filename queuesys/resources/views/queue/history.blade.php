<x-app-layout>
    <x-slot name="title">
        History
    </x-slot>

    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                Visitor Logs
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                View and track completed, skipped, and transferred visitors.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Search and Filters --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-4">
                    <h3 class="font-bold text-gray-900">Search and Filter</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Find visitor records using their ticket, name, office, or date.
                    </p>
                </div>

                <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-12">
                    <div class="md:col-span-6">
                        <label for="history-search" class="mb-1.5 block text-xs font-semibold text-gray-600">
                            Search Visitor
                        </label>

                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-5.2-5.2m1.7-4.3a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>

                            <input
                                id="history-search"
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Ticket number, name, office..."
                                autocomplete="off"
                                class="w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            >
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <label for="history-date" class="mb-1.5 block text-xs font-semibold text-gray-600">
                            Date
                        </label>

                        <input
                            id="history-date"
                            type="date"
                            name="date"
                            value="{{ request('date') }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                    </div>

                    <div class="flex items-end gap-2 md:col-span-3">
                        <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-5.2-5.2m1.7-4.3a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>

                        <a href="{{ route('queue.history') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </form>
            </section>

            {{-- History Table --}}
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Visitor Records</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Detailed records of visitor queue activity.
                        </p>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ $history->total() }} Records
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[850px] text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold">Ticket</th>
                                <th class="px-5 py-4 font-semibold">Visitor</th>

                                @if ($isAdmin)
                                    <th class="px-5 py-4 font-semibold">Office</th>
                                @endif

                                @if ($isStaff || $isHead)
                                    <th class="px-5 py-4 font-semibold">Course</th>
                                @endif

                                <th class="px-5 py-4 font-semibold">Cashier</th>
                                <th class="px-5 py-4 font-semibold">Status</th>
                                <th class="px-5 py-4 font-semibold">Date</th>
                                <th class="px-5 py-4 text-center font-semibold">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @php
                                $statusColors = [
                                    'done' => 'bg-emerald-100 text-emerald-800',
                                    'skipped' => 'bg-amber-100 text-amber-800',
                                    'transferred' => 'bg-blue-100 text-blue-800',
                                    'other' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp

                            @forelse ($history as $h)
                                @php
                                    $visitor = $h['visitor'] ?? $h;
                                    $tickets = $h['tickets'] ?? collect([$h]);

                                    $transferData = $visitor->transfers
                                        ->sortBy('transferred_at')
                                        ->map(function ($transfer) {
                                            return [
                                                'transferred_at' => $transfer->transferred_at,
                                                'from_queue_number' => $transfer->from_queue_number,
                                                'to_queue_number' => $transfer->to_queue_number,
                                                'from_office_id' => $transfer->from_office_id,
                                                'to_office_id' => $transfer->to_office_id,
                                                'from_office' => [
                                                    'name' => $transfer->fromOffice->name ?? '—',
                                                ],
                                                'to_office' => [
                                                    'name' => $transfer->toOffice->name ?? '—',
                                                ],
                                                'transferred_by' => [
                                                    'name' => $transfer->transferredBy->name ?? 'Unknown',
                                                ],
                                            ];
                                        })
                                        ->values();

                                    $displayStatus = $visitor->status;

                                    if (is_null($visitor->office_id)) {
                                        $displayStatus = 'other';
                                    }

                                    if ($isStaff || $isHead) {
                                        $officeTransfers = $visitor->transfers
                                            ->filter(function ($transfer) use ($staffOfficeId) {
                                                return $transfer->from_office_id == $staffOfficeId ||
                                                    $transfer->to_office_id == $staffOfficeId;
                                            })
                                            ->sortByDesc('transferred_at')
                                            ->values();

                                        $latestOfficeTransfer = $officeTransfers->first();

                                        if ($latestOfficeTransfer) {
                                            if ($latestOfficeTransfer->from_office_id == $staffOfficeId) {
                                                $displayStatus = 'transferred';
                                            }

                                            if (
                                                $latestOfficeTransfer->to_office_id == $staffOfficeId &&
                                                $visitor->office_id == $staffOfficeId
                                            ) {
                                                $displayStatus = $visitor->status;
                                            }
                                        }
                                    }
                                @endphp

                                <tr class="transition hover:bg-emerald-50/40">
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="font-bold text-emerald-700">
                                            {{ $visitor->ticket_number ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="font-semibold text-gray-900">
                                            {{ $visitor->name }}
                                        </span>
                                    </td>

                                    @if ($isAdmin)
                                        <td class="px-5 py-4 text-gray-600">
                                            @if (is_null($visitor->office_id))
                                                {{ $visitor->other_office ?? '—' }}
                                            @else
                                                {{ $visitor->office->name ?? '—' }}
                                            @endif
                                        </td>
                                    @endif

                                    @if ($isStaff || $isHead)
                                        <td class="px-5 py-4 text-gray-600">
                                            {{ $visitor->course->name ?? '—' }}
                                        </td>
                                    @endif

                                    <td class="px-5 py-4 text-gray-600">
                                        {{ $visitor->cashier->name ?? '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $displayStatus }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                        {{ $visitor->updated_at->timezone('Asia/Manila')->format('M d, Y') }}
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <button
                                            type="button"
                                            class="infoBtn inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                            data-name="{{ $visitor->name }}"
                                            data-contact="{{ $visitor->contact_number ?? '' }}"
                                            data-tickets='@json($tickets)'
                                            data-transfers='@json($transferData)'
                                            data-registration-office-id="{{ $visitor->registration_office_id }}"
                                            data-registration-office="{{ $visitor->registration_office_name }}"
                                            data-created-at="{{ $visitor->created_at }}"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c-1.5 4-4.5 6-9 6s-7.5-2-9-6c1.5-4 4.5-6 9-6s7.5 2 9 6z" />
                                            </svg>
                                            View Info
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($isAdmin ? 7 : 6) + (($isStaff || $isHead) ? 1 : 0) }}"
                                        class="px-5 py-16 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>

                                        <h4 class="mt-4 font-semibold text-gray-700">
                                            No Visitor Records Found
                                        </h4>

                                        <p class="mt-1 text-sm text-gray-500">
                                            Try changing your search or date filter.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($history->hasPages())
                    <div class="border-t border-gray-100 px-5 py-4 sm:px-6">
                        {{ $history->links() }}
                    </div>
                @endif
            </section>

        </div>
    </div>

    {{-- Visitor Info Modal --}}
    <div id="infoModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-950/60 p-4 backdrop-blur-sm">

        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl">

            {{-- Modal Header --}}
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 bg-white px-6 py-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">
                        Visitor Details
                    </p>

                    <h3 class="mt-1 text-xl font-bold text-gray-900">
                        Visitor Information
                    </h3>
                </div>

                <button id="closeModal" type="button"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                    aria-label="Close modal">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">

                {{-- Basic Information --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Visitor Name
                            </p>
                            <p id="mName" class="mt-1 font-semibold text-gray-900">
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Contact Number
                            </p>
                            <p id="mContact" class="mt-1 font-semibold text-gray-900">
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Ticket Timeline --}}
                <div class="mt-6">
                    <div class="mb-5">
                        <h4 class="font-bold text-gray-900">
                            Ticket History
                        </h4>
                        <p class="mt-1 text-sm text-gray-500">
                            Track the visitor's queue activity and office transfers.
                        </p>
                    </div>

                    <div id="timeline" class="space-y-8"></div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                <button type="button"
                    onclick="document.getElementById('closeModal').click()"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        window.isStaff = @json($isStaff);
        window.isHead = @json($isHead);
        window.staffOfficeId = @json($staffOfficeId);
    </script>

    @vite('resources/js/history-modal.js')
</x-app-layout>
