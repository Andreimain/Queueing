<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ ucfirst($office) }} Queue
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="mb-4 text-green-600 text-center font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                <div id="queue-area">
                    {{-- Now Serving --}}
                    <div class="mb-6 text-center border rounded-lg p-6 bg-gray-50 shadow-inner">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Now Serving</h3>

                        @if($serving)
                            <div class="text-4xl font-bold text-blue-600">#{{ $serving->queue_number }}</div>
                            <div class="text-gray-700 text-lg mt-1">{{ $serving->first_name }} {{ $serving->last_name }}</div>
                        @elseif($waiting->count())
                            <p class="text-gray-500 mt-2">Press "Serve Next" to start serving visitors.</p>
                        @else
                            <p class="text-gray-500 mt-2">No one is currently being served.</p>
                        @endif

                        @if($waiting->count())
                            <div class="mt-4 text-sm text-gray-500">
                                <strong>Next in line:</strong>
                                #{{ $waiting->first()->queue_number }} —
                                {{ $waiting->first()->first_name }} {{ $waiting->first()->last_name }}
                            </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mb-6 text-center">
                        {{-- Serve Next --}}
                        @if($waiting->count() > 0 || !$serving)
                            <form method="POST" action="{{ route('office.queue.next', $office) }}" class="inline-block">
                                @csrf
                                @php $isDisabled = !$waiting->count(); @endphp
                                <button type="submit"
                                    class="py-2 px-4 rounded shadow font-semibold transition text-sm
                                        {{ $isDisabled ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700' }}"
                                    {{ $isDisabled ? 'disabled' : '' }}
                                    style="color: white; background-color: blue;">
                                    Serve Next
                                </button>
                            </form>
                        @endif

                        {{-- Skip --}}
                        @if($serving)
                            <form method="POST" action="{{ route('office.queue.skip', $office) }}" class="inline-block ml-4">
                                @csrf
                                <button type="submit"
                                    class="py-2 px-4 rounded shadow font-semibold transition text-sm bg-yellow-500 text-white hover:bg-yellow-600"
                                    style="color: white; background-color: orange;">
                                    Skip
                                </button>
                            </form>
                        @endif

                        {{-- Done --}}
                        @if($serving)
                            <form method="POST" action="{{ route('office.queue.done', $office) }}" class="inline-block ml-4">
                                @csrf
                                <button type="submit"
                                    class="py-2 px-4 rounded shadow font-semibold transition text-sm bg-green-600 text-white hover:bg-green-700"
                                    style="color: white; background-color: green;">
                                    Done
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Waiting List --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Waiting List</h3>

                        @if($waiting->count())
                            <ul class="space-y-2">
                                @foreach($waiting as $visitor)
                                    <li class="p-3 bg-white border rounded text-sm shadow-sm">
                                        <span class="font-semibold text-blue-600">#{{ $visitor->queue_number }}</span> —
                                        {{ $visitor->first_name }} {{ $visitor->last_name }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500">No visitors in the queue.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Auto Refresh Script --}}
    <script>
        setInterval(function () {
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    let doc = new DOMParser().parseFromString(html, 'text/html');
                    let newContent = doc.querySelector('#queue-area').innerHTML;
                    document.querySelector('#queue-area').innerHTML = newContent;
                })
                .catch(err => console.error('Error refreshing queue:', err));
        }, 3000);
    </script>
</x-app-layout>
