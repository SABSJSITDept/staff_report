@extends('layouts.app')
@section('title', 'Daily Reports')

@php
    $isHod = Auth::check() && Auth::user()->role === 'staff' && Auth::user()->staff && Auth::user()->staff->isHod();
@endphp

@section('content')

{{-- Toast --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 w-96 pointer-events-none"></div>

{{-- ===================== DETAIL MODAL ===================== --}}
<div id="detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeDetailModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">Report Details</h3>
                    <p class="text-xs text-gray-400 mt-0.5" id="modal-report-date">—</p>
                </div>
            </div>
            <button onclick="closeDetailModal()"
                    class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="detail-content" class="overflow-y-auto flex-1 p-6">
            <div class="flex items-center justify-center py-12">
                <div class="w-8 h-8 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>
    </div>
</div>

{{-- Page Header --}}
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-10">
    <div class="flex items-center gap-5">
        <div class="w-16 h-16 rounded-[2rem] gradient-bg flex items-center justify-center shadow-2xl shadow-indigo-200">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Work Progress Tracker</h1>
            <p class="text-slate-400 text-sm mt-1">Real-time overview of organization-wide daily activities</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3">
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card-premium flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Logs</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalReports }}</p>
        </div>
    </div>

    <div class="card-premium flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Today</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $todayReports }}</p>
        </div>
    </div>

    <div class="card-premium flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tasks</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalTasks }}</p>
        </div>
    </div>

    <div class="card-premium flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Rate</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $completionRate }}%</p>
        </div>
    </div>
</div>

{{-- Filters Bar --}}
<div class="card-premium mb-8 overflow-visible">
    <form action="{{ route('daily-report.index') }}" method="GET" id="export-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        @if(Auth::user()->role !== 'staff' || $isHod)
        <div class="space-y-2">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Office / Branch</label>
            <div class="relative">
                <select name="office_id" id="office-filter" onchange="filterStaffByOffice()" class="form-input-modern appearance-none pr-10">
                    <option value="">All Offices</option>
                    @foreach($offices as $o)
                        <option value="{{ $o->id }}" {{ request('office_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                    @endforeach
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Staff Member</label>
            <div class="relative">
                <select name="staff_id" id="staff-filter" onchange="applyFilters()" class="form-input-modern appearance-none pr-10">
                    <option value="">All Personnel</option>
                    @foreach($allStaff as $s)
                        <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }} data-office="{{ $s->staff->office_id ?? '' }}" data-name="{{ strtolower($s->name) }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        <div class="space-y-2">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Start Date</label>
            <input type="date" name="start_date" id="start-date" value="{{ request('start_date') }}" onchange="applyFilters()" class="form-input-modern">
        </div>

        <div class="space-y-2">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">End Date</label>
            <input type="date" name="end_date" id="end-date" value="{{ request('end_date') }}" onchange="applyFilters()" class="form-input-modern">
        </div>

        <div class="lg:col-span-2 flex items-end gap-3">
            <div class="flex-1 flex gap-2">
                <button type="button" onclick="setQuickRange('week')" class="flex-1 px-4 py-3.5 rounded-2xl bg-indigo-50 text-indigo-600 text-xs font-bold hover:bg-indigo-100 transition">Week</button>
                <button type="button" onclick="setQuickRange('month')" class="flex-1 px-4 py-3.5 rounded-2xl bg-indigo-50 text-indigo-600 text-xs font-bold hover:bg-indigo-100 transition">Month</button>
            </div>
            
            <div class="relative group">
                <button type="button" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </button>
                <div class="absolute right-0 bottom-full mb-3 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden hidden group-hover:block z-50 animate-in fade-in slide-in-from-bottom-2">
                    <button type="submit" onclick="document.getElementById('export-form').action='{{ route('daily-report.export') }}'" name="type" value="excel" class="w-full px-5 py-4 text-left text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 flex items-center gap-3 transition">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        Excel Sheet
                    </button>
                    <button type="submit" onclick="document.getElementById('export-form').action='{{ route('daily-report.export') }}'" name="type" value="pdf" class="w-full px-5 py-4 text-left text-sm text-slate-700 hover:bg-rose-50 hover:text-rose-700 flex items-center gap-3 transition border-t border-slate-50">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        PDF Document
                    </button>
                </div>
            </div>

            <button type="button" onclick="clearFilters()" class="btn-secondary px-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357-2H15"/>
                </svg>
            </button>
        </div>
    </form>
</div>

{{-- Grouped Datewise Section --}}
<div class="space-y-6" id="reports-container">
    @php
        $groupedReports = collect($reports->items())->groupBy(fn($r) => $r->report_date->format('Y-m-d'));
        $totalTasksCount = 0;
        foreach($reports->items() as $r) {
            $totalTasksCount += $r->tasks->count();
        }
    @endphp

    @forelse($groupedReports as $dateStr => $dayReports)
        @php
            $carbonDate = \Carbon\Carbon::parse($dateStr);
            // Calculate total tasks and total time spent on this day
            $allTasks = collect();
            foreach($dayReports as $r) {
                $allTasks = $allTasks->concat($r->tasks);
            }
            $totalTasksForDay = $allTasks->count();
            
            // Sum time
            $dayMinutes = 0;
            foreach($allTasks as $task) {
                $ts = strtolower($task->time_spend);
                if (preg_match('/(\d+)\s*h/', $ts, $m)) $dayMinutes += $m[1] * 60;
                if (preg_match('/(\d+)\s*m/', $ts, $m)) $dayMinutes += $m[1];
                if (preg_match('/(\d+):(\d+)/', $ts, $m)) $dayMinutes += $m[1] * 60 + $m[2];
            }
            $dh = floor($dayMinutes / 60);
            $dm = $dayMinutes % 60;
            $dayTimeStr = ($dh > 0 ? $dh.'h ' : '') . ($dm > 0 ? $dm.'m' : '');
        @endphp
        
        <div class="date-group card-premium !p-0 overflow-hidden" data-date="{{ $dateStr }}">
            <!-- Date Group Header Banner -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold tracking-tight">{{ $carbonDate->format('l, d M, Y') }}</h3>
                        <p class="text-[11px] text-slate-300/90 font-medium">Work Logs & Tasks</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="bg-indigo-500/20 text-indigo-300 border border-indigo-400/20 px-3 py-1 rounded-lg group-task-count">
                        {{ $totalTasksForDay }} {{ Str::plural('task', $totalTasksForDay) }}
                    </span>
                    <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-400/20 px-3 py-1 rounded-lg flex items-center gap-1 group-task-time-container" style="{{ $dayTimeStr ? '' : 'display: none;' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="group-task-time-text">{{ $dayTimeStr }}</span>
                    </span>
                </div>
            </div>
            
            <!-- Tasks List/Table inside Date Group -->
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                            <th class="px-6 py-4 w-[45%]">Task Details</th>
                            <th class="px-6 py-4 w-[25%] text-center">Timings & Duration</th>
                            <th class="px-6 py-4 w-[20%] text-center">Status</th>
                            <th class="px-6 py-4 w-[10%] text-right text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $staffGrouped = $dayReports->groupBy('staff_id');
                        @endphp
                        @foreach($staffGrouped as $staffId => $staffReports)
                            @if(Auth::user()->role !== 'staff')
                                @php $firstRep = $staffReports->first(); @endphp
                                <tr class="bg-indigo-50/50 group-staff-row" data-staff-id="{{ $staffId }}">
                                    <td colspan="4" class="px-6 py-3 border-y border-indigo-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl gradient-bg flex items-center justify-center text-white font-bold text-xs shadow-sm flex-shrink-0">
                                                {{ strtoupper(substr($firstRep->staff->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0 flex items-center gap-2">
                                                <p class="text-sm font-bold text-slate-800 leading-tight truncate">{{ $firstRep->staff->name ?? '—' }}</p>
                                                <span class="text-[9px] font-bold text-indigo-500 uppercase tracking-tighter bg-indigo-100/50 px-1.5 py-0.5 rounded-md inline-block">{{ $firstRep->staff->role ?? 'staff' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @foreach($staffReports as $report)
                                @foreach($report->tasks as $task)
                                    <tr class="hover:bg-slate-50/50 transition-colors task-row" data-staff-id="{{ $report->staff_id }}" data-time-spend="{{ strtolower($task->time_spend) }}">
                                        
                                        <td class="px-6 py-4 {{ Auth::user()->role !== 'staff' ? 'pl-12' : '' }}">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-800 text-sm leading-tight flex items-center gap-2">
                                                {{ $task->task_title }}
                                                @if($task->assigned_by)
                                                    <span class="text-[9px] uppercase tracking-wider font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded w-fit border border-indigo-100">
                                                        Assigned by {{ $task->assignedBy->name ?? 'Manager' }}
                                                    </span>
                                                @endif
                                                <a href="{{ route('daily-report.task.report', $task->id) }}" target="_blank" class="text-emerald-400 hover:text-emerald-600 transition" title="View Full Report (PDF/Excel)">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </a>
                                                <button type="button" onclick="openCommentsModal({{ $task->id }})" class="relative text-sky-400 hover:text-sky-600 transition" title="Discuss Task">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                                                    @if($task->comments_count > 0)
                                                        <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[8px] font-bold px-1 py-0.5 rounded-full leading-none">{{ $task->comments_count }}</span>
                                                    @endif
                                                </button>
                                                @if($task->is_carry)
                                                    <span class="text-[8px] font-bold text-amber-600 uppercase tracking-tighter bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100/50">Continued</span>
                                                @endif
                                            </p>
                                            @if($task->description)
                                                <p class="text-xs text-slate-400 mt-1 leading-relaxed whitespace-pre-wrap" title="{{ $task->description }}">{{ $task->description }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center gap-1.5 justify-center">
                                            @if($task->sessions && $task->sessions->count() > 0)
                                                @foreach($task->sessions as $session)
                                                    @if($session->start_time && $session->end_time)
                                                        <div class="text-[10px] text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded border border-slate-200/50 whitespace-nowrap">
                                                            {{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }} — {{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}
                                                        </div>
                                                    @elseif($session->start_time)
                                                        <div class="text-[10px] text-green-600 font-semibold bg-green-50 px-2 py-0.5 rounded border border-green-100/50 animate-pulse whitespace-nowrap">
                                                            Started: {{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @elseif($task->start_time && $task->end_time)
                                                <div class="text-[10px] text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded border border-slate-200/50 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }} — {{ \Carbon\Carbon::parse($task->end_time)->format('h:i A') }}
                                                </div>
                                            @elseif($task->start_time)
                                                <div class="text-[10px] text-green-600 font-semibold bg-green-50 px-2 py-0.5 rounded border border-green-100/50 animate-pulse whitespace-nowrap">
                                                    Started: {{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }}
                                                </div>
                                            @endif
                                            
                                            @if($task->time_spend)
                                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold border border-indigo-100">
                                                    {{ $task->time_spend }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $badgeClasses = [
                                                'completed'  => 'bg-green-50 text-green-700 border border-green-100',
                                                'in_progress'=> 'bg-blue-50 text-blue-700 border border-blue-100',
                                                'pending'    => 'bg-amber-50 text-amber-700 border border-amber-100',
                                                'paused'     => 'bg-gray-100 text-gray-600 border border-gray-200',
                                            ][$task->status] ?? 'bg-slate-50 text-slate-600';
                                            
                                            $statusLabel = [
                                                'completed'  => 'Completed',
                                                'in_progress'=> 'In Progress',
                                                'pending'    => 'Pending',
                                                'paused'     => 'Paused',
                                            ][$task->status] ?? $task->status;
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClasses }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button data-id="{{ $report->id }}" onclick="viewDetail(this.dataset.id)"
                                                    title="View Full Report Details"
                                                    class="w-7 h-7 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-700">No reports found</h3>
            <p class="text-sm text-gray-500 mt-1">No daily reports have been submitted yet.</p>
        </div>
    @endforelse
</div>

@if($reports->count() > 0)
<div class="sticky bottom-4 z-40 mt-8 mb-2">
    <div class="shadow-lg shadow-slate-200/50 rounded-xl border border-slate-200/80 bg-white/95 backdrop-blur-xl">
        {{ $reports->appends(request()->query())->links('vendor.pagination.premium') }}
    </div>
</div>
@endif

{{-- No results message --}}
<div id="no-results" class="hidden mt-3">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-10 text-center">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <p class="text-sm font-medium text-gray-600">No reports found</p>
        <p class="text-xs text-gray-400 mt-1">Try a different search or date</p>
        <button onclick="clearFilters()" class="mt-3 text-xs text-indigo-600 hover:underline font-medium">Clear filters</button>
    </div>
</div>

{{-- Comments Modal --}}
<div id="comments-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCommentsModal()"></div>
    
    <div class="relative w-full max-w-2xl bg-white shadow-2xl rounded-2xl overflow-hidden flex flex-col h-[650px] max-h-[90vh] border border-slate-200/60 transform transition-all">
        <!-- Header -->
        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3 w-full overflow-hidden">
                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <div class="min-w-0 flex-1 pr-4">
                    <h3 class="text-base font-bold text-slate-800 truncate" id="comments-modal-title">Task Discussion</h3>
                    <p class="text-xs font-semibold text-slate-500 truncate mt-0.5" id="comments-task-name"></p>
                </div>
            </div>
            <button type="button" onclick="closeCommentsModal()" class="text-slate-400 hover:text-slate-600 transition bg-slate-50 hover:bg-slate-100 p-2 rounded-full shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <!-- Chat Body -->
        <div id="comments-list" class="flex-1 overflow-y-auto p-5 sm:p-7 bg-[#f8f9fa] space-y-2 custom-scrollbar relative">
            <!-- Comments injected via JS -->
            <div class="flex h-full items-center justify-center">
                <div class="flex items-center gap-2 text-slate-500 text-sm font-medium">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Loading discussion...
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="bg-white border-t border-slate-100 p-4 sm:px-6 shrink-0 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.02)]">
            <form id="comment-form" onsubmit="submitComment(event)" class="relative flex items-end gap-3">
                <input type="hidden" id="comment-task-id">
                <div class="relative flex-1">
                    <textarea id="comment-input" rows="1" required placeholder="Type your message..." 
                              oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"
                              class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl pl-4 pr-10 py-3.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-none custom-scrollbar shadow-inner" style="min-height: 48px; max-height: 120px;"></textarea>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl p-3.5 shadow-md hover:shadow-lg transition shrink-0 self-end disabled:opacity-50 disabled:cursor-not-allowed group">
                    <svg class="w-5 h-5 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ── Toast ──────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const cfg = {
            success: { bg: 'bg-white border-l-4 border-green-500', icon: 'text-green-500', path: 'M5 13l4 4L19 7' },
            error:   { bg: 'bg-white border-l-4 border-red-500',   icon: 'text-red-500',   path: 'M6 18L18 6M6 6l12 12' },
        }[type] || { bg: 'bg-white border-l-4 border-gray-400', icon: 'text-gray-400', path: 'M13 16h-1v-4h-1m1-4h.01' };

        const t = document.createElement('div');
        t.className = `pointer-events-auto flex items-start gap-3 ${cfg.bg} rounded-xl px-4 py-3 shadow-lg text-sm`;
        t.innerHTML = `
            <svg class="w-5 h-5 ${cfg.icon} flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="${cfg.path}"/>
            </svg>
            <span class="text-gray-700 leading-relaxed">${message}</span>`;
        document.getElementById('toast-container').appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateX(16px)'; t.style.transition = 'all .3s'; setTimeout(() => t.remove(), 300); }, 3500);
    }

    // ── Detail Modal ───────────────────────────────────────
    const statusBadge = {
        completed:  'bg-green-50 text-green-700 border border-green-100',
        in_progress:'bg-blue-50 text-blue-700 border border-blue-100',
        pending:    'bg-amber-50 text-amber-700 border border-amber-100',
        paused:     'bg-gray-100 text-gray-600 border border-gray-200',
    };
    const statusLabel = { completed:'Completed', in_progress:'In Progress', pending:'Pending', paused:'Paused' };

    async function viewDetail(id) {
        const m = document.getElementById('detail-modal');
        m.classList.remove('hidden');
        m.classList.add('flex');
        document.getElementById('detail-content').innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="w-8 h-8 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            </div>`;

        try {
            const res  = await fetch('/daily-report/' + id, { headers: { 'Accept': 'application/json' } });
            
            if (res.status === 419) {
                document.getElementById('detail-content').innerHTML =
                    `<p class="text-center text-red-400 py-8 text-sm">Session expire ho gayi hai. Please page refresh karein.</p>`;
                return;
            }

            if (!res.ok) {
                throw new Error('Server returned ' + res.status);
            }
            const d    = await res.json();
            document.getElementById('modal-report-date').textContent = d.report_date || '—';

            const infoGrid = `
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Staff</p>
                        <p class="font-semibold text-gray-800 text-sm">${esc(d.staff?.name || '—')}</p>
                        <p class="text-xs text-gray-500 mt-0.5">${esc(d.staff?.email || '')}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Report Date</p>
                        <p class="font-semibold text-gray-800 text-sm">${d.report_date || '—'}</p>
                    </div>
                </div>`;

            const totalMinutes = (d.tasks || []).reduce((acc, t) => {
                const ts = String(t.time_spend || '').toLowerCase();
                let m = 0;
                let match;
                if (match = ts.match(/(\d+)\s*h/)) m += parseInt(match[1]) * 60;
                if (match = ts.match(/(\d+)\s*m/)) m += parseInt(match[1]);
                if (match = ts.match(/(\d+):(\d+)/)) m += parseInt(match[1]) * 60 + parseInt(match[2]);
                return acc + m;
            }, 0);
            const th = Math.floor(totalMinutes / 60);
            const tm = totalMinutes % 60;
            const totalStr = (th > 0 ? th + 'h ' : '') + (tm > 0 ? tm + 'm' : '') || '—';

            const formatTime = (dtStr) => {
                if (!dtStr) return '';
                // Check if HH:MM
                if (dtStr.match(/^\d{2}:\d{2}$/)) {
                    const [h, m] = dtStr.split(':').map(Number);
                    const ampm = h >= 12 ? 'PM' : 'AM';
                    return `${h % 12 || 12}:${String(m).padStart(2, '0')} ${ampm}`;
                }
                const date = new Date(dtStr);
                if (isNaN(date.getTime())) return '';
                const h = date.getHours();
                const m = date.getMinutes();
                const ampm = h >= 12 ? 'PM' : 'AM';
                return `${h % 12 || 12}:${String(m).padStart(2, '0')} ${ampm}`;
            };

            const carryTasks = (d.tasks || []).filter(t => t.is_carry);
            const newTasks   = (d.tasks || []).filter(t => !t.is_carry);

            const renderTask = (t, i, typeLabel) => {
                const startTimeStr = t.start_time ? formatTime(t.start_time) : '';
                const endTimeStr = t.end_time ? formatTime(t.end_time) : '';
                const durationStr = startTimeStr && endTimeStr ? `${startTimeStr} — ${endTimeStr}` : '';

                return `
                <div class="flex items-start gap-3 border ${t.is_carry ? 'border-amber-100 bg-amber-50/30' : 'border-gray-100 bg-gray-50'} rounded-xl p-3.5 hover:bg-white transition group">
                    <div class="w-6 h-6 rounded-full ${t.is_carry ? 'bg-amber-100 text-amber-600' : 'bg-indigo-100 text-indigo-600'} flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">${i+1}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm leading-tight">${esc(t.task_title)}</p>
                                ${t.is_carry ? `<span class="text-[9px] font-bold text-amber-600 uppercase tracking-tighter bg-amber-100/50 px-1.5 py-0.5 rounded mt-1 inline-block">Continued Task</span>` : ''}
                            </div>
                            <span class="flex-shrink-0 px-2 py-0.5 rounded-lg text-xs font-semibold ${statusBadge[t.status]||''}">${statusLabel[t.status]||t.status}</span>
                        </div>
                        ${t.description ? `<p class="text-xs text-gray-500 mt-1.5 leading-relaxed whitespace-pre-wrap">${esc(t.description)}</p>` : ''}
                        
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            ${t.sessions && t.sessions.length > 0 ? t.sessions.map(s => {
                                const stStr = formatTime(s.start_time);
                                const etStr = formatTime(s.end_time);
                                const dStr = stStr && etStr ? `${stStr} — ${etStr}` : (stStr ? `Started: ${stStr}` : '');
                                return dStr ? `
                                    <div class="flex items-center gap-1.5 bg-slate-100 px-2 py-0.5 rounded text-slate-600 border border-slate-200">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-[10px] font-semibold">${dStr}</span>
                                    </div>
                                ` : '';
                            }).join('') : (durationStr ? `
                                <div class="flex items-center gap-1.5 bg-slate-100 px-2 py-0.5 rounded text-slate-600 border border-slate-200">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-[10px] font-semibold">${durationStr}</span>
                                </div>
                            ` : '')}
                            ${t.is_carry && t.previous_time ? `
                                <div class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-[10px] text-gray-400 font-medium">Prev: <span class="text-gray-600">${esc(t.previous_time)}</span></span>
                                </div>
                            ` : ''}
                            ${t.time_spend ? `
                                <div class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-[10px] text-indigo-500 font-bold">${t.is_carry ? 'Today: ' : ''}${esc(t.time_spend)}</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>`;
            };

            let tasksHtml = `
                <div class="flex items-center justify-between mb-4 bg-slate-900 rounded-xl px-4 py-3 text-white">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Progress</p>
                        <p class="text-xs font-medium text-slate-300 mt-0.5">${(d.tasks||[]).filter(t=>t.status==='completed').length}/${(d.tasks||[]).length} tasks completed</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Time</p>
                        <p class="text-lg font-bold text-indigo-400">${totalStr}</p>
                    </div>
                </div>`;

            if (carryTasks.length > 0) {
                tasksHtml += `
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">Continued Tasks</span>
                            <div class="h-px flex-1 bg-amber-100"></div>
                        </div>
                        <div class="space-y-2.5">
                            ${carryTasks.map((t, i) => renderTask(t, i)).join('')}
                        </div>
                    </div>`;
            }

            if (newTasks.length > 0) {
                tasksHtml += `
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">Today's New Tasks</span>
                            <div class="h-px flex-1 bg-indigo-100"></div>
                        </div>
                        <div class="space-y-2.5">
                            ${newTasks.map((t, i) => renderTask(t, carryTasks.length + i)).join('')}
                        </div>
                    </div>`;
            }

            if ((d.tasks || []).length === 0) {
                tasksHtml = `<div class="text-center py-12 text-gray-400 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100">
                    <p class="text-xs font-medium">No tasks logged for this report</p>
                </div>`;
            }

            document.getElementById('detail-content').innerHTML = infoGrid + tasksHtml;
        } catch (e) {
            document.getElementById('detail-content').innerHTML =
                `<p class="text-center text-red-400 py-8 text-sm">Failed to load data. (${e.message})</p>`;
        }
    }

    function closeDetailModal() {
        const m = document.getElementById('detail-modal');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    function esc(s) {
        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Filters ────────────────────────────────────────────
    function applyFilters() {
        const form = document.getElementById('export-form');
        form.action = "{{ route('daily-report.index') }}";
        form.submit();
    }

    function setQuickRange(type) {
        const now = new Date();
        let start, end;
        
        if (type === 'week') {
            const day = now.getDay(); // 0 (Sun) to 6 (Sat)
            const diff = now.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is sunday
            start = new Date(now.setDate(diff));
            end = new Date(now.setDate(diff + 6));
        } else if (type === 'month') {
            start = new Date(now.getFullYear(), now.getMonth(), 1);
            end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        }

        document.getElementById('start-date').value = start.toISOString().split('T')[0];
        document.getElementById('end-date').value = end.toISOString().split('T')[0];
        applyFilters();
    }

    function clearFilters() {
        const form = document.getElementById('export-form');
        const sf = document.getElementById('staff-filter');
        const of = document.getElementById('office-filter');
        if (of) of.value = '';
        if (sf) sf.value = '';
        document.getElementById('start-date').value = '';
        document.getElementById('end-date').value = '';
        
        form.action = "{{ route('daily-report.index') }}";
        form.submit();
    }

    function filterStaffByOffice(shouldSubmit = true) {
        const officeId = document.getElementById('office-filter').value;
        const staffSelect = document.getElementById('staff-filter');
        if (!staffSelect) return;

        const options = staffSelect.querySelectorAll('option:not([value=""])');
        
        let hasVisibleStaff = false;
        options.forEach(opt => {
            const optOffice = opt.getAttribute('data-office');
            if (officeId === '' || optOffice === officeId) {
                opt.style.display = '';
                hasVisibleStaff = true;
            } else {
                opt.style.display = 'none';
            }
        });

        // Reset the staff dropdown if the selected user gets hidden
        const selectedOption = staffSelect.options[staffSelect.selectedIndex];
        if (selectedOption.value !== "" && selectedOption.style.display === 'none') {
            staffSelect.value = "";
        }
        
        if (shouldSubmit) {
            applyFilters();
        }
    }

    // Run on load in case the browser pre-fills the office dropdown
    document.addEventListener('DOMContentLoaded', () => {
        if(document.getElementById('office-filter')) filterStaffByOffice(false);
    });

    // Comments Logic
    function openCommentsModal(taskId) {
        document.getElementById('comment-task-id').value = taskId;
        document.getElementById('comments-list').innerHTML = '<div class="text-center text-slate-400 text-xs py-4">Loading comments...</div>';
        document.getElementById('comments-modal-title').textContent = 'Loading...';
        document.getElementById('comments-task-name').textContent = '';
        document.getElementById('comments-modal').classList.remove('hidden');

        fetch(`{{ url("daily-report/task") }}/${taskId}/comments`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        }).then(res => res.json()).then(data => {
            if(data.success) {
                document.getElementById('comments-modal-title').textContent = 'Task Discussion';
                document.getElementById('comments-task-name').textContent = data.task_title + ' (' + data.staff_name + ')';
                renderComments(data.comments);
            } else {
                alert(data.message || 'Error loading comments');
                closeCommentsModal();
            }
        }).catch(err => {
            alert('Network error.');
            closeCommentsModal();
        });
    }

    function closeCommentsModal() {
        document.getElementById('comments-modal').classList.add('hidden');
    }

    function renderComments(comments) {
        const list = document.getElementById('comments-list');
        list.innerHTML = '';
        
        // Add Activity Stream Header
        const headerHtml = `
            <div class="mb-6 border-b border-slate-200/60 pb-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Activity Stream</span>
            </div>
        `;
        list.innerHTML = headerHtml;

        if(comments.length === 0) {
            list.innerHTML += `
                <div class="flex flex-col items-center justify-center text-center px-4 py-10">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm border border-slate-100">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">No activity yet</p>
                    <p class="text-xs text-slate-400 mt-1">Start the conversation by typing below.</p>
                </div>
            `;
            return;
        }

        comments.forEach(c => {
            if (c.is_system) {
                list.innerHTML += `
                    <div class="flex flex-row items-center gap-3 mb-4 w-full">
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300 ml-4 shrink-0"></div>
                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200/60 shadow-sm">${c.comment}</span>
                        <span class="text-[10px] text-slate-400 font-medium ml-2">${c.created_at}</span>
                    </div>
                `;
                return;
            }

            const isOwn = c.is_own;
            const alignClass = isOwn ? 'flex-row-reverse' : 'flex-row';
            const bubbleBg = isOwn ? 'bg-[#5a45ff] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 shadow-sm';
            const borderRadius = isOwn ? 'rounded-2xl rounded-tr-md' : 'rounded-2xl rounded-tl-md';
            const avatarBg = isOwn ? 'bg-[#1e1e2d]' : 'bg-[#1e1e2d]';
            const nameAlign = isOwn ? 'justify-end' : 'justify-start';
            
            // Trim the comment to avoid huge blank spaces
            const trimmedComment = c.comment ? c.comment.trim() : '';
            const firstLetter = c.user_name ? c.user_name.charAt(0).toUpperCase() : '?';

            // Custom logic for avatar background based on isOwn (screenshot shows different bg for Aayush vs Aditya)
            // But we'll stick to a dark slate background for avatar or indigo for self
            const actualAvatarBg = isOwn ? 'bg-[#5a45ff]' : 'bg-[#101828]';

            list.innerHTML += `
                <div class="flex ${alignClass} items-start gap-3 mb-6 w-full">
                    <!-- Avatar -->
                    <div class="w-9 h-9 ${actualAvatarBg} text-white flex items-center justify-center font-bold text-sm rounded-[10px] shrink-0 shadow-sm mt-1">
                        ${firstLetter}
                    </div>
                    
                    <!-- Content -->
                    <div class="flex flex-col max-w-[85%] sm:max-w-[75%]">
                        <!-- Header (Name & Time) -->
                        <div class="flex items-baseline gap-2 mb-1.5 ${nameAlign}">
                            <span class="text-[11px] font-bold text-slate-800 uppercase tracking-widest">${c.user_name}</span>
                            <span class="text-[10px] font-semibold text-slate-400">${c.created_at}</span>
                        </div>
                        
                        <!-- Message Box -->
                        <div class="${bubbleBg} ${borderRadius} p-4 sm:p-5 text-[13px] leading-relaxed relative flex flex-col w-fit ${isOwn ? 'ml-auto' : 'mr-auto'}">
                            <span class="whitespace-pre-wrap break-words" style="min-width: 0;">${trimmedComment}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        setTimeout(() => {
            list.scrollTop = list.scrollHeight;
        }, 10);
    }

    function submitComment(e) {
        e.preventDefault();
        const taskId = document.getElementById('comment-task-id').value;
        const commentInput = document.getElementById('comment-input');
        const comment = commentInput.value.trim();
        if(!comment) return;

        const btn = e.target.querySelector('button');
        btn.disabled = true;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('comment', comment);

        fetch(`{{ url("daily-report/task") }}/${taskId}/comments`, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        }).then(res => res.json()).then(data => {
            if(data.success) {
                commentInput.value = '';
                commentInput.style.height = '48px'; // Reset height
                openCommentsModal(taskId);
            } else {
                alert(data.message || 'Error posting comment');
            }
            btn.disabled = false;
        }).catch(err => {
            alert('Network error.');
            btn.disabled = false;
        });
    }
    // Auto-open comments modal if URL has open_comment parameter
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const openCommentTaskId = urlParams.get('open_comment');
        if (openCommentTaskId && typeof openCommentsModal === 'function') {
            setTimeout(() => {
                openCommentsModal(openCommentTaskId);
                const newUrl = window.location.pathname + window.location.search.replace(new RegExp('[\?&]open_comment=[^&]+'), '').replace(/^&/, '?');
                window.history.replaceState({}, document.title, newUrl);
            }, 300);
        }
    });
</script>
@endpush
