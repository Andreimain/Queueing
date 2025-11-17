<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $office->name }} Queue Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-4 text-green-600 text-center font-medium">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 text-red-600 text-center font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                <div id="queue-area">

                    {{-- Now Serving --}}
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">Now Serving</h3>
                    <div class="flex justify-center flex-wrap gap-4 mb-6">
                        @forelse ($serving as $visitor)
                            @php
                                $cashierColors = [
                                    1 => 'border-emerald-500',
                                    2 => 'border-blue-500',
                                    3 => 'border-purple-500',
                                ];
                                $borderColor = $cashierColors[$visitor->cashier_id] ?? 'border-gray-300';
                            @endphp

                            <div class="bg-emerald-50 rounded-lg shadow-md p-6 w-64 text-center border-4 {{ $borderColor }}">
                                <div class="text-3xl font-bold {{ $visitor->priority ? 'text-red-600' : 'text-emerald-700' }}">
                                    #{{ $visitor->queue_number }}
                                </div>
                                <div class="text-lg mt-2 font-semibold">
                                    {{ $visitor->first_name }} {{ $visitor->last_name }} - {{ $visitor->ticket_number }}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    Cashier: {{ $visitor->cashier->name ?? 'Unassigned' }}
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center w-full">No visitors are currently being served.</p>
                        @endforelse
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mb-6 flex justify-center gap-4 flex-wrap">
                        {{-- Serve Next --}}
                        @if ($waiting->count() > 0 || !$serving->count())
                            <form method="POST" action="{{ route('office.queue.next', $office->id) }}">
                                @csrf
                                <button type="submit" class="py-2 px-6 rounded shadow font-semibold text-white bg-emerald-600 hover:bg-emerald-700">
                                    Serve Next
                                </button>
                            </form>
                        @endif

                        {{-- Skip --}}
                        @if ($serving->count())
                            <form method="POST" action="{{ route('office.queue.skip', $office->id) }}">
                                @csrf
                                <button type="submit" class="py-2 px-6 rounded shadow font-semibold text-white bg-orange-600 hover:bg-orange-700">
                                    Skip
                                </button>
                            </form>
                        @endif

                        {{-- Done --}}
                        @if ($serving->count())
                            <form method="POST" action="{{ route('office.queue.done', $office->id) }}">
                                @csrf
                                <button type="submit" class="py-2 px-6 rounded shadow font-semibold text-white bg-green-600 hover:bg-green-700">
                                    Done
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Waiting List --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Waiting List</h3>

                        @if ($waiting->count())
                            <form id="skipForm" method="POST" action="{{ route('office.queue.skip', $office->id) }}">
                                @csrf
                                <ul class="space-y-2">
                                    @foreach ($waiting as $visitor)
                                        <li class="p-3 bg-white border rounded shadow-sm flex justify-between items-center">
                                            <div class="flex items-center space-x-2">
                                                <input type="checkbox" name="selected_visitors[]"
                                                    value="{{ $visitor->id }}"
                                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                                <span class="font-semibold {{ $visitor->priority ? 'text-red-600' : 'text-emerald-700' }}">
                                                    #{{ $visitor->queue_number }}
                                                </span> —
                                                {{ $visitor->first_name }} {{ $visitor->last_name }} - {{ $visitor->ticket_number }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="mt-4 text-right">
                                    <button type="submit"
                                        class="bg-orange-600 text-white px-4 py-2 rounded shadow hover:bg-orange-700 disabled:opacity-50"
                                        id="skipSelectedBtn" disabled>
                                        Skip Selected
                                    </button>
                                </div>
                            </form>
                        @else
                            <p class="text-gray-500">No visitors in the queue.</p>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>

    @vite('resources/js/queue-refresh.js')
</x-app-layout>
