<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-emerald-100 px-4 py-10">

    <main class="w-full max-w-md">
        <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-xl shadow-emerald-900/5">

            <!-- Header -->
            <div class="bg-emerald-700 px-6 py-8 text-center text-white">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15h.01M12 15h.01M15 15h.01" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                    You're in the Queue!
                </h1>

                <p class="mt-2 text-sm text-emerald-100">
                    Your registration was successful.
                </p>
            </div>

            <!-- Ticket Details -->
            <div class="px-6 py-8 text-center sm:px-8">

                <p class="text-sm font-medium uppercase tracking-wider text-gray-500">
                    Your queue ticket for
                </p>

                <h2 class="mt-1 text-xl font-bold text-gray-800">
                    {{ $visitor->office->name }}
                </h2>

                <!-- Ticket Number -->
                <div class="my-7 rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50 px-4 py-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">
                        Ticket Number
                    </p>

                    <p class="mt-3 break-words text-5xl font-extrabold tracking-wider text-emerald-700 sm:text-6xl">
                        {{ $visitor->ticket_number }}
                    </p>

                    <p class="mt-3 text-xs text-emerald-600">
                        Please keep this number for your reference.
                    </p>
                </div>

                <!-- QR Code -->
                <div class="mb-7">
                    <h3 class="text-sm font-semibold text-gray-800">
                        Track Your Queue
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Scan this QR code to view the current queue status.
                    </p>

                    <div class="mx-auto mt-4 flex w-fit items-center justify-center rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                        {!! QrCode::size(160)->color(5, 150, 105)->generate(route('monitor.show', $visitor->office->id)) !!}
                    </div>

                    <p class="mt-3 text-xs text-gray-400">
                        {{ $visitor->office->name }} Queue Monitor
                    </p>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="{{ route('monitor.show', $visitor->office->id) }}"
                       class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7M8 12h13" />
                        </svg>
                        View {{ $visitor->office->name }} Queue
                    </a>

                    <a href="{{ route('visitor.create') }}"
                       class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3.5 text-sm font-semibold text-gray-700 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Register Another User
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-5 text-center text-xs text-gray-400">
            Please wait for your ticket number to be called.
        </p>
    </main>

</body>
</html>
