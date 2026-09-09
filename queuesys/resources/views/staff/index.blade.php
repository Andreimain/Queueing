<x-app-layout>
    <x-slot name="title">Users Management</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Management
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[95rem] sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-[1fr_1.8fr] gap-10 items-start">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold text-green-700 mb-4">
                            Register New User
                        </h3>

                        <form action="{{ route('staff.store') }}" method="POST"
                              class="space-y-4" autocomplete="off">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-green-700">
                                    Name
                                </label>
                                <input type="text" name="name" required
                                    class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-green-700">
                                    Email
                                </label>
                                <input type="email" name="email" required
                                    class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-green-700">
                                    Password
                                </label>
                                <input type="password" name="password" required
                                    class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-green-700">
                                    Role
                                </label>

                                @if(auth()->user()->isHead())
                                    <input type="hidden" name="role" value="staff">

                                    <input type="text" value="Staff" disabled
                                        class="mt-1 w-full border border-green-400 rounded p-2 bg-gray-100 text-gray-600">
                                @else
                                    <select name="role" id="roleSelect" required
                                        class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                                        <option value="" disabled selected>-- Select Role --</option>
                                        <option value="staff">Staff</option>
                                        <option value="head">Head</option>
                                        <option value="guard">Guard</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                @endif
                            </div>

                            <div id="officeContainer">
                                <label class="block text-sm font-medium text-green-700">
                                    Office
                                </label>

                                <select name="office_id" id="officeSelect" required
                                    class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500">
                                    @if(auth()->user()->isHead())
                                        <option value="{{ auth()->user()->office_id }}"
                                            data-abbreviation="{{ auth()->user()->office->abbreviation }}">
                                            {{ auth()->user()->office->name }}
                                        </option>
                                    @else
                                        <option value="" disabled selected>
                                            -- Select Office --
                                        </option>

                                        @foreach ($offices as $office)
                                            <option value="{{ $office->id }}"
                                                data-abbreviation="{{ $office->abbreviation }}">
                                                {{ $office->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div id="courseAssignment" class="hidden">
                                <label class="block text-sm font-medium text-green-700 mb-2">
                                    Assign Courses
                                </label>

                                <div class="border border-green-300 rounded p-3 bg-white max-h-48 overflow-y-auto space-y-2">
                                    @forelse ($courses as $course)
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox"
                                                name="courses[]"
                                                value="{{ $course->id }}"
                                                class="rounded border-green-400 text-green-600 focus:ring-green-500">

                                            <span class="text-sm text-gray-700">
                                                {{ $course->name }}
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-gray-500">
                                            No courses available.
                                        </p>
                                    @endforelse
                                </div>

                                <p class="text-xs text-gray-500 mt-1">
                                    Select all courses this Registrar staff member can handle.
                                </p>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="py-2 px-4 rounded shadow font-semibold text-sm text-white bg-green-600 hover:bg-green-700 transition">
                                    Add User
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold text-green-700 mb-4">
                            User List
                        </h3>

                        <div class="w-full overflow-x-auto">
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
                                        <th class="px-4 py-2 border border-green-700 text-left">
                                            Name
                                        </th>
                                        <th class="px-4 py-2 border border-green-700 text-left">
                                            Email
                                        </th>
                                        <th class="px-4 py-2 border border-green-700 text-left">
                                            Role
                                        </th>
                                        <th class="px-4 py-2 border border-green-700 text-left">
                                            Office
                                        </th>
                                        <th class="px-4 py-2 border border-green-700 text-center">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="text-black">
                                    @forelse ($staff as $person)
                                        <tr class="hover:bg-green-100">
                                            <td class="px-4 py-2 border border-green-300 truncate"
                                                title="{{ $person->name }}">
                                                {{ $person->name }}
                                            </td>

                                            <td class="px-4 py-2 border border-green-300 truncate"
                                                title="{{ $person->email }}">
                                                {{ $person->email }}
                                            </td>

                                            <td class="px-4 py-2 border border-green-300 truncate">
                                                {{ $person->role ?? 'staff' }}
                                            </td>

                                            <td class="px-4 py-2 border border-green-300 truncate"
                                                title="{{ $person->office->name ?? 'N/A' }}">
                                                {{ $person->office->name ?? 'N/A' }}
                                            </td>

                                            <td class="px-4 py-2 border border-green-300 text-center">
                                                <div class="flex justify-center items-center gap-2">
                                                    <a href="{{ route('staff.edit', $person->id) }}"
                                                        class="py-1 px-3 rounded shadow text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600">
                                                        Edit
                                                    </a>

                                                    <form action="{{ route('staff.destroy', $person->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Are you sure you want to delete this staff?');">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                            class="py-1 px-3 rounded shadow text-sm font-semibold text-white bg-red-600 hover:bg-red-700">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5"
                                                class="px-4 py-4 text-center text-gray-500">
                                                No users found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $staff->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/staff-management.js')
</x-app-layout>
