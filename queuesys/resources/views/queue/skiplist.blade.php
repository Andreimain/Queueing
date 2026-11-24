<x-app-layout>
    <x-slot name="title">
        Skipped Visitors
    </x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Skipped Visitors
            <span class="ml-2 text-sm font-normal text-gray-500">
                Today: <span id="current-time"></span>
            </span>
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Search --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <form method="GET" action="{{ route('skipped.list') }}" id="searchForm"
                    class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                    <div class="w-full sm:max-w-md">
                        <label for="searchInput"
                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Search visitors
                        </label>

                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" stroke-width="2" />
                                <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4" />
                            </svg>

                            <input
                                type="text"
                                name="q"
                                id="searchInput"
                                value="{{ request('q') }}"
                                placeholder="Search ticket or visitor name..."
                                autocomplete="off"
                                class="w-full rounded-xl border-gray-200 py-2.5 pl-10 pr-3 text-sm shadow-sm transition focus:border-amber-500 focus:ring-amber-500"
                            >
                        </div>
                    </div>

                    <div class="text-xs text-gray-400">
                        Search by visitor name or ticket number.
                    </div>
                </form>
            </div>

            {{-- Skipped Visitors Table --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h4 class="font-semibold text-gray-900">
                            Skipped Queue List
                        </h4>
                        <p class="mt-0.5 text-sm text-gray-500">
                            Select one or more visitors to perform an action.
                        </p>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                        {{ $skipped->total() }} Records
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table id="skippedTable" class="w-full min-w-[750px] text-sm">

                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <th class="w-14 px-5 py-4 text-center">
                                    <input
                                        type="checkbox"
                                        id="select-all"
                                        aria-label="Select all visitors"
                                        class="h-4 w-4 cursor-pointer rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                    >
                                </th>
                                <th class="px-5 py-4">Ticket</th>
                                <th class="px-5 py-4">Full Name</th>
                                <th class="px-5 py-4">Office</th>
                                <th class="px-5 py-4">Skipped At</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($skipped as $visitor)
                                <tr class="transition hover:bg-amber-50/40">

                                    <td class="px-5 py-4 text-center">
                                        <input
                                            type="checkbox"
                                            name="selected[]"
                                            value="{{ $visitor->id }}"
                                            class="row-checkbox h-4 w-4 cursor-pointer rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                            aria-label="Select {{ $visitor->name }}"
                                        >
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-lg bg-amber-50 px-2.5 py-1 font-mono text-xs font-bold text-amber-700">
                                            {{ $visitor->ticket_number }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="font-semibold text-gray-900">
                                            {{ $visitor->name }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-gray-600">
                                        {{ $visitor->office->name }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                                                <path stroke-linecap="round" stroke-width="1.8"
                                                    d="M12 7v5l3 2" />
                                            </svg>
                                            {{ $visitor->updated_at->timezone('Asia/Manila')->format('h:i A') }}
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                    d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </div>
                                        <p class="mt-4 font-semibold text-gray-700">
                                            No skipped visitors today
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            There are currently no skipped visitors to display.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Queue Actions --}}
                <div class="flex flex-col gap-3 border-t border-gray-100 bg-gray-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

                    <p class="text-xs text-gray-500">
                        Select visitors from the list to enable queue actions.
                    </p>

                    <div class="flex flex-col gap-2 sm:flex-row">

                        {{-- Restore --}}
                        <form method="POST" action="{{ route('skipped.restore') }}" id="restoreForm">
                            @csrf
                            <input type="hidden" name="selected_ids" id="selectedIds">

                            <button
                                type="submit"
                                id="restoreButton"
                                disabled
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40 sm:w-auto"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12a9 9 0 1 0 3-6.7L3 8m0-5v5h5" />
                                </svg>
                                Restore Selected
                            </button>
                        </form>

                        {{-- Swap --}}
                        <form method="POST" action="{{ route('skipped.swap') }}" id="swapForm">
                            @csrf
                            <input type="hidden" name="selected_id" id="swapSelectedId">

                            <button
                                type="submit"
                                id="swapButton"
                                disabled
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40 sm:w-auto"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h11l-3-3m3 3-3 3M17 17H6l3 3m-3-3 3-3" />
                                </svg>
                                Swap Selected
                            </button>
                        </form>

                    </div>
                </div>

                {{-- Pagination --}}
                @if ($skipped->hasPages())
                    <div class="border-t border-gray-100 px-5 py-4 sm:px-6">
                        {{ $skipped->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    @vite('resources/js/skipped.js')
</x-app-layout>
