<x-guest-layout>
    <x-slot name="title">
        Home
    </x-slot>

    <div class="min-h-screen w-full bg-gradient-to-br from-emerald-50 via-white to-gray-50">
        <div class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">

            <!-- Hero Section -->
            <section class="relative mb-14 overflow-hidden rounded-3xl bg-emerald-800 px-6 py-12 text-center shadow-xl shadow-emerald-900/10 sm:px-12 sm:py-16">
                <!-- Decorative Background -->
                <div class="pointer-events-none absolute -right-16 -top-24 h-72 w-72 rounded-full bg-emerald-600/30 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-16 h-72 w-72 rounded-full bg-teal-400/20 blur-3xl"></div>

                <div class="relative mx-auto max-w-3xl">
                    <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl border border-white/20 bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-9 w-9 text-white"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>

                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.25em] text-emerald-200">
                        Lorma Queue Directory
                    </p>

                    <h1 class="text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Welcome to Lorma
                        <span class="block text-emerald-200">Queue Directory</span>
                    </h1>

                    <!-- Action Links -->
                    <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="{{ route('visitor.create') }}"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-emerald-800 shadow-sm transition hover:bg-emerald-50 sm:w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 4v16m8-8H4" />
                            </svg>
                            Register a Visit
                        </a>

                        <a href="{{ route('login') }}"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/20 sm:w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4m-6-4l5-5m0 0l-5-5m5 5H3" />
                            </svg>
                            Staff Login
                        </a>
                    </div>
                </div>
            </section>

            <!-- Office Section -->
            <section>
                <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">
                            Live Queue Monitoring
                        </p>
                        <h2 class="mt-2 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                            Select an Office
                        </h2>
                        <p class="mt-2 text-sm text-gray-500 sm:text-base">
                            Choose an office to view its current queue status.
                        </p>
                    </div>

                    <div class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        </span>
                        Queue monitors available
                    </div>
                </div>

                <!-- Office Grid -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse($offices as $office)
                        <a href="{{ route('monitor.show', $office->id) }}"
                           class="group block h-full">
                            <div class="flex h-full min-h-[210px] flex-col items-center justify-center rounded-2xl border border-gray-200 bg-white p-7 text-center shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-900/5">

                                <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition duration-300 group-hover:bg-emerald-600 group-hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-8 w-8"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="1.7">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>

                                <h3 class="text-xl font-bold text-gray-800 transition-colors group-hover:text-emerald-700">
                                    {{ $office->name }}
                                </h3>

                                <p class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-emerald-600">
                                    View Live Monitor
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4 transition-transform group-hover:translate-x-1"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M5 12h14m-6-6l6 6-6 6" />
                                    </svg>
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-8 w-8 text-gray-400"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>

                            <h3 class="mt-4 text-lg font-bold text-gray-800">
                                No Offices Available
                            </h3>

                            <p class="mt-2 text-sm text-gray-500">
                                There are currently no offices available to display.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Footer -->
            <footer class="mt-14 border-t border-gray-200 pt-6 text-center">
                <p class="text-xs text-gray-400">
                    Lorma Queue Directory
                </p>
            </footer>

        </div>
    </div>
</x-guest-layout>
