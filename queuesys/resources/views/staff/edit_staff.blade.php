<x-guest-layout>
    <div class="w-full max-w-md mx-auto bg-white p-8 rounded-lg shadow mt-10">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Edit Staff</h1>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('staff.update', $staff->id) }}" class="space-y-4" autocomplete="off">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" required value="{{ old('name', $staff->name) }}"
                    class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" required value="{{ old('email', $staff->email) }}"
                    class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Password <span class="text-gray-500 text-xs">(leave blank to keep current)</span>
                </label>
                <input type="password" name="password" class="mt-1 w-full p-2 border border-gray-300 rounded-md">
            </div>

            @if (auth()->user()->isHead())
                <input type="hidden" name="office_id" value="{{ auth()->user()->office_id }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <input type="text" value="Staff" disabled
                        class="mt-1 w-full p-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Office</label>
                    <select id="officeSelect" required
                        class="mt-1 w-full border border-green-400 rounded p-2 bg-gray-100 focus:ring-green-500 focus:border-green-500">
                        <option value="{{ auth()->user()->office_id }}"
                            data-abbreviation="{{ auth()->user()->office->abbreviation }}" selected>
                            {{ auth()->user()->office->name }}
                        </option>
                    </select>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" required class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                        <option value="staff" {{ $staff->role === 'staff' ? 'selected' : '' }}>
                            Staff
                        </option>
                        <option value="head" {{ $staff->role === 'head' ? 'selected' : '' }}>
                            Head
                        </option>
                        <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Office</label>

                    @if ($staff->role === 'admin')
                        <input type="text" value="N/A" disabled
                            class="mt-1 w-full p-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                        <input type="hidden" name="office_id" value="">
                    @else
                        <select name="office_id" id="officeSelect" required
                            class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                            <option value="" disabled>-- Select Office --</option>

                            @foreach ($offices as $office)
                                <option value="{{ $office->id }}" data-abbreviation="{{ $office->abbreviation }}"
                                    {{ $staff->office_id == $office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endif

            <div id="courseAssignment" class="hidden">
                <label class="block text-sm font-medium text-green-700 mb-2">
                    Assign Courses
                </label>

                <div class="border border-green-300 rounded p-3 bg-white max-h-48 overflow-y-auto space-y-2">
                    @foreach ($courses as $course)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                {{ $staff->courses->contains($course->id) ? 'checked' : '' }}
                                class="rounded border-green-400 text-green-600 focus:ring-green-500">

                            <span class="text-sm text-gray-700">
                                {{ $course->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    Select all courses this Registrar staff member can handle.
                </p>
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white font-semibold py-2 rounded hover:bg-green-700 transition">
                Update Staff
            </button>
        </form>
    </div>

    @vite('resources/js/staff-management.js')
</x-guest-layout>
