<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Visitors Registration</h1>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('visitor.store') }}" class="space-y-4" autocomplete="off">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" name="first_name" required
                       value="{{ old('first_name') }}"
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" name="last_name" required
                       value="{{ old('last_name') }}"
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="text" name="contact_number" required
                       value="{{ old('contact_number') }}"
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Student ID No. / Visitor ID No.</label>
                <input type="text" name="id_number" required
                       value="{{ old('id_number') }}"
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Select Office</label>
                <select name="office_id" required
                        class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                    <option value="" disabled selected>-- Choose an office --</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit"
                        class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700 transition">
                    Join Queue
                </button>
            </div>
        </form>
    </div>
</body>
</html>
