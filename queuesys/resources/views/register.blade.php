<x-guest-layout>
    <div class="w-full max-w-md bg-white/80 backdrop-blur-sm border border-emerald-200 p-8 rounded-2xl shadow-lg">
        <h1 class="text-3xl font-bold text-center text-emerald-800 mb-8">Registration</h1>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-3 bg-emerald-100 text-emerald-700 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form id="visitorForm" method="POST" action="{{ route('visitor.store') }}" class="space-y-5" autocomplete="off">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-emerald-800">First Name</label>
                <input type="text" name="first_name" required
                       value="{{ old('first_name') }}"
                       class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-emerald-800">Last Name</label>
                <input type="text" name="last_name" required
                       value="{{ old('last_name') }}"
                       class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-emerald-800">Contact Number</label>
                <input type="text" name="contact_number" required
                       value="{{ old('contact_number') }}"
                       class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-emerald-800">Student ID No. / Visitor ID No.</label>
                <input type="text" name="id_number"
                       value="{{ old('id_number') }}"
                       class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-emerald-800">Select Office</label>
                <select name="office_id" required
                        class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition">
                    <option value="" disabled selected>-- Choose an office --</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4">
                <button id="submitButton" type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-emerald-600 text-white font-semibold py-2.5 rounded-lg hover:bg-emerald-700 transition">
                    Join Queue
                </button>
            </div>
        </form>
    </div>

    @vite('resources/js/register-form.js')
</x-guest-layout>
