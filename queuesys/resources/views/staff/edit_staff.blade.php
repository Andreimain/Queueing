<x-app-layout>
    <x-slot name="title">
        Edit Staff
    </x-slot>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">
                Edit Staff
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Update staff account information and assignments.
            </p>
        </div>
    </x-slot>

    @php
        $isHead = auth()->user()->isHead();
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

                {{-- Card Header --}}
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-green-100 text-green-700 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m12-13a4 4 0 11-8 0 4 4 0 018 0zm2 4h4m-2-2v4" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Staff Information
                            </h3>
                            <p class="text-sm text-gray-500">
                                Editing {{ $staff->name }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">

                    {{-- Alerts --}}
                    @if ($errors->any())
                        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>

                                <div>
                                    <p class="font-semibold text-red-800 text-sm">
                                        Please check the following:
                                    </p>
                                    <ul class="list-disc list-inside text-sm text-red-700 mt-1 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4">
                            <div class="flex items-center gap-2 text-sm font-medium text-green-800">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.update', $staff->id) }}"
                        class="space-y-6" autocomplete="off">
                        @csrf
                        @method('PUT')

                        {{-- Account Details --}}
                        <div>
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                                    Account Details
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    Basic information associated with this account.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Full Name
                                    </label>
                                    <input type="text" id="name" name="name" required
                                        value="{{ old('name', $staff->name) }}"
                                        class="mt-1.5 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                                        placeholder="Enter full name">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Email Address
                                    </label>
                                    <input type="email" id="email" name="email" required
                                        value="{{ old('email', $staff->email) }}"
                                        class="mt-1.5 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                                        placeholder="Enter email address">
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="password" class="block text-sm font-medium text-gray-700">
                                        New Password
                                    </label>
                                    <input type="password" id="password" name="password"
                                        class="mt-1.5 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                                        placeholder="Leave blank to keep current password">

                                    <p class="text-xs text-gray-500 mt-1.5">
                                        Leave this field blank if you don't want to change the password.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        {{-- Role and Office --}}
                        <div>
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                                    Role and Office
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    Manage the staff member's role and office assignment.
                                </p>
                            </div>

                            @if ($isHead)
                                <input type="hidden" name="office_id" value="{{ auth()->user()->office_id }}">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Role
                                        </label>
                                        <input type="text" value="Staff" disabled
                                            class="mt-1.5 w-full rounded-lg border-gray-300 bg-gray-100 text-gray-600 text-sm shadow-sm">
                                    </div>

                                    <div>
                                        <label for="officeSelect" class="block text-sm font-medium text-gray-700">
                                            Office
                                        </label>
                                        <select id="officeSelect" required
                                            class="mt-1.5 w-full rounded-lg border-gray-300 bg-gray-100 text-gray-600 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                                            <option value="{{ auth()->user()->office_id }}"
                                                data-abbreviation="{{ auth()->user()->office->abbreviation }}"
                                                selected>
                                                {{ auth()->user()->office->name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label for="roleSelect" class="block text-sm font-medium text-gray-700">
                                            Role
                                        </label>
                                        <select name="role" id="roleSelect" required
                                            class="mt-1.5 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                                            <option value="staff" {{ $staff->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                            <option value="head" {{ $staff->role === 'head' ? 'selected' : '' }}>Head</option>
                                            <option value="guard" {{ $staff->role === 'guard' ? 'selected' : '' }}>Guard</option>
                                            <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </div>

                                    <div id="officeContainer">
                                        <label for="officeSelect" class="block text-sm font-medium text-gray-700">
                                            Office
                                        </label>
                                        <select name="office_id" id="officeSelect" required
                                            class="mt-1.5 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                                            <option value="" disabled>-- Select Office --</option>

                                            @foreach ($offices as $office)
                                                <option value="{{ $office->id }}"
                                                    data-abbreviation="{{ $office->abbreviation }}"
                                                    {{ (string) old('office_id', $staff->office_id) === (string) $office->id ? 'selected' : '' }}>
                                                    {{ $office->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Registrar Handle --}}
                        <div id="handleContainer" class="hidden border-t border-gray-100 pt-6">
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                                    Registrar Assignment
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    Select the type of Registrar service this staff member handles.
                                </p>
                            </div>

                            <label for="handleSelect" class="block text-sm font-medium text-gray-700">
                                Handle
                            </label>
                            <select name="handle" id="handleSelect"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">-- Select Handle --</option>
                                <option value="Enrollment" {{ old('handle', $staff->handle) === 'Enrollment' ? 'selected' : '' }}>
                                    Enrollment
                                </option>
                                <option value="Document Request" {{ old('handle', $staff->handle) === 'Document Request' ? 'selected' : '' }}>
                                    Document Request
                                </option>
                            </select>
                        </div>

                        {{-- Course Assignment --}}
                        <div id="courseAssignment" class="hidden border-t border-gray-100 pt-6">
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-800">
                                    Assign Courses
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    Select all courses this Registrar Enrollment staff member can handle.
                                </p>
                            </div>

                            <div class="border border-gray-200 rounded-xl bg-gray-50 p-4 max-h-60 overflow-y-auto">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($courses as $course)
                                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-3 cursor-pointer hover:border-green-300 hover:bg-green-50 transition">
                                            <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                                {{ in_array($course->id, old('courses', $staff->courses->pluck('id')->all())) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                            <span class="text-sm text-gray-700">
                                                {{ $course->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-gray-100 pt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <a href="{{ url()->previous() }}"
                                class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                Cancel
                            </a>

                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-green-600 text-white text-sm font-semibold shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Update Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    @vite('resources/js/staff-management.js')
</x-app-layout>
