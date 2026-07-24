@extends('layouts.app')

@section('title', 'Staff Rating Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Staff Rating Dashboard</h2>
            <p class="text-slate-500 mt-1">Select a staff member to rate their performance.</p>
        </div>
        
        <form action="{{ route('ratings.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            @if(!$showOfficeSelectionFirst || request()->filled('office_id'))
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="pl-10 w-full sm:w-64 rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white py-2 transition-colors">
                </div>
            @endif
            
            <select name="office_id" id="office_id_select" class="rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white py-2 px-3 transition-colors" onchange="this.form.submit()">
                @if($showOfficeSelectionFirst)
                    <option value="" disabled {{ !request()->filled('office_id') ? 'selected' : '' }}>Select Office...</option>
                @else
                    <option value="">All Offices</option>
                @endif
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>
                        {{ $office->name }}
                    </option>
                @endforeach
            </select>

            @if(!$showOfficeSelectionFirst || request()->filled('office_id'))
                <select name="staff_id" id="staff_id_select" class="rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white py-2 px-3 transition-colors">
                    <option value="">All Staff</option>
                    @foreach($allStaff as $stf)
                        <option value="{{ $stf->id }}" {{ request('staff_id') == $stf->id ? 'selected' : '' }}>
                            {{ $stf->name }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-indigo-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Filter
                </button>
            @endif
            
            @if(request()->hasAny(['search', 'office_id', 'staff_id']))
                <a href="{{ route('ratings.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">
                    Clear
                </a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($showOfficeSelectionFirst && !request()->filled('office_id'))
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-12 text-center max-w-2xl mx-auto my-8">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Select an Office First</h3>
            <p class="text-slate-500 mt-2">Please select an office from the dropdown above to view its staff members and rate their performance.</p>
        </div>
    @else
        {{-- Mobile Card Layout --}}
        <div class="md:hidden space-y-3">
            @forelse($staff as $member)
                @php
                    $isRated = \App\Models\RatingReportCard::where('staff_id', $member->id)
                                ->where('rating_given_by_id', auth()->id())
                                ->exists();
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0">
                            <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div class="ml-3 min-w-0">
                                <div class="text-sm font-medium text-slate-900 truncate">{{ $member->name }}</div>
                                <div class="text-xs text-slate-500">ID: #{{ $member->id }}</div>
                            </div>
                        </div>
                        @if($isRated)
                            <span class="flex-shrink-0 inline-flex items-center px-3 py-1.5 bg-green-50 border border-green-200 rounded-lg text-xs font-medium text-green-700">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Rated
                            </span>
                        @else
                            <a href="{{ route('ratings.create', $member->id) }}" class="flex-shrink-0 inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-transparent rounded-lg text-xs font-medium text-indigo-700 hover:bg-indigo-100 shadow-sm transition-all duration-200">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                Rate Staff
                            </a>
                        @endif
                    </div>
                    <div class="mt-2 ml-13 pl-0.5">
                        <div class="text-xs text-slate-500 truncate">{{ $member->email }}</div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-slate-500 text-lg font-medium">No staff found for rating.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop Table Layout --}}
        <div class="hidden md:block bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Staff Member</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($staff as $member)
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-slate-900">{{ $member->name }}</div>
                                            <div class="text-xs text-slate-500">ID: #{{ $member->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-600">{{ $member->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @php
                                        $isRated = \App\Models\RatingReportCard::where('staff_id', $member->id)
                                                    ->where('rating_given_by_id', auth()->id())
                                                    ->exists();
                                    @endphp
                                    @if($isRated)
                                        <span class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg text-sm font-medium text-green-700 shadow-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Already Rated
                                        </span>
                                    @else
                                        <a href="{{ route('ratings.create', $member->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-transparent rounded-lg text-sm font-medium text-indigo-700 hover:bg-indigo-100 hover:text-indigo-800 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                            Rate Staff
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-slate-500 text-lg font-medium">No staff found for rating.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    const allStaffData = @json($allStaff->map(function($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'office_id' => $user->staff ? $user->staff->office_id : null
        ];
    }));

    const currentStaffId = "{{ request('staff_id') }}";

    function filterStaffByOffice() {
        const officeId = document.getElementById('office_id_select').value;
        const staffSelect = document.getElementById('staff_id_select');
        if (!staffSelect) return;
        
        // Clear current options
        staffSelect.innerHTML = '<option value="">All Staff</option>';
        
        allStaffData.forEach(staff => {
            if (!officeId || staff.office_id == officeId) {
                const option = document.createElement('option');
                option.value = staff.id;
                option.textContent = staff.name;
                if (currentStaffId == staff.id) {
                    option.selected = true;
                }
                staffSelect.appendChild(option);
            }
        });
    }

    // Run once on load to set initial state
    document.addEventListener('DOMContentLoaded', filterStaffByOffice);
</script>
@endpush
@endsection
