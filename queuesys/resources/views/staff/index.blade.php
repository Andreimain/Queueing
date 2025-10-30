<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Management
        </h2>
    </x-slot>

    <div class="py-6">
        <!-- Wider container -->
        <div class="mx-auto w-full max-w-[95rem] sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Grid with smaller left column -->
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_1.8fr] gap-10 items-start">

                        <!-- Register New User (narrower card) -->
                        <div
                            class="bg-green-50 border border-green-200 rounded-lg p-6 shadow flex flex-col justify-between">
                            <h3 class="text-lg font-semibold text-green-700 mb-4">Register New User</h3>

                            <form action="{{ route('staff.store') }}" method="POST" class="space-y-4"
                                autocomplete="off">
                                @csrf

                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-green-700">Name</label>
                                    <input type="text" name="name" required
                                        class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-green-700">Email</label>
                                    <input type="email" name="email" required
                                        class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-green-700">Password</label>
                                    <input type="password" name="password" required
                                        class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                                </div>

                                <!-- Office -->
                                <div>
                                    <label class="block text-sm font-medium text-green-700">Office</label>
                                    <select name="office_id" required
                                        class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                                        <option value="" disabled selected>-- Select Office --</option>
                                        @foreach ($offices as $office)
                                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-6">
                                    <button type="submit"
                                        class="py-2 px-4 rounded shadow font-semibold text-sm text-white"
                                        style="background-color: green;">
                                        Add User
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- User List (wider card) -->
                        <div
                            class="bg-green-50 border border-green-200 rounded-lg p-6 shadow flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-green-700 mb-4">User List</h3>

                                <div class="w-full overflow-hidden">
                                    <table class="w-full table-fixed border border-green-300 text-sm">
                                        <colgroup>
                                            <col style="width:20%">
                                            <col style="width:30%">
                                            <col style="width:10%">
                                            <col style="width:20%">
                                            <col style="width:20%">
                                        </colgroup>

                                        <thead class="bg-green-600 text-black">
                                            <tr>
                                                <th class="px-4 py-2 border border-green-700 text-left">Name</th>
                                                <th class="px-4 py-2 border border-green-700 text-left">Email</th>
                                                <th class="px-4 py-2 border border-green-700 text-left">Role</th>
                                                <th class="px-4 py-2 border border-green-700 text-left">Office</th>
                                                <th class="px-4 py-2 border border-green-700 text-center">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody class="text-black">
                                            @forelse($staff as $person)
                                                <tr class="hover:bg-green-100">
                                                    <td class="px-4 py-2 border border-green-300 align-top truncate"
                                                        title="{{ $person->name }}">
                                                        {{ $person->name }}
                                                    </td>
                                                    <td class="px-4 py-2 border border-green-300 align-top truncate"
                                                        title="{{ $person->email }}">
                                                        {{ $person->email }}
                                                    </td>
                                                    <td class="px-4 py-2 border border-green-300 align-top truncate"
                                                        title="{{ $person->role ?? 'staff' }}">
                                                        {{ $person->role ?? 'staff' }}
                                                    </td>
                                                    <td class="px-4 py-2 border border-green-300 align-top truncate"
                                                        title="{{ $person->office->name ?? 'N/A' }}">
                                                        {{ $person->office->name ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-4 py-2 border border-green-300 text-center align-top">
                                                        <div class="flex justify-center items-center gap-2">
                                                            <a href="{{ route('staff.edit', $person->id) }}"
                                                                class="py-1 px-3 rounded shadow text-sm font-semibold text-white"
                                                                style="background-color: orange;">
                                                                Edit
                                                            </a>

                                                            <form action="{{ route('staff.destroy', $person->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Are you sure you want to delete this staff?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="py-1 px-3 rounded shadow text-sm font-semibold text-white"
                                                                    style="background-color: red;">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                                        No users found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $staff->links() }}
                                </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
