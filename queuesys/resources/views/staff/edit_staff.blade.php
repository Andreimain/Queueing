<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Staff</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Edit Staff</h1>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('staff.update', $staff->id) }}" class="space-y-4" autocomplete="off">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" required
                       value="{{ old('name', $staff->name) }}"
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" required
                       value="{{ old('email', $staff->email) }}"
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Password <span class="text-gray-500 text-xs">(leave blank to keep current)</span>
                </label>
                <input type="password" name="password"
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" required
                        class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                    <option value="staff" {{ $staff->role === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <!-- Office -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Office</label>

                @if($staff->role === 'admin')
                    <input type="text" value="N/A" disabled
                        class="mt-1 w-full p-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                    <input type="hidden" name="office_id" value="{{ $staff->office_id }}">
                @else
                    <select name="office_id" required
                            class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                        <option value="" disabled>-- Select Office --</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}"
                                {{ $staff->office_id == $office->id ? 'selected' : '' }}>
                                {{ $office->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Submit -->
            <div>
                <button type="submit"
                        class="w-full bg-green-600 text-white font-semibold py-2 rounded hover:bg-green-700 transition">
                    Update Staff
                </button>
            </div>
        </form>
    </div>
</body>
</html>
