@props(['title', 'value', 'icon' => null, 'key' => null])

<div class="bg-white/90 backdrop-blur border border-emerald-100 rounded-2xl shadow-sm p-6 flex items-center justify-between">
    <div>
        <p class="text-sm font-medium text-emerald-600 uppercase tracking-wider">{{ $title }}</p>
        <p class="mt-2 text-3xl font-bold text-gray-900" @if($key) data-key="{{ $key }}" @endif>{{ $value }}</p>
    </div>
    @if($icon)
        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
            {{ $icon }}
        </div>
    @endif
</div>
