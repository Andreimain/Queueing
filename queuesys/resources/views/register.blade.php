<x-guest-layout>
    <x-slot name="title">
        Registration
    </x-slot>

    <div class="w-full max-w-md bg-white/80 backdrop-blur-sm border border-emerald-200 p-8 rounded-2xl shadow-lg">
        <h1 class="text-3xl font-bold text-center text-emerald-800 mb-8">
            Registration
        </h1>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-emerald-100 text-emerald-700 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form
            id="visitorForm"
            method="POST"
            action="{{ route('visitor.store') }}"
            class="space-y-5"
            autocomplete="off"
        >
            @csrf

            {{-- Registration Type --}}
            <div>
                <label class="block text-sm font-semibold text-emerald-800 mb-2">
                    Please select if you are a:
                </label>

                <div class="grid grid-cols-2 gap-3">

                    {{-- Student --}}
                    <label
                        class="flex items-center gap-2 p-3 border border-emerald-300 rounded-md cursor-pointer hover:bg-emerald-50 transition"
                    >
                        <input
                            type="radio"
                            name="type"
                            value="student"
                            id="studentType"
                            class="text-emerald-600 focus:ring-emerald-500"
                            {{ old('type') === 'student' ? 'checked' : '' }}
                        >

                        <span class="text-sm font-medium text-emerald-800">
                            Student
                        </span>
                    </label>

                    {{-- Visitor --}}
                    <label
                        class="flex items-center gap-2 p-3 border border-emerald-300 rounded-md cursor-pointer hover:bg-emerald-50 transition"
                    >
                        <input
                            type="radio"
                            name="type"
                            value="visitor"
                            id="visitorType"
                            class="text-emerald-600 focus:ring-emerald-500"
                            {{ old('type') === 'visitor' ? 'checked' : '' }}
                        >

                        <span class="text-sm font-medium text-emerald-800">
                            Visitor
                        </span>
                    </label>

                </div>
            </div>

            {{-- Full Name --}}
            <div>
                <label class="block text-sm font-semibold text-emerald-800">
                    Full Name
                </label>

                <input
                    type="text"
                    name="name"
                    required
                    value="{{ old('name') }}"
                    class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md
                           focus:ring-2 focus:ring-emerald-400
                           focus:border-emerald-400 transition"
                >
            </div>

            {{-- Contact Number --}}
            <div>
                <label class="block text-sm font-semibold text-emerald-800">
                    Contact Number
                </label>

                <input
                    type="text"
                    name="contact_number"
                    required
                    value="{{ old('contact_number') }}"
                    class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md
                           focus:ring-2 focus:ring-emerald-400
                           focus:border-emerald-400 transition"
                >
            </div>

            {{-- ID Number --}}
            <div>
                <label
                    id="idNumberLabel"
                    class="block text-sm font-semibold text-emerald-800"
                >
                    ID Number
                </label>

                <input
                    type="text"
                    name="id_number"
                    id="idNumber"
                    value="{{ old('id_number') }}"
                    class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md
                           focus:ring-2 focus:ring-emerald-400
                           focus:border-emerald-400 transition"
                >
            </div>

            {{-- Student Course --}}
            <div id="courseField" class="hidden">
                <label class="block text-sm font-semibold text-emerald-800">
                    Course
                </label>

                <select
                    name="course_id"
                    id="courseSelect"
                    class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md
                           focus:ring-2 focus:ring-emerald-400
                           focus:border-emerald-400 transition"
                >
                    <option value="" disabled selected>
                        -- Choose Course --
                    </option>

                    @foreach ($courses as $course)
                        <option
                            value="{{ $course->id }}"
                            {{ old('course_id') == $course->id ? 'selected' : '' }}
                        >
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Office --}}
            <div>
                <label class="block text-sm font-semibold text-emerald-800">
                    Select Office
                </label>

                <select
                    name="office_id"
                    required
                    class="mt-1 w-full p-2.5 border border-emerald-300 rounded-md
                           focus:ring-2 focus:ring-emerald-400
                           focus:border-emerald-400 transition"
                >
                    <option
                        value=""
                        disabled
                        {{ old('office_id') ? '' : 'selected' }}
                    >
                        -- Choose an office --
                    </option>

                    @foreach ($offices as $office)
                        <option
                            value="{{ $office->id }}"
                            {{ old('office_id') == $office->id ? 'selected' : '' }}
                        >
                            {{ $office->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit --}}
            <div class="pt-4">
                <button
                    id="submitButton"
                    type="submit"
                    class="w-full flex items-center justify-center gap-2
                           bg-emerald-600 text-white font-semibold py-2.5
                           rounded-lg hover:bg-emerald-700 transition"
                >
                    Join Queue
                </button>
            </div>
        </form>
    </div>

    @vite('resources/js/register-form.js')
</x-guest-layout>
