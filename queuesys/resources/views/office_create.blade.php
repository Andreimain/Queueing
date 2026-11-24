<x-app-layout>
    <x-slot name="title">
        Offices Management
    </x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Office Management
        </h2>
    </x-slot>

    @php
        $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

            {{-- Error Message --}}
            @if (session('error'))
                <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v4m0 4h.01M10.3 3.9 2.8 17.5A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <div class="mb-2 flex items-center gap-2 font-semibold">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v4m0 4h.01M10.3 3.9 2.8 17.5A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                        </svg>
                        Please check the following:
                    </div>

                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(300px,0.85fr)_minmax(0,1.5fr)]">

                {{-- Office Form --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="border-b border-gray-100 bg-gray-50/70 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M3 21h18M5 21V7l8-4v18m-8-9h8m-8 4h8m4-5h2m-2 4h2m-2 4h2" />
                                </svg>
                            </div>

                            <div>
                                <h4 id="formTitle" class="font-semibold text-gray-900">
                                    Add New Office
                                </h4>
                                <p class="text-xs text-gray-500">
                                    Enter the office details below.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <form
                            id="officeForm"
                            method="POST"
                            action="{{ route('admin.offices.store') }}"
                            class="space-y-5"
                        >
                            @csrf

                            <input type="hidden" id="office_id" name="office_id">

                            {{-- Office Name --}}
                            <div>
                                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Office Name
                                </label>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    maxlength="255"
                                    placeholder="Enter office name"
                                    class="w-full rounded-xl border-gray-200 px-3 py-2.5 text-sm shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500"
                                >
                            </div>

                            {{-- Abbreviation --}}
                            <div>
                                <label for="abbreviation" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Abbreviation
                                </label>

                                <input
                                    type="text"
                                    id="abbreviation"
                                    name="abbreviation"
                                    value="{{ old('abbreviation') }}"
                                    required
                                    maxlength="10"
                                    placeholder="e.g. RO"
                                    class="w-full rounded-xl border-gray-200 px-3 py-2.5 text-sm uppercase shadow-sm transition focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                <p class="mt-1.5 text-xs text-gray-400">
                                    Maximum of 10 characters.
                                </p>
                            </div>

                            {{-- Form Actions --}}
                            <div class="flex flex-col gap-2 border-t border-gray-100 pt-4 sm:flex-row">
                                <button
                                    type="submit"
                                    id="submitBtn"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v14m-7-7h14" />
                                    </svg>
                                    Add Office
                                </button>

                                <button
                                    type="button"
                                    id="cancelBtn"
                                    class="hidden items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Existing Offices --}}
                <div class="min-w-0 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h4 class="font-semibold text-gray-900">
                                Existing Offices
                            </h4>
                            <p class="mt-0.5 text-sm text-gray-500">
                                View and manage registered offices.
                            </p>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full {{ $tab === 'deleted' ? 'bg-orange-50 text-orange-700' : 'bg-emerald-50 text-emerald-700' }} px-3 py-1 text-xs font-semibold">
                            {{ $offices->count() }} {{ $tab === 'deleted' ? 'Deleted' : 'Offices' }}
                        </span>
                    </div>

                    {{-- Active / Deleted Tabs --}}
                    @if ($isSuperAdmin)
                        <div class="flex gap-5 border-b border-gray-100 px-5 sm:px-6">
                            <a
                                href="{{ route('admin.offices.create', ['tab' => 'active']) }}"
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
                                href="{{ route('admin.offices.create', ['tab' => 'deleted']) }}"
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

                    {{-- Office Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[500px] text-sm">
                            <thead class="{{ $tab === 'deleted' ? 'bg-orange-50' : 'bg-gray-50' }}">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    <th class="px-5 py-4">Office Name</th>
                                    <th class="px-5 py-4">Abbreviation</th>
                                    <th class="px-5 py-4 text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse ($offices as $office)
                                    <tr class="transition {{ $tab === 'deleted' ? 'bg-orange-50/30 hover:bg-orange-50' : 'hover:bg-gray-50/80' }}">

                                        <td class="px-5 py-4">
                                            <span class="font-semibold text-gray-900">
                                                {{ $office->name }}
                                            </span>
                                        </td>

                                        <td class="px-5 py-4">
                                            <span class="inline-flex rounded-lg bg-gray-100 px-2.5 py-1 font-mono text-xs font-bold text-gray-700">
                                                {{ $office->abbreviation }}
                                            </span>
                                        </td>

                                        <td class="px-5 py-4">
                                            <div class="flex items-center justify-center gap-2">

                                                @if ($tab === 'active')
                                                    <button
                                                        type="button"
                                                        class="editOfficeBtn inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700"
                                                        data-id="{{ $office->id }}"
                                                        data-name="{{ $office->name }}"
                                                        data-abbreviation="{{ $office->abbreviation }}"
                                                    >
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="m16 4 4 4M4 20l4-1 11-11a2.8 2.8 0 0 0-4-4L4 15l-1 5Z" />
                                                        </svg>
                                                        Edit
                                                    </button>

                                                    @if ($isSuperAdmin)
                                                        <form
                                                            method="POST"
                                                            action="{{ route('admin.offices.destroy', $office->id) }}"
                                                            onsubmit="return confirm('Move this office to Deleted?')"
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

                                                @elseif ($isSuperAdmin)
                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.offices.restore', $office->id) }}"
                                                        onsubmit="return confirm('Restore this office?')"
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
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-14 text-center">
                                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                        d="M3 21h18M5 21V7l8-4v18m-8-9h8m-8 4h8m4-5h2m-2 4h2m-2 4h2" />
                                                </svg>
                                            </div>

                                            <p class="mt-4 font-semibold text-gray-700">
                                                No {{ $tab === 'deleted' ? 'deleted' : 'active' }} offices found
                                            </p>
                                            <p class="mt-1 text-sm text-gray-500">
                                                There are no office records to display.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/offices.js')
</x-app-layout>
