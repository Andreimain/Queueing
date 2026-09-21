<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Visitor IN/OUT
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Process visitor entry and exit.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <x-card>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Visitor IN/OUT Processing
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Enter a visitor ID to find a visitor currently inside the campus.
                    </p>
                </div>

                {{-- Visitor Search --}}
                <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                    <div class="flex flex-col sm:flex-row gap-3">

                        <input id="visitorIdInput" type="text" placeholder="Enter Visitor ID"
                            class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        <button id="findVisitorButton" type="button"
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
                            Find Visitor
                        </button>

                    </div>

                    <div id="visitorResult" class="mt-4"></div>
                </div>

            </x-card>

        </div>
    </div>

    @vite('resources/js/guard-process.js')
</x-app-layout>
