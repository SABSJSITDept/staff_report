@extends('layouts.app')
@section('title', 'Live Tasks')

@section('content')



<!-- Softer Search & Refresh Header -->
<div class="mb-8 flex flex-row flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Live Staff Tracker</h2>
        <p class="text-slate-400 text-xs mt-1">Real-time enterprise overview of active, paused, and completed tasks today.</p>
    </div>
    
    <div class="flex items-center gap-3 w-full md:w-auto">
        <!-- Staff Dropdown Select -->
        <div class="relative flex-1 md:w-64">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <select id="staffSearch" class="block w-full pl-10 pr-10 py-2 border border-slate-200/80 rounded-2xl bg-white text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-400 text-sm shadow-xs transition appearance-none cursor-pointer">
                <option value="" data-office="">All Staff Members</option>
                @foreach($staffListForDropdown as $staff)
                    <option value="{{ strtolower($staff->name) }}" data-office="{{ $staff->staff?->office_id }}">{{ $staff->name }}</option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none z-10">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </div>

        <!-- Office Filter Dropdown for Admin Only -->
        @if(Auth::user()->role === 'admin')
            @php
                $offices = $staffListForDropdown->map(fn($s) => $s->staff?->office)->filter()->unique('id');
            @endphp
            <div class="relative flex-1 md:w-56">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                    </svg>
                </div>
                <select id="officeFilter" class="block w-full pl-10 pr-10 py-2 border border-slate-200/80 rounded-2xl bg-white text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-400 text-sm shadow-xs transition appearance-none cursor-pointer">
                    <option value="">All Offices</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none z-10">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>
        @endif

        <button onclick="location.reload()" class="flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-600 text-sm font-semibold rounded-2xl transition shadow-xs shrink-0">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span class="hidden sm:inline">Refresh</span>
        </button>
        @if(in_array(Auth::user()->role, ['admin', 'manager']))
        <button onclick="openAssignTaskModal()" class="flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-2xl transition shadow-md shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            <span class="hidden sm:inline">Assign Task</span>
        </button>
        @endif
    </div>
</div>

<!-- Soft-Tone Header Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-100 flex items-center gap-5 hover:shadow-sm transition duration-300">
        <div class="w-12 h-12 rounded-2xl bg-indigo-50/60 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A9.642 9.642 0 0012 21a9.647 9.647 0 00-3-1.765V19.13M6 16.5a4.125 4.125 0 017.533-2.493M6 16.5a8.959 8.959 0 01-2.625.372c-1.378 0-2.67-.305-3.83-.852a4.125 4.125 0 017.533-2.493M3 9.071a3 3 0 113-3 3 3 0 01-3 3zm18 0a3 3 0 113-3 3 3 0 01-3 3zm-9 3a4 4 0 110-8 4 4 0 010 8z" />
            </svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Staff</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5" id="statTotalStaff">{{ $totalStaffCount }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-100 flex items-center gap-5 hover:shadow-sm transition duration-300">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50/60 flex items-center justify-center flex-shrink-0 relative">
            <span class="absolute top-2.5 right-2.5 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Now</p>
            <p class="text-2xl font-bold text-emerald-600 mt-0.5" id="statLive">{{ $liveCount }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-100 flex items-center gap-5 hover:shadow-sm transition duration-300">
        <div class="w-12 h-12 rounded-2xl bg-amber-50/60 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5-6v6M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Paused Tasks</p>
            <p class="text-2xl font-bold text-amber-500 mt-0.5" id="statPaused">{{ $pausedCount }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-100 flex items-center gap-5 hover:shadow-sm transition duration-300">
        <div class="w-12 h-12 rounded-2xl bg-slate-50/60 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Idle Staff</p>
            <p class="text-2xl font-bold text-slate-600 mt-0.5" id="statIdle">{{ $idleCount }}</p>
        </div>
    </div>
</div>

<!-- Breathable Softer Table Container -->
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-6" id="staffTableContainer">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left align-middle" id="staffTable">
            <thead>
                <tr class="bg-slate-50/40 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    <th class="px-8 py-5 w-[25%]">Staff Member</th>
                    <th class="px-6 py-5 w-[15%] text-center">Status</th>
                    <th class="px-8 py-5 w-[45%]">Today's Tasks</th>
                    <th class="px-6 py-5 w-[15%] text-center">Summary</th>
                </tr>
            </thead>
            <tbody class="divide-y-4 divide-slate-100">
                @forelse($allStaff as $staff)
                    @php
                        $staffTasks = $todayTasks->get($staff->id) ?? collect();
                        $activeTask = $staffTasks->where('status', 'in_progress')->first();
                        $pausedTasks = $staffTasks->where('status', 'paused');
                        $completedTasks = $staffTasks->where('status', 'completed')->sortByDesc('end_time');
                        
                        $isLive = !is_null($activeTask);
                        $hasPaused = $pausedTasks->count() > 0;
                        $workedToday = $completedTasks->count() > 0;

                        // Sort: In Progress first, Paused second, Completed third
                        $sortedTasks = $staffTasks->sortBy(function($t) {
                            return match($t->status) {
                                'in_progress' => 1,
                                'paused' => 2,
                                'completed' => 3,
                                default => 4,
                            };
                        });

                        // Calculate total time tracked today
                        $totalMinutes = 0;
                        foreach($staffTasks as $t) {
                            $ts = strtolower($t->time_spend);
                            if (preg_match('/(\d+)\s*h/', $ts, $m)) $totalMinutes += $m[1] * 60;
                            if (preg_match('/(\d+)\s*m/', $ts, $m)) $totalMinutes += $m[1];
                            if (preg_match('/(\d+):(\d+)/', $ts, $m)) $totalMinutes += $m[1] * 60 + $m[2];
                        }
                        $th = floor($totalMinutes / 60);
                        $tm = $totalMinutes % 60;
                        $totalTimeStr = ($th > 0 ? $th.'h ' : '') . ($tm > 0 ? $tm.'m' : '');
                        if (!$totalTimeStr && $isLive) $totalTimeStr = 'Tracking...';
                        elseif (!$totalTimeStr) $totalTimeStr = '0m';

                        $totalTasksCount = $staffTasks->count();
                        $completedCount = $completedTasks->count();
                        $completionPercent = $totalTasksCount > 0 ? round(($completedCount / $totalTasksCount) * 100) : 0;
                    @endphp
                    @php
                        $rowStatus = 'idle';
                        if ($isLive) $rowStatus = 'live';
                        elseif ($hasPaused) $rowStatus = 'paused';
                        elseif ($workedToday) $rowStatus = 'completed';
                    @endphp
                    <tr class="staff-row hover:bg-slate-50 transition-colors border-b border-slate-200 even:bg-slate-50/50" data-name="{{ strtolower($staff->name) }}" data-office="{{ $staff->staff->office_id ?? '' }}" data-status="{{ $rowStatus }}">
                        <!-- Personnel Info -->
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                @if($staff->staff && $staff->staff->photo)
                                    <img src="{{ asset('storage/' . $staff->staff->photo) }}" class="w-11 h-11 rounded-full border border-slate-100 object-cover shrink-0">
                                @else
                                    <div class="w-11 h-11 rounded-full {{ $isLive ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-slate-50 border-slate-100 text-slate-400' }} border flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h4 class="text-sm font-semibold text-slate-800 leading-snug truncate" title="{{ $staff->name }}">{{ $staff->name }}</h4>
                                    <p class="text-xs text-slate-400 font-medium truncate mt-0.5">{{ $staff->staff->designation ?? 'Staff Member' }}</p>
                                    
                                    {{-- Softly Styled Department & Office Badges --}}
                                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                        @if($staff->staff && $staff->staff->department)
                                            <span class="text-[9px] font-bold text-indigo-500 bg-indigo-50/50 px-1.5 py-0.5 rounded border border-indigo-100/30 uppercase tracking-wider">
                                                {{ $staff->staff->department->name }}
                                            </span>
                                        @endif
                                        @if($staff->staff && $staff->staff->office)
                                            <span class="text-[9px] font-bold text-sky-500 bg-sky-50/50 px-1.5 py-0.5 rounded border border-sky-100/30 uppercase tracking-wider">
                                                {{ $staff->staff->office->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Soft Tone Current Status Badges -->
                        <td class="px-6 py-6 text-center">
                            @if($isLive)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100/60 uppercase tracking-wider">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                    </span>
                                    Live Now
                                </span>
                            @elseif($hasPaused)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100/60 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Paused
                                </span>
                            @elseif($workedToday)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-slate-50 text-slate-600 border border-slate-200/60 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Idle (Worked)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-slate-50/50 text-slate-400 border border-slate-100 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                    Idle
                                </span>
                            @endif
                        </td>

                        <!-- Today's Tasks: Minimalist Vertical Timeline -->
                        <td class="px-8 py-6">
                            @if($sortedTasks->isNotEmpty())
                                <div class="relative pl-6 space-y-5">
                                    <!-- Soft vertical tracking line -->
                                    <div class="absolute left-[9px] top-1.5 bottom-1.5 w-0.5 bg-slate-100"></div>

                                    @foreach($sortedTasks as $task)
                                        @php
                                            $dotColor = 'bg-slate-300 ring-4 ring-slate-50';
                                            $titleColor = 'text-slate-600 font-semibold';
                                            
                                            if ($task->status === 'in_progress') {
                                                $dotColor = 'bg-emerald-500 ring-4 ring-emerald-50';
                                                $titleColor = 'text-slate-800 font-bold';
                                            } elseif ($task->status === 'paused') {
                                                $dotColor = 'bg-amber-400 ring-4 ring-amber-50';
                                                $titleColor = 'text-slate-700 font-semibold';
                                            }
                                        @endphp

                                        <div class="relative flex items-start gap-4 text-left group">
                                            <!-- Timeline Status Dot -->
                                            <div class="absolute -left-[22px] top-1 w-2.5 h-2.5 rounded-full {{ $dotColor }} z-10 transition duration-300"></div>

                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-baseline gap-2 flex-wrap">
                                                    <div class="flex items-center gap-1.5">
                                                        <h5 class="text-xs {{ $titleColor }} break-words leading-tight">{{ $task->task_title }}</h5>
                                                        <button type="button" onclick="viewTaskHistory({{ $task->id }})" class="text-indigo-400 hover:text-indigo-600 transition" title="View Time History">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </button>
                                                        <a href="{{ route('daily-report.task.report', $task->id) }}" target="_blank" class="text-emerald-400 hover:text-emerald-600 transition" title="View Full Report (PDF/Excel)">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        </a>
                                                        <button type="button" onclick="openCommentsModal({{ $task->id }})" class="relative text-sky-400 hover:text-sky-600 transition" title="Discuss Task">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                                                            @if($task->comments_count > 0)
                                                                <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[8px] font-bold px-1 py-0.5 rounded-full leading-none">{{ $task->comments_count }}</span>
                                                            @endif
                                                        </button>
                                                    </div>
                                                    @if($task->assigned_by)
                                                        <span class="text-[8px] font-bold text-indigo-500 bg-indigo-50/60 px-1 py-0.2 rounded border border-indigo-100/40 uppercase tracking-wider scale-95 origin-left">Assigned by {{ $task->assignedBy->name ?? 'Manager' }}</span>
                                                    @endif
                                                    @if($task->is_carry)
                                                        <span class="text-[8px] font-bold text-amber-500 bg-amber-50/60 px-1 py-0.2 rounded border border-amber-100/40 uppercase tracking-wider scale-95 origin-left">Continued</span>
                                                    @endif
                                                </div>

                                                @if($task->description)
                                                    @php
                                                        $descLines = explode("\n", $task->description);
                                                        $todayStr = '[' . now()->format('d M');
                                                        $filteredDesc = implode("\n", array_filter($descLines, function($line) use ($todayStr) {
                                                            $line = trim($line);
                                                            if (preg_match('/^\[\d{2} [A-Za-z]{3}, \d{2}:\d{2} [A-Z]{2}\]/', $line)) {
                                                                return str_starts_with($line, $todayStr);
                                                            }
                                                            return true;
                                                        }));
                                                    @endphp
                                                    @if(trim($filteredDesc))
                                                        <p class="text-[11px] text-slate-400 mt-1 break-words leading-relaxed whitespace-pre-wrap">{{ trim($filteredDesc) }}</p>
                                                    @endif
                                                @endif

                                                <!-- Soft metadata duration labels -->
                                                <div class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-2.5 font-medium">
                                                    <div class="flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span>
                                                            {{ \Carbon\Carbon::parse($task->start_time ?: $task->created_at)->format('h:i A') }}
                                                            @if($task->status !== 'in_progress' && $task->status !== 'paused')
                                                                — {{ $task->end_time ? \Carbon\Carbon::parse($task->end_time)->format('h:i A') : 'Completed' }}
                                                            @endif
                                                        </span>
                                                    </div>

                                                    @if($task->status === 'in_progress')
                                                        <span class="inline-flex items-center gap-1 text-[9px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider animate-pulse">
                                                            Active ({{ \Carbon\Carbon::parse($task->start_time)->diffForHumans(null, true) }})
                                                        </span>
                                                    @elseif($task->status === 'paused')
                                                        <span class="inline-flex items-center gap-1 text-[9px] text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">
                                                            Paused
                                                        </span>
                                                    @else
                                                        <span class="text-[9px] bg-slate-50 text-slate-500 px-1.5 py-0.5 rounded border border-slate-100/50">{{ $task->time_spend ?: '—' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100 border-dashed flex flex-col items-center justify-center text-center py-5">
                                    <svg class="w-5 h-5 text-slate-300 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p class="text-[11px] font-semibold text-slate-400">No tasks tracked today.</p>
                                </div>
                            @endif
                        </td>

                        <!-- Soft Tone Summary info & Progress bar -->
                        <td class="px-6 py-6 text-center">
                            <div class="flex flex-col items-center justify-center gap-2.5">
                                <div class="text-center">
                                    <p class="text-xs font-bold text-slate-700 leading-tight">{{ $totalTimeStr }}</p>
                                    <p class="text-[9px] text-slate-400 font-medium uppercase tracking-widest mt-0.5">Tracked Today</p>
                                </div>
                                
                                @if($totalTasksCount > 0)
                                    <div class="w-24 mt-1">
                                        <div class="flex justify-between items-center text-[9px] font-bold text-slate-400 mb-1">
                                            <span>Progress</span>
                                            <span>{{ $completedCount }}/{{ $totalTasksCount }}</span>
                                        </div>
                                        <div class="w-full bg-slate-50 h-1.5 rounded-full overflow-hidden border border-slate-100">
                                            <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $completionPercent }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-300 text-xs font-semibold">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-16 text-center">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                    <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700">No Staff Members Found</h4>
                                <p class="text-xs text-slate-400 mt-1">There are no active staff members to display.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                
                {{-- No results search fallback row --}}
                <tr id="noResultsRow" class="hidden">
                    <td colspan="4" class="px-8 py-16 text-center">
                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3 border border-slate-100">
                                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-700">No matching staff found</h4>
                            <p class="text-xs text-slate-400 mt-1">Try searching for a different name.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @if(method_exists($allStaff, 'links'))
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $allStaff->appends(request()->query())->links() }}
    </div>
    @endif
</div>

{{-- Assign Task Modal --}}
<div id="assign-task-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" onclick="closeAssignTaskModal()"></div>
        <div class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-3xl border border-slate-100">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-800">Assign New Task</h3>
                <button type="button" onclick="closeAssignTaskModal()" class="text-slate-400 hover:text-slate-600 transition bg-slate-50 hover:bg-slate-100 p-1.5 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="assign-task-form" onsubmit="submitAssignTask(event)">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Select Staff</label>
                    <select name="staff_id" id="assign-staff-id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select Staff --</option>
                        @foreach($staffListForDropdown as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Task Title</label>
                    <input type="text" name="task_title" id="assign-task-title" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="e.g. Update client presentation">
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Description (Optional)</label>
                    <textarea name="description" id="assign-task-desc" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="Additional details..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAssignTaskModal()" class="px-5 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit" id="assign-task-btn" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
                        Assign Task
                    </button>
                </div>
            </form>
        </div>
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

{{-- Task History Modal --}}
<div id="history-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" onclick="closeHistoryModal()"></div>

        <div class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-3xl border border-slate-100">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-800" id="history-modal-title">Task Timeline History</h3>
                <button type="button" onclick="closeHistoryModal()" class="text-slate-400 hover:text-slate-600 transition bg-slate-50 hover:bg-slate-100 p-1.5 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="mb-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Task Title</p>
                <p class="text-sm font-semibold text-slate-700" id="history-task-name"></p>
            </div>

            <div class="max-h-[300px] overflow-y-auto mb-4 border border-slate-100 rounded-2xl bg-slate-50/50 relative">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-100/80 text-slate-500 text-[9px] uppercase font-bold sticky top-0 backdrop-blur-md">
                        <tr>
                            <th class="px-4 py-2.5">Date</th>
                            <th class="px-4 py-2.5">Status</th>
                            <th class="px-4 py-2.5">Description</th>
                            <th class="px-4 py-2.5 text-right">Time Logged</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body" class="divide-y divide-slate-100 bg-white">
                        <!-- Content injected via JS -->
                    </tbody>
                </table>
            </div>

            <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-4 flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-900">Total Accumulated Time:</span>
                <span class="text-lg font-extrabold text-indigo-600" id="history-total-time">0h 0m</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const staffSelect = document.getElementById('staffSearch');
        const officeSelect = document.getElementById('officeFilter');
        const staffRows = document.querySelectorAll('.staff-row');

        function applyFilters() {
            const selectedStaffName = staffSelect ? staffSelect.value.trim().toLowerCase() : '';
            const selectedOfficeId = officeSelect ? officeSelect.value.trim() : '';
            let visibleCount = 0;

            let countTotal = 0;
            let countLive = 0;
            let countPaused = 0;
            let countIdle = 0;

            staffRows.forEach(row => {
                const staffName = row.getAttribute('data-name') || '';
                const officeId = row.getAttribute('data-office') || '';
                const rowStatus = row.getAttribute('data-status') || 'idle';

                const matchesStaff = !selectedStaffName || staffName === selectedStaffName;
                const matchesOffice = !selectedOfficeId || officeId === selectedOfficeId;

                if (matchesStaff && matchesOffice) {
                    row.style.display = '';
                    visibleCount++;
                    
                    countTotal++;
                    if (rowStatus === 'live') countLive++;
                    else if (rowStatus === 'paused') countPaused++;
                    else if (rowStatus === 'idle') countIdle++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update Header Stats
            const statTotalStaff = document.getElementById('statTotalStaff');
            const statLive = document.getElementById('statLive');
            const statPaused = document.getElementById('statPaused');
            const statIdle = document.getElementById('statIdle');

            if (statTotalStaff) statTotalStaff.textContent = countTotal;
            if (statLive) statLive.textContent = countLive;
            if (statPaused) statPaused.textContent = countPaused;
            if (statIdle) statIdle.textContent = countIdle;

            // Handle "No results found" row
            const noResultsRow = document.getElementById('noResultsRow');
            if (noResultsRow) {
                if (visibleCount === 0 && staffRows.length > 0) {
                    noResultsRow.classList.remove('hidden');
                } else {
                    noResultsRow.classList.add('hidden');
                }
            }

            // Update Staff Dropdown options to match Office
            if (staffSelect && officeSelect) {
                let currentStaffHidden = false;
                Array.from(staffSelect.options).forEach(opt => {
                    if (opt.value === '') return; // keep "All Staff" always visible
                    const optOfficeId = opt.getAttribute('data-office');
                    if (selectedOfficeId && optOfficeId !== selectedOfficeId) {
                        opt.style.display = 'none';
                        opt.disabled = true;
                        if (staffSelect.value === opt.value) {
                            currentStaffHidden = true;
                        }
                    } else {
                        opt.style.display = '';
                        opt.disabled = false;
                    }
                });

                if (currentStaffHidden) {
                    staffSelect.value = '';
                    // Need to re-trigger applyFilters to show all staff of this office
                    setTimeout(applyFilters, 0);
                }
            }
        }

        if (staffSelect) {
            staffSelect.addEventListener('change', applyFilters);
        }
        if (officeSelect) {
            officeSelect.addEventListener('change', applyFilters);
        }
    });

    // History Modal Logic
    function viewTaskHistory(taskId) {
        document.getElementById('history-table-body').innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-slate-400 text-xs">Loading history...</td></tr>';
        document.getElementById('history-task-name').textContent = 'Loading...';
        document.getElementById('history-total-time').textContent = '...';
        document.getElementById('history-modal').classList.remove('hidden');

        fetch(`{{ url("daily-report/task") }}/${taskId}/history`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        }).then(res => res.json()).then(data => {
            if(data.success) {
                document.getElementById('history-task-name').textContent = data.task_title;
                document.getElementById('history-total-time').textContent = data.total_time;
                
                const tbody = document.getElementById('history-table-body');
                tbody.innerHTML = '';
                
                if (data.history.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-slate-400 text-xs">No history found.</td></tr>';
                } else {
                    data.history.forEach(item => {
                        let statusHtml = '';
                        if (item.status === 'in_progress') statusHtml = '<span class="text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider">Active</span>';
                        else if (item.status === 'paused') statusHtml = '<span class="text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider">Paused</span>';
                        else statusHtml = '<span class="text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100 text-[9px] font-bold uppercase tracking-wider">Completed</span>';

                        tbody.innerHTML += `
                            <tr class="hover:bg-slate-50/50 transition border-b border-slate-50 last:border-0">
                                <td class="px-4 py-3 text-xs font-semibold text-slate-700 align-top">${item.date}</td>
                                <td class="px-4 py-3 align-top">${statusHtml}</td>
                                <td class="px-4 py-3 text-[11px] text-slate-500 whitespace-pre-wrap leading-relaxed">${item.description || '—'}</td>
                                <td class="px-4 py-3 text-right text-xs font-bold text-slate-800 align-top">${item.time_spend}</td>
                            </tr>
                        `;
                    });
                }
            } else {
                alert(data.message || 'Error loading history');
                closeHistoryModal();
            }
        }).catch(err => {
            alert('Network error loading history.');
            closeHistoryModal();
        });
    }

    function closeHistoryModal() {
        document.getElementById('history-modal').classList.add('hidden');
    }

    // Assign Task Logic
    function openAssignTaskModal() {
        document.getElementById('assign-task-form').reset();
        document.getElementById('assign-task-modal').classList.remove('hidden');
    }

    function closeAssignTaskModal() {
        document.getElementById('assign-task-modal').classList.add('hidden');
    }

    function submitAssignTask(e) {
        e.preventDefault();
        const btn = document.getElementById('assign-task-btn');
        btn.disabled = true;
        btn.innerHTML = 'Assigning...';

        const formData = new FormData(e.target);
        
        fetch('{{ route("daily-report.assign-task") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        }).then(res => res.json()).then(data => {
            if(data.success) {
                alert('Task assigned successfully!');
                location.reload();
            } else {
                alert(data.message || 'Error assigning task.');
                btn.disabled = false;
                btn.innerHTML = 'Assign Task';
            }
        }).catch(err => {
            alert('Network error.');
            btn.disabled = false;
            btn.innerHTML = 'Assign Task';
        });
    }

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
            const nameAlign = isOwn ? 'justify-end' : 'justify-start';
            
            // Trim the comment to avoid huge blank spaces
            const trimmedComment = c.comment ? c.comment.trim() : '';
            const firstLetter = c.user_name ? c.user_name.charAt(0).toUpperCase() : '?';

            // Custom logic for avatar background based on isOwn
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
        const comment = document.getElementById('comment-input').value;
        if(!comment.trim()) return;

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
                document.getElementById('comment-input').value = '';
                // Just reload all comments to ensure order, or just append the single comment
                // For simplicity, we just fetch again
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
@endsection
