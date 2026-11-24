<x-app-layout>
    <x-slot name="title">
        Visitor Monitoring
    </x-slot>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">
                Visitor Monitoring
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Monitor visitors currently inside the campus.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            {{-- Page Overview --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19.128a9.38 9.38 0 0 0 3.75.772 9.38 9.38 0 0 0 3.75-.772M15 19.128v-.003c0-1.487-.37-2.887-1.023-4.115M15 19.128v.003c0 .405-.04.802-.116 1.187A9.37 9.37 0 0 1 12 20.75a9.37 9.37 0 0 1-2.884-.432A4.5 4.5 0 0 1 9 19.128m6 0a6 6 0 0 0-12 0m12 0v.003m-12-.003v.003m0 0A9.38 9.38 0 0 0 6.75 20.75c.99 0 1.94-.153 2.834-.437M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm6 0a2.25 2.25 0 1 0 0-4.5A2.25 2.25 0 0 0 18 12Z"/>
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Current Visitors
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Visitors currently inside the campus
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="inline-flex w-fit items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        <span id="visitorCount" class="text-sm font-semibold text-emerald-700">
                            {{ $visitors->count() }}
                            visitor{{ $visitors->count() !== 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Visitor ID Cards --}}
                <div class="p-5 sm:p-6">
                    <div id="visitorCards" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                        @forelse($visitors as $visitor)
                            <div class="visitor-card flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                                {{-- Visitor Photo --}}
                                <div class="relative flex h-56 items-center justify-center overflow-hidden bg-gray-100">
                                    @if ($visitor->photo_path)
                                        <img
                                            src="{{ route('guard.visitors.photo', $visitor) }}"
                                            alt="Visitor photo"
                                            class="h-full w-full object-cover"
                                        >
                                    @else
                                        <div class="flex flex-col items-center gap-2 text-gray-400">
                                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 0 0 3.75.772 9.38 9.38 0 0 0 3.75-.772M15 19.128v-.003c0-1.487-.37-2.887-1.023-4.115M15 19.128v.003c0 .405-.04.802-.116 1.187A9.37 9.37 0 0 1 12 20.75a9.37 9.37 0 0 1-2.884-.432A4.5 4.5 0 0 1 9 19.128m6 0a6 6 0 0 0-12 0m12 0v.003m-12-.003v.003m0 0A9.38 9.38 0 0 0 6.75 20.75c.99 0 1.94-.153 2.834-.437M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                            </svg>
                                            <span class="text-sm font-medium">No Photo Available</span>
                                        </div>
                                    @endif

                                    {{-- Presence Badge --}}
                                    <div class="absolute right-3 top-3">
                                        @if ($visitor->presence_status === 'in')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-white/95 px-3 py-1.5 text-xs font-bold text-emerald-700 shadow-sm">
                                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                INSIDE
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white/95 px-3 py-1.5 text-xs font-bold text-gray-600 shadow-sm">
                                                <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                                OUTSIDE
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Visitor Information --}}
                                <div class="flex flex-1 flex-col p-5">
                                    @if ($visitor->ticket_number)
                                        <div class="mb-4 rounded-xl bg-gray-50 px-4 py-3 text-center">
                                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                                                Ticket Number
                                            </p>
                                            <p class="mt-1 text-2xl font-bold tracking-wide text-gray-900">
                                                {{ $visitor->ticket_number }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                                Visitor ID
                                            </p>
                                            <p class="mt-1 break-words text-sm font-semibold text-gray-800">
                                                {{ $visitor->id_number ?? '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                                Office
                                            </p>
                                            <p class="mt-1 break-words text-sm font-semibold text-gray-800">
                                                {{ $visitor->office?->name ?? ($visitor->other_office ?? 'N/A') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Queue Status --}}
                                    @if ($visitor->ticket_number)
                                        <div class="mt-5 border-t border-gray-100 pt-4">
                                            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">
                                                Queue Status
                                            </p>

                                            @if ($visitor->status === 'serving')
                                                <span class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700">
                                                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                                    Serving
                                                </span>
                                            @elseif ($visitor->status === 'waiting')
                                                <span class="inline-flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                                    Waiting
                                                </span>
                                            @elseif ($visitor->status === 'skipped')
                                                <span class="inline-flex items-center gap-2 rounded-lg bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-700">
                                                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                                                    Skipped
                                                </span>
                                            @elseif ($visitor->status === 'transferred')
                                                <span class="inline-flex items-center gap-2 rounded-lg bg-purple-50 px-3 py-2 text-xs font-semibold text-purple-700">
                                                    <span class="h-2 w-2 rounded-full bg-purple-500"></span>
                                                    Transferred
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div id="emptyVisitors" class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 px-6 py-16 text-center">
                                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-white text-gray-400 shadow-sm">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 0 0 3.75.772 9.38 9.38 0 0 0 3.75-.772M15 19.128v-.003c0-1.487-.37-2.887-1.023-4.115M15 19.128v.003c0 .405-.04.802-.116 1.187A9.37 9.37 0 0 1 12 20.75a9.37 9.37 0 0 1-2.884-.432A4.5 4.5 0 0 1 9 19.128m6 0a6 6 0 0 0-12 0m12 0v.003m-12-.003v.003m0 0A9.38 9.38 0 0 0 6.75 20.75c.99 0 1.94-.153 2.834-.437M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                    </svg>
                                </span>
                                <h3 class="mt-4 text-base font-semibold text-gray-800">
                                    No Visitors Currently Inside
                                </h3>
                                <p class="mt-1 max-w-sm text-sm text-gray-500">
                                    Visitor records will appear here when someone enters the campus.
                                </p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>

        </div>
    </div>

    @vite('resources/js/guard-monitoring.js')
</x-app-layout>
