<x-app-layout>
    <x-slot name="title">
        Visitor History
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Visitor History
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6">

        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('guard.history') }}" class="mb-4 flex flex-wrap items-center gap-2">

            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search ticket, name, office..."
                autocomplete="off"
                class="border rounded px-3 py-2 w-full md:w-64"
            >

            <input
                type="date"
                name="date"
                value="{{ request('date') }}"
                class="border rounded px-3 py-2"
            >

            <button
                type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
            >
                Search
            </button>

            <a
                href="{{ route('guard.history') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
            >
                Reset
            </a>

        </form>

        <x-card>

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Visitor History
                    </h3>

                    <p class="text-sm text-gray-500">
                        History of visitors across all offices
                    </p>
                </div>

                <div class="text-sm text-gray-500">
                    {{ $visitors->total() }}
                    visitor{{ $visitors->total() !== 1 ? 's' : '' }}
                </div>
            </div>

            {{-- History Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 text-left">Ticket</th>
                            <th class="p-3 text-left">Visitor</th>
                            <th class="p-3 text-left">Office</th>
                            <th class="p-3 text-left">Window</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($visitors as $visitor)

                            <tr class="border-t hover:bg-gray-50">

                                {{-- Ticket --}}
                                <td class="p-3 font-semibold">
                                    {{ $visitor->ticket_number ?? '—' }}
                                </td>

                                {{-- Visitor --}}
                                <td class="p-3">
                                    {{ $visitor->name ?? '—' }}
                                </td>

                                {{-- Office --}}
                                <td class="p-3">
                                    @if ($visitor->office)
                                        {{ $visitor->office->name }}
                                    @elseif ($visitor->other_office)
                                        Others - {{ $visitor->other_office }}
                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- Window --}}
                                <td class="p-3">
                                    {{ $visitor->cashier?->name ?? '—' }}
                                </td>

                                {{-- Status --}}
                                <td class="p-3">

                                    @if ($visitor->status === 'done')

                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            Completed
                                        </span>

                                    @elseif ($visitor->status === 'skipped')

                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Skipped
                                        </span>

                                    @elseif ($visitor->status === 'transferred')

                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Transferred
                                        </span>

                                    @else

                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($visitor->status ?? 'Others') }}
                                        </span>

                                    @endif

                                </td>

                                {{-- Date --}}
                                <td class="p-3 text-gray-600">
                                    {{ $visitor->updated_at->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-500">
                                    No history found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            {{-- Pagination --}}
            @if ($visitors->hasPages())
                <div class="mt-6">
                    {{ $visitors->links() }}
                </div>
            @endif

        </x-card>

    </div>
</x-app-layout>
