<nav x-data="{ open: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-sm">
    @php
        $user = Auth::user();
        $isAdminUser = $user->isAdmin() || $user->isSuperAdmin();
        $isGuardUser = $user->isGuard();
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Left Side --}}
            <div class="flex items-center gap-8">

                {{-- Logo --}}
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-2.5 shrink-0 group">

                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h14a1 1 0 001-1V10" />
                        </svg>
                    </div>

                    <div class="hidden lg:block">
                        <span class="block text-sm font-bold text-gray-800 leading-tight">
                            Queue Directory
                        </span>
                        <span class="block text-[10px] font-medium tracking-wider uppercase text-gray-400">
                            Management System
                        </span>
                    </div>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden sm:flex items-center gap-1">

                    @unless($isGuardUser)

                        {{-- Queues --}}
                        @if($isAdminUser)
                            <div x-data="{ show: false }"
                                class="relative"
                                @mouseenter="show = true"
                                @mouseleave="show = false">

                                <button
                                    @click="show = !show"
                                    :aria-expanded="show"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition
                                    {{ request()->routeIs('office.queue') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-700' }}">

                                    <svg class="w-4 h-4" fill="none"
                                        stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>

                                    Queues

                                    <svg class="w-4 h-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': show }"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="show"
                                    x-cloak
                                    @click.outside="show = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                    class="absolute left-0 top-full mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">

                                    <div class="px-4 py-2 mb-1">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">
                                            Office Queues
                                        </p>
                                    </div>

                                    @foreach($offices as $office)
                                        <a href="{{ route('office.queue', $office->id) }}"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm transition
                                            {{ request()->routeIs('office.queue') && request()->route('officeId') == $office->id ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-700' }}">

                                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500">
                                                <svg class="w-4 h-4" fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M3 7h18M3 12h18M3 17h18" />
                                                </svg>
                                            </span>

                                            {{ $office->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($user->office_id)
                            <a href="{{ route('office.queue', $user->office_id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition
                                {{ request()->routeIs('office.queue') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-700' }}">

                                <svg class="w-4 h-4" fill="none"
                                    stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>

                                Queues
                            </a>
                        @endif

                        {{-- View Skipped --}}
                        <a href="{{ route('skipped.list') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition
                            {{ request()->routeIs('skipped.list') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-700' }}">

                            <svg class="w-4 h-4" fill="none"
                                stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8 7h12M8 12h12M8 17h12M3 7h.01M3 12h.01M3 17h.01" />
                            </svg>

                            View Skipped
                        </a>

                        {{-- Manage Users --}}
                        @if($user->isAdmin() || $user->isSuperAdmin() || $user->isHead())
                            <a href="{{ route('staff.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition
                                {{ request()->routeIs('staff.index') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-700' }}">

                                <svg class="w-4 h-4" fill="none"
                                    stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17 20h5v-2a3 3 0 00-5.196-2.043M17 20H7m10 0v-2c0-.653-.126-1.277-.354-1.847M7 20H2v-2a3 3 0 015.196-2.043M7 20v-2c0-.653.126-1.277.354-1.847m0 0a5.002 5.002 0 019.292 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>

                                Manage Users
                            </a>
                        @endif

                        {{-- Manage Office --}}
                        @if($user->isAdmin() || $user->isSuperAdmin())
                            <a href="{{ route('admin.offices.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition
                                {{ request()->routeIs('admin.offices.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-700' }}">

                                <svg class="w-4 h-4" fill="none"
                                    stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01" />
                                </svg>

                                Manage Office
                            </a>
                        @endif

                    @endunless
                </div>
            </div>

            {{-- Desktop User Menu --}}
            <div class="hidden sm:flex items-center">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-gray-50 transition focus:outline-none">

                            <div class="flex items-center justify-center w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>

                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-800 leading-tight">
                                    {{ $user->name }}
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </p>
                            </div>

                            <svg class="w-4 h-4 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $user->name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $user->email }}
                            </p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none"
                                    stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 12a4 4 0 100-8 4 4 0 000 8zM4 20a8 8 0 0116 0" />
                                </svg>
                                Profile
                            </span>
                        </x-dropdown-link>

                        <div class="border-t border-gray-100"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-red-600 hover:bg-red-50">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none"
                                        stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 17l5-5-5-5M20 12H9m4 8H5a2 2 0 01-2-2V6a2 2 0 012-2h8" />
                                    </svg>
                                    Log Out
                                </span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile Hamburger --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-gray-500 hover:text-emerald-700 hover:bg-emerald-50 transition focus:outline-none">

                    <svg class="w-6 h-6" stroke="currentColor"
                        fill="none" viewBox="0 0 24 24">

                        <path :class="{ 'hidden': open, 'inline-flex': !open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path :class="{ 'hidden': !open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Navigation --}}
    <div x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="sm:hidden border-t border-gray-100 bg-white">

        <div class="px-4 py-4 space-y-2">

            {{-- Mobile User Information --}}
            <div class="flex items-center gap-3 px-3 py-3 mb-3 rounded-xl bg-gray-50">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">
                        {{ $user->name }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </p>
                </div>
            </div>

            @unless($isGuardUser)

                {{-- Mobile Queues --}}
                @if($isAdminUser)
                    <div x-data="{ queuesOpen: {{ request()->routeIs('office.queue') ? 'true' : 'false' }} }"
                        class="rounded-xl border border-gray-100 overflow-hidden">

                        <button @click="queuesOpen = !queuesOpen"
                            class="flex items-center justify-between w-full px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">

                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600"
                                    fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                Queues
                            </span>

                            <svg class="w-4 h-4 transition-transform"
                                :class="{ 'rotate-180': queuesOpen }"
                                fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="queuesOpen" x-collapse
                            class="bg-gray-50 border-t border-gray-100 py-2">

                            @foreach($offices as $office)
                                <a href="{{ route('office.queue', $office->id) }}"
                                    class="block px-12 py-2.5 text-sm transition
                                    {{ request()->routeIs('office.queue') && request()->route('officeId') == $office->id ? 'text-emerald-700 font-semibold bg-emerald-50' : 'text-gray-600 hover:text-emerald-700' }}">
                                    {{ $office->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @elseif($user->office_id)
                    <a href="{{ route('office.queue', $user->office_id) }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                        {{ request()->routeIs('office.queue') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5" fill="none"
                            stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        Queues
                    </a>
                @endif

                {{-- Mobile View Skipped --}}
                <a href="{{ route('skipped.list') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ request()->routeIs('skipped.list') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50' }}">

                    <svg class="w-5 h-5" fill="none"
                        stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 7h12M8 12h12M8 17h12M3 7h.01M3 12h.01M3 17h.01" />
                    </svg>

                    View Skipped
                </a>

                {{-- Mobile Manage Users --}}
                @if($user->isAdmin() || $user->isSuperAdmin() || $user->isHead())
                    <a href="{{ route('staff.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                        {{ request()->routeIs('staff.index') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50' }}">

                        <svg class="w-5 h-5" fill="none"
                            stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.196-2.043M17 20H7m10 0v-2c0-.653-.126-1.277-.354-1.847M7 20H2v-2a3 3 0 015.196-2.043M7 20v-2c0-.653.126-1.277.354-1.847m0 0a5.002 5.002 0 019.292 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>

                        Manage Users
                    </a>
                @endif

                {{-- Mobile Manage Office --}}
                @if($user->isAdmin() || $user->isSuperAdmin())
                    <a href="{{ route('admin.offices.create') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                        {{ request()->routeIs('admin.offices.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50' }}">

                        <svg class="w-5 h-5" fill="none"
                            stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01" />
                        </svg>

                        Manage Office
                    </a>
                @endif

            @endunless

            {{-- Mobile Account Links --}}
            <div class="border-t border-gray-100 pt-3 mt-3 space-y-2">

                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 hover:text-emerald-700 transition">

                    <svg class="w-5 h-5" fill="none"
                        stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 12a4 4 0 100-8 4 4 0 000 8zM4 20a8 8 0 0116 0" />
                    </svg>

                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                        class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 transition">

                        <svg class="w-5 h-5" fill="none"
                            stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 17l5-5-5-5M20 12H9m4 8H5a2 2 0 01-2-2V6a2 2 0 012-2h8" />
                        </svg>

                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
