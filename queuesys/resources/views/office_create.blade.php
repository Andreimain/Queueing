<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Office Management
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">

                        <!-- Add New Office -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 shadow">
                            <h3 class="text-lg font-semibold text-green-700 mb-4">Add New Office</h3>

                            @if(session('success'))
                                <div class="mb-4 p-3 text-green-700 bg-green-100 border border-green-300 rounded">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="mb-4 p-3 text-red-700 bg-red-100 border border-red-300 rounded">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.offices.store') }}" class="space-y-4" autocomplete="off">
                                @csrf

                                <div>
                                    <label class="block text-sm font-medium text-green-700">Office Name</label>
                                    <input type="text" name="name" required
                                           class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500"
                                           placeholder="Enter office name">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-green-700">Abbreviation</label>
                                    <input type="text" name="abbreviation" required
                                           class="mt-1 w-full border border-green-400 rounded p-2 focus:ring-green-500 focus:border-green-500"
                                           placeholder="e.g., BO, LB, SA, RO">
                                </div>

                                <div class="mt-6">
                                    <button type="submit"
                                            class="py-2 px-4 rounded shadow font-semibold text-sm"
                                            style="color: white; background-color: green;">
                                        Add Office
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Existing Office List -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 shadow mt-8 md:mt-0">
                            <h3 class="text-lg font-semibold text-green-700 mb-4">Existing Offices</h3>

                            @php
                                $offices = \App\Models\Office::all();
                            @endphp

                            <div class="overflow-x-auto w-full">
                                <table class="w-full border border-green-300 text-sm table-auto">
                                    <thead class="bg-green-600 text-black">
                                        <tr>
                                            <th class="px-4 py-2 border border-green-700 text-left">Office Name</th>
                                            <th class="px-4 py-2 border border-green-700 text-left">Abbreviation</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-black">
                                        @forelse($offices as $office)
                                            <tr class="hover:bg-green-100">
                                                <td class="px-4 py-2 border border-green-300">{{ $office->name }}</td>
                                                <td class="px-4 py-2 border border-green-300">{{ $office->abbreviation }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="px-4 py-2 text-center text-gray-500">
                                                    No offices found.
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
        </div>
    </div>
</x-app-layout>
