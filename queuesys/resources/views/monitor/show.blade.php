<x-guest-layout>
    <div class="w-full max-w-7xl mx-auto p-4 sm:p-6">

        <!-- Title -->
        <h1 class="text-3xl sm:text-4xl font-bold mb-8 text-center text-emerald-700">
            {{ $office->name }} Queue Display
        </h1>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">

            <!-- Now Serving -->
            <div class="border border-emerald-200 p-6 sm:p-10 rounded-2xl shadow bg-white/80 backdrop-blur flex flex-col justify-center h-[60vh] sm:h-[80vh]">
                <h2 class="text-3xl sm:text-5xl font-semibold mb-4 sm:mb-6 text-center text-emerald-800">Now Serving</h2>
                <div id="current-queue" class="text-center">
                    @if($currentQueue)
                        <div class="text-5xl sm:text-6xl font-bold text-emerald-600 break-words">
                            {{ $currentQueue->id_number }}
                        </div>
                    @else
                        <div class="text-2xl sm:text-3xl text-gray-500">
                            No one is being served right now.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Queues -->
            <div class="border border-emerald-200 p-6 sm:p-10 rounded-2xl shadow bg-white/80 backdrop-blur h-[60vh] sm:h-[80vh] overflow-y-auto">
                <h2 class="text-3xl sm:text-5xl font-semibold mb-4 sm:mb-6 text-center text-emerald-800">Upcoming Queues</h2>
                <ul id="upcoming-queues" class="list-disc pl-6 sm:pl-8 space-y-3 sm:space-y-4 text-2xl sm:text-3xl">
                    @forelse($upcomingQueues as $queue)
                        <li class="break-words text-gray-800">{{ $queue->id_number }}</li>
                    @empty
                        <li class="text-gray-500">No upcoming queues</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>

    <!-- Auto Refresh Script -->
    <script>
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function refreshQueue() {
            fetch("{{ route('monitor.data', $office->id) }}")
                .then(response => response.json())
                .then(data => {
                    const currentQueueDiv = document.getElementById('current-queue');
                    const upcomingList = document.getElementById('upcoming-queues');

                    if (data.currentQueue) {
                        currentQueueDiv.innerHTML = `
                            <div class="text-5xl sm:text-6xl font-bold text-emerald-600 break-words">
                                ${escapeHtml(data.currentQueue.id_number)}
                            </div>`;
                    } else {
                        currentQueueDiv.innerHTML = `
                            <div class="text-2xl sm:text-3xl text-gray-500">
                                No one is being served right now.
                            </div>`;
                    }

                    if (data.upcomingQueues.length > 0) {
                        upcomingList.innerHTML = data.upcomingQueues.map(q =>
                            `<li class="text-2xl sm:text-3xl break-words text-gray-800">${escapeHtml(q.id_number)}</li>`
                        ).join('');
                    } else {
                        upcomingList.innerHTML = `<li class="text-gray-500">No upcoming queues</li>`;
                    }
                })
                .catch(error => console.error('Error refreshing queue:', error));
        }

        setInterval(refreshQueue, 1000);
    </script>
</x-guest-layout>
