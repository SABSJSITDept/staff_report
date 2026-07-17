@extends('layouts.app')
@section('title', 'Employee of the Month')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Employee of the Month</h2>
    <p class="text-gray-500 text-sm mt-1">Manage office-wise appreciations</p>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 shadow-sm border border-green-200">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 shadow-sm border border-red-200">
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 shadow-sm border border-red-200">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Form to Add -->
    <div class="md:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Assign Award</h3>
        <form action="{{ route('admin.employee-of-month.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Office</label>
                <select name="office_id" required class="w-full rounded-lg border-gray-300 border p-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Select Office --</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }} - {{ $office->city }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Staff</label>
                <select name="staff_id" required class="w-full rounded-lg border-gray-300 border p-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Select Staff --</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->designation ?? 'Staff' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select name="month" required class="w-full rounded-lg border-gray-300 border p-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <input type="number" name="year" required value="{{ date('Y') }}" class="w-full rounded-lg border-gray-300 border p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Appreciation Message</label>
                <textarea name="description" rows="3" required placeholder="Write a nice appreciation message..." class="w-full rounded-lg border-gray-300 border p-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition shadow-sm">
                Save Award
            </button>
        </form>
    </div>

    <!-- List of Awards -->
    <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Past & Present Awards</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Period</th>
                        <th class="px-6 py-3">Office</th>
                        <th class="px-6 py-3">Staff</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($records as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ date('F', mktime(0, 0, 0, $record->month, 1)) }} {{ $record->year }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $record->office->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-800 font-medium">
                            {{ $record->staff->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.employee-of-month.destroy', $record->id) }}" method="POST" onsubmit="return confirm('Remove this award?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium transition">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400">No awards assigned yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
