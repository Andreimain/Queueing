<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Office Management
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">

                        <!-- FORM -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 shadow">
                            <h3 class="text-lg font-semibold text-green-700 mb-4">
                                <span id="formTitle">Add New Office</span>
                            </h3>

                            <form id="officeForm" method="POST" action="{{ route('admin.offices.store') }}"
                                class="space-y-4">
                                @csrf
                                <input type="hidden" id="office_id" name="office_id">

                                <div>
                                    <label class="text-sm text-green-700">Office Name</label>
                                    <input type="text" id="name" name="name"
                                        class="mt-1 w-full border border-green-400 rounded p-2">
                                </div>

                                <div>
                                    <label class="text-sm text-green-700">Abbreviation</label>
                                    <input type="text" id="abbreviation" name="abbreviation"
                                        class="mt-1 w-full border border-green-400 rounded p-2">
                                </div>

                                <div class="flex gap-2 mt-4">
                                    <button type="submit" id="submitBtn"
                                        class="px-4 py-2 bg-green-600 text-white rounded">
                                        Add Office
                                    </button>

                                    <button type="button" id="cancelBtn"
                                        class="px-4 py-2 bg-gray-400 text-white rounded hidden">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- TABLE -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 shadow">
                            <h3 class="text-lg font-semibold text-green-700 mb-4">Existing Offices</h3>

                            @php
                                $offices = \App\Models\Office::all();
                            @endphp

                            <table class="w-full border text-sm">
                                <thead class="bg-green-600 text-black">
                                    <tr>
                                        <th class="px-4 py-2 border">Name</th>
                                        <th class="px-4 py-2 border">Abbreviation</th>
                                        <th class="px-4 py-2 border">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($offices as $office)
                                        <tr>
                                            <td class="px-4 py-2 border">{{ $office->name }}</td>
                                            <td class="px-4 py-2 border">{{ $office->abbreviation }}</td>
                                            <td class="px-4 py-2 border">
                                                <button type="button"
                                                    class="editOfficeBtn py-1 px-3 rounded shadow text-sm font-semibold text-white"
                                                    style="background-color: orange;" data-id="{{ $office->id }}"
                                                    data-name="{{ $office->name }}"
                                                    data-abbreviation="{{ $office->abbreviation }}">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/offices.js')
</x-app-layout>
