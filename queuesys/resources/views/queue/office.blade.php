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
                    <div class="mb-6 text-center border rounded-lg p-6 bg-gray-50 shadow-inner">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Now Serving</h3>

                        @if ($serving)
                            <div class="text-4xl font-bold {{ $serving->priority ? 'text-red-600' : 'text-blue-600' }}">
                                #{{ $serving->queue_number }}
                            </div>
                            <div class="text-gray-700 text-lg mt-1">
                                {{ $serving->first_name }} {{ $serving->last_name }}
                            </div>
                            {{-- <div class="mt-2 text-sm {{ $serving->priority ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                                {{ $serving->priority ? 'Priority Ticket' : 'Regular Ticket' }}
                            </div> --}}
                        @elseif($waiting->count())
                            <p class="text-gray-500 mt-2">Press "Serve Next" to start serving visitors.</p>
                        @else
                            <p class="text-gray-500 mt-2">No one is currently being served.</p>
                        @endif

                        {{-- Next in Line --}}
                        @if ($waiting->count())
                            <div class="mt-4 text-sm text-gray-500">
                                <strong>Next in line:</strong>
                                #{{ $waiting->first()->queue_number }} —
                                {{ $waiting->first()->first_name }} {{ $waiting->first()->last_name }}
                                {{-- ({{ $waiting->first()->priority ? 'Priority' : 'Regular' }}) --}}
                            </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mb-6 text-center">
                        {{-- Serve Next --}}
                        @if ($waiting->count() > 0 || !$serving)
                            <form method="POST" action="{{ route('office.queue.next', $office->id) }}"
                                class="inline-block">
                                @csrf
                                <button type="submit" class="py-2 px-4 rounded shadow font-semibold transition text-sm"
                                    style="color: white; background-color: blue;"
                                    {{ !$waiting->count() ? 'disabled' : '' }}>
                                    Serve Next
                                </button>
                            </form>
                        @endif

                        {{-- Skip --}}
                        @if ($serving)
                            <form method="POST" action="{{ route('office.queue.skip', $office->id) }}"
                                class="inline-block ml-4">
                                @csrf
                                <button type="submit" class="py-2 px-4 rounded shadow font-semibold transition text-sm"
                                    style="color: white; background-color: orange;">
                                    Skip
                                </button>
                            </form>
                        @endif

                        {{-- Done --}}
                        @if ($serving)
                            <form method="POST" action="{{ route('office.queue.done', $office->id) }}"
                                class="inline-block ml-4">
                                @csrf
                                <button type="submit" class="py-2 px-4 rounded shadow font-semibold transition text-sm"
                                    style="color: white; background-color: green;">
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
                                        <li
                                            class="p-3 bg-white border rounded text-sm shadow-sm flex justify-between items-center">
                                            <div class="flex items-center space-x-2">
                                                <input type="checkbox" name="selected_visitors[]"
                                                    value="{{ $visitor->id }}"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span
                                                    class="font-semibold {{ $visitor->priority ? 'text-red-600' : 'text-blue-600' }}">
                                                    #{{ $visitor->queue_number }}
                                                </span> —
                                                {{ $visitor->first_name }} {{ $visitor->last_name }}
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

    <script>
        let pauseRefresh = false;

        document.addEventListener('change', () => {
            const checked = document.querySelectorAll('input[name="selected_visitors[]"]:checked').length;
            const btn = document.getElementById('skipSelectedBtn');
            if (btn) btn.disabled = checked === 0;

            pauseRefresh = checked > 0;
        });

        setInterval(function() {
            if (pauseRefresh) return;

            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#queue-area').innerHTML;
                    document.querySelector('#queue-area').innerHTML = newContent;
                })
                .catch(err => console.error('Error refreshing queue:', err));
        }, 3000);
    </script>

</x-app-layout>
