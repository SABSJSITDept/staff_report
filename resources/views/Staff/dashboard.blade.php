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

    <div class="flex items-center gap-2">
        <a href="{{ route('staff.guide') }}"
           class="px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-xl transition hover:bg-indigo-100 flex items-center gap-2 border border-indigo-100 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            User Guide
        </a>
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

{{-- Global Announcements & Employee of the Month --}}
<div class="mb-6 flex flex-col gap-4">

    {{-- Employee of the Month --}}
    @if($featuredEmployee)
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between border border-indigo-500">
        <div class="relative z-10 flex items-center gap-5 w-full">
            @if($featuredEmployee->staff->photo)
                <img src="{{ asset('storage/' . $featuredEmployee->staff->photo) }}" class="w-20 h-20 rounded-full border-4 border-white shadow-lg object-cover">
            @else
                <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl shadow-inner border-2 border-white/50">🏆</div>
            @endif
            <div class="flex-1">
                @if($featuredEmployee->staff_id == $staffDetail->id)
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-gradient-to-r from-yellow-400 to-amber-500 text-yellow-900 text-xs font-extrabold rounded-full tracking-wider uppercase shadow-md mb-2">
                        <span>🌟 Congratulations! You are the Star of {{ date('F', mktime(0, 0, 0, $featuredEmployee->month, 1)) }}! 🌟</span>
                    </div>
                    <h3 class="font-extrabold text-3xl mt-1 text-transparent bg-clip-text bg-gradient-to-r from-white to-indigo-100">Outstanding Work, {{ Auth::user()->name }}!</h3>
                    <p class="text-indigo-50 text-sm mt-2 leading-relaxed max-w-2xl border-l-4 border-yellow-400 pl-3 italic">
                        "{{ $featuredEmployee->description }}"
                    </p>
                @else
                    <span class="px-3 py-1 bg-yellow-400 text-yellow-900 text-xs font-extrabold rounded-full tracking-wider uppercase shadow-sm">
                        Employee of the Month - {{ date('F', mktime(0, 0, 0, $featuredEmployee->month, 1)) }}
                    </span>
                    <h3 class="font-bold text-2xl mt-2">{{ $featuredEmployee->staff->name }}</h3>
                    <p class="text-indigo-100 text-sm mt-1 leading-relaxed max-w-2xl">
                        "{{ $featuredEmployee->description }}"
                    </p>
                @endif
            </div>
        </div>
        
        {{-- Previous Winners for the Year --}}
        @if($otherEmployees->count() > 0)
        <div class="relative z-10 mt-6 md:mt-0 md:ml-6 pl-0 md:pl-6 border-t md:border-t-0 md:border-l border-white/20 w-full md:w-auto">
            <h4 class="text-xs text-indigo-200 font-semibold uppercase tracking-wider mb-3">Other Winners in {{ now()->year }}</h4>
            <div class="flex flex-col gap-2">
                @foreach($otherEmployees as $other)
                    <div class="flex items-center gap-3 bg-white/10 rounded-lg p-2 border border-white/10 hover:bg-white/20 transition">
                        @if($other->staff->photo)
                            <img src="{{ asset('storage/' . $other->staff->photo) }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-indigo-800 flex items-center justify-center text-xs">🏅</div>
                        @endif
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold leading-tight">{{ $other->staff->name }}</span>
                            <span class="text-[10px] text-indigo-200 uppercase">{{ date('M', mktime(0, 0, 0, $other->month, 1)) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="absolute -right-10 -bottom-10 opacity-10 transform -rotate-12 scale-150 pointer-events-none">
            <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
        </div>
    </div>
    @endif

    {{-- Global Birthdays & Anniversaries --}}
    @if($todaysBirthdays->count() > 0 || $todaysAnniversaries->count() > 0)
    <div class="grid grid-cols-1 {{ ($todaysBirthdays->count() > 0 && $todaysAnniversaries->count() > 0) ? 'md:grid-cols-2' : '' }} gap-4">
        
        @if($todaysBirthdays->count() > 0)
        <div class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-xl shadow-inner">🎂</div>
                    <h3 class="font-bold text-lg">Today's Birthdays</h3>
                </div>
                <div class="flex flex-wrap gap-3">
                    @foreach($todaysBirthdays as $bdayStaff)
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg py-1.5 px-3 border border-white/20">
                            <span class="font-medium text-sm">{{ $bdayStaff->name }}</span>
                            @if($bdayStaff->id === Auth::id())
                                <span class="bg-white text-rose-500 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase">You</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 opacity-10 transform rotate-12 scale-150">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M19 15v2h2v2h-2v2h-2v-2h-2v-2h2v-2h2zM7 9a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 110-8 4 4 0 010 8zm10-2a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 110-8 4 4 0 010 8zM7 17a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 110-8 4 4 0 010 8zm10-2a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 110-8 4 4 0 010 8z"/></svg>
            </div>
        </div>
        @endif

        @if($todaysAnniversaries->count() > 0)
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-xl shadow-inner">🎊</div>
                    <h3 class="font-bold text-lg">Work Anniversaries</h3>
                </div>
                <div class="flex flex-wrap gap-3">
                    @foreach($todaysAnniversaries as $anniStaff)
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg py-1.5 px-3 border border-white/20">
                            <span class="font-medium text-sm">{{ $anniStaff->name }}</span>
                            <span class="bg-white/20 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">{{ $anniStaff->yearsOfService }} yr(s)</span>
                            @if($anniStaff->id === Auth::id())
                                <span class="bg-white text-orange-500 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase">You</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 opacity-10 transform rotate-12 scale-150">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.27 14.3H3.73L12 5.45zM11 11v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
            </div>
        </div>
        @endif

    </div>
    @endif

</div>


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

{{-- Recent Profile Requests Section --}}
<div class="mt-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">Your Profile Update Requests</h3>
        <p class="text-xs text-gray-500">Status of your recent requests</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($profileRequests->count() > 0)
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] font-bold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Requested On</th>
                    <th class="px-6 py-4">Updates</th>
                    <th class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($profileRequests as $preq)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-500">{{ $preq->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($preq->requested_data as $key => $val)
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[10px] border border-indigo-100">
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($preq->status === 'pending')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-[11px] font-bold">Pending</span>
                        @elseif($preq->status === 'approved')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[11px] font-bold">Approved</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[11px] font-bold">Rejected</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-10 text-center flex flex-col items-center">
            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-gray-400 text-sm">No recent profile update requests.</p>
        </div>
        @endif
    </div>
</div>

{{-- Recent Reports Section --}}
<div class="mt-8 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">Recent Reports</h3>
        <a href="{{ route('daily-report.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View All</a>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($recentReports->count() > 0)
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] font-bold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Tasks</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($recentReports as $rep)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ \Carbon\Carbon::parse($rep->report_date)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $rep->tasks->count() }} tasks</td>
                    <td class="px-6 py-4 text-right text-gray-400">—</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-10 text-center text-gray-400">No reports found.</div>
        @endif
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
        <form id="profile-update-form" class="space-y-4" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border" value="{{ $staffDetail->name ?? Auth::user()->name }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Father's Name</label>
                    <input type="text" name="f_name" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border" value="{{ $staffDetail->f_name ?? '' }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="dob" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border" value="{{ $staffDetail->dob ?? '' }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mobile Number</label>
                    <input type="text" name="mobile" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border" value="{{ $staffDetail->mobile ?? '' }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border" value="{{ Auth::user()->email }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date of Joining</label>
                    <input type="date" name="doj" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border" value="{{ $staffDetail->doj ?? '' }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Upload Photo</label>
                    <input type="file" name="photo" accept="image/*" class="mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border rounded-md p-1">
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border">{{ $staffDetail->address ?? '' }}</textarea>
                </div>
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
                // Success Message UI
                alertBox.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
                alertBox.innerHTML = `
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>${data.message}</span>
                    </div>
                `;
                setTimeout(() => {
                    location.reload(); // Reload to show new status in table
                }, 2000);
            } else {
                alertBox.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
                alertBox.innerText = data.message || 'Error submitting request.';
            }
        })
        .catch(err => {
            alertBox.classList.remove('hidden');
            alertBox.classList.add('bg-red-50', 'text-red-700');
            alertBox.innerText = 'Something went wrong. Please try again.';
        });
    });

    document.getElementById('change-password-form')?.addEventListener('submit', function(e) {
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
