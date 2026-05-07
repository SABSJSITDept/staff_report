@extends('layouts.app')
@section('title', 'Staff Dashboard')

@section('content')

{{-- Welcome Header --}}
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Staff Dashboard</h2>
        <p class="text-gray-500 text-sm mt-1">
            Welcome, <strong>{{ Auth::user()->name }}</strong>! Have a great day.
            <span class="text-gray-400 ml-1">{{ now()->format('l, d M Y') }}</span>
        </p>
    </div>
    <a href="{{ route('daily-report.create') }}"
       class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Submit Today's Report
    </a>
    <div class="flex items-center gap-2">
        <button onclick="document.getElementById('update-profile-modal').classList.remove('hidden')"
           class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl transition shadow-sm">
            Update Profile
        </button>
        <button onclick="document.getElementById('change-password-modal').classList.remove('hidden')"
           class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl transition shadow-sm">
            Change Password
        </button>
    </div>
</div>

{{-- Today's Status Banner --}}
@if($todayReport)
<div class="mb-6 bg-green-50 border border-green-200 rounded-xl px-5 py-3.5 flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-sm text-green-800 font-medium">Today's report has been submitted!</p>
        <p class="text-xs text-green-600 mt-0.5">{{ $todayReport->tasks->count() }} task(s) added.</p>
    </div>
    <a href="{{ route('daily-report.edit', $todayReport->id) }}"
       class="text-xs font-medium text-green-700 hover:text-green-900 underline transition">Edit</a>
</div>
@else
<div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-3.5 flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <p class="text-sm text-yellow-800 font-medium flex-1">Today's report has not been submitted yet. Please submit it soon!</p>
    <a href="{{ route('daily-report.create') }}"
       class="text-xs font-medium text-yellow-700 hover:text-yellow-900 underline transition">Submit Now</a>
</div>
@endif

@php
    $weekCount = \App\Models\DailyReport::where('staff_id', Auth::id())
        ->whereBetween('report_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
    $monthCount = \App\Models\DailyReport::where('staff_id', Auth::id())
        ->whereMonth('report_date', now()->month)
        ->whereYear('report_date', now()->year)->count();
    $weekTasks = \App\Models\DailyReportTask::whereHas('dailyReport', function($q) {
        $q->where('staff_id', Auth::id())
          ->whereBetween('report_date', [now()->startOfWeek(), now()->endOfWeek()]);
    })->count();
@endphp

{{-- Two Column Layout: Profile (left) + Stats (right) — 50/50 --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

    {{-- Profile Card — Landscape Style --}}
    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">

            {{-- Avatar + Name + Role --}}
            <div class="flex flex-col items-center text-center">
                @if($staffDetail && $staffDetail->photo)
                    <img src="{{ asset('storage/' . $staffDetail->photo) }}"
                         alt="Photo"
                         class="w-24 h-24 rounded-full border-4 border-indigo-100 object-cover shadow-md" />
                @else
                    <div class="w-24 h-24 rounded-full bg-indigo-50 border-4 border-indigo-100 flex items-center justify-center shadow-md">
                        <span class="text-indigo-600 font-bold text-3xl">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                @endif

                <h3 class="text-xl font-bold text-gray-800 mt-4">{{ Auth::user()->name }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">{{ $staffDetail->designation ?? ucfirst(Auth::user()->role) }}</p>

                @if($staffDetail)
                <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                    {{ $staffDetail->department->name ?? '' }}
                    @if(($staffDetail->department->name ?? '') && ($staffDetail->office->name ?? ''))
                        &middot;
                    @endif
                    {{ $staffDetail->office->name ?? '' }}
                </p>
                @endif
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 my-5"></div>

            {{-- Contact Icon Row --}}
            <div class="flex items-center justify-center gap-3 flex-wrap">
                @if($staffDetail && $staffDetail->mobile)
                <a href="tel:{{ $staffDetail->mobile }}"
                   title="{{ $staffDetail->mobile }}"
                   class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:border-orange-400 hover:text-orange-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </a>
                @endif
                <a href="mailto:{{ Auth::user()->email }}"
                   title="{{ Auth::user()->email }}"
                   class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:border-indigo-400 hover:text-indigo-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </a>
                @if($staffDetail)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                    {{ $staffDetail->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $staffDetail->status === 'Active' ? 'bg-green-500' : 'bg-red-400' }}"></span>
                    {{ $staffDetail->status }}
                </span>
                @endif
            </div>

            {{-- Detail Grid (2-col) --}}
            @if($staffDetail)
            <div class="border-t border-gray-100 mt-5 pt-5 grid grid-cols-2 gap-4">

                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Father's Name</p>
                        <p class="text-xs text-gray-700 font-medium mt-0.5">{{ $staffDetail->f_name }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Designation</p>
                        <p class="text-xs text-gray-700 font-medium mt-0.5">{{ $staffDetail->designation }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Department</p>
                        <p class="text-xs text-gray-700 font-medium mt-0.5">{{ $staffDetail->department->name ?? '—' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Office</p>
                        <p class="text-xs text-gray-700 font-medium mt-0.5">{{ $staffDetail->office->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $staffDetail->office->city ?? '' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Date of Birth</p>
                        <p class="text-xs text-gray-700 font-medium mt-0.5">{{ \Carbon\Carbon::parse($staffDetail->dob)->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-cyan-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-cyan-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Date of Joining</p>
                        <p class="text-xs text-gray-700 font-medium mt-0.5">{{ \Carbon\Carbon::parse($staffDetail->doj)->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 col-span-2">
                    <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Address</p>
                        <p class="text-xs text-gray-700 font-medium mt-0.5 leading-snug">{{ $staffDetail->address }}</p>
                    </div>
                </div>

            </div>
            @else
            <div class="border-t border-gray-100 mt-5 pt-5 flex flex-col items-center text-center text-gray-400 py-4">
                <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-sm">Profile details not found.</p>
                <p class="text-xs mt-1">Please contact the admin.</p>
            </div>
            @endif

        </div>
    </div>

    {{-- Right: This Week Stats --}}
    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7 h-full flex flex-col">

            <h3 class="text-sm font-semibold text-gray-700 mb-1">This Week's Overview</h3>
            <p class="text-xs text-gray-400 mb-5">
                {{ now()->startOfWeek()->format('d M') }} — {{ now()->endOfWeek()->format('d M Y') }}
            </p>

            {{-- Stat Cards --}}
            <div class="flex flex-col gap-3 flex-1">
                {{-- Purple Card --}}
                <div class="rounded-xl bg-indigo-600 px-6 py-5 flex items-center justify-between text-white shadow-md shadow-indigo-200">
                    <p class="text-5xl font-extrabold tracking-tight">{{ $weekTasks }}</p>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-indigo-100 leading-tight">This Week<br>Tasks</p>
                    </div>
                </div>
                {{-- Orange Card --}}
                <div class="rounded-xl bg-orange-500 px-6 py-5 flex items-center justify-between text-white shadow-md shadow-orange-200">
                    <p class="text-5xl font-extrabold tracking-tight">{{ $weekCount }}</p>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-orange-100 leading-tight">This Week<br>Reports</p>
                    </div>
                </div>
            </div>

            {{-- Mobile & Email --}}
            <div class="border-t border-gray-100 mt-6 pt-5 space-y-3">
                @if($staffDetail && $staffDetail->mobile)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Mobile</p>
                        <p class="text-sm font-medium text-gray-800">{{ $staffDetail->mobile }}</p>
                    </div>
                </div>
                @endif
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Email</p>
                        <p class="text-sm font-medium text-gray-800">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Update Profile Modal --}}
<div id="update-profile-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Request Profile Update</h3>
            <button onclick="document.getElementById('update-profile-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="profile-update-form" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Mobile Number</label>
                <input type="text" name="mobile" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border" value="{{ $staffDetail->mobile ?? '' }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border" value="{{ Auth::user()->email }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border">{{ $staffDetail->address ?? '' }}</textarea>
            </div>
            <div id="profile-alert" class="hidden rounded-md p-3 text-sm"></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('update-profile-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Submit Request</button>
            </div>
        </form>
    </div>
</div>

{{-- Change Password Modal --}}
<div id="change-password-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Change Password</h3>
            <button onclick="document.getElementById('change-password-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="change-password-form" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" name="current_password" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="new_password" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border">
            </div>
            <div id="password-alert" class="hidden rounded-md p-3 text-sm"></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('change-password-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Change Password</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('profile-update-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const alertBox = document.getElementById('profile-alert');
        
        fetch('{{ route('staff.profile.update.request') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            alertBox.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'bg-green-50', 'text-green-700');
            if (data.success) {
                alertBox.classList.add('bg-green-50', 'text-green-700');
                alertBox.innerText = data.message;
                setTimeout(() => {
                    document.getElementById('update-profile-modal').classList.add('hidden');
                    alertBox.classList.add('hidden');
                }, 2000);
            } else {
                alertBox.classList.add('bg-red-50', 'text-red-700');
                alertBox.innerText = data.message || 'Error submitting request.';
            }
        })
        .catch(err => {
            alertBox.classList.remove('hidden');
            alertBox.classList.add('bg-red-50', 'text-red-700');
            alertBox.innerText = 'Something went wrong. Please try again.';
        });
    });

    document.getElementById('change-password-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const alertBox = document.getElementById('password-alert');
        
        fetch('{{ route('password.update') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async res => {
            const data = await res.json();
            alertBox.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'bg-green-50', 'text-green-700');
            if (data.success) {
                alertBox.classList.add('bg-green-50', 'text-green-700');
                alertBox.innerText = data.message;
                setTimeout(() => {
                    document.getElementById('change-password-modal').classList.add('hidden');
                    alertBox.classList.add('hidden');
                    document.getElementById('change-password-form').reset();
                }, 2000);
            } else {
                alertBox.classList.add('bg-red-50', 'text-red-700');
                alertBox.innerText = data.message || 'Error updating password.';
            }
        })
        .catch(err => {
            alertBox.classList.remove('hidden');
            alertBox.classList.add('bg-red-50', 'text-red-700');
            alertBox.innerText = 'Something went wrong. Please check your inputs.';
        });
    });
</script>
@endpush
