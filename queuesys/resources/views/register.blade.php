<x-guest-layout>
    <x-slot name="title">
        Registration
    </x-slot>

    <div class="w-full max-w-2xl mx-auto">

        <!-- Registration Card -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">

            <!-- Header -->
            <div class="px-8 pt-9 pb-7 text-center border-b border-gray-100">
                <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-1v6m3-3h-6" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Visitor Registration
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    Please provide the required information to register your visit.
                </p>
            </div>

            <div class="px-6 py-7 sm:px-8 sm:py-8">

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                            <div>
                                <p class="text-sm font-semibold text-red-800">
                                    Please check the following errors:
                                </p>

                                <ul class="mt-2 list-disc list-inside space-y-1 text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m5 12 4 4L19 6" />
                            </svg>

                            <p class="text-sm font-medium text-emerald-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                @endif

                <form
                    id="visitorForm"
                    method="POST"
                    action="{{ route('visitor.store') }}"
                    enctype="multipart/form-data"
                    class="space-y-7"
                    autocomplete="off">

                    @csrf

                    <!-- Registration Type -->
                    <div>
                        <div class="mb-3">
                            <h3 class="text-sm font-semibold text-gray-900">
                                Registration Type
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Please select whether you are a student or visitor.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">

                            <!-- Student -->
                            <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4 cursor-pointer transition hover:border-emerald-400 hover:bg-emerald-50">
                                <input
                                    type="radio"
                                    name="type"
                                    value="student"
                                    id="studentType"
                                    class="border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                    {{ old('type') === 'student' ? 'checked' : '' }}>

                                <div>
                                    <span class="block text-sm font-semibold text-gray-800">
                                        Student
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Currently enrolled
                                    </span>
                                </div>
                            </label>

                            <!-- Visitor -->
                            <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4 cursor-pointer transition hover:border-emerald-400 hover:bg-emerald-50">
                                <input
                                    type="radio"
                                    name="type"
                                    value="visitor"
                                    id="visitorType"
                                    class="border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                    {{ old('type') === 'visitor' ? 'checked' : '' }}>

                                <div>
                                    <span class="block text-sm font-semibold text-gray-800">
                                        Visitor
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Guest or external visitor
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <!-- Personal Information -->
                    <div class="space-y-5">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Personal Information
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Enter your basic information below.
                            </p>
                        </div>

                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                required
                                value="{{ old('name') }}"
                                placeholder="Enter your full name"
                                class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500">
                        </div>

                        <!-- Contact Number -->
                        <div>
                            <label for="contact_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                Contact Number
                            </label>

                            <input
                                type="text"
                                name="contact_number"
                                id="contact_number"
                                required
                                value="{{ old('contact_number') }}"
                                placeholder="Enter your contact number"
                                class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500">
                        </div>

                        <!-- ID Number -->
                        <div>
                            <label id="idNumberLabel" for="idNumber" class="block text-sm font-semibold text-gray-700 mb-2">
                                ID Number
                            </label>

                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    name="id_number"
                                    id="idNumber"
                                    value="{{ old('id_number') }}"
                                    placeholder="Enter your ID number"
                                    class="min-w-0 flex-1 rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500">

                                <!-- Scan ID remains hidden -->
                                <button
                                    type="button"
                                    id="scanIdButton"
                                    class="!hidden shrink-0 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                                    tabindex="-1"
                                    aria-hidden="true">
                                    Scan ID
                                </button>
                            </div>

                            <p
                                id="scanStatus"
                                class="!hidden mt-2 text-xs text-emerald-700"
                                aria-hidden="true">
                                Waiting for RFID scan...
                            </p>
                        </div>
                    </div>

                    <!-- Visitor Photo -->
                    <div id="photoField" class="hidden border-t border-gray-100 pt-7">
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">
                                Visitor Photo
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Take a photo for visitor identification.
                            </p>
                        </div>

                        <!-- Privacy Notice -->
                        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v3m0 4h.01M10.3 3.9 2.8 17.1A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.9L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                                </svg>

                                <div>
                                    <p class="text-sm font-semibold text-amber-900">
                                        Privacy Notice
                                    </p>

                                    <p class="mt-1 text-xs leading-relaxed text-amber-800">
                                        Your photo is collected for visitor identification and campus security purposes.
                                        It is stored securely and deleted upon exiting the campus.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Camera -->
                        <div id="cameraContainer" class="hidden">
                            <video
                                id="cameraPreview"
                                autoplay
                                playsinline
                                class="w-full aspect-video rounded-xl border border-gray-200 bg-black object-cover">
                            </video>

                            <canvas id="photoCanvas" class="hidden"></canvas>

                            <div class="mt-3 grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    id="capturePhotoButton"
                                    class="rounded-xl bg-emerald-600 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                    Take Photo
                                </button>

                                <button
                                    type="button"
                                    id="stopCameraButton"
                                    class="rounded-xl bg-gray-100 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                                    Cancel
                                </button>
                            </div>
                        </div>

                        <!-- Photo Preview -->
                        <div id="photoPreviewContainer" class="hidden">
                            <img
                                id="photoPreview"
                                src=""
                                alt="Visitor photo preview"
                                class="w-full aspect-square rounded-xl border border-gray-200 bg-gray-50 object-cover">

                            <button
                                type="button"
                                id="retakePhotoButton"
                                class="mt-3 w-full rounded-xl bg-gray-100 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                                Retake Photo
                            </button>
                        </div>

                        <!-- Open Camera -->
                        <div id="photoOptions">
                            <button
                                type="button"
                                id="openCameraButton"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M3 7h3l2-3h8l2 3h3v13H3V7Zm9 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z" />
                                </svg>

                                Take Photo
                            </button>
                        </div>

                        <!-- Captured Photo -->
                        <input
                            type="file"
                            name="photo"
                            id="photo"
                            class="hidden"
                            accept="image/jpeg">
                    </div>

                    <!-- Student Course -->
                    <div id="courseField" class="hidden border-t border-gray-100 pt-7">
                        <h3 class="mb-3 text-sm font-semibold text-gray-900">
                            Academic Information
                        </h3>

                        <label for="courseSelect" class="block text-sm font-semibold text-gray-700 mb-2">
                            Course
                        </label>

                        <select
                            name="course_id"
                            id="courseSelect"
                            class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-emerald-500">

                            <option value="" disabled {{ old('course_id') ? '' : 'selected' }}>
                                -- Choose Course --
                            </option>

                            @foreach ($courses as $course)
                                <option
                                    value="{{ $course->id }}"
                                    {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Office Information -->
                    <div class="border-t border-gray-100 pt-7 space-y-5">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Office Information
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Select the office you intend to visit.
                            </p>
                        </div>

                        <!-- Office -->
                        <div>
                            <label for="officeSelect" class="block text-sm font-semibold text-gray-700 mb-2">
                                Select Office
                            </label>

                            <select
                                name="office_id"
                                id="officeSelect"
                                required
                                class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-emerald-500">

                                <option value="" disabled {{ old('office_id') ? '' : 'selected' }}>
                                    -- Choose an office --
                                </option>

                                @foreach ($offices as $office)
                                    <option
                                        value="{{ $office->id }}"
                                        data-abbreviation="{{ strtoupper($office->abbreviation) }}"
                                        {{ old('office_id') == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach

                                <option
                                    value="others"
                                    data-abbreviation="OTHERS"
                                    {{ old('office_id') === 'others' ? 'selected' : '' }}>
                                    Others
                                </option>
                            </select>
                        </div>

                        <!-- Registrar Purpose -->
                        <div
                            id="registrarPurposeField"
                            class="{{ old('purpose') ? '' : 'hidden' }}">

                            <label
                                for="purposeSelect"
                                class="block text-sm font-semibold text-gray-700 mb-2">
                                Purpose
                            </label>

                            <select
                                name="purpose"
                                id="purposeSelect"
                                class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-emerald-500">

                                <option value="" disabled {{ old('purpose') ? '' : 'selected' }}>
                                    -- Choose Purpose --
                                </option>

                                <option
                                    value="enrollment"
                                    {{ old('purpose') === 'enrollment' ? 'selected' : '' }}>
                                    Enrollment
                                </option>

                                <option
                                    value="document_request"
                                    {{ old('purpose') === 'document_request' ? 'selected' : '' }}>
                                    Document Request
                                </option>
                            </select>
                        </div>

                        <!-- Other Office -->
                        <div id="otherOfficeField" class="{{ old('office_id') === 'others' ? '' : 'hidden' }}">
                            <label for="otherOfficeInput" class="block text-sm font-semibold text-gray-700 mb-2">
                                Specify Office
                            </label>

                            <input
                                type="text"
                                name="other_office"
                                id="otherOfficeInput"
                                value="{{ old('other_office') }}"
                                placeholder="e.g. Library"
                                class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500">
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="border-t border-gray-100 pt-7">
                        <button
                            id="submitButton"
                            type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14m-7-7 7 7-7 7" />
                            </svg>

                            Register
                        </button>

                        <p class="mt-3 text-center text-xs text-gray-400">
                            Please make sure all information is correct before submitting.
                        </p>
                    </div>

                </form>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-6 text-center text-xs text-gray-400">
            Secure visitor registration for the Queue Management System
        </p>
    </div>

    @vite('resources/js/register-form.js')
</x-guest-layout>
