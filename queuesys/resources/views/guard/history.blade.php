<x-app-layout>
    <x-slot name="title">
        Visitor History
    </x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Visitor History
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Search & Filter --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <form method="GET" action="{{ route('guard.history') }}"
                    class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_220px_auto_auto]">

                    <div>
                        <label for="historySearch" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Search visitors
                        </label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" stroke-width="2" />
                                <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4" />
                            </svg>
                            <input
                                id="historySearch"
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Ticket, name, office..."
                                autocomplete="off"
                                class="w-full rounded-xl border-gray-200 py-2.5 pl-10 pr-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="historyDate" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Date
                        </label>
                        <input
                            id="historyDate"
                            type="date"
                            name="date"
                            value="{{ request('date') }}"
                            class="w-full rounded-xl border-gray-200 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div class="flex items-end">
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" stroke-width="2" />
                                <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4" />
                            </svg>
                            Search
                        </button>
                    </div>

                    <div class="flex items-end">
                        <a
                            href="{{ route('guard.history') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 sm:w-auto"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h5M5 9a8 8 0 1 1-1 5" />
                            </svg>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Visitor History Table --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h4 class="font-semibold text-gray-900">
                            Visitor Records
                        </h4>
                        <p class="mt-0.5 text-sm text-gray-500">
                            Complete visitor entry and exit records.
                        </p>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {{ $visitors->total() }} Total Records
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[950px] text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="px-5 py-4">Ticket</th>
                                <th class="px-5 py-4">Visitor</th>
                                <th class="px-5 py-4">Office</th>
                                <th class="px-5 py-4">Window</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4">IN Time</th>
                                <th class="px-5 py-4">OUT Time</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($visitors as $visitor)
                                <tr class="transition hover:bg-gray-50/80">

                                    {{-- Ticket --}}
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-lg bg-gray-100 px-2.5 py-1 font-mono text-xs font-bold text-gray-700">
                                            {{ $visitor->ticket_number ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- Visitor --}}
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $visitor->name ?? '—' }}
                                        </div>
                                    </td>

                                    {{-- Office --}}
                                    <td class="px-5 py-4">
                                        @if ($visitor->office)
                                            <span class="text-gray-700">
                                                {{ $visitor->office->name }}
                                            </span>
                                        @elseif ($visitor->other_office)
                                            <span class="text-gray-700">
                                                Others - {{ $visitor->other_office }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    {{-- Window --}}
                                    <td class="px-5 py-4">
                                        <span class="text-gray-700">
                                            {{ $visitor->cashier?->name ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="whitespace-nowrap px-5 py-4">
                                        @if ($visitor->status === 'done')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                Completed
                                            </span>
                                        @elseif ($visitor->status === 'skipped')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                Skipped
                                            </span>
                                        @elseif ($visitor->status === 'transferred')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                Transferred
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                {{ ucfirst($visitor->status ?? 'Others') }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- IN Time --}}
                                    <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                        @if ($visitor->in_at)
                                            <div class="font-medium text-gray-700">
                                                {{ $visitor->in_at->timezone('Asia/Manila')->format('M d, Y') }}
                                            </div>
                                            <div class="mt-0.5 text-xs text-gray-400">
                                                {{ $visitor->in_at->timezone('Asia/Manila')->format('h:i A') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    {{-- OUT Time --}}
                                    <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                        @if ($visitor->out_at)
                                            <div class="font-medium text-gray-700">
                                                {{ $visitor->out_at->timezone('Asia/Manila')->format('M d, Y') }}
                                            </div>
                                            <div class="mt-0.5 text-xs text-gray-400">
                                                {{ $visitor->out_at->timezone('Asia/Manila')->format('h:i A') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-16 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                                    d="M8 7h8m-8 4h8m-8 4h5M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                                            </svg>
                                        </div>
                                        <p class="mt-4 font-semibold text-gray-700">
                                            No visitor history found
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Try changing your search terms or date filter.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($visitors->hasPages())
                    <div class="border-t border-gray-100 px-5 py-4 sm:px-6">
                        {{ $visitors->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
