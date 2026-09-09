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

                    <!-- Visitor count -->
                    <div id="visitorCount" class="text-sm text-gray-500">
                        {{ $visitors->count() }}
                        visitor{{ $visitors->count() !== 1 ? 's' : '' }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Ticket
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Visitor
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Office
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Window
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>

                        <tbody id="visitorTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse($visitors as $visitor)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-bold text-gray-900">
                                            {{ $visitor->ticket_number }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                        {{ $visitor->name }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                        {{ $visitor->office?->name ?? 'N/A' }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                        {{ $visitor->cashier?->name ?? 'Waiting' }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($visitor->status === 'serving')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                Serving
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                Waiting
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        No visitors are currently waiting or being served.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

        </div>
    </div>
    @vite('resources/js/guard-monitoring.js')
</x-app-layout>
