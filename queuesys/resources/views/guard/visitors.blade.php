<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Visitor Monitoring
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Monitor visitors currently waiting or being served.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-card>

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Current Visitors
                        </h3>

                        <p class="text-sm text-gray-500">
                            Visitors across all offices
                        </p>
                    </div>

                    <div id="visitorCount" class="text-sm text-gray-500">
                        {{ $visitors->count() }}
                        visitor{{ $visitors->count() !== 1 ? 's' : '' }}
                    </div>
                </div>

                {{-- Visitor ID Cards --}}
                <div id="visitorCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                    @forelse($visitors as $visitor)
                        <div
                            class="visitor-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5 flex flex-col items-center text-center">

                            {{-- Photo Box --}}
                            <div
                                class="w-40 h-48 rounded-lg border-2 border-gray-300 bg-gray-100 overflow-hidden flex items-center justify-center">
                                @if ($visitor->photo_path)
                                    <img src="{{ route('guard.visitors.photo', $visitor) }}" alt="Visitor photo"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-sm text-gray-400">
                                        No Photo
                                    </span>
                                @endif
                            </div>

                            {{-- Ticket Number --}}
                            <div class="mt-5">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Ticket Number
                                </p>

                                <p class="mt-1 text-2xl font-bold text-gray-900">
                                    {{ $visitor->ticket_number ?? '—' }}
                                </p>
                            </div>

                            {{-- Visitor ID --}}
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Visitor ID
                                </p>

                                <p class="mt-1 text-base font-semibold text-gray-800">
                                    {{ $visitor->id_number ?? '—' }}
                                </p>
                            </div>

                            {{-- Office --}}
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Office
                                </p>

                                <p class="mt-1 text-base font-semibold text-gray-800">
                                    {{ $visitor->office?->name ?? ($visitor->other_office ?? 'N/A') }}
                                </p>
                            </div>

                            {{-- Status --}}
                            <div class="mt-5">
                                @if ($visitor->status === 'serving')
                                    <span
                                        class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        ● Serving
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                        ● Waiting
                                    </span>
                                @endif
                            </div>

                        </div>

                    @empty

                        <div id="emptyVisitors" class="col-span-full py-12 text-center text-gray-500">
                            No visitors are currently waiting or being served.
                        </div>
                    @endforelse

                </div>

            </x-card>

        </div>
    </div>

    @vite('resources/js/guard-monitoring.js')
</x-app-layout>
