<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            List of Skipped Visitors
            (Today: <span id="current-time"></span>)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Search bar -->
                    <div class="flex justify-end mb-4">
                        <form method="GET" action="{{ route('skipped.list') }}" id="searchForm" class="w-full md:w-1/3">
                            <input type="text" name="q" id="searchInput"
                                   value="{{ request('q') }}"
                                   placeholder="Search visitor..."
                                   autocomplete="off"
                                   class="border border-green-400 focus:border-green-500 focus:ring-green-500 rounded-md w-full px-3 py-2">
                        </form>
                    </div>

                    <!-- Skipped Visitors Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-fixed border border-green-300 text-sm">
                            <colgroup>
                                <col style="width:5%">
                                <col style="width:20%">
                                <col style="width:20%">
                                <col style="width:25%">
                                <col style="width:25%">
                            </colgroup>

                            <thead class="bg-green-600 text-black">
                                <tr>
                                    <th class="px-4 py-2 border border-green-700 text-center">
                                        <input type="checkbox" id="select-all" class="cursor-pointer">
                                    </th>
                                    <th class="px-4 py-2 border border-green-700 text-left">First Name</th>
                                    <th class="px-4 py-2 border border-green-700 text-left">Last Name</th>
                                    <th class="px-4 py-2 border border-green-700 text-left">Office</th>
                                    <th class="px-4 py-2 border border-green-700 text-left">Skipped At</th>
                                </tr>
                            </thead>

                            <tbody class="text-black">
                                @forelse($skipped as $visitor)
                                    <tr class="hover:bg-green-100">
                                        <td class="px-4 py-2 border border-green-300 text-center">
                                            <input type="checkbox" name="selected[]" value="{{ $visitor->id }}" class="row-checkbox cursor-pointer">
                                        </td>
                                        <td class="px-4 py-2 border border-green-300 truncate">{{ $visitor->first_name }}</td>
                                        <td class="px-4 py-2 border border-green-300 truncate">{{ $visitor->last_name }}</td>
                                        <td class="px-4 py-2 border border-green-300 truncate">{{ $visitor->office->name }}</td>
                                        <td class="px-4 py-2 border border-green-300 truncate">
                                            {{ $visitor->updated_at->timezone('Asia/Manila')->format('h:i A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                            No skipped visitors today.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Restore button -->
                    <div class="mt-6 text-center">
                        <form method="POST" action="{{ route('skipped.restore') }}" id="restoreForm" class="inline-block">
                            @csrf
                            <input type="hidden" name="selected_ids" id="selectedIds">
                            <button type="submit"
                                class="py-2 px-4 rounded shadow font-semibold text-sm text-white hover:bg-green-700 transition"
                                style="background-color: green;">
                                Restore Selected
                            </button>
                        </form>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $skipped->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Live time
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

        // Select all checkboxes
        document.getElementById('select-all').addEventListener('change', function(e) {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
        });

        // Collect selected IDs before submit
        document.getElementById("restoreForm").addEventListener("submit", function (e) {
            const selected = Array.from(document.querySelectorAll(".row-checkbox:checked"))
                .map(cb => cb.value);
            if (selected.length === 0) {
                e.preventDefault();
                alert("Please select at least one visitor to restore.");
                return false;
            }
            document.getElementById("selectedIds").value = selected.join(",");
        });

        // Instant search
        let typingTimer;
        const searchInput = document.getElementById("searchInput");
        const searchForm = document.getElementById("searchForm");

        searchInput.addEventListener("keyup", function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => searchForm.submit(), 500);
        });
    </script>
</x-app-layout>
