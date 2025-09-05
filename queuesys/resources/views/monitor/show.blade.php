<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $office->name }} Monitoring</title>
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">

    <!-- Title -->
    <h1 class="text-4xl font-bold mb-8 text-center">{{ $office->name }} Queue Display</h1>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-2 gap-8 max-w-10xl mx-auto w-full">

        <!-- Current Queue (Left Box) -->
        <div class="border p-10 rounded-2xl shadow bg-white flex flex-col justify-center h-[80vh]">
            <h2 class="text-5xl font-semibold mb-6 text-center">Now Serving</h2>
            <div id="current-queue" class="text-center">
                @if($currentQueue)
                    <div class="text-6xl font-bold text-green-600">
                        {{ $currentQueue->id_number }}
                    </div>
                @else
                    <div class="text-3xl text-gray-500">
                        No one is being served right now.
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Queues (Right Box) -->
        <div class="border p-10 rounded-2xl shadow bg-white h-[80vh] overflow-y-auto">
            <h2 class="text-5xl font-semibold mb-6 text-center">Upcoming Queues</h2>
            <ul id="upcoming-queues" class="list-disc pl-8 space-y-4 text-3xl">
                @forelse($upcomingQueues as $queue)
                    <li>{{ $queue->id_number }}</li>
                @empty
                    <li class="text-gray-500">No upcoming queues</li>
                @endforelse
            </ul>
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
                    let currentQueueDiv = document.getElementById('current-queue');
                    if (data.currentQueue) {
                        currentQueueDiv.innerHTML =
                            `<div class="text-6xl font-bold text-green-600">
                                ${escapeHtml(data.currentQueue.id_number)}
                            </div>`;
                    } else {
                        currentQueueDiv.innerHTML =
                            `<div class="text-3xl text-gray-500">
                                No one is being served right now.
                            </div>`;
                    }

                    let upcomingList = document.getElementById('upcoming-queues');
                    if (data.upcomingQueues.length > 0) {
                        upcomingList.innerHTML = data.upcomingQueues.map(q =>
                            `<li class="text-3xl">${escapeHtml(q.id_number)}</li>`
                        ).join('');
                    } else {
                        upcomingList.innerHTML = `<li class="text-gray-500">No upcoming queues</li>`;
                    }
                })
                .catch(error => console.error('Error refreshing queue:', error));
        }

        setInterval(refreshQueue, 1000);
    </script>

</body>
</html>
