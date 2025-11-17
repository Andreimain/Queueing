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

                    <!-- Top controls (search bar on the right) -->
                    <div class="flex justify-end mb-4">
                        <form method="GET" action="{{ route('skipped.list') }}" id="searchForm" class="w-1/3">
                            <input type="text" name="q" id="searchInput"
                                   value="{{ request('q') }}"
                                   placeholder="Search visitor..."
                                   autocomplete="off"
                                   class="border border-emerald-400 focus:border-emerald-500 focus:ring-emerald-500 rounded-md w-full px-3 py-2">
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="skippedTable" class="w-full border border-emerald-300 text-sm">
                            <thead class="bg-emerald-600 text-white">
                                <tr>
                                    <th class="px-4 py-2 border border-emerald-700 text-center">
                                        <input type="checkbox" id="select-all" class="cursor-pointer">
                                    </th>
                                    <th class="px-4 py-2 border border-emerald-700 text-left">First Name</th>
                                    <th class="px-4 py-2 border border-emerald-700 text-left">Last Name</th>
                                    <th class="px-4 py-2 border border-emerald-700 text-left">Office</th>
                                    <th class="px-4 py-2 border border-emerald-700 text-left">Skipped At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($skipped as $visitor)
                                    <tr class="hover:bg-emerald-50">
                                        <td class="px-4 py-2 border border-emerald-300 text-center">
                                            <input type="checkbox" name="selected[]" value="{{ $visitor->id }}" class="row-checkbox cursor-pointer">
                                        </td>
                                        <td class="px-4 py-2 border border-emerald-300">{{ $visitor->first_name }}</td>
                                        <td class="px-4 py-2 border border-emerald-300">{{ $visitor->last_name }}</td>
                                        <td class="px-4 py-2 border border-emerald-300">{{ $visitor->office->name }}</td>
                                        <td class="px-4 py-2 border border-emerald-300">
                                            {{ $visitor->updated_at->timezone('Asia/Manila')->format('h:i A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                            No skipped visitors today.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Restore Selected button -->
                    <div class="mt-4 text-center">
                        <form method="POST" action="{{ route('skipped.restore') }}" id="restoreForm" class="inline-block">
                            @csrf
                            <input type="hidden" name="selected_ids" id="selectedIds">

                            <button type="submit"
                                class="py-2 px-4 rounded shadow font-semibold text-sm text-white bg-emerald-600 hover:bg-emerald-700 transition">
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

    @vite('resources/js/skipped.js')
</x-app-layout>
