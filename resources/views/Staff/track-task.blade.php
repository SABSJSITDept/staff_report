@extends('layouts.app')
@section('title', 'Track My Task')

@section('content')

{{-- Welcome Header --}}
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Track My Task</h2>
        <p class="text-gray-500 text-sm mt-1">
            Welcome, <strong>{{ Auth::user()->name }}</strong>! Manage your live task and see today's summary.
            <span class="text-gray-400 ml-1">{{ now()->format('l, d M Y') }}</span>
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('staff.dashboard') }}"
           class="px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-xl transition hover:bg-indigo-100 flex items-center gap-2 border border-indigo-100 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
    </div>
</div>

@if(isset($pendingOutputTask) && $pendingOutputTask)
    <div class="mb-6 p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-amber-950">Work Outcome Submission Required</h4>
                <p class="text-xs font-semibold text-amber-800/90 mt-0.5">
                    Task <strong>"{{ $pendingOutputTask->task_title }}"</strong> was stopped and requires work outcome. You cannot start a new task until this outcome is submitted.
                </p>
            </div>
        </div>
        <button type="button" onclick="openTaskActionModal({{ $pendingOutputTask->id }}, 'stop', '{{ addslashes($pendingOutputTask->task_title) }}')"
                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition shadow-sm shrink-0 whitespace-nowrap flex items-center gap-2">
            <span>Submit Outcome Now</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </button>
    </div>
@endif

{{-- Browser Notification Permission Banner --}}
<div id="notification-permission-banner" class="hidden mb-4 p-3.5 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-between gap-3 text-xs text-indigo-900 shadow-sm">
    <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0 text-sm font-bold shadow-xs">
            🔔
        </div>
        <div>
            <span class="font-bold">Enable Desktop Notifications:</span>
            <span class="text-indigo-700 ml-1">Get instant desktop alerts and window focus when your task stops (at 1:00 PM, 6:30 PM, or after 2 hours).</span>
        </div>
    </div>
    <button type="button" onclick="requestBrowserNotificationPermission(true)" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shrink-0 shadow-sm text-xs">
        Enable Notifications
    </button>
</div>

{{-- Live Task Tracker Widget --}}
<div class="mb-6">
    <div class="bg-white rounded-2xl shadow-sm border {{ $activeTask ? 'border-green-300' : 'border-gray-100' }} overflow-hidden relative">
        <div class="px-6 py-5 {{ $activeTask ? 'bg-gradient-to-r from-green-50 to-white' : 'bg-gradient-to-r from-indigo-50 to-white' }}">
            
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $activeTask ? 'bg-green-100' : 'bg-indigo-100' }}">
                    <svg class="w-5 h-5 {{ $activeTask ? 'text-green-600' : 'text-indigo-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">{{ $activeTask ? 'Currently Working On' : 'Start a New Task' }}</h3>
                    <p class="text-xs {{ $activeTask ? 'text-green-600 font-medium' : 'text-gray-500' }}">
                        {{ $activeTask ? 'You have a task in progress.' : 'Enter your task details and start tracking.' }}
                    </p>
                </div>
            </div>

            @if($activeTask)
                {{-- Active Task Form --}}
                <form id="end-task-form" class="space-y-4">
                    @csrf
                    <input type="hidden" id="active_task_id" value="{{ $activeTask->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Task Title</label>
                            <input type="text" value="{{ $activeTask->task_title }}" readonly
                                   class="w-full px-4 py-2.5 border border-green-200 rounded-xl text-sm bg-green-50/50 text-gray-700 cursor-not-allowed">
                        </div>
                        @if($activeTask->description)
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Previous Updates</label>
                            <div class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-600 whitespace-pre-wrap max-h-32 overflow-y-auto">{{ $activeTask->description }}</div>
                        </div>
                        @endif
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Add New Update</label>
                            <textarea id="end_task_desc" rows="2" placeholder="Type your new update here..."
                                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-green-400 focus:outline-none transition resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="text-sm font-semibold text-green-700 flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                </span>
                                Started: {{ \Carbon\Carbon::parse($activeTask->start_time)->format('h:i A') }}
                            </div>
                            @if(isset($activeTaskDeadline) && $activeTaskDeadline)
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 text-amber-900 text-xs font-bold border border-amber-300 shadow-xs" title="Auto-stop scheduled: {{ $activeTaskDeadline['reason'] }}">
                                    <svg class="w-3.5 h-3.5 text-amber-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Auto-stops in:</span>
                                    <span id="active-task-countdown" class="font-mono text-amber-950 font-extrabold tracking-wide">--:--:--</span>
                                    <span class="text-[10px] text-amber-700 font-semibold uppercase">({{ $activeTaskDeadline['label'] }})</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="logActiveTaskUpdate({{ $activeTask->id }})" id="log-task-btn"
                                    class="flex items-center gap-2 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Log Update
                            </button>
                            <button type="button" onclick="pauseTask({{ $activeTask->id }})" id="pause-task-btn"
                                    class="flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition shadow-sm"
                                    title="Pause task without requiring outcome">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                </svg>
                                <span>Pause Task</span>
                            </button>
                            <button type="button" onclick="openTaskActionModal({{ $activeTask->id }}, 'stop', '{{ addslashes($activeTask->task_title) }}')" id="end-task-btn"
                                    class="flex items-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                                </svg>
                                <span>End Task</span>
                            </button>
                        </div>
                    </div>
                </form>
            @else
                {{-- Start Task Form --}}
                <form id="start-task-form" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Task Title <span class="text-red-500">*</span></label>
                            <input type="text" id="start_task_title" required placeholder="e.g. Server Maintenance"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-400 focus:outline-none transition">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Initial Description (Optional)</label>
                            <textarea id="start_task_desc" rows="2" placeholder="Briefly describe what you are about to do..."
                                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-400 focus:outline-none transition resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" id="start-task-btn"
                                class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Start Task
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>

{{-- Other Task Tracker Widget --}}
<div class="mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
        <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-white">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Quick Add: Other Task</h3>
                    <p class="text-xs text-gray-500">
                        Add miscellaneous updates. This task will not track time and multiple updates will be saved under "Other Task" today.
                    </p>
                </div>
            </div>
            
            <form id="other-task-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <textarea id="other_task_desc" required rows="2" placeholder="Describe the miscellaneous task you just did..."
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none transition resize-none"></textarea>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" id="other-task-btn"
                            class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add to Other Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Today's Work Summary (Live & Completed Tasks) --}}
<div class="mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                Today's Tracked Tasks
            </h3>
            <span class="text-xs text-gray-500 font-semibold">{{ $todayTasks->count() }} Tasks tracked today</span>
        </div>
        
        @if($todayTasks->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Task Title</th>
                            <th class="px-6 py-4">Description</th>
                            <th class="px-6 py-4 text-center">Timings</th>
                            <th class="px-6 py-4 text-center">Time Spent</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($todayTasks as $t)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800 max-w-[250px]" title="{{ $t->task_title }}">
                                    <div class="flex items-center gap-2">
                                        <div class="flex flex-col min-w-0 flex-1">
                                            <span class="truncate">{{ $t->task_title }}</span>
                                            @if($t->assigned_by)
                                                <span class="mt-0.5 text-[9px] uppercase tracking-wider font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100 truncate w-fit max-w-full">
                                                    Assigned by {{ $t->assignedBy->name ?? 'Manager' }}
                                                </span>
                                            @endif
                                        </div>
                                        <button type="button" onclick="viewTaskHistory({{ $t->id }})" class="text-indigo-400 hover:text-indigo-600 shrink-0" title="View Time History">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                        <button type="button" onclick="openCommentsModal({{ $t->id }})" class="relative text-sky-400 hover:text-sky-600 shrink-0 transition" title="Discuss Task">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                                            @if($t->comments_count > 0)
                                                <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[8px] font-bold px-1 py-0.5 rounded-full leading-none">{{ $t->comments_count }}</span>
                                            @endif
                                        </button>
                                        <button type="button" onclick="openOutputsModal({{ $t->id }})" class="relative text-amber-500 hover:text-amber-700 shrink-0 transition" title="View Work Outputs & Attachments">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            @if(($t->outputs_count ?? $t->outputs->count()) > 0)
                                                <span class="absolute -top-1.5 -right-1.5 bg-amber-500 text-white text-[8px] font-bold px-1 py-0.5 rounded-full leading-none">{{ $t->outputs_count ?? $t->outputs->count() }}</span>
                                            @endif
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 max-w-[300px] whitespace-pre-wrap leading-relaxed" title="{{ $t->description }}">{{ $t->description ?: '—' }}</td>
                                <td class="px-6 py-4 text-center text-xs text-gray-500 font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($t->start_time ?: $t->created_at)->format('h:i A') }} 
                                    — 
                                    @if($t->status === 'in_progress')
                                        Active Now
                                    @elseif($t->status === 'paused')
                                        Paused
                                    @else
                                        {{ $t->end_time ? \Carbon\Carbon::parse($t->end_time)->format('h:i A') : 'Completed' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($t->status === 'in_progress')
                                        <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-100 animate-pulse">Tracking...</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-gray-50 text-gray-700 rounded-lg text-xs font-semibold border border-gray-100">{{ $t->time_spend ?: '—' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($t->status === 'in_progress')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-ping"></span>
                                            Live
                                        </span>
                                    @elseif($t->status === 'paused')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Paused
                                        </span>
                                    @else
                                        @if($t->needsStopOutput())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse" title="Task stopped. Work outcome submission required.">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Outcome Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                Completed
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($t->status === 'paused')
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" onclick="resumeTask({{ $t->id }})"
                                                    class="inline-flex items-center justify-center p-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg border border-green-200 transition shadow-xs"
                                                    title="Resume Task">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347c-.75.412-1.667-.13-1.667-.986V5.653z" />
                                                </svg>
                                            </button>
                                            <button type="button" onclick="openTaskActionModal({{ $t->id }}, 'stop', '{{ addslashes($t->task_title) }}')"
                                                    class="inline-flex items-center justify-center p-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg border border-red-200 transition shadow-xs"
                                                    title="End Task">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9A2.25 2.25 0 015.25 16.5v-9z" />
                                                </svg>
                                            </button>
                                        </div>
                                    @elseif($t->status === 'in_progress')
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" onclick="pauseTask({{ $t->id }})"
                                                    class="inline-flex items-center justify-center p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg border border-amber-200 transition shadow-xs"
                                                    title="Pause Task">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                                </svg>
                                            </button>
                                            <button type="button" onclick="openTaskActionModal({{ $t->id }}, 'stop', '{{ addslashes($t->task_title) }}')"
                                                    class="inline-flex items-center justify-center p-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg border border-red-200 transition shadow-xs"
                                                    title="End Task">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9A2.25 2.25 0 015.25 16.5v-9z" />
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        @if($t->needsStopOutput())
                                            <button type="button" onclick="openTaskActionModal({{ $t->id }}, 'stop', '{{ addslashes($t->task_title) }}')"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition shadow-xs animate-pulse"
                                                    title="Submit Outcome for Stopped Task">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                                <span>Submit Outcome</span>
                                            </button>
                                        @else
                                            <button type="button" onclick="restartCompletedTask('{{ addslashes($t->task_title) }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-slate-500 hover:text-indigo-600 bg-slate-100 hover:bg-indigo-50 border border-slate-200 rounded-lg text-[11px] font-semibold transition"
                                                    title="Start tracking this task again">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                                                <span>Start Again</span>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 text-center flex flex-col items-center">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center mb-2 border border-gray-100">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 00-2 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-gray-400 text-xs font-medium">You haven't started any tasks today yet.</p>
            </div>
        @endif
    </div>
</div>

{{-- Task History Modal --}}
<div id="history-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" onclick="closeHistoryModal()"></div>

        <div class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900" id="history-modal-title">Task History</h3>
                <button type="button" onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Task Title</p>
                <p class="text-sm font-bold text-gray-800" id="history-task-name"></p>
            </div>

            <div class="max-h-[300px] overflow-y-auto mb-4 border border-gray-100 rounded-xl bg-gray-50">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-500 text-[10px] uppercase font-bold sticky top-0">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2 text-right">Time Spent</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body" class="divide-y divide-gray-100 bg-white">
                        <!-- Content injected via JS -->
                    </tbody>
                </table>
            </div>

            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-center justify-between">
                <span class="text-sm font-bold text-indigo-900">Total Accumulated Time:</span>
                <span class="text-lg font-extrabold text-indigo-700" id="history-total-time">0h 0m</span>
            </div>
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
        <div id="comments-list" class="flex-1 overflow-y-auto p-4 sm:p-6 bg-[#efeae2] space-y-2 custom-scrollbar relative">
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

{{-- Task Action Modal (Submit Outcome on Stop) --}}
<div id="task-action-modal" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeTaskActionModal()"></div>

    <div class="relative w-full max-w-lg bg-white shadow-2xl rounded-2xl overflow-hidden border border-slate-200 transform transition-all my-8 z-10">
        {{-- Header --}}
        <div id="action-modal-header" class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-3">
                <div id="action-modal-icon-container" class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0 shadow-sm bg-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                </div>
                <div>
                    <h3 id="action-modal-title" class="text-base font-bold text-slate-800">End Task — Submit Work Outcome</h3>
                    <p id="action-modal-subtitle" class="text-xs text-slate-500 font-medium">Please report your work outcome before stopping this task</p>
                </div>
            </div>
            <button type="button" onclick="closeTaskActionModal()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-200/50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="task-action-form" onsubmit="submitTaskAction(event)" class="p-6 space-y-4">
            <input type="hidden" id="action-task-id">
            <input type="hidden" id="action-type" value="stop">

            {{-- Task Title Info Box --}}
            <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-3 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-indigo-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <div class="min-w-0 flex-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Task Title</div>
                    <div id="action-modal-task-title" class="text-xs font-bold text-slate-800 truncate"></div>
                </div>
            </div>

            {{-- Comment / Work Output --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Work Outcome / Remarks <span class="text-red-500">*</span>
                </label>
                <textarea id="action-modal-comment" rows="3" required
                          placeholder="Describe the outcome, work results, or progress achieved before ending this task..."
                          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition resize-none"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Please summarize what was completed or achieved in this work session.</p>
            </div>

            {{-- File Upload --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Upload Output File (Photo / Excel / PDF)</span>
                    <span class="text-[11px] font-normal text-slate-400">Optional</span>
                </label>
                <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-xl p-4 transition-all bg-slate-50/50 hover:bg-indigo-50/20 text-center relative cursor-pointer"
                     onclick="document.getElementById('action-modal-file').click()">
                    <input type="file" id="action-modal-file" name="output_file"
                           accept="image/*,.pdf,.xlsx,.xls,.csv,.doc,.docx,.zip"
                           class="hidden" onchange="handleActionFileSelect(this)">
                    <div id="action-file-placeholder" class="flex flex-col items-center">
                        <svg class="w-8 h-8 text-slate-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/>
                        </svg>
                        <p class="text-xs font-semibold text-slate-700">Click to upload file</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Images (PNG, JPG), Excel (XLSX, CSV), PDF or Docs (up to 50MB)</p>
                    </div>
                    <div id="action-file-preview" class="hidden flex items-center justify-between bg-white border border-slate-200 rounded-lg p-2.5 text-left">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 font-bold text-xs" id="action-file-icon">
                                📄
                            </div>
                            <div class="min-w-0">
                                <p id="action-file-name" class="text-xs font-semibold text-slate-800 truncate"></p>
                                <p id="action-file-size" class="text-[10px] text-slate-400"></p>
                            </div>
                        </div>
                        <button type="button" onclick="event.stopPropagation(); clearActionFile();" class="text-slate-400 hover:text-red-500 p-1 rounded transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeTaskActionModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" id="action-modal-submit-btn" class="flex items-center gap-2 px-5 py-2 text-xs font-bold text-white rounded-xl transition shadow-sm">
                    {{-- Text injected via JS --}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Task Outputs & Files Modal --}}
<div id="outputs-modal" class="fixed inset-0 z-[105] hidden flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeOutputsModal()"></div>

    <div class="relative w-full max-w-2xl bg-white shadow-2xl rounded-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[85vh] my-8 z-10">
        {{-- Header --}}
        <div class="bg-white px-6 py-4.5 border-b border-slate-100 flex items-center justify-between shrink-0 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Task Work Outputs & Files</h3>
                    <p id="outputs-task-name" class="text-xs text-slate-500 font-medium"></p>
                </div>
            </div>
            <button type="button" onclick="closeOutputsModal()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Outputs List Body --}}
        <div id="outputs-list" class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/50">
            <!-- Injected via JS -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Toast System ──────────────────────────────────────────
    function showToast(message, type = 'success') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-sm w-full';
            document.body.appendChild(container);
        }

        const cfg = {
            success: { bg: 'bg-white border-l-4 border-green-500', icon: 'text-green-500', path: 'M5 13l4 4L19 7' },
            error:   { bg: 'bg-white border-l-4 border-red-500',   icon: 'text-red-500',   path: 'M6 18L18 6M6 6l12 12' },
            warning: { bg: 'bg-white border-l-4 border-amber-500', icon: 'text-amber-500', path: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' }
        }[type] || { bg: 'bg-white border-l-4 border-gray-400', icon: 'text-gray-400', path: 'M13 16h-1v-4h-1m1-4h.01' };

        const t = document.createElement('div');
        t.className = `pointer-events-auto flex items-start gap-3 ${cfg.bg} rounded-xl p-4 shadow-xl text-sm transition duration-300 transform translate-x-4 opacity-0`;
        t.style.transition = 'all 0.3s ease';
        
        t.innerHTML = `
            <svg class="w-5 h-5 ${cfg.icon} flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="${cfg.path}"/>
            </svg>
            <span class="text-gray-700 leading-relaxed font-semibold">${message}</span>`;
            
        container.appendChild(t);
        
        // Trigger reflow for transition
        requestAnimationFrame(() => {
            t.classList.remove('translate-x-4', 'opacity-0');
            t.classList.add('translate-x-0', 'opacity-100');
        });

        setTimeout(() => { 
            t.classList.remove('translate-x-0', 'opacity-100');
            t.classList.add('translate-x-4', 'opacity-0');
            setTimeout(() => t.remove(), 300); 
        }, 3500);
    }

    // Premium Action Toast Confirmation
    function showConfirmToast(message, onConfirm) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-sm w-full';
            document.body.appendChild(container);
        }

        const t = document.createElement('div');
        t.className = 'pointer-events-auto flex flex-col gap-3 bg-white border-l-4 border-amber-500 rounded-xl p-4 shadow-xl text-sm transition duration-300 transform translate-x-4 opacity-0';
        t.style.transition = 'all 0.3s ease';

        t.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-gray-800 font-bold mb-1">Confirm Pause</p>
                    <p class="text-gray-600 leading-normal text-xs font-semibold">${message}</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-1">
                <button type="button" class="px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-100 transition" id="toast-cancel-btn">
                    Cancel
                </button>
                <button type="button" class="px-3 py-1.5 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition" id="toast-confirm-btn">
                    Yes, Pause
                </button>
            </div>
        `;

        container.appendChild(t);

        requestAnimationFrame(() => {
            t.classList.remove('translate-x-4', 'opacity-0');
            t.classList.add('translate-x-0', 'opacity-100');
        });

        const closeToast = () => {
            t.classList.remove('translate-x-0', 'opacity-100');
            t.classList.add('translate-x-4', 'opacity-0');
            setTimeout(() => t.remove(), 300);
        };

        t.querySelector('#toast-cancel-btn').onclick = () => {
            closeToast();
        };

        t.querySelector('#toast-confirm-btn').onclick = () => {
            closeToast();
            onConfirm();
        };
    }

    // Live Task Logic
    const startForm = document.getElementById('start-task-form');
    if (startForm) {
        startForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('start-task-btn');
            btn.disabled = true;
            btn.innerHTML = 'Starting...';

            fetch('{{ route('daily-report.task.start') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    task_title: document.getElementById('start_task_title').value,
                    description: document.getElementById('start_task_desc').value,
                })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    btn.disabled = false;
                    btn.innerHTML = 'Start Task';
                    if (data.require_output && data.pending_task_id) {
                        showToast(data.message, 'warning');
                        openTaskActionModal(data.pending_task_id, 'stop', data.pending_task_title || '');
                    } else {
                        showToast(data.message, 'error');
                    }
                }
            }).catch(err => {
                showToast('An error occurred.', 'error'); btn.disabled = false; btn.innerHTML = 'Start Task';
            });
        });
    }

    const endForm = document.getElementById('end-task-form');
    if (endForm) {
        endForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const taskId = document.getElementById('active_task_id').value;
            const activeTitleEl = document.querySelector('input[readonly].cursor-not-allowed');
            const title = activeTitleEl ? activeTitleEl.value : 'Active Task';
            openTaskActionModal(taskId, 'stop', title);
        });
    }

    const otherForm = document.getElementById('other-task-form');
    if (otherForm) {
        otherForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('other-task-btn');
            btn.disabled = true;
            btn.innerHTML = 'Adding...';

            fetch('{{ route('daily-report.task.other') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    description: document.getElementById('other_task_desc').value,
                })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    btn.disabled = false;
                    btn.innerHTML = 'Add to Other Task';
                    if (data.require_output && data.pending_task_id) {
                        showToast(data.message, 'warning');
                        openTaskActionModal(data.pending_task_id, 'stop', data.pending_task_title || '');
                    } else {
                        showToast(data.message, 'error');
                    }
                }
            }).catch(err => {
                showToast('An error occurred.', 'error'); btn.disabled = false; btn.innerHTML = 'Add to Other Task';
            });
        });
    }

    // Log Active Task Update without pausing
    function logActiveTaskUpdate(taskId) {
        const descInput = document.getElementById('end_task_desc');
        const descValue = descInput ? descInput.value.trim() : '';
        
        if (!descValue) {
            showToast('Please enter a description to log.', 'error');
            return;
        }

        const btn = document.getElementById('log-task-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Logging...';
        }

        fetch('{{ url("daily-report/task") }}/' + taskId + '/update-desc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                description: descValue
            })
        }).then(res => res.json()).then(data => {
            if(data.success) {
                showToast(data.message, 'success');
                if (descInput) descInput.value = ''; // clear the box
                setTimeout(() => location.reload(), 1000);
            } else { 
                showToast(data.message, 'error'); 
                if (btn) { btn.disabled = false; btn.innerHTML = 'Log Update'; } 
            }
        }).catch(err => {
            showToast('An error occurred while logging update.', 'error'); 
            if (btn) { btn.disabled = false; btn.innerHTML = 'Log Update'; }
        });
    }

    // Modal Control: Task Action (Pause / End Task with Output & File)
    function openTaskActionModal(taskId, action, taskTitle = '') {
        document.getElementById('action-task-id').value = taskId;
        document.getElementById('action-type').value = action;
        
        if (!taskTitle) {
            const activeTitleEl = document.querySelector('input[readonly].cursor-not-allowed');
            taskTitle = activeTitleEl ? activeTitleEl.value : 'Task #' + taskId;
        }
        document.getElementById('action-modal-task-title').textContent = taskTitle;

        // Pre-fill comment if user typed in top widget box
        const topDescInput = document.getElementById('end_task_desc');
        const commentInput = document.getElementById('action-modal-comment');
        if (topDescInput && topDescInput.value.trim()) {
            commentInput.value = topDescInput.value.trim();
        } else {
            commentInput.value = '';
        }

        clearActionFile();

        const titleEl = document.getElementById('action-modal-title');
        const subtitleEl = document.getElementById('action-modal-subtitle');
        const iconContainer = document.getElementById('action-modal-icon-container');
        const submitBtn = document.getElementById('action-modal-submit-btn');

        titleEl.textContent = 'End Task — Submit Work Outcome';
        subtitleEl.textContent = 'Please specify the work outcome, results, or remarks before stopping this task';
        iconContainer.className = 'w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0 shadow-sm bg-red-500';
        iconContainer.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>`;
        submitBtn.className = 'flex items-center gap-2 px-5 py-2 text-xs font-bold text-white rounded-xl transition shadow-sm bg-red-600 hover:bg-red-700';
        submitBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span>Confirm & End Task</span>
        `;

        document.getElementById('task-action-modal').classList.remove('hidden');
        setTimeout(() => commentInput.focus(), 100);
    }

    function closeTaskActionModal() {
        document.getElementById('task-action-modal').classList.add('hidden');
        const submitBtn = document.getElementById('action-modal-submit-btn');
        if (submitBtn) submitBtn.disabled = false;
    }

    function handleActionFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            document.getElementById('action-file-placeholder').classList.add('hidden');
            document.getElementById('action-file-preview').classList.remove('hidden');
            document.getElementById('action-file-name').textContent = file.name;
            
            let sizeStr = '';
            if (file.size > 1048576) {
                sizeStr = (file.size / 1048576).toFixed(2) + ' MB';
            } else {
                sizeStr = (file.size / 1024).toFixed(1) + ' KB';
            }
            document.getElementById('action-file-size').textContent = sizeStr;

            const ext = file.name.split('.').pop().toLowerCase();
            let iconEmoji = '📄';
            if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) iconEmoji = '🖼️';
            else if (['xlsx', 'xls', 'csv'].includes(ext)) iconEmoji = '📊';
            else if (['pdf'].includes(ext)) iconEmoji = '📑';
            else if (['doc', 'docx'].includes(ext)) iconEmoji = '📝';
            else if (['zip', 'rar'].includes(ext)) iconEmoji = '📦';
            document.getElementById('action-file-icon').textContent = iconEmoji;
        }
    }

    function clearActionFile() {
        const fileInput = document.getElementById('action-modal-file');
        if (fileInput) fileInput.value = '';
        document.getElementById('action-file-placeholder').classList.remove('hidden');
        document.getElementById('action-file-preview').classList.add('hidden');
    }

    function submitTaskAction(e) {
        e.preventDefault();
        const taskId = document.getElementById('action-task-id').value;
        const comment = document.getElementById('action-modal-comment').value.trim();
        const fileInput = document.getElementById('action-modal-file');
        const submitBtn = document.getElementById('action-modal-submit-btn');

        if (!comment) {
            showToast('Please enter the work outcome / remarks before proceeding.', 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Submitting...`;

        const formData = new FormData();
        formData.append('output_comment', comment);
        formData.append('comment', comment);
        if (fileInput && fileInput.files && fileInput.files[0]) {
            formData.append('output_file', fileInput.files[0]);
        }

        const endpoint = '{{ url("daily-report/task") }}/' + taskId + '/end';

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Work outcome recorded successfully!', 'success');
                setTimeout(() => {
                    location.reload();
                }, 600);
            } else {
                showToast(data.message || 'Failed to submit outcome.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<span>Confirm & End Task</span>`;
            }
        })
        .catch(err => {
            console.error(err);
            showToast('An unexpected network error occurred.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span>Confirm & End Task</span>`;
        });
    }

    // Direct Pause Task (No outcome required)
    function pauseTask(taskId) {
        const pauseBtn = document.getElementById('pause-task-btn');
        if (pauseBtn) {
            pauseBtn.disabled = true;
            pauseBtn.innerHTML = `<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Pausing...`;
        }

        fetch('{{ url("daily-report/task") }}/' + taskId + '/pause', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Task paused successfully', 'success');
                setTimeout(() => location.reload(), 400);
            } else {
                showToast(data.message || 'Failed to pause task.', 'error');
                if (pauseBtn) {
                    pauseBtn.disabled = false;
                    pauseBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" /></svg> Pause Task`;
                }
            }
        })
        .catch(err => {
            console.error(err);
            showToast('An error occurred while pausing.', 'error');
            if (pauseBtn) {
                pauseBtn.disabled = false;
                pauseBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" /></svg> Pause Task`;
            }
        });
    }

    // Delegations for backward compatibility
    function pauseActiveTask(taskId) {
        pauseTask(taskId);
    }

    function endPausedTaskDirect(taskId) {
        openTaskActionModal(taskId, 'stop');
    }

    function endActiveTaskDirect(taskId) {
        openTaskActionModal(taskId, 'stop');
    }

    // Resume Task
    function resumeTask(taskId) {
        fetch('{{ url("daily-report/task") }}/' + taskId + '/resume', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(res => res.json()).then(data => {
            if(data.success) {
                location.reload();
            } else {
                if (data.require_output) {
                    showToast(data.message, 'warning');
                    openTaskActionModal(data.pending_task_id || taskId, 'stop', data.pending_task_title || '');
                } else {
                    showToast(data.message, 'error');
                }
            }
        }).catch(err => {
            showToast('An error occurred while resuming.', 'error');
        });
    }

    function restartCompletedTask(title) {
        const input = document.getElementById('start_task_title');
        if (input) {
            input.value = title;
            input.focus();
            const startBtn = document.getElementById('start-task-btn');
            if (startBtn) {
                startBtn.classList.add('ring-4', 'ring-indigo-300');
                setTimeout(() => startBtn.classList.remove('ring-4', 'ring-indigo-300'), 1500);
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // Modal Control: Task Outputs & Attached Files
    function openOutputsModal(taskId) {
        const list = document.getElementById('outputs-list');
        list.innerHTML = '<div class="text-center py-10 text-slate-400 text-xs flex flex-col items-center gap-2"><span class="animate-spin inline-block w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full"></span>Loading task outputs & files...</div>';
        document.getElementById('outputs-task-name').textContent = 'Loading...';
        document.getElementById('outputs-modal').classList.remove('hidden');

        fetch(`{{ url("daily-report/task") }}/${taskId}/outputs`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                showToast(data.message || 'Error loading outputs', 'error');
                closeOutputsModal();
                return;
            }

            document.getElementById('outputs-task-name').textContent = `${data.task_title} (${data.staff_name})`;

            if (!data.outputs || data.outputs.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-12 flex flex-col items-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700">No output records found</p>
                        <p class="text-xs text-slate-400 mt-1">No outputs or files were submitted for this task yet.</p>
                    </div>
                `;
                return;
            }

            list.innerHTML = '';
            data.outputs.forEach(out => {
                const isPause = out.action_type === 'pause';
                const badgeClass = isPause ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200';
                const badgeLabel = isPause ? '⏸ Paused Output' : '⏹ Completed Output';

                let fileHtml = '';
                if (out.file_path && out.file_url) {
                    const ext = (out.file_type || '').toLowerCase();
                    let iconEmoji = '📄';
                    let isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext);
                    if (isImage) iconEmoji = '🖼️';
                    else if (['xlsx', 'xls', 'csv'].includes(ext)) iconEmoji = '📊';
                    else if (['pdf'].includes(ext)) iconEmoji = '📑';
                    else if (['doc', 'docx'].includes(ext)) iconEmoji = '📝';

                    fileHtml = `
                        <div class="mt-3 p-3 bg-white border border-slate-200 rounded-xl flex items-center justify-between gap-3 shadow-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-base shrink-0">
                                    ${iconEmoji}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate" title="${out.file_name || 'Attached File'}">${out.file_name || 'Download File'}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">${out.file_size || ''} ${ext ? '• ' + ext.toUpperCase() : ''}</p>
                                </div>
                            </div>
                            <a href="${out.file_url}" target="_blank" download class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition shadow-xs shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                View / Download
                            </a>
                        </div>
                    `;

                    if (isImage) {
                        fileHtml += `
                            <div class="mt-2 rounded-xl overflow-hidden border border-slate-200 max-h-48 bg-slate-100 flex items-center justify-center p-1">
                                <a href="${out.file_url}" target="_blank">
                                    <img src="${out.file_url}" alt="Preview" class="object-contain max-h-44 rounded-lg hover:opacity-95 transition">
                                </a>
                            </div>
                        `;
                    }
                }

                list.innerHTML += `
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs hover:border-slate-300 transition">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border uppercase tracking-wider ${badgeClass}">
                                    ${badgeLabel}
                                </span>
                                <span class="text-xs font-bold text-slate-700">${out.user_name}</span>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-400">${out.created_at}</span>
                        </div>
                        <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-wrap bg-slate-50/70 p-3 rounded-xl border border-slate-100">${out.comment}</div>
                        ${fileHtml}
                    </div>
                `;
            });
        })
        .catch(err => {
            console.error(err);
            showToast('Error loading outputs.', 'error');
            closeOutputsModal();
        });
    }

    function closeOutputsModal() {
        document.getElementById('outputs-modal').classList.add('hidden');
    }

    // History Modal Logic
    function viewTaskHistory(taskId) {
        document.getElementById('history-table-body').innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 text-xs">Loading history...</td></tr>';
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
                    tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 text-xs">No history found.</td></tr>';
                } else {
                    data.history.forEach(item => {
                        let statusHtml = '';
                        if (item.status === 'in_progress') statusHtml = '<span class="text-green-600 bg-green-50 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase">Active</span>';
                        else if (item.status === 'paused') statusHtml = '<span class="text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase">Paused</span>';
                        else statusHtml = '<span class="text-gray-500 bg-gray-50 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-100">Completed</span>';

                        tbody.innerHTML += `
                            <tr class="border-b border-gray-100 last:border-0">
                                <td class="px-4 py-3 font-medium text-gray-700 align-top">${item.date}</td>
                                <td class="px-4 py-3 align-top">${statusHtml}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 whitespace-pre-wrap leading-relaxed">${item.description || '—'}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-800 align-top whitespace-nowrap">${item.time_spend}</td>
                            </tr>
                        `;
                    });
                }
            } else {
                showToast(data.message || 'Error loading history', 'error');
                closeHistoryModal();
            }
        }).catch(err => {
            showToast('Network error loading history.', 'error');
            closeHistoryModal();
        });
    }

    function closeHistoryModal() {
        document.getElementById('history-modal').classList.add('hidden');
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
                showToast(data.message || 'Error loading comments', 'error');
                closeCommentsModal();
            }
        }).catch(err => {
            showToast('Network error.', 'error');
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
                openCommentsModal(taskId);
            } else {
                showToast(data.message || 'Error posting comment', 'error');
            }
            btn.disabled = false;
        }).catch(err => {
            showToast('Network error.', 'error');
            btn.disabled = false;
        });
    }
    // Auto-stop countdown, audio chime & desktop browser notifications
    window.ACTIVE_TASK_AUTO_STOP_AT = @json(isset($activeTaskDeadline) && $activeTaskDeadline ? $activeTaskDeadline['deadline']->toIso8601String() : null);
    window.ACTIVE_TASK_ID = @json($activeTask ? $activeTask->id : null);
    window.ACTIVE_TASK_TITLE = @json($activeTask ? $activeTask->task_title : null);
    window.ACTIVE_TASK_LABEL = @json(isset($activeTaskDeadline) && $activeTaskDeadline ? $activeTaskDeadline['label'] : null);

    let autoStopTimerInterval = null;
    let autoStopTriggered = false;
    let titleFlashInterval = null;
    const originalDocumentTitle = document.title;

    function playAutoStopSound() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const playTone = (freq, start, duration) => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime + start);
                gain.gain.setValueAtTime(0.2, audioCtx.currentTime + start);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + start + duration);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(audioCtx.currentTime + start);
                osc.stop(audioCtx.currentTime + start + duration);
            };

            // 3 ascending chime tones, repeated for urgency
            playTone(523.25, 0.0, 0.15); // C5
            playTone(659.25, 0.18, 0.15); // E5
            playTone(783.99, 0.36, 0.25); // G5

            setTimeout(() => {
                playTone(523.25, 0.0, 0.15);
                playTone(659.25, 0.18, 0.15);
                playTone(1046.50, 0.36, 0.35); // C6
            }, 600);
        } catch (e) {
            console.warn('Audio chime play failed:', e);
        }
    }

    function startTitleFlashing(flashText) {
        if (titleFlashInterval) clearInterval(titleFlashInterval);
        let isOriginal = false;
        titleFlashInterval = setInterval(() => {
            document.title = isOriginal ? originalDocumentTitle : flashText;
            isOriginal = !isOriginal;
        }, 800);

        const stopFlashing = () => {
            if (titleFlashInterval) {
                clearInterval(titleFlashInterval);
                titleFlashInterval = null;
                document.title = originalDocumentTitle;
            }
            window.removeEventListener('focus', stopFlashing);
            window.removeEventListener('click', stopFlashing);
        };
        window.addEventListener('focus', stopFlashing);
        window.addEventListener('click', stopFlashing);
    }

    function checkNotificationPermissionBanner() {
        if ("Notification" in window) {
            const banner = document.getElementById('notification-permission-banner');
            if (Notification.permission === "default" && banner) {
                banner.classList.remove('hidden');
            } else if (Notification.permission === "granted" && banner) {
                banner.classList.add('hidden');
            }
        }
    }

    function requestBrowserNotificationPermission(isUserClick = false) {
        if ("Notification" in window) {
            Notification.requestPermission().then(perm => {
                const banner = document.getElementById('notification-permission-banner');
                if (perm === 'granted') {
                    if (banner) banner.classList.add('hidden');
                    if (isUserClick) showToast('Browser desktop notifications enabled successfully!', 'success');
                } else if (isUserClick) {
                    showToast('Notification permission was not granted.', 'warning');
                }
            });
        }
    }

    function showAutoStopBrowserNotification(taskTitle, reason) {
        if ("Notification" in window && Notification.permission === "granted") {
            try {
                const title = '⚠️ Task Auto-Stopped!';
                const message = `Task "${taskTitle || 'Your task'}" has stopped (${reason || 'Cutoff reached'}). Click here to submit your work outcome.`;
                const notif = new Notification(title, {
                    body: message,
                    icon: '/images/logo.png',
                    tag: 'task-auto-stop',
                    requireInteraction: true // Stays on screen until clicked
                });

                notif.onclick = function() {
                    window.focus();
                    this.close();
                };
            } catch (e) {
                console.warn('Notification error:', e);
            }
        } else if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }
    }

    function initAutoStopCountdown() {
        if (!window.ACTIVE_TASK_AUTO_STOP_AT) return;

        const deadlineTime = new Date(window.ACTIVE_TASK_AUTO_STOP_AT).getTime();
        const timerEl = document.getElementById('active-task-countdown');

        function update() {
            const now = new Date().getTime();
            const diff = deadlineTime - now;

            if (diff <= 0) {
                if (timerEl) timerEl.textContent = '00:00:00';
                clearInterval(autoStopTimerInterval);
                triggerAutoStopModal();
                return;
            }

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            const pad = (n) => String(n).padStart(2, '0');
            if (timerEl) {
                timerEl.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            }
        }

        update();
        autoStopTimerInterval = setInterval(update, 1000);
    }

    function triggerAutoStopModal() {
        if (autoStopTriggered) return;
        autoStopTriggered = true;

        // 1. Play audible sound
        playAutoStopSound();

        // 2. Browser Desktop Notification (Alert on Desktop even if minimized / another software open)
        showAutoStopBrowserNotification(window.ACTIVE_TASK_TITLE, window.ACTIVE_TASK_LABEL);

        // 3. Bring window to front
        window.focus();

        // 4. Flash title bar
        startTitleFlashing('⚠️ TASK STOPPED - SUBMIT OUTCOME!');

        // 5. Backend sync & open modal
        fetch('{{ route("daily-report.task.check-auto-stop") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            showToast(`Task Auto-Stopped! ${window.ACTIVE_TASK_LABEL || 'Scheduled cutoff'} reached. Please submit your work outcome.`, 'warning');
            openTaskActionModal(window.ACTIVE_TASK_ID, 'stop', window.ACTIVE_TASK_TITLE);
        })
        .catch(err => {
            openTaskActionModal(window.ACTIVE_TASK_ID, 'stop', window.ACTIVE_TASK_TITLE);
        });
    }

    // Periodic check every 30 seconds
    setInterval(() => {
        fetch('{{ route("daily-report.task.check-auto-stop") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.stopped_count > 0) {
                playAutoStopSound();
                showAutoStopBrowserNotification(data.pending_task ? data.pending_task.task_title : 'Your Task', 'Scheduled cutoff reached');
                window.focus();
                startTitleFlashing('⚠️ TASK STOPPED - SUBMIT OUTCOME!');

                const modal = document.getElementById('task-action-modal');
                if (modal && modal.classList.contains('hidden')) {
                    showToast('A task was automatically stopped. Please submit its work outcome.', 'warning');
                    if (data.pending_task) {
                        openTaskActionModal(data.pending_task.id, 'stop', data.pending_task.task_title);
                    } else {
                        setTimeout(() => location.reload(), 800);
                    }
                }
            }
        })
        .catch(() => {});
    }, 30000);

    // Initialization on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', () => {
        checkNotificationPermissionBanner();
        initAutoStopCountdown();

        @if(isset($pendingOutputTask) && $pendingOutputTask)
            setTimeout(() => {
                playAutoStopSound();
                showAutoStopBrowserNotification('{{ addslashes($pendingOutputTask->task_title) }}', 'Outcome Required');
                window.focus();
                startTitleFlashing('⚠️ TASK STOPPED - SUBMIT OUTCOME!');
                openTaskActionModal({{ $pendingOutputTask->id }}, 'stop', '{{ addslashes($pendingOutputTask->task_title) }}');
            }, 300);
        @endif

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
