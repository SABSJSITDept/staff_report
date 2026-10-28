@extends('layouts.app')
@section('title', $project->title . ' - Project Group Chat')

@section('content')
<div class="max-w-7xl mx-auto -mt-3 md:-mt-4">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between mb-3 px-1">
        <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-700 transition group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            <span>Back to All Projects</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="openProjectInfoModal()" class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl transition shadow-2xs flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Group Info & Tasks
            </button>
        </div>
    </div>

    {{-- MAIN WHATSAPP WINDOW CONTAINER --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-xl border border-slate-200/90 overflow-hidden flex flex-col h-[calc(100vh-140px)] min-h-[580px]">

        {{-- 1. WHATSAPP TOP HEADER BAR --}}
        <div class="bg-[#008069] text-white px-4 py-3 flex items-center justify-between shadow-md shrink-0 z-20">
            <div class="flex items-center gap-3 min-w-0 cursor-pointer" onclick="openProjectInfoModal()">
                {{-- Group Profile Avatar --}}
                <div class="relative shrink-0">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-full bg-emerald-800 text-white font-black text-sm md:text-base flex items-center justify-center border-2 border-emerald-400/40 shadow-inner">
                        {{ strtoupper(substr($project->title, 0, 2)) }}
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-400 border-2 border-[#008069] rounded-full"></span>
                </div>

                {{-- Group Subject & Participants preview --}}
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="font-bold text-white text-sm md:text-base tracking-tight truncate leading-tight">{{ $project->title }}</h2>
                        <span class="px-2 py-0.2 rounded-full text-[10px] font-black uppercase bg-emerald-900/60 text-emerald-200 border border-emerald-400/20 hidden sm:inline">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                    <p class="text-emerald-100 text-[11px] md:text-xs truncate font-medium">
                        <span class="text-emerald-200 font-bold">Group Members:</span>
                        You,
                        @foreach($project->members->where('id', '!=', Auth::id())->take(3) as $m)
                            {{ $m->name }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                        @if($project->members->where('id', '!=', Auth::id())->count() > 3)
                            +{{ $project->members->where('id', '!=', Auth::id())->count() - 3 }} more
                        @endif
                    </p>
                </div>
            </div>

            {{-- Header Right Controls --}}
            <div class="flex items-center gap-1.5 md:gap-2">
                {{-- Mobile View Toggle (Chat vs Tasks) --}}
                <div class="md:hidden flex bg-emerald-900/40 rounded-xl p-0.5 border border-emerald-600/50">
                    <button type="button" onclick="setMobileView('chat')" id="mobileBtnChat"
                        class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-emerald-900 transition">
                        💬 Chat
                    </button>
                    <button type="button" onclick="setMobileView('tasks')" id="mobileBtnTasks"
                        class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-100 transition">
                        📋 Tasks ({{ $tasks->count() }})
                    </button>
                </div>

                {{-- Add Task Button --}}
                <button type="button" onclick="openAddTaskModal()"
                    class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-white text-xs font-black rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">Add Task</span>
                </button>

                {{-- Add Member (if owner/admin) --}}
                @if(($project->isOwner(Auth::id()) || Auth::user()->isAdmin()) && $availableStaff->isNotEmpty())
                    <button type="button" onclick="openAddMemberModal()" title="Add Member"
                        class="p-2 text-emerald-100 hover:text-white hover:bg-emerald-700/60 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </button>
                @endif

                {{-- Group Info Icon --}}
                <button type="button" onclick="openProjectInfoModal()" title="Group Info"
                    class="p-2 text-emerald-100 hover:text-white hover:bg-emerald-700/60 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </button>
            </div>
        </div>

        {{-- 2. SPLIT BODY: LEFT TASK TRACKER + RIGHT WHATSAPP CHAT STREAM --}}
        <div class="flex-1 flex overflow-hidden">

            {{-- ============================================================== --}}
            {{-- LEFT PANE: PROJECT TASKS LIST (WhatsApp Sidebar Style)         --}}
            {{-- ============================================================== --}}
            <div id="tasksSidebar" class="hidden md:flex flex-col w-80 lg:w-96 border-r border-slate-200 bg-white shrink-0">

                {{-- Sidebar Top: Search & Quick Filters --}}
                <div class="p-3 bg-[#f0f2f5] border-b border-slate-200 space-y-2">
                    <div class="relative">
                        <input type="text" id="taskSearchSidebar" oninput="filterSidebarTasks()" placeholder="Search project tasks..."
                            class="w-full pl-8 pr-3 py-1.5 bg-white rounded-lg text-xs text-slate-800 placeholder-slate-400 outline-none border border-slate-200 focus:border-emerald-600 transition font-medium">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    {{-- Quick Filter Chips --}}
                    <div class="flex items-center gap-1 overflow-x-auto pb-0.5">
                        <button type="button" onclick="filterTaskStatus('all')" class="task-filter-pill px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#008069] text-white shrink-0" data-filter="all">
                            All ({{ $taskStats['total'] }})
                        </button>
                        <button type="button" onclick="filterTaskStatus('my_tasks')" class="task-filter-pill px-2.5 py-1 rounded-full text-[11px] font-bold bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 shrink-0" data-filter="my_tasks">
                            ⭐ My Tasks ({{ $taskStats['assigned_to_me'] }})
                        </button>
                        <button type="button" onclick="filterTaskStatus('pending')" class="task-filter-pill px-2.5 py-1 rounded-full text-[11px] font-bold bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 shrink-0" data-filter="pending">
                            Pending ({{ $taskStats['pending'] }})
                        </button>
                        <button type="button" onclick="filterTaskStatus('completed')" class="task-filter-pill px-2.5 py-1 rounded-full text-[11px] font-bold bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 shrink-0" data-filter="completed">
                            Done ({{ $taskStats['completed'] }})
                        </button>
                    </div>
                </div>

                {{-- Task Items List --}}
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100" id="sidebarTasksList">
                    @forelse($tasks as $t)
                        @php
                            $isDone = $t->status === 'completed';
                            $canUpdate = Auth::user()->isAdmin() || $t->assigned_to == Auth::id();
                            $isMine = $t->assigned_to == Auth::id();
                            $statusColors = [
                                'pending'     => 'bg-amber-100 text-amber-800 border-amber-200',
                                'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'review'      => 'bg-purple-100 text-purple-800 border-purple-200',
                                'completed'   => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            ];
                        @endphp
                        <div class="sidebar-task-item p-3 hover:bg-[#f5f6f6] transition cursor-pointer relative group {{ $isMine ? 'bg-emerald-50/20' : '' }}"
                            data-task-id="{{ $t->id }}"
                            data-title="{{ strtolower($t->title) }}"
                            data-assignee="{{ strtolower($t->assignedTo->name ?? '') }}"
                            data-status="{{ $t->status }}"
                            data-mine="{{ $isMine ? '1' : '0' }}">

                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                    {{-- Quick Checkbox/Status Icon --}}
                                    @if($canUpdate)
                                        <form action="{{ route('project-tasks.status', $t->id) }}" method="POST" class="mt-0.5 shrink-0">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $isDone ? 'in_progress' : 'completed' }}">
                                            <button type="submit" class="w-5 h-5 rounded-md border flex items-center justify-center transition {{ $isDone ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-300 hover:border-emerald-500 bg-white' }}" title="{{ $isDone ? 'Mark as Incomplete' : 'Mark as Completed' }}">
                                                @if($isDone)
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                    @else
                                        <div class="w-5 h-5 rounded-md border border-slate-200 bg-slate-50 flex items-center justify-center text-[10px] text-slate-400 mt-0.5 shrink-0" title="Only assigned staff can update">
                                            🔒
                                        </div>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs font-bold text-slate-800 leading-snug {{ $isDone ? 'line-through text-slate-400' : '' }}">
                                            {{ $t->title }}
                                        </h4>

                                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5 text-[10px]">
                                            <span class="px-1.5 py-0.2 rounded font-black uppercase border {{ $statusColors[$t->status] ?? 'bg-slate-100 text-slate-600' }}">
                                                {{ ucfirst(str_replace('_', ' ', $t->status)) }}
                                            </span>

                                            <span class="text-slate-500 font-semibold truncate">
                                                @if($isMine)
                                                    <strong class="text-emerald-700">🙋 You</strong>
                                                @else
                                                    👤 {{ $t->assignedTo->name ?? 'Unassigned' }}
                                                @endif
                                            </span>

                                            @if($t->due_date)
                                                <span class="{{ $t->due_date->isPast() && !$isDone ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                                    📅 {{ $t->due_date->format('d M') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Quick Edit for assigned --}}
                                @if($canUpdate)
                                    <button onclick='openEditTaskModal(@json($t))' class="text-slate-400 hover:text-emerald-600 p-1 rounded transition opacity-0 group-hover:opacity-100 shrink-0" title="Edit Task">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No tasks created yet.<br>Click "+ Add Task" to create one.
                        </div>
                    @endforelse
                </div>

                {{-- Sidebar Bottom Stats --}}
                <div class="p-2.5 bg-slate-50 border-t border-slate-200 text-[11px] text-slate-500 flex items-center justify-between font-semibold">
                    <span>{{ $taskStats['completed'] }} of {{ $taskStats['total'] }} tasks completed</span>
                    <span class="text-emerald-700 font-bold">{{ $taskStats['progress'] }}%</span>
                </div>
            </div>

            {{-- ============================================================== --}}
            {{-- RIGHT PANE: WHATSAPP GROUP CHAT STREAM                         --}}
            {{-- ============================================================== --}}
            <div id="chatPane" class="flex-1 flex flex-col bg-[#efeae2] relative min-w-0">

                {{-- Subtle WhatsApp Chat Doodle Background Pattern Overlay --}}
                <div class="absolute inset-0 opacity-[0.06] pointer-events-none" style="background-image: radial-gradient(#008069 0.75px, transparent 0.75px), radial-gradient(#008069 0.75px, #efeae2 0.75px); background-size: 30px 30px; background-position: 0 0, 15px 15px;"></div>

                {{-- Chat Messages Feed (Scrollable) --}}
                <div id="chatMessagesContainer" class="flex-1 overflow-y-auto p-3 md:p-5 space-y-3 relative z-10">

                    {{-- Security / Policy Pill --}}
                    <div class="flex justify-center">
                        <div class="bg-[#ffeecd] text-[#54656f] text-[11px] font-medium px-3.5 py-1.5 rounded-lg shadow-2xs max-w-md text-center border border-amber-200/50">
                            🔒 <strong>Office Project Group:</strong> Tasks and messages are shared with office colleagues in this project. Each task can only be updated by its assigned staff.
                        </div>
                    </div>

                    {{-- Date Separator Pill --}}
                    <div class="flex justify-center my-2">
                        <span class="bg-white/90 text-slate-500 font-bold text-[10px] uppercase tracking-wider px-3 py-1 rounded-md shadow-2xs border border-slate-200/50">
                            TODAY
                        </span>
                    </div>

                    {{-- Loop through group messages --}}
                    @forelse($messages as $msg)
                        @php
                            $isMe = $msg->user_id == Auth::id();
                            $userName = $msg->user ? $msg->user->name : 'System';
                            $userInitial = substr($userName, 0, 1);
                            $timeStr = $msg->created_at->format('h:i A');
                            $msgColors = ['#1f7a8c', '#00a884', '#e542a3', '#6f42c1', '#d97706', '#2563eb'];
                            $nameColor = $msgColors[crc32($userName) % count($msgColors)];
                        @endphp

                        @if($msg->type === 'system')
                            {{-- System Event Bubble --}}
                            <div class="flex justify-center">
                                <span class="bg-white/80 text-slate-600 text-xs px-3 py-1 rounded-lg shadow-2xs border border-slate-200/60 flex items-center gap-1.5">
                                    <span>📢</span>
                                    <span><strong>{{ $userName }}</strong> {{ $msg->message }}</span>
                                    <span class="text-[9px] text-slate-400 ml-1">{{ $timeStr }}</span>
                                </span>
                            </div>

                        @elseif($msg->type === 'status_update')
                            {{-- Task Status Change Event Pill --}}
                            <div class="flex justify-center">
                                <span class="bg-emerald-50 text-emerald-900 text-xs px-3.5 py-1.5 rounded-xl shadow-2xs border border-emerald-200 flex items-center gap-1.5">
                                    <span>⚡</span>
                                    <span><strong>{{ $userName }}</strong> {{ $msg->message }}</span>
                                    <span class="text-[9px] text-emerald-600 font-bold ml-1">{{ $timeStr }}</span>
                                </span>
                            </div>

                        @elseif($msg->type === 'task_card' && $msg->task)
                            {{-- INTERACTIVE TASK CARD BUBBLE (WhatsApp Shared Rich Card) --}}
                            @php
                                $cardTask = $msg->task;
                                $isTaskDone = $cardTask->status === 'completed';
                                $canUpdateThisTask = Auth::user()->isAdmin() || $cardTask->assigned_to == Auth::id();
                                $isTaskAssignedToMe = $cardTask->assigned_to == Auth::id();
                                $taskPriorityColors = [
                                    'urgent' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'high'   => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'medium' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'low'    => 'bg-slate-100 text-slate-700 border-slate-200',
                                ];
                            @endphp

                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-sm md:max-w-md w-full rounded-2xl shadow-sm border p-3.5 {{ $isMe ? 'bg-[#d9fdd3] border-emerald-200 rounded-tr-none' : 'bg-white border-slate-200 rounded-tl-none' }}">
                                    {{-- Sender Name if not Me --}}
                                    @if(!$isMe)
                                        <div class="text-xs font-bold mb-1.5" style="color: {{ $nameColor }}">
                                            {{ $userName }}
                                        </div>
                                    @endif

                                    {{-- Task Card Content Box --}}
                                    <div class="bg-white/90 rounded-xl p-3 border border-slate-200/80 shadow-2xs space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider border {{ $taskPriorityColors[$cardTask->priority] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $cardTask->priority }}
                                            </span>
                                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                                {{ ucfirst(str_replace('_', ' ', $cardTask->status)) }}
                                            </span>
                                        </div>

                                        <h4 class="text-sm font-black text-slate-900 leading-snug {{ $isTaskDone ? 'line-through text-slate-400' : '' }}">
                                            📋 {{ $cardTask->title }}
                                        </h4>

                                        @if($cardTask->description)
                                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                                {{ $cardTask->description }}
                                            </p>
                                        @endif

                                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                            <div class="font-semibold text-slate-700 flex items-center gap-1">
                                                <span>👤</span>
                                                <span>{{ $isTaskAssignedToMe ? 'Assigned to You' : 'Assigned to ' . ($cardTask->assignedTo->name ?? 'Colleague') }}</span>
                                            </div>
                                            @if($cardTask->due_date)
                                                <span class="text-slate-500 font-semibold">📅 {{ $cardTask->due_date->format('d M') }}</span>
                                            @endif
                                        </div>

                                        {{-- 1-Click Status Button right on the card for assigned member! --}}
                                        <div class="pt-2 border-t border-slate-100">
                                            @if($canUpdateThisTask)
                                                <form action="{{ route('project-tasks.status', $cardTask->id) }}" method="POST">
                                                    @csrf
                                                    @if($cardTask->status === 'pending')
                                                        <input type="hidden" name="status" value="in_progress">
                                                        <button type="submit" class="w-full py-1.5 px-3 bg-[#008069] hover:bg-[#00705b] text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center justify-center gap-1">
                                                            ⚡ Start Task (Move to In Progress)
                                                        </button>
                                                    @elseif($cardTask->status === 'in_progress')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="w-full py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center justify-center gap-1">
                                                            ✅ Mark as Completed
                                                        </button>
                                                    @elseif($cardTask->status === 'review')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="w-full py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center justify-center gap-1">
                                                            ✅ Approve & Complete
                                                        </button>
                                                    @else
                                                        <input type="hidden" name="status" value="in_progress">
                                                        <button type="submit" class="w-full py-1 px-3 text-slate-600 hover:bg-slate-100 text-xs font-semibold rounded-lg transition text-center">
                                                            ↺ Reopen Task
                                                        </button>
                                                    @endif
                                                </form>
                                            @else
                                                <div class="text-[10px] text-slate-500 font-medium italic flex items-center gap-1">
                                                    🔒 Only {{ $cardTask->assignedTo->name ?? 'assignee' }} can update this task.
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Timestamp & Tick --}}
                                    <div class="flex items-center justify-end gap-1 mt-1 text-[10px] text-slate-500">
                                        <span>{{ $timeStr }}</span>
                                        @if($isMe)
                                            <span class="text-blue-500 font-bold">✓✓</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        @else
                            {{-- STANDARD CHAT MESSAGE BUBBLE (WhatsApp Style) --}}
                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs sm:max-w-md lg:max-w-lg rounded-2xl p-2.5 sm:p-3 shadow-xs border relative {{ $isMe ? 'bg-[#d9fdd3] border-emerald-200/80 rounded-tr-none' : 'bg-white border-slate-200/80 rounded-tl-none' }}">
                                    {{-- Sender Name in Color (WhatsApp group style) --}}
                                    @if(!$isMe)
                                        <div class="text-[11px] font-bold mb-0.5" style="color: {{ $nameColor }}">
                                            {{ $userName }}
                                        </div>
                                    @endif

                                    {{-- If this message quotes/references a task --}}
                                    @if($msg->task)
                                        <div class="bg-black/5 border-l-4 border-emerald-600 p-1.5 rounded text-[11px] mb-1.5 text-slate-700">
                                            <span class="font-bold">Task:</span> {{ $msg->task->title }}
                                        </div>
                                    @endif

                                    {{-- Message Body --}}
                                    <p class="text-xs sm:text-sm text-slate-800 leading-relaxed break-words pr-12">
                                        {{ $msg->message }}
                                    </p>

                                    {{-- Time & Checkmarks (Bottom right, WhatsApp style) --}}
                                    <div class="absolute right-2 bottom-1 flex items-center gap-1 text-[10px] text-slate-500 font-medium">
                                        <span>{{ $timeStr }}</span>
                                        @if($isMe)
                                            <span class="text-blue-500 font-bold">✓✓</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="h-64 flex flex-col items-center justify-center text-slate-400 text-xs space-y-2">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-2xl shadow-xs">
                                💬
                            </div>
                            <p class="font-bold text-slate-600">Welcome to {{ $project->title }} Group!</p>
                            <p>Type a message below or click "+ Add Task" to start collaborating.</p>
                        </div>
                    @endforelse

                    <div id="chat-bottom"></div>
                </div>

                {{-- 3. WHATSAPP BOTTOM CHAT INPUT BAR --}}
                <div class="bg-[#f0f2f5] p-2.5 md:p-3 border-t border-slate-200 shrink-0 relative z-20">
                    <form action="{{ route('projects.messages.store', $project->id) }}" method="POST" class="flex items-center gap-2">
                        @csrf

                        {{-- Attachment / Action Button (+) --}}
                        <div class="relative">
                            <button type="button" onclick="toggleAttachmentMenu()" id="btnAttachment"
                                class="p-2 text-slate-600 hover:text-emerald-700 hover:bg-slate-200/80 rounded-full transition" title="Add Task or Action">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            </button>

                            {{-- Attachment Dropup Menu --}}
                            <div id="attachmentMenu" class="hidden absolute bottom-12 left-0 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 p-1.5 space-y-1 z-30">
                                <button type="button" onclick="openAddTaskModal(); toggleAttachmentMenu();"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 rounded-xl transition text-left">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">📋</span>
                                    <span>Assign New Task</span>
                                </button>

                                @if(($project->isOwner(Auth::id()) || Auth::user()->isAdmin()) && $availableStaff->isNotEmpty())
                                    <button type="button" onclick="openAddMemberModal(); toggleAttachmentMenu();"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-800 rounded-xl transition text-left">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">👥</span>
                                        <span>Add Office Member</span>
                                    </button>
                                @endif

                                <button type="button" onclick="openProjectInfoModal(); toggleAttachmentMenu();"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 rounded-xl transition text-left">
                                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center">ℹ️</span>
                                    <span>Project Info & Rules</span>
                                </button>
                            </div>
                        </div>

                        {{-- Optional Task Mention / Link Selector --}}
                        <select name="project_task_id" class="hidden lg:block w-36 text-[11px] font-semibold py-2 px-2 rounded-xl border border-slate-200 bg-white text-slate-600 focus:outline-none focus:border-emerald-600 cursor-pointer truncate">
                            <option value="">💬 General Chat</option>
                            @foreach($tasks as $t)
                                <option value="{{ $t->id }}">Task: {{ $t->title }}</option>
                            @endforeach
                        </select>

                        {{-- Message Input (WhatsApp style) --}}
                        <input type="text" name="message" required autocomplete="off" placeholder="Type a message, note, or update..."
                            class="flex-1 px-4 py-2.5 bg-white rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none border border-slate-200 focus:border-emerald-600 shadow-2xs font-medium transition">

                        {{-- Green Circular Send Button --}}
                        <button type="submit"
                            class="w-10 h-10 rounded-full bg-[#008069] hover:bg-[#00705b] text-white flex items-center justify-center shadow-md transition transform active:scale-95 shrink-0" title="Send Message">
                            <svg class="w-4 h-4 transform rotate-45 -ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ============================================================== --}}
{{-- MODAL 1: ADD TASK MODAL (Centered & Scroll-safe)               --}}
{{-- ============================================================== --}}
<div id="addTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs p-4 sm:p-6">
    <div class="min-h-full flex items-center justify-center py-6">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Assign Task to Group</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Assign this task to yourself or any colleague in this project.</p>
                </div>
                <button onclick="closeAddTaskModal()" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('projects.tasks.store', $project->id) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Task Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g., Audit monthly GST filing, Add export button..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Assign Task To <span class="text-rose-500">*</span></label>
                    <select name="assigned_to" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                        <option value="{{ Auth::id() }}">🙋 Assign to Myself ({{ Auth::user()->name }})</option>
                        @foreach($project->members as $member)
                            @if($member->id != Auth::id())
                                <option value="{{ $member->id }}">👤 Assign to {{ $member->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Priority</label>
                        <select name="priority" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">🔥 Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target / Due Date</label>
                        <input type="date" name="due_date" min="{{ date('Y-m-d') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Initial Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                        <option value="pending" selected>⏳ To Do / Pending</option>
                        <option value="in_progress">⚡ In Progress</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Task Instructions / Description</label>
                    <textarea name="description" rows="2.5" placeholder="Specify requirements or checklist details..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeAddTaskModal()" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#008069] hover:bg-[#00705b] text-white text-sm font-bold rounded-xl shadow-md transition">
                        Add Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 2: EDIT TASK MODAL (Only Assigned Member)                --}}
{{-- ============================================================== --}}
<div id="editTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs p-4 sm:p-6">
    <div class="min-h-full flex items-center justify-center py-6">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Update Task Details</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Only the assigned staff member can update this task.</p>
                </div>
                <button onclick="closeEditTaskModal()" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form id="editTaskForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Task Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" id="editTitle" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                </div>

                <div id="editAssignedToWrapper">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Assigned Member</label>
                    <select name="assigned_to" id="editAssignedTo" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                        @foreach($project->members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} {{ $member->id == Auth::id() ? '(Myself)' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="editStatus" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                            <option value="pending">⏳ Pending</option>
                            <option value="in_progress">⚡ In Progress</option>
                            <option value="review">👀 Under Review</option>
                            <option value="completed">✅ Completed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Priority</label>
                        <select name="priority" id="editPriority" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">🔥 Urgent</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target / Due Date</label>
                    <input type="date" name="due_date" id="editDueDate"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description / Notes</label>
                    <textarea name="description" id="editDescription" rows="2.5"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeEditTaskModal()" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#008069] hover:bg-[#00705b] text-white text-sm font-bold rounded-xl shadow-md transition">
                        Update Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 3: GROUP INFO & DETAILS MODAL (WhatsApp Style Group Info)--}}
{{-- ============================================================== --}}
<div id="projectInfoModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs p-4 sm:p-6">
    <div class="min-h-full flex items-center justify-center py-6">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative space-y-5">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-[#008069] text-white font-black text-lg flex items-center justify-center shadow-inner">
                        {{ strtoupper(substr($project->title, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 leading-tight">{{ $project->title }}</h3>
                        <p class="text-xs text-slate-500">Created by <strong class="text-slate-800">{{ $project->creator->name }}</strong></p>
                    </div>
                </div>
                <button onclick="closeProjectInfoModal()" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Description --}}
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 text-xs text-slate-700 leading-relaxed">
                <strong class="text-slate-900 block mb-0.5">Group Description:</strong>
                {{ $project->description ?: 'No description provided.' }}
            </div>

            {{-- Progress Summary --}}
            <div class="space-y-1.5">
                <div class="flex justify-between items-center text-xs font-bold">
                    <span class="text-slate-600">Task Completion</span>
                    <span class="text-emerald-700 font-black">{{ $taskStats['progress'] }}% ({{ $taskStats['completed'] }}/{{ $taskStats['total'] }})</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-[#008069] h-2 rounded-full transition-all duration-500" style="width: {{ $taskStats['progress'] }}%"></div>
                </div>
            </div>

            {{-- Members List --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $project->members->count() }} Participants</span>
                    @if(($project->isOwner(Auth::id()) || Auth::user()->isAdmin()) && $availableStaff->isNotEmpty())
                        <button onclick="closeProjectInfoModal(); openAddMemberModal();" class="text-xs font-bold text-[#008069] hover:underline">
                            + Add Colleague
                        </button>
                    @endif
                </div>

                <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1 divide-y divide-slate-100">
                    @foreach($project->members as $member)
                        <div class="flex items-center justify-between pt-1.5 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 font-black text-xs flex items-center justify-center">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800">{{ $member->name }}</span>
                                    @if($member->id == Auth::id())
                                        <span class="text-[10px] text-emerald-600 font-bold ml-1">(You)</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($member->id == $project->created_by)
                                    <span class="bg-emerald-100 text-emerald-800 text-[9px] font-black uppercase px-2 py-0.5 rounded-full">Group Lead</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            @if($project->isOwner(Auth::id()) || Auth::user()->isAdmin())
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <button onclick="closeProjectInfoModal(); openEditProjectModal();" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                        Edit Project Info
                    </button>

                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Delete this project group permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl transition">
                            Delete Group
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 4: ADD OFFICE COLLEAGUES MODAL                           --}}
{{-- ============================================================== --}}
@if($availableStaff->isNotEmpty())
<div id="addMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs p-4 sm:p-6">
    <div class="min-h-full flex items-center justify-center py-6">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Add Office Colleagues</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Select colleagues to join this project group.</p>
                </div>
                <button onclick="closeAddMemberModal()" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('projects.members.add', $project->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="max-h-56 overflow-y-auto space-y-1.5 border border-slate-200 rounded-2xl p-2.5 bg-slate-50/50">
                    @foreach($availableStaff as $staff)
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white transition cursor-pointer">
                            <input type="checkbox" name="member_ids[]" value="{{ $staff->user_id }}" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <div>
                                <div class="text-xs font-bold text-slate-800">{{ $staff->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $staff->designation ?? 'Staff' }} • {{ $staff->department->name ?? 'Dept' }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeAddMemberModal()" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-bold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#008069] hover:bg-[#00705b] text-white text-xs font-bold rounded-xl shadow-md transition">Add Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ============================================================== --}}
{{-- MODAL 5: EDIT PROJECT DETAILS MODAL                            --}}
{{-- ============================================================== --}}
<div id="editProjectModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs p-4 sm:p-6">
    <div class="min-h-full flex items-center justify-center py-6">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-start justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Edit Project Details</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Update project title, description, or dates.</p>
                </div>
                <button onclick="closeEditProjectModal()" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('projects.update', $project->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Project Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ $project->title }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                            <option value="planning" {{ $project->status === 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="in_progress" {{ $project->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="on_hold" {{ $project->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Priority</label>
                        <select name="priority" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                            <option value="low" {{ $project->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $project->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $project->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $project->priority === 'urgent' ? 'selected' : '' }}>🔥 Urgent</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $project->start_date ? $project->start_date->format('Y-m-d') : '' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target / Due Date</label>
                        <input type="date" name="due_date" value="{{ $project->due_date ? $project->due_date->format('Y-m-d') : '' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" rows="2.5"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 outline-none transition font-medium">{{ $project->description }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeEditProjectModal()" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-bold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#008069] hover:bg-[#00705b] text-white text-sm font-bold rounded-xl shadow-md transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Scroll chat to bottom automatically
    function scrollChatToBottom() {
        const container = document.getElementById('chatMessagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        scrollChatToBottom();
    });

    // Attachment menu toggle
    function toggleAttachmentMenu() {
        const menu = document.getElementById('attachmentMenu');
        if (menu) menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const btn = document.getElementById('btnAttachment');
        const menu = document.getElementById('attachmentMenu');
        if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    // Mobile View Toggle
    function setMobileView(view) {
        const sidebar = document.getElementById('tasksSidebar');
        const chat = document.getElementById('chatPane');
        const btnChat = document.getElementById('mobileBtnChat');
        const btnTasks = document.getElementById('mobileBtnTasks');

        if (view === 'tasks') {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('flex', 'w-full');
            chat.classList.add('hidden');
            btnTasks.className = 'px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-emerald-900 transition';
            btnChat.className = 'px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-100 transition';
        } else {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('w-full');
            chat.classList.remove('hidden');
            btnChat.className = 'px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-emerald-900 transition';
            btnTasks.className = 'px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-100 transition';
            scrollChatToBottom();
        }
    }

    // Task Search in Sidebar
    function filterSidebarTasks() {
        const query = document.getElementById('taskSearchSidebar').value.toLowerCase().trim();
        const items = document.querySelectorAll('.sidebar-task-item');

        items.forEach(item => {
            const title = item.getAttribute('data-title') || '';
            const assignee = item.getAttribute('data-assignee') || '';
            if (!query || title.includes(query) || assignee.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Filter Task Status Pills
    function filterTaskStatus(status) {
        const pills = document.querySelectorAll('.task-filter-pill');
        pills.forEach(p => {
            if (p.getAttribute('data-filter') === status) {
                p.className = 'task-filter-pill px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#008069] text-white shrink-0';
            } else {
                p.className = 'task-filter-pill px-2.5 py-1 rounded-full text-[11px] font-bold bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 shrink-0';
            }
        });

        const items = document.querySelectorAll('.sidebar-task-item');
        items.forEach(item => {
            const itemStatus = item.getAttribute('data-status') || '';
            const isMine = item.getAttribute('data-mine') === '1';

            if (status === 'all') {
                item.style.display = '';
            } else if (status === 'my_tasks') {
                item.style.display = isMine ? '' : 'none';
            } else if (status === 'pending') {
                item.style.display = (itemStatus === 'pending' || itemStatus === 'in_progress') ? '' : 'none';
            } else if (status === 'completed') {
                item.style.display = itemStatus === 'completed' ? '' : 'none';
            }
        });
    }

    // Modals
    function openAddTaskModal() {
        document.getElementById('addTaskModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddTaskModal() {
        document.getElementById('addTaskModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openEditTaskModal(task) {
        document.getElementById('editTaskModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('editTaskForm').action = `/project-tasks/${task.id}`;
        document.getElementById('editTitle').value = task.title || '';
        document.getElementById('editAssignedTo').value = task.assigned_to || '';
        document.getElementById('editPriority').value = task.priority || 'medium';
        document.getElementById('editStatus').value = task.status || 'pending';
        document.getElementById('editDescription').value = task.description || '';
        if (task.due_date) {
            document.getElementById('editDueDate').value = typeof task.due_date === 'string' ? task.due_date.substring(0, 10) : '';
        } else {
            document.getElementById('editDueDate').value = '';
        }
    }

    function closeEditTaskModal() {
        document.getElementById('editTaskModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openProjectInfoModal() {
        document.getElementById('projectInfoModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeProjectInfoModal() {
        document.getElementById('projectInfoModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openAddMemberModal() {
        const modal = document.getElementById('addMemberModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAddMemberModal() {
        const modal = document.getElementById('addMemberModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function openEditProjectModal() {
        document.getElementById('editProjectModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditProjectModal() {
        document.getElementById('editProjectModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Escape listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddTaskModal();
            closeEditTaskModal();
            closeProjectInfoModal();
            closeAddMemberModal();
            closeEditProjectModal();
        }
    });

    // Backdrop click handlers
    ['addTaskModal', 'editTaskModal', 'projectInfoModal', 'addMemberModal', 'editProjectModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', function(e) {
                if (e.target === this || e.target === this.firstElementChild) {
                    this.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        }
    });
</script>
@endpush
@endsection
