<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Recorded</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-emerald-100 px-4 py-10">

    <main class="w-full max-w-md">
        <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-xl shadow-emerald-900/5">

            <!-- Header -->
            <div class="bg-emerald-700 px-6 py-8 text-center text-white">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-9 w-9"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                    Registration Recorded
                </h1>

                <p class="mt-2 text-sm text-emerald-100">
                    Your visit has been successfully recorded.
                </p>
            </div>

            <!-- Content -->
            <div class="px-6 py-8 text-center sm:px-8">

                <div class="mb-7">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-8 w-8 text-emerald-600"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h2 class="mt-4 text-lg font-semibold text-gray-800">
                        Thank You for Visiting!
                    </h2>

                    <p class="mt-2 text-sm leading-relaxed text-gray-500">
                        Your visit details have been saved successfully. You may now proceed with your transaction.
                    </p>
                </div>

                <!-- Office Details -->
                <div class="mb-8 rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50 px-5 py-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">
                        Office Visited
                    </p>

                    <p class="mt-3 break-words text-2xl font-bold text-emerald-700">
                        {{ $visitor->other_office }}
                    </p>
                </div>

                <!-- Action -->
                <a href="{{ route('visitor.create') }}"
                   class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 4v16m8-8H4" />
                    </svg>

                    Register Another Visitor
                </a>
            </div>
        </div>

        <p class="mt-5 text-center text-xs text-gray-400">
            Thank you for visiting our office.
        </p>
    </main>

</body>
</html>
