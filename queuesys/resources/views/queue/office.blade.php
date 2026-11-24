<x-app-layout>
    <x-slot name="title">
        Queues
    </x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    {{ $office->name }}
                </h2>
            </div>

            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Live Queue
            </div>
        </div>
    </x-slot>

    @php
        $currentUser = auth()->user();
        $isRegistrar = strtoupper($office->abbreviation) === 'RO';
        $currentHandle = $currentUser->handle ?: 'Enrollment';

        $transferStaff = $allOffices
            ->mapWithKeys(function ($targetOffice) use ($isRegistrar, $office, $currentHandle) {
                $staff = $targetOffice->users
                    ->filter(function ($user) use ($isRegistrar, $office, $targetOffice, $currentHandle) {
                        if ($user->role !== 'staff') {
                            return false;
                        }

                        if ($isRegistrar && $targetOffice->id === $office->id) {
                            return ($user->handle ?: 'Enrollment') === $currentHandle;
                        }

                        return true;
                    })
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                        ];
                    })
                    ->values()
                    ->all();

                return [$targetOffice->id => $staff];
            })
            ->all();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div id="queue-area" class="space-y-6">

                {{-- Queue Overview --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Currently Serving</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">
                                    {{ $serving->count() }}
                                </p>
                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.196-2.038M17 20H7m10 0v-2c0-.653-.126-1.277-.354-1.854M7 20H2v-2a3 3 0 015.196-2.038M7 20v-2c0-.653.126-1.277.354-1.854m0 0a5.002 5.002 0 019.292 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">Active queue transactions</p>
                    </div>

                    <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Waiting in Queue</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">
                                    {{ $waiting->count() }}
                                </p>
                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">Visitors waiting to be served</p>
                    </div>
                </div>

                {{-- Now Serving --}}
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div
                        class="flex flex-col gap-2 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Now Serving</h3>
                            <p class="mt-1 text-sm text-gray-500">Visitors currently being handled by cashiers.</p>
                        </div>

                        <span
                            class="inline-flex w-fit items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ $serving->count() }} Active
                        </span>
                    </div>

                    <div class="p-5 sm:p-6">
                        @if ($serving->count())
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($serving as $visitor)
                                    @php
                                        $cashierColors = [
                                            1 => 'border-emerald-500',
                                            2 => 'border-blue-500',
                                            3 => 'border-purple-500',
                                        ];

                                        $borderColor = $cashierColors[$visitor->cashier_id] ?? 'border-gray-300';
                                        $isMyVisitor = $visitor->cashier_id === auth()->id();
                                    @endphp

                                    <div class="relative overflow-hidden rounded-xl border border-gray-200 border-t-4 bg-white shadow-sm {{ $borderColor }} {{ $isMyVisitor ? 'ring-2 ring-emerald-100' : '' }}"
                                        @if ($isMyVisitor) data-serving="true" @endif>
                                        <div class="p-5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p
                                                        class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                                        Queue Number
                                                    </p>

                                                    <p
                                                        class="mt-1 text-4xl font-extrabold tracking-tight {{ $visitor->priority ? 'text-red-600' : 'text-emerald-700' }}">
                                                        #{{ $visitor->queue_number }}
                                                    </p>
                                                </div>

                                                @if ($visitor->priority)
                                                    <span
                                                        class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                                        Priority
                                                    </span>
                                                @else
                                                    <span
                                                        class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                        Serving
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-4 border-t border-gray-100 pt-4">
                                                <p class="truncate text-lg font-bold text-gray-900">
                                                    {{ $visitor->name }}
                                                </p>

                                                <p class="mt-1 text-sm font-medium text-gray-500">
                                                    Ticket: {{ $visitor->ticket_number }}
                                                </p>

                                                <div class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                                                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m16 0v-2a4 4 0 00-4-4h-1m-3-9a4 4 0 11-8 0 4 4 0 018 0zm7 4a3 3 0 10-3-3" />
                                                    </svg>
                                                    <span class="truncate">
                                                        {{ $visitor->cashier->name ?? 'Unassigned' }}
                                                    </span>

                                                    @if ($isMyVisitor)
                                                        <span
                                                            class="ml-auto shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                                            You
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Transfer Form --}}
                                            @if (auth()->user()->isStaff() && $isMyVisitor)
                                                <form method="POST"
                                                    action="{{ route('office.queue.transfer', $visitor->id) }}"
                                                    class="transfer-form mt-5 border-t border-gray-100 pt-4">
                                                    @csrf

                                                    <label class="mb-1 block text-xs font-semibold text-gray-600">
                                                        Transfer to Office
                                                    </label>

                                                    <select name="new_office_id"
                                                        class="transfer-office w-full rounded-lg border-gray-300 bg-gray-50 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                        data-visitor-id="{{ $visitor->id }}" required>
                                                        <option value="" disabled selected>Choose an office
                                                        </option>

                                                        <option value="{{ $office->id }}">
                                                            {{ $office->name }}
                                                        </option>

                                                        @foreach ($allOffices as $targetOffice)
                                                            @if ($targetOffice->id !== $office->id)
                                                                <option value="{{ $targetOffice->id }}">
                                                                    {{ $targetOffice->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>

                                                    <label class="mb-1 mt-3 block text-xs font-semibold text-gray-600">
                                                        Transfer to Staff
                                                    </label>

                                                    <select name="new_cashier_id"
                                                        class="transfer-staff w-full rounded-lg border-gray-300 bg-gray-50 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                        data-visitor-id="{{ $visitor->id }}" disabled>
                                                        <option value="" disabled selected>Choose a staff member
                                                        </option>
                                                    </select>

                                                    <button type="submit"
                                                        class="mt-3 w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                        Transfer Visitor
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-5 py-12 text-center">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.196-2.038M17 20H7m10 0v-2c0-.653-.126-1.277-.354-1.854M7 20H2v-2a3 3 0 015.196-2.038M7 20v-2c0-.653.126-1.277.354-1.854m0 0a5.002 5.002 0 019.292 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>

                                <h4 class="mt-4 font-semibold text-gray-700">No Active Transactions</h4>
                                <p class="mt-1 text-sm text-gray-500">
                                    No visitors are currently being served.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Queue Actions --}}
                @if (auth()->user()->isStaff())
                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-4">
                            <h3 class="font-bold text-gray-900">Queue Actions</h3>
                            <p class="mt-1 text-sm text-gray-500">Manage the current queue transactions.</p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            {{-- Serve Next --}}
                            @if ($waiting->count() > 0 || !$serving->count())
                                <form method="POST" action="{{ route('office.queue.next', $office->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 5l7 7m0 0l-7 7m7-7H4" />
                                        </svg>
                                        Serve Next
                                    </button>
                                </form>
                            @endif

                            {{-- Skip --}}
                            @if ($serving->count())
                                <form method="POST" action="{{ route('office.queue.skip', $office->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 5l7 7m0 0l-7 7m7-7H4" />
                                        </svg>
                                        Skip
                                    </button>
                                </form>
                            @endif

                            {{-- Done --}}
                            @if ($serving->count())
                                <form method="POST" action="{{ route('office.queue.done', $office->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Done
                                    </button>
                                </form>
                            @endif

                            {{-- Call Again --}}
                            @if ($serving->contains('cashier_id', auth()->id()))
                                <form method="POST" action="{{ route('office.queue.call-again', $office->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Call Again
                                    </button>
                                </form>
                            @endif
                        </div>
                    </section>
                @endif

                {{-- Waiting List --}}
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div
                        class="flex flex-col gap-3 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Waiting List</h3>
                            <p class="mt-1 text-sm text-gray-500">Visitors waiting for their turn.</p>
                        </div>

                        <span
                            class="inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                            {{ $waiting->count() }} Waiting
                        </span>
                    </div>

                    <div class="p-5 sm:p-6">
                        @if ($waiting->count())
                            @if (auth()->user()->isStaff())
                                <form id="skipForm" method="POST"
                                    action="{{ route('office.queue.skip', $office->id) }}">
                                    @csrf

                                    <div class="mb-4 flex items-center justify-between gap-3">
                                        <p class="text-sm text-gray-500">
                                            Select visitors to skip.
                                        </p>

                                        <span class="text-xs font-medium text-gray-400">
                                            {{ $waiting->count() }} entries
                                        </span>
                                    </div>

                                    <ul class="space-y-3">
                                        @foreach ($waiting as $visitor)
                                            <li
                                                class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-4 transition hover:border-emerald-200 hover:bg-emerald-50/30 sm:flex-row sm:items-center sm:justify-between">
                                                <div class="flex min-w-0 items-center gap-3">
                                                    <input type="checkbox" name="selected_visitors[]"
                                                        value="{{ $visitor->id }}"
                                                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">

                                                    <div
                                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $visitor->priority ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                                        <span class="text-sm font-bold">
                                                            #{{ $visitor->queue_number }}
                                                        </span>
                                                    </div>

                                                    <div class="min-w-0">
                                                        <p class="truncate font-semibold text-gray-900">
                                                            {{ $visitor->name }}
                                                        </p>
                                                        <p class="mt-0.5 text-xs text-gray-500">
                                                            Ticket: {{ $visitor->ticket_number }}
                                                        </p>
                                                    </div>
                                                </div>

                                                @if ($visitor->priority)
                                                    <span
                                                        class="ml-14 w-fit rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 sm:ml-0">
                                                        Priority
                                                    </span>
                                                @else
                                                    <span
                                                        class="ml-14 w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 sm:ml-0">
                                                        Waiting
                                                    </span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div
                                        class="mt-5 flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                        <p class="text-xs text-gray-500">
                                            Only selected visitors will be skipped.
                                        </p>

                                        <button type="submit"
                                            class="rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            id="skipSelectedBtn" disabled>
                                            Skip Selected
                                        </button>
                                    </div>
                                </form>
                            @else
                                <ul class="space-y-3">
                                    @foreach ($waiting as $visitor)
                                        <li
                                            class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <div
                                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $visitor->priority ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                                    <span class="text-sm font-bold">
                                                        #{{ $visitor->queue_number }}
                                                    </span>
                                                </div>

                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold text-gray-900">
                                                        {{ $visitor->name }}
                                                    </p>
                                                    <p class="mt-0.5 text-xs text-gray-500">
                                                        Ticket: {{ $visitor->ticket_number }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($visitor->priority)
                                                <span
                                                    class="ml-14 w-fit rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 sm:ml-0">
                                                    Priority
                                                </span>
                                            @else
                                                <span
                                                    class="ml-14 w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 sm:ml-0">
                                                    Waiting
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        @else
                            <div
                                class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-5 py-12 text-center">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                                    </svg>
                                </div>

                                <h4 class="mt-4 font-semibold text-gray-700">Queue is Empty</h4>
                                <p class="mt-1 text-sm text-gray-500">
                                    There are currently no visitors waiting.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>

            </div>
        </div>
    </div>

    {{-- Transfer Confirmation Modal --}}
    <div id="transferModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-950/60 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl bg-white p-7 text-center shadow-2xl">
            <div
                class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h3 class="mt-4 text-xl font-bold text-gray-900">
                Transfer Successful
            </h3>

            <p class="mt-3 text-sm leading-relaxed text-gray-600">
                Your visitor has been transferred successfully.
            </p>

            <div class="mt-4 rounded-xl bg-amber-50 p-4 text-left text-sm text-amber-800">
                <strong>Important:</strong>
                Please inform the visitor that they will keep the same ticket.
            </div>

            <button id="closeModalBtn"
                class="mt-6 w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                Close
            </button>
        </div>
    </div>

    <script>
        window.currentOfficeId = {{ $office->id }};
        window.currentUserId = {{ auth()->id() }};
        window.transferStaff = {!! json_encode($transferStaff) !!};
    </script>

    @vite('resources/js/queue-refresh.js')
    @vite('resources/js/transfer-modal.js')
</x-app-layout>
