<x-guest-layout>
    <div class="w-full max-w-7xl mx-auto p-4 sm:p-6">

        <!-- Title -->
        <h1 class="text-3xl sm:text-4xl font-bold mb-8 text-center text-emerald-700">
            {{ $office->name }} Queue Display
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">

            <!-- Cashiers / Now Serving -->
            <div class="space-y-6">
                @foreach ($cashiers as $cashier)
                    <div data-cashier-id="{{ $cashier->id }}" class="border border-emerald-200 p-6 sm:p-8 rounded-2xl shadow bg-white/80 backdrop-blur flex flex-col justify-center h-[20vh] sm:h-[25vh]">
                        <h2 class="text-xl sm:text-2xl font-semibold mb-2 text-center text-emerald-800">
                            {{ $cashier->name }}
                        </h2>
                        <div class="text-center serving">
                            @if(isset($servingVisitors[$cashier->id]))
                                <div class="text-3xl sm:text-4xl font-bold text-emerald-600 break-words">
                                    {{ $servingVisitors[$cashier->id]->ticket_number }}
                                </div>
                                <div class="text-gray-700 mt-1 text-lg">
                                    #{{ $servingVisitors[$cashier->id]->queue_number }}
                                </div>
                            @else
                                <div class="text-2xl sm:text-3xl text-gray-400">Idle</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Upcoming Queues -->
            <div class="border border-emerald-200 p-6 sm:p-10 rounded-2xl shadow bg-white/80 backdrop-blur h-[80vh] overflow-y-auto">
                <h2 class="text-3xl sm:text-5xl font-semibold mb-4 sm:mb-6 text-center text-emerald-800">Upcoming Queues</h2>
                <ul id="upcoming-queues" class="list-disc pl-6 sm:pl-8 space-y-3 sm:space-y-4 text-2xl sm:text-3xl">
                    @forelse($upcomingQueues as $queue)
                        <li class="break-words text-gray-800">{{ $queue->ticket_number }}</li>
                    @empty
                        <li class="text-gray-500">No upcoming queues</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>

    <script>
        window.officeId = {{ $office->id }};
    </script>
    @vite('resources/js/monitor-refresh.js')
</x-guest-layout>
