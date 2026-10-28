@extends('layouts.app')
@section('title', 'Task Planner & To-Do List')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-indigo-700 via-indigo-600 to-indigo-800 p-6 md:p-8 rounded-3xl text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-semibold text-indigo-100 backdrop-blur-md mb-3 border border-white/10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                Personal Task Planner & To-Do
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">My Task List</h1>
            <p class="text-indigo-100 text-sm mt-1 max-w-xl">
                Track your recurring daily routine tasks and schedule upcoming work with target deadlines.
            </p>
        </div>
        <div class="relative z-10 flex flex-wrap gap-2.5">
            <button onclick="openAddTaskModal('daily')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-md transition transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                + Daily Routine Task
            </button>
            <button onclick="openAddTaskModal('scheduled')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-indigo-700 hover:bg-indigo-50 text-sm font-bold rounded-xl shadow-md transition transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                + Scheduled Future Task
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Total --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Tasks</p>
                <p class="text-2xl font-black text-slate-800">{{ $stats['total'] }}</p>
            </div>
        </div>

        {{-- Daily Routine --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Daily Done Today</p>
                <p class="text-2xl font-black text-slate-800">
                    <span class="text-emerald-600">{{ $stats['daily_completed_today'] }}</span>
                    <span class="text-slate-400 text-sm font-semibold">/ {{ $stats['daily_total'] }}</span>
                </p>
            </div>
        </div>

        {{-- Upcoming / Scheduled --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Upcoming Tasks</p>
                <p class="text-2xl font-black text-slate-800">{{ $stats['upcoming'] }}</p>
            </div>
        </div>

        {{-- Overdue / Completed --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl {{ $stats['overdue'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center shrink-0">
                @if($stats['overdue'] > 0)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                @else
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @endif
            </div>
            <div>
                @if($stats['overdue'] > 0)
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-500">Overdue Tasks</p>
                    <p class="text-2xl font-black text-rose-600">{{ $stats['overdue'] }}</p>
                @else
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Completed Tasks</p>
                    <p class="text-2xl font-black text-emerald-600">{{ $stats['completed'] }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabs & Filters --}}
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex flex-wrap gap-1.5">
            <a href="{{ route('personal-tasks.index', ['filter' => 'all']) }}"
                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 {{ $filter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>📌</span> All Tasks
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'all' ? 'bg-indigo-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('personal-tasks.index', ['filter' => 'daily']) }}"
                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 {{ $filter === 'daily' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>🔄</span> Daily Routine
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'daily' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stats['daily_total'] }}</span>
            </a>
            <a href="{{ route('personal-tasks.index', ['filter' => 'scheduled']) }}"
                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 {{ $filter === 'scheduled' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>📅</span> Scheduled Tasks
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'scheduled' ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stats['scheduled_total'] }}</span>
            </a>
            <a href="{{ route('personal-tasks.index', ['filter' => 'completed']) }}"
                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 {{ $filter === 'completed' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>✅</span> Completed
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'completed' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stats['completed'] }}</span>
            </a>
        </div>
        <div class="text-xs text-slate-400 font-semibold px-2">
            Sorted by priority & deadline
        </div>
    </div>

    {{-- Tasks List --}}
    @if($tasks->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-3xl mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No tasks found</h3>
            <p class="text-slate-500 text-sm mt-1 max-w-md mx-auto">
                You have not added any tasks in this view. Add a daily recurring routine or a task for an upcoming date.
            </p>
            <div class="flex justify-center gap-3 mt-5">
                <button onclick="openAddTaskModal('daily')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition">
                    + Add Daily Routine Task
                </button>
                <button onclick="openAddTaskModal('scheduled')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition">
                    + Add Scheduled Task
                </button>
            </div>
        </div>
    @else
        <div class="space-y-3">
            @foreach($tasks as $task)
                @php
                    $isDoneToday = $task->isDaily() ? ($task->last_completed_at && $task->last_completed_at->isToday()) : ($task->status === 'completed');
                    $isScheduled = $task->isScheduled();
                    $isOverdue = $isScheduled && !$isDoneToday && $task->due_date && $task->due_date->isPast() && !$task->due_date->isToday();
                    $isDueToday = $isScheduled && $task->due_date && $task->due_date->isToday();
                @endphp

                <div class="bg-white rounded-2xl border transition hover:shadow-md p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $isDoneToday ? 'border-emerald-200 bg-emerald-50/20' : ($isOverdue ? 'border-rose-200 bg-rose-50/10' : 'border-slate-100') }}">
                    <div class="flex items-start gap-3.5 flex-1 min-w-0">
                        {{-- Toggle Button / Checkbox --}}
                        <form action="{{ route('personal-tasks.toggle', $task->id) }}" method="POST" class="mt-0.5 shrink-0">
                            @csrf
                            <button type="submit" title="{{ $isDoneToday ? 'Mark as Pending' : 'Mark as Completed' }}"
                                class="w-7 h-7 rounded-lg border-2 flex items-center justify-center transition {{ $isDoneToday ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-emerald-500 text-transparent hover:text-emerald-500' }}">
                                <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </button>
                        </form>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                {{-- Task Type Badge --}}
                                @if($task->isDaily())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                        Daily Routine
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        Scheduled Task
                                    </span>
                                @endif

                                {{-- Due Date / Recurrence Info --}}
                                @if($task->isScheduled() && $task->due_date)
                                    @if($isOverdue)
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                            ⚠️ Overdue: {{ $task->due_date->format('d M, Y') }}
                                        </span>
                                    @elseif($isDueToday)
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            ⏰ Due Today
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                            📅 Due: {{ $task->due_date->format('d M, Y') }} ({{ $task->due_date->diffForHumans() }})
                                        </span>
                                    @endif
                                @endif

                                {{-- Priority Badge --}}
                                @php
                                    $pColors = [
                                        'urgent' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'high'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'medium' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'low'    => 'bg-slate-50 text-slate-600 border-slate-200',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $pColors[$task->priority] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                    {{ $task->priority }}
                                </span>

                                {{-- Daily status indicator --}}
                                @if($task->isDaily())
                                    @if($isDoneToday)
                                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                            <span>✓</span> Completed for Today
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-slate-500">
                                            Pending for Today
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <h4 class="text-base font-bold text-slate-800 {{ $isDoneToday ? 'line-through text-slate-400' : '' }}">
                                {{ $task->title }}
                            </h4>

                            @if($task->description)
                                <p class="text-xs sm:text-sm text-slate-500 mt-1 {{ $isDoneToday ? 'line-through text-slate-400' : '' }}">
                                    {{ $task->description }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 w-full sm:w-auto justify-end">
                        <button onclick='openEditTaskModal(@json($task))'
                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition"
                            title="Edit Task">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </button>

                        <form action="{{ route('personal-tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Delete Task">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- Add Task Modal (Optimized & Cleanly Centered) --}}
<div id="addTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-4 sm:p-6">
    <div class="min-h-full flex items-center justify-center py-6">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
            {{-- Modal Header --}}
            <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-black text-slate-800" id="modalHeading">Add New Task</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Create a daily routine recurring task or a scheduled future task.</p>
                </div>
                <button onclick="closeAddTaskModal()" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('personal-tasks.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Type selection radio cards --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Task Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="daily" id="typeDaily" class="peer sr-only" onchange="handleTypeChange('daily')" checked>
                            <div class="p-3 rounded-xl border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 text-center transition hover:border-slate-300">
                                <svg class="w-5 h-5 mx-auto text-emerald-600 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                <span class="text-xs font-bold text-slate-800 block">Daily Routine</span>
                                <span class="text-[10px] text-slate-500 block">Repeats every day</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="scheduled" id="typeScheduled" class="peer sr-only" onchange="handleTypeChange('scheduled')">
                            <div class="p-3 rounded-xl border-2 border-slate-200 peer-checked:border-blue-500 peer-checked:bg-blue-50/50 text-center transition hover:border-slate-300">
                                <svg class="w-5 h-5 mx-auto text-blue-600 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span class="text-xs font-bold text-slate-800 block">Scheduled Task</span>
                                <span class="text-[10px] text-slate-500 block">Specific target date</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Task Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g., Daily Account Audit or File Monthly GST Return"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                </div>

                {{-- Due Date (for scheduled tasks) --}}
                <div id="dueDateGroup" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Due Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="due_date" id="addDueDateInput" min="{{ date('Y-m-d') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Priority</label>
                    <select name="priority" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description / Notes (Optional)</label>
                    <textarea name="description" rows="2.5" placeholder="Enter any important details, checklist or instructions..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeAddTaskModal()" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                        Save Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Task Modal (Optimized & Cleanly Centered) --}}
<div id="editTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-4 sm:p-6">
    <div class="min-h-full flex items-center justify-center py-6">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-black text-slate-800">Edit Task</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Update task details and settings.</p>
                </div>
                <button onclick="closeEditTaskModal()" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form id="editTaskForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <input type="hidden" name="type" id="editType">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Task Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" id="editTitle" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                </div>

                <div id="editDueDateGroup">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Due Date</label>
                    <input type="date" name="due_date" id="editDueDate"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Priority</label>
                        <select name="priority" id="editPriority" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="editStatus" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description / Notes</label>
                    <textarea name="description" id="editDescription" rows="2.5"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeEditTaskModal()" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                        Update Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openAddTaskModal(type) {
        document.getElementById('addTaskModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (type === 'daily') {
            document.getElementById('typeDaily').checked = true;
            handleTypeChange('daily');
        } else {
            document.getElementById('typeScheduled').checked = true;
            handleTypeChange('scheduled');
        }
    }

    function closeAddTaskModal() {
        document.getElementById('addTaskModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function handleTypeChange(type) {
        const dueDateGroup = document.getElementById('dueDateGroup');
        const dueDateInput = document.getElementById('addDueDateInput');
        if (type === 'scheduled') {
            dueDateGroup.classList.remove('hidden');
            dueDateInput.setAttribute('required', 'required');
            if (!dueDateInput.value) {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                dueDateInput.value = tomorrow.toISOString().split('T')[0];
            }
        } else {
            dueDateGroup.classList.add('hidden');
            dueDateInput.removeAttribute('required');
        }
    }

    function openEditTaskModal(task) {
        document.getElementById('editTaskModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('editTaskForm').action = `/personal-tasks/${task.id}`;
        document.getElementById('editTitle').value = task.title || '';
        document.getElementById('editType').value = task.type || 'daily';
        document.getElementById('editPriority').value = task.priority || 'medium';
        document.getElementById('editStatus').value = task.status || 'pending';
        document.getElementById('editDescription').value = task.description || '';

        const editDueDateGroup = document.getElementById('editDueDateGroup');
        const editDueDate = document.getElementById('editDueDate');
        if (task.type === 'scheduled') {
            editDueDateGroup.classList.remove('hidden');
            if (task.due_date) {
                editDueDate.value = typeof task.due_date === 'string' ? task.due_date.substring(0, 10) : '';
            } else {
                editDueDate.value = '';
            }
        } else {
            editDueDateGroup.classList.add('hidden');
        }
    }

    function closeEditTaskModal() {
        document.getElementById('editTaskModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddTaskModal();
            closeEditTaskModal();
        }
    });

    // Close on backdrop click
    document.getElementById('addTaskModal').addEventListener('click', function(e) {
        if (e.target === this || e.target === this.firstElementChild) {
            closeAddTaskModal();
        }
    });
    document.getElementById('editTaskModal').addEventListener('click', function(e) {
        if (e.target === this || e.target === this.firstElementChild) {
            closeEditTaskModal();
        }
    });
</script>
@endpush
@endsection
