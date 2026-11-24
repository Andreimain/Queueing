<x-app-layout>
    <x-slot name="title">
        Visitor IN/OUT
    </x-slot>

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

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Page Introduction --}}
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-900">
                    Visitor Processing
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Search for a visitor or scan their RFID card to process their exit.
                </p>
            </div>

            {{-- Main Processing Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                {{-- Card Header --}}
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <div>
                            <h4 class="font-bold text-gray-900">
                                Visitor IN/OUT Processing
                            </h4>
                            <p class="text-sm text-gray-500 mt-0.5">
                                Enter a Visitor ID or use the RFID scanner.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    {{-- Manual Visitor Search --}}
                    <div>
                        <label for="visitorIdInput"
                            class="block text-sm font-semibold text-gray-700 mb-2">
                            Visitor ID
                        </label>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v11a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0h4" />
                                    </svg>
                                </div>

                                <input
                                    id="visitorIdInput"
                                    type="text"
                                    placeholder="Enter Visitor ID"
                                    autocomplete="off"
                                    class="w-full pl-10 rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 py-3 text-sm">
                            </div>

                            <button
                                id="findVisitorButton"
                                type="button"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Find Visitor
                            </button>
                        </div>
                    </div>

                    {{-- RFID Scanner Section --}}
                    <div class="rounded-xl border border-dashed border-blue-200 bg-blue-50/50 p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 shrink-0 rounded-xl bg-white text-blue-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 8v8m4-10v12m4-10v8M4 6a12 12 0 000 12m16-12a12 12 0 010 12" />
                                    </svg>
                                </div>

                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">
                                        RFID Scanner
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Scan a visitor's RFID card to find their record.
                                    </p>
                                </div>
                            </div>

                            <div class="inline-flex items-center gap-2 self-start sm:self-auto px-3 py-2 rounded-lg bg-white border border-blue-100">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span id="scannerStatus" class="text-xs font-semibold text-gray-600">
                                    RFID Scanner Ready
                                </span>
                            </div>

                        </div>
                    </div>

                    {{-- Hidden RFID Scanner Input --}}
                    <input
                        id="rfidScannerInput"
                        type="text"
                        autocomplete="off"
                        tabindex="-1"
                        aria-hidden="true"
                        class="absolute opacity-0 pointer-events-none">

                    {{-- Visitor Result --}}
                    <div id="visitorResult" class="space-y-4"></div>

                </div>
            </div>

            {{-- Information Note --}}
            <div class="mt-5 flex items-start gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4">
                <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Make sure the visitor information is correct before completing an IN/OUT transaction.
                    You can use either manual Visitor ID entry or RFID scanning.
                </p>
            </div>

        </div>
    </div>

    @vite('resources/js/guard-process.js')
</x-app-layout>
