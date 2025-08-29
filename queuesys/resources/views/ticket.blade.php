<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Queue Number</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // redirect back after 3 seconds
        setTimeout(() => {
            window.location.href = "{{ route('visitor.create') }}";
        }, 5000);
    </script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
        <h1 class="text-3xl font-bold mb-4">You have joined the queue!</h1>
        <p class="text-lg mb-2">Your queue number for <strong>{{ $visitor->office }}</strong> is:</p>
        <div class="text-6xl font-bold text-green-600 mb-6">
            {{ $visitor->queue_number }}
        </div>
        <p class="text-gray-600">Redirecting back to registration...</p>
    </div>

</body>
</html>
