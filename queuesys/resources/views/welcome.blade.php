<x-guest-layout>
    <div class="w-full max-w-5xl text-center">

        <!-- Title -->
        <h1 class="text-3xl sm:text-5xl font-bold mb-10 text-emerald-700">
            Select an Office
        </h1>

        <!-- Office Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($offices as $office)
                <a href="{{ route('monitor.show', $office->id) }}"
                   class="block bg-white/80 backdrop-blur border border-emerald-200 rounded-2xl shadow-md hover:shadow-lg hover:bg-emerald-50 transition-all duration-200 p-8 text-center">
                    <h2 class="text-2xl sm:text-3xl font-semibold text-emerald-800 break-words">
                        {{ $office->name }}
                    </h2>
                    <p class="text-emerald-600 mt-2 text-lg">Tap to view queue display</p>
                </a>
            @empty
                <p class="text-gray-500 text-lg text-center col-span-full">
                    No offices available.
                </p>
            @endforelse
        </div>

    </div>
</x-guest-layout>
