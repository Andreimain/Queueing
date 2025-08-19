<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            List of Skipped Visitors for All Offices
            (Today: <span id="current-time"></span>)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Expanded + responsive table -->
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 text-sm">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-4 py-2 border text-left">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="select-all" class="cursor-pointer">
                                            <label for="select-all" class="ml-2 font-semibold text-gray-800">Select All</label>
                                        </div>
                                    </th>
                                    <th class="px-4 py-2 border">Queue #</th>
                                    <th class="px-4 py-2 border">First Name</th>
                                    <th class="px-4 py-2 border">Last Name</th>
                                    <th class="px-4 py-2 border">Office</th>
                                    <th class="px-4 py-2 border">Skipped At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($skipped as $visitor)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border text-center">
                                            <input type="checkbox" name="selected[]" value="{{ $visitor->id }}" class="row-checkbox cursor-pointer">
                                        </td>
                                        <td class="px-4 py-2 border">{{ $visitor->queue_number }}</td>
                                        <td class="px-4 py-2 border">{{ $visitor->first_name }}</td>
                                        <td class="px-4 py-2 border">{{ $visitor->last_name }}</td>
                                        <td class="px-4 py-2 border">{{ $visitor->office }}</td>
                                        <td class="px-4 py-2 border">
                                            {{ $visitor->updated_at->timezone('Asia/Manila')->format('h:i A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-2 text-center text-gray-500">
                                            No skipped visitors today.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $skipped->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Live Manila Time for header
        function updateTime() {
            const options = {
                timeZone: "Asia/Manila",
                year: "numeric", month: "long", day: "numeric",
                hour: "2-digit", minute: "2-digit", second: "2-digit"
            };
            document.getElementById("current-time").innerText =
                new Date().toLocaleString("en-US", options);
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Select All checkbox logic
        document.getElementById('select-all').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        });
    </script>
</x-app-layout>
