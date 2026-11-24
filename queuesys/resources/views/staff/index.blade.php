<x-app-layout>
    <x-slot name="title">Users Management</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            User Management
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto w-full max-w-[95rem] px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m5 12 4 4L19 6" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(320px,0.85fr)_minmax(0,2fr)]">

                {{-- Register New User --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 bg-gray-50/70 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M12 5v14m-7-7h14" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">
                                    Register New User
                                </h4>
                                <p class="text-xs text-gray-500">
                                    Create a new system account.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <form action="{{ route('staff.store') }}" method="POST" class="space-y-4" autocomplete="off">
                            @csrf

                            {{-- Name --}}
                            <div>
                                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Name
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    placeholder="Enter full name"
                                    class="w-full rounded-xl border-gray-200 px-3 py-2.5 text-sm shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="Enter email address"
                                    class="w-full rounded-xl border-gray-200 px-3 py-2.5 text-sm shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Password
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    placeholder="Enter password"
                                    class="w-full rounded-xl border-gray-200 px-3 py-2.5 text-sm shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Role --}}
                            <div>
                                <label for="roleSelect" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Role
                                </label>

                                @if(auth()->user()->isHead())
                                    <input type="hidden" name="role" value="staff">
                                    <input
                                        type="text"
                                        value="Staff"
                                        disabled
                                        class="w-full rounded-xl border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-600"
                                    >
                                @else
                                    <select
                                        name="role"
                                        id="roleSelect"
                                        required
                                        class="w-full rounded-xl border-gray-200 px-3 py-2.5 text-sm shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500"
                                    >
                                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>
                                            -- Select Role --
                                        </option>
                                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="head" {{ old('role') === 'head' ? 'selected' : '' }}>Head</option>
                                        <option value="guard" {{ old('role') === 'guard' ? 'selected' : '' }}>Guard</option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                @endif

                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Office --}}
                            <div id="officeContainer">
                                <label for="officeSelect" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Office
                                </label>

                                <select
                                    name="office_id"
                                    id="officeSelect"
                                    required
                                    class="w-full rounded-xl border-gray-200 px-3 py-2.5 text-sm shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    @if(auth()->user()->isHead())
                                        <option
                                            value="{{ auth()->user()->office_id }}"
                                            data-abbreviation="{{ auth()->user()->office->abbreviation }}"
                                            selected
                                        >
                                            {{ auth()->user()->office->name }}
                                        </option>
                                    @else
                                        <option value="" disabled {{ old('office_id') ? '' : 'selected' }}>
                                            -- Select Office --
                                        </option>
                                        @foreach ($offices as $office)
                                            <option
                                                value="{{ $office->id }}"
                                                data-abbreviation="{{ $office->abbreviation }}"
                                                {{ (string) old('office_id') === (string) $office->id ? 'selected' : '' }}
                                            >
                                                {{ $office->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                                @error('office_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Handle --}}
                            <div id="handleContainer" class="hidden">
                                <label for="handleSelect" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Handle
                                </label>

                                <select
                                    name="handle"
                                    id="handleSelect"
                                    class="w-full rounded-xl border-gray-200 px-3 py-2.5 text-sm shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="">-- Select Handle --</option>
                                    <option value="Enrollment" {{ old('handle') === 'Enrollment' ? 'selected' : '' }}>
                                        Enrollment
                                    </option>
                                    <option value="Document Request" {{ old('handle') === 'Document Request' ? 'selected' : '' }}>
                                        Document Request
                                    </option>
                                </select>

                                @error('handle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Course Assignment --}}
                            <div id="courseAssignment" class="hidden">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Assign Courses
                                </label>

                                <div class="max-h-48 space-y-2 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50 p-3">
                                    @forelse ($courses as $course)
                                        <label class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 transition hover:bg-white">
                                            <input
                                                type="checkbox"
                                                name="courses[]"
                                                value="{{ $course->id }}"
                                                {{ in_array($course->id, old('courses', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                            >
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

                                <p class="mt-2 text-xs text-gray-500">
                                    Select all courses this Registrar Enrollment staff member can handle.
                                </p>
                            </div>

                            @error('courses')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- Submit --}}
                            <div class="border-t border-gray-100 pt-4">
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v14m-7-7h14" />
                                    </svg>
                                    Add User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- User List --}}
                <div class="min-w-0 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h4 class="font-semibold text-gray-900">
                                User List
                            </h4>
                            <p class="mt-0.5 text-sm text-gray-500">
                                View and manage registered system users.
                            </p>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ $staff->total() }} {{ $tab === 'deleted' ? 'Deleted' : 'Users' }}
                        </span>
                    </div>

                    {{-- Active / Deleted Tabs --}}
                    @if(auth()->user()->isSuperAdmin())
                        <div class="flex gap-5 border-b border-gray-100 px-5 sm:px-6">
                            <a
                                href="{{ route('staff.index', ['tab' => 'active']) }}"
                                class="relative inline-flex items-center gap-2 py-3 text-sm font-semibold transition
                                    {{ $tab === 'active'
                                        ? 'text-emerald-700 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-full after:bg-emerald-600'
                                        : 'text-gray-500 hover:text-gray-800' }}"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m5 12 4 4L19 6" />
                                </svg>
                                Active
                            </a>

                            <a
                                href="{{ route('staff.index', ['tab' => 'deleted']) }}"
                                class="relative inline-flex items-center gap-2 py-3 text-sm font-semibold transition
                                    {{ $tab === 'deleted'
                                        ? 'text-orange-700 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-full after:bg-orange-500'
                                        : 'text-gray-500 hover:text-gray-800' }}"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 6h18m-2 0-1 14H6L5 6m3 0V4h8v2" />
                                </svg>
                                Deleted
                            </a>
                        </div>
                    @endif

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[850px] text-sm">
                            <thead class="{{ $tab === 'deleted' ? 'bg-orange-50' : 'bg-gray-50' }}">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    <th class="px-4 py-4 sm:px-5">Name</th>
                                    <th class="px-4 py-4 sm:px-5">Email</th>
                                    <th class="px-4 py-4 sm:px-5">Role</th>
                                    <th class="px-4 py-4 sm:px-5">Office</th>
                                    <th class="px-4 py-4 sm:px-5">Handle</th>
                                    <th class="px-4 py-4 text-center sm:px-5">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse ($staff as $person)
                                    <tr class="transition {{ $tab === 'deleted' ? 'bg-orange-50/30 hover:bg-orange-50' : 'hover:bg-gray-50/80' }}">

                                        <td class="px-4 py-4 sm:px-5">
                                            <span class="font-semibold text-gray-900">
                                                {{ $person->name }}
                                            </span>
                                        </td>

                                        <td class="break-all px-4 py-4 text-gray-600 sm:px-5">
                                            {{ $person->email }}
                                        </td>

                                        <td class="px-4 py-4 sm:px-5">
                                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium capitalize text-gray-700">
                                                {{ $person->role ?? 'staff' }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4 text-gray-600 sm:px-5">
                                            {{ $person->office->name ?? 'N/A' }}
                                        </td>

                                        <td class="px-4 py-4 text-gray-600 sm:px-5">
                                            {{ $person->handle ?? 'N/A' }}
                                        </td>

                                        <td class="px-4 py-4 sm:px-5">
                                            <div class="flex items-center justify-center gap-2">

                                                @if($tab === 'deleted' && auth()->user()->isSuperAdmin())
                                                    <form
                                                        action="{{ route('staff.restore', $person->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to restore this user?');"
                                                    >
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                                                        >
                                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M3 12a9 9 0 1 0 3-6.7L3 8m0-5v5h5" />
                                                            </svg>
                                                            Restore
                                                        </button>
                                                    </form>
                                                @else
                                                    <a
                                                        href="{{ route('staff.edit', $person->id) }}"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700"
                                                    >
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="m16 4 4 4M4 20l4-1 11-11a2.8 2.8 0 0 0-4-4L4 15l-1 5Z" />
                                                        </svg>
                                                        Edit
                                                    </a>

                                                    @if(auth()->user()->isSuperAdmin())
                                                        <form
                                                            action="{{ route('staff.destroy', $person->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this user?');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700"
                                                            >
                                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M3 6h18m-2 0-1 14H6L5 6m3 0V4h8v2" />
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-14 text-center">
                                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                        d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75M14 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                                                </svg>
                                            </div>
                                            <p class="mt-4 font-semibold text-gray-700">
                                                {{ $tab === 'deleted' ? 'No deleted users found' : 'No users found' }}
                                            </p>
                                            <p class="mt-1 text-sm text-gray-500">
                                                There are no records to display.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($staff->hasPages())
                        <div class="border-t border-gray-100 px-5 py-4 sm:px-6">
                            {{ $staff->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/staff-management.js')
</x-app-layout>
