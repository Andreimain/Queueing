<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>System Under Maintenance</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-emerald-50 flex items-center justify-center px-6">

    <div class="w-full max-w-lg text-center">

        <div class="bg-white rounded-2xl shadow-lg border border-emerald-100 p-8">

            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center">
                    <span class="text-4xl">
                        🛠️
                    </span>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-emerald-800">
                System Under Maintenance
            </h1>

            <p class="mt-4 text-gray-600 leading-relaxed">
                The LORMA Queueing System is currently undergoing
                maintenance.
            </p>

            <p class="mt-2 text-gray-500 text-sm">
                We are working to improve the system and will be
                back shortly.
            </p>

            <div class="mt-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl">
                <p class="text-sm text-emerald-800 font-medium">
                    Thank you for your patience.
                </p>
            </div>

        </div>

        <p class="mt-6 text-sm text-gray-400">
            LORMA Queueing System
        </p>

    </div>

</body>

</html>
