<x-guest-layout>
    <x-slot name="title">
        Monitoring
    </x-slot>

    <x-slot name="containerClass">
        min-h-screen w-full bg-gray-50
    </x-slot>

    <main class="w-full min-w-0 px-4 py-6 sm:px-6 lg:px-10">

        <!-- Header -->
        <header class="mb-8">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-center text-center">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    </span>

                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700">
                        Live Queue Monitoring
                    </span>
                </div>

                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">
                    {{ $office->name }}
                </h1>

                <p class="mt-2 text-sm text-gray-500 sm:text-base">
                    Queue Display
                </p>
            </div>
        </header>

        <!-- Now Serving -->
        <section class="mb-10 w-full">

            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">
                        Now Serving
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Current queue numbers being handled
                    </p>
                </div>

                <div class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 shadow-sm">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M17 20h5v-2a4 4 0 0 0-5.5-3.7M9 20H2v-2a4 4 0 0 1 7-2.6M16 20v-2a4 4 0 0 0-8 0v2m8-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm5 1a3 3 0 1 0-3-3" />
                    </svg>

                    <span class="text-sm font-semibold text-gray-700">
                        {{ count($cashiers) }} Cashiers
                    </span>
                </div>
            </div>

            <!-- Responsive Cashier Grid -->
            <div
                class="grid gap-5 sm:gap-6"
                style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr));"
            >
                @forelse ($cashiers as $cashier)
                    <div
                        data-cashier-id="{{ $cashier->id }}"
                        class="flex min-h-[240px] min-w-0 flex-col items-center justify-center rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-sm transition hover:shadow-md sm:min-h-[270px] sm:p-8"
                    >
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M3 7h18M5 7V5h14v2m-1 0v12H6V7m4 4h4m-4 4h4" />
                            </svg>
                        </div>

                        <h3 class="mb-5 text-lg font-semibold text-gray-800 sm:text-xl">
                            {{ $cashier->name }}
                        </h3>

                        <div class="serving w-full text-center">
                            @if (isset($servingVisitors[$cashier->id]))
                                <div class="mb-2 text-xs font-semibold uppercase tracking-widest text-emerald-600">
                                    Now Serving
                                </div>

                                <div class="break-words text-4xl font-extrabold tracking-tight text-emerald-600 sm:text-5xl lg:text-6xl">
                                    {{ $servingVisitors[$cashier->id]->ticket_number }}
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-4 py-2">
                                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>

                                    <span class="text-sm font-semibold text-gray-500">
                                        Idle
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M17 20h5v-2a4 4 0 0 0-5.5-3.7M9 20H2v-2a4 4 0 0 1 7-2.6M16 20v-2a4 4 0 0 0-8 0v2m8-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>

                        <h3 class="text-base font-semibold text-gray-800">
                            No Cashiers Available
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            There are currently no cashiers assigned to this office.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Upcoming Queues -->
        <section class="w-full">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <!-- Section Header -->
                <div class="flex flex-col items-center justify-between gap-2 border-b border-gray-100 px-5 py-5 sm:flex-row sm:px-8">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">
                            Upcoming Queues
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Visitors waiting to be served
                        </p>
                    </div>

                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        Waiting List
                    </span>
                </div>

                <!-- Queue List -->
                <div class="p-5 sm:p-8">
                    <ul
                        id="upcoming-queues"
                        class="grid gap-4"
                        style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr));"
                    >
                        @forelse ($upcomingQueues as $queue)
                            <li class="flex min-h-[90px] items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 px-5 py-5 text-center text-2xl font-bold tracking-wide text-emerald-800 transition hover:border-emerald-300 hover:bg-emerald-100 sm:text-3xl">
                                {{ $queue->ticket_number }}
                            </li>
                        @empty
                            <li class="col-span-full py-10 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                                    </svg>
                                </div>

                                <p class="text-sm font-medium text-gray-500">
                                    No upcoming queues
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    New queue numbers will appear here.
                                </p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>

    </main>

    <script>
        window.officeId = {{ Js::from($office->id) }};
    </script>

    @vite('resources/js/monitor-refresh.js')
</x-guest-layout>
