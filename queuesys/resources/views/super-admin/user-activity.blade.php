<x-app-layout>
    <x-slot name="title">
        User Activity Monitor
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Activity Monitor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <x-card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Online Users</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-600">
                                {{ $counts['online'] }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Active within the last 2 minutes
                            </p>
                        </div>
                        <div
                            class="h-12 w-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Idle Users</p>
                            <p class="mt-2 text-3xl font-bold text-amber-600">
                                {{ $counts['idle'] }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                No activity for 2–10 minutes
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Offline Users</p>
                            <p class="mt-2 text-3xl font-bold text-gray-600">
                                {{ $counts['offline'] }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Over 10 minutes or no recorded activity
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- Search and Filters --}}
            <x-card>
                <form method="GET" action="{{ route('super-admin.user-activity.index') }}"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            Search User
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Enter user name..."
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                            Role
                        </label>
                        <select name="role" id="role"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">All Roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected(request('role') === $role)>
                                    {{ ucwords(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="office_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Office
                        </label>
                        <select name="office_id" id="office_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">All Offices</option>
                            @foreach ($offices as $office)
                                <option value="{{ $office->id }}" @selected((string) request('office_id') === (string) $office->id)>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="inline-flex flex-1 items-center justify-center px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                            Search
                        </button>

                        <a href="{{ route('super-admin.user-activity.index') }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </x-card>


            {{-- User Activity Table --}}
            <div class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm">
                <div
                    class="px-6 py-4 border-b border-gray-100 flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            User Activity
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            View the latest recorded activity for each account.
                        </p>
                    </div>

                    <span class="text-sm text-gray-500">
                        {{ $users->total() }} user(s) found
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-fixed text-sm text-left">
                        <colgroup>
                            <col style="width: 26%">
                            <col style="width: 16%">
                            <col style="width: 18%">
                            <col style="width: 17%">
                            <col style="width: 23%">
                        </colgroup>

                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 font-medium">User</th>
                                <th class="px-6 py-3 font-medium">Role</th>
                                <th class="px-6 py-3 font-medium">Office</th>
                                <th class="px-6 py-3 font-medium">Activity Status</th>
                                <th class="px-6 py-3 font-medium">Last Activity</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 truncate" title="{{ $user->name }}">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 truncate" title="{{ $user->email }}">
                                            {{ $user->email }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-gray-700 break-words">
                                        {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                    </td>

                                    <td class="px-6 py-4 text-gray-700 break-words">
                                        {{ $user->office?->name ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($user->activity_status === 'online')
                                            <x-badge color="emerald">
                                                <span class="inline-flex items-center gap-2">
                                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                    Online
                                                </span>
                                            </x-badge>
                                        @elseif ($user->activity_status === 'idle')
                                            <x-badge color="amber">
                                                <span class="inline-flex items-center gap-2">
                                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                                    Idle
                                                </span>
                                            </x-badge>
                                        @else
                                            <x-badge color="gray">
                                                <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                                Offline
                                                </span>
                                            </x-badge>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-gray-600">
                                        @if ($user->last_activity_at)
                                            <span class="block truncate"
                                                title="{{ $user->last_activity_at->format('M d, Y h:i:s A') }}">
                                                {{ $user->last_activity_at->diffForHumans() }}
                                            </span>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ $user->last_activity_at->format('M d, Y h:i A') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">
                                                No activity recorded
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        No users match your search or filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

            <p class="text-xs text-gray-500">
                Activity status is based on the last recorded request to the system.
            </p>

        </div>
    </div>
</x-app-layout>
