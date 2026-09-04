<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registration Recorded</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-100 via-white to-emerald-50">

    <div class="bg-white/80 backdrop-blur-sm border border-emerald-200 p-10 rounded-2xl shadow-lg text-center w-[90%] max-w-md">

        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-emerald-100 flex items-center justify-center">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-emerald-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7"
                />
            </svg>

        </div>

        <h1 class="text-3xl font-bold mb-4 text-emerald-800">
            Registration Recorded
        </h1>

        <p class="text-lg text-gray-700 mb-6">
            Your visit has been successfully recorded.
        </p>

        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-8">

            <p class="text-sm text-gray-500 mb-1">
                Office Visited
            </p>

            <p class="text-xl font-bold text-emerald-700">
                {{ $visitor->other_office }}
            </p>

        </div>

        <a
            href="{{ route('visitor.create') }}"
            class="block w-full px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition"
        >
            Register Another Visitor
        </a>

    </div>

</body>
</html>
