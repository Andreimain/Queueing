<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Queue Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-100 via-white to-emerald-50">

    <div class="bg-white/80 backdrop-blur-sm border border-emerald-200 p-10 rounded-2xl shadow-lg text-center w-[90%] max-w-md">
        <h1 class="text-3xl font-bold mb-4 text-emerald-800">You have joined the queue!</h1>

        <p class="text-lg mb-2 text-emerald-700">
            Your registered ID number for
            <strong class="text-emerald-600">{{ $visitor->office->name }}</strong> is:
        </p>

        <!-- Display ID Number -->
        <div class="text-6xl font-extrabold text-emerald-600 my-6">
            {{ $visitor->id_number }}
        </div>

        <!-- Space for QR Code -->
        <div id="qr-placeholder"
             class="h-32 flex items-center justify-center mb-8 border-2 border-dashed border-emerald-300 rounded-xl bg-emerald-50/50">
            <span class="text-emerald-400">QR Code Placeholder</span>
        </div>

        <!-- Stacked Buttons -->
        <div class="flex flex-col gap-3">
            <a href="{{ route('welcome') }}"
               class="px-6 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition">
                ← View Queue List
            </a>

            <a href="{{ route('visitor.create') }}"
               class="px-6 py-2 bg-white text-emerald-700 font-semibold rounded-lg border border-emerald-300 hover:bg-emerald-100 transition">
                ⟳ Register Another Visitor
            </a>
        </div>
    </div>

</body>
</html>
