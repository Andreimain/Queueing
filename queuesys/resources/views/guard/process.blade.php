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
                        Enter a visitor ID manually or scan an RFID card to record a visitor as OUT.
                    </p>
                </div>

                {{-- Visitor Search --}}
                <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">

                    <div class="flex flex-col sm:flex-row gap-3">

                        <div class="relative w-full flex-1">

                            <input id="visitorIdInput" type="text" placeholder="Enter Visitor ID"
                                autocomplete="off"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        </div>

                        <button id="findVisitorButton" type="button"
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
                            Find Visitor
                        </button>

                    </div>

                    {{-- RFID Scanner Status --}}
                    <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                        <span class="text-lg">
                            📡
                        </span>

                        <span id="scannerStatus">
                            RFID Scanner Ready
                        </span>
                    </div>

                    {{-- Hidden RFID Scanner Input --}}
                    <input id="rfidScannerInput"
                        type="text"
                        autocomplete="off"
                        tabindex="-1"
                        aria-hidden="true"
                        class="absolute opacity-0 pointer-events-none">

                    <div id="visitorResult" class="mt-4"></div>
                </div>

            </x-card>

        </div>
    </div>

    @vite('resources/js/guard-process.js')
</x-app-layout>
