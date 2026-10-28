@extends('layouts.app')
@section('title', 'Office Projects & Team Tasks')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Modern Hero Banner --}}
    <div class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 p-6 md:p-8 rounded-3xl text-white shadow-xl relative overflow-hidden border border-indigo-700/30">
        <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-0 right-1/4 w-40 h-40 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-semibold text-indigo-200 backdrop-blur-md border border-white/10">
                    <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    Office Team Workspaces
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Office Projects & Collaboration</h1>
                <p class="text-indigo-200/90 text-sm leading-relaxed">
                    Collaborate with colleagues in your office, delegate tasks, track deadlines, and monitor project milestones in real time.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <button onclick="openCreateProjectModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold rounded-2xl shadow-lg shadow-indigo-600/30 transition transform active:scale-95 border border-indigo-400/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    + Create Project
                </button>
            </div>
        </div>
    </div>

    {{-- Interactive Metric Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Total --}}
        <a href="{{ route('projects.index', ['filter' => 'all']) }}"
           class="bg-white p-5 rounded-2xl border transition-all duration-200 hover:shadow-md hover:border-indigo-200 flex items-center gap-4 group {{ $filter === 'all' ? 'border-indigo-400 ring-2 ring-indigo-50' : 'border-slate-100' }}">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Projects</p>
                <p class="text-2xl font-black text-slate-800">{{ $stats['total'] }}</p>
            </div>
        </a>

        {{-- Created By Me --}}
        <a href="{{ route('projects.index', ['filter' => 'my_projects']) }}"
           class="bg-white p-5 rounded-2xl border transition-all duration-200 hover:shadow-md hover:border-blue-200 flex items-center gap-4 group {{ $filter === 'my_projects' ? 'border-blue-400 ring-2 ring-blue-50' : 'border-slate-100' }}">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">My Projects</p>
                <p class="text-2xl font-black text-slate-800">{{ $stats['my_created'] }}</p>
            </div>
        </a>

        {{-- Shared With Me --}}
        <a href="{{ route('projects.index', ['filter' => 'shared_with_me']) }}"
           class="bg-white p-5 rounded-2xl border transition-all duration-200 hover:shadow-md hover:border-amber-200 flex items-center gap-4 group {{ $filter === 'shared_with_me' ? 'border-amber-400 ring-2 ring-amber-50' : 'border-slate-100' }}">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-600 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Shared With Me</p>
                <p class="text-2xl font-black text-slate-800">{{ $stats['shared'] }}</p>
            </div>
        </a>

        {{-- In Progress --}}
        <a href="{{ route('projects.index', ['filter' => 'in_progress']) }}"
           class="bg-white p-5 rounded-2xl border transition-all duration-200 hover:shadow-md hover:border-emerald-200 flex items-center gap-4 group {{ $filter === 'in_progress' ? 'border-emerald-400 ring-2 ring-emerald-50' : 'border-slate-100' }}">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">In Progress</p>
                <p class="text-2xl font-black text-emerald-600">{{ $stats['in_progress'] }}</p>
            </div>
        </a>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        {{-- Filter Pills --}}
        <div class="flex flex-wrap items-center gap-1.5 overflow-x-auto">
            <a href="{{ route('projects.index', ['filter' => 'all']) }}"
                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 {{ $filter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                All Projects
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'all' ? 'bg-indigo-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('projects.index', ['filter' => 'my_projects']) }}"
                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 {{ $filter === 'my_projects' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Created by Me
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'my_projects' ? 'bg-indigo-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stats['my_created'] }}</span>
            </a>
            <a href="{{ route('projects.index', ['filter' => 'shared_with_me']) }}"
                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 {{ $filter === 'shared_with_me' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Shared with Me
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'shared_with_me' ? 'bg-indigo-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stats['shared'] }}</span>
            </a>
            <a href="{{ route('projects.index', ['filter' => 'in_progress']) }}"
                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 {{ $filter === 'in_progress' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Active
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'in_progress' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stats['in_progress'] }}</span>
            </a>
            <a href="{{ route('projects.index', ['filter' => 'completed']) }}"
                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 {{ $filter === 'completed' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Completed
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'completed' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $stats['completed'] }}</span>
            </a>
        </div>

        {{-- Live Search Box --}}
        <div class="relative w-full md:w-72 shrink-0">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" id="projectSearchInput" oninput="filterProjectCards()" placeholder="Search project name..."
                class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
        </div>
    </div>

    {{-- Projects Grid --}}
    @if($projects->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm max-w-lg mx-auto">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-3xl mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No projects here yet</h3>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Start a collaborative project and delegate work seamlessly to your office staff.
            </p>
            <button onclick="openCreateProjectModal()" class="mt-5 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition transform active:scale-95">
                + Create First Project
            </button>
        </div>
    @else
        <div id="projectsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($projects as $project)
                @php
                    $progress = $project->progressPercentage();
                    $isOwner = $project->created_by == Auth::id();
                    $totalTasks = $project->tasks->count();
                    $completedTasks = $project->tasks->where('status', 'completed')->count();
                    $pendingTasks = $totalTasks - $completedTasks;
                    
                    $statusConfig = [
                        'planning'    => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500', 'border' => 'border-blue-200', 'label' => 'Planning'],
                        'in_progress' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'border' => 'border-emerald-200', 'label' => 'In Progress'],
                        'on_hold'     => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'border' => 'border-amber-200', 'label' => 'On Hold'],
                        'completed'   => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'dot' => 'bg-slate-400', 'border' => 'border-slate-200', 'label' => 'Completed'],
                    ];
                    $sc = $statusConfig[$project->status] ?? $statusConfig['in_progress'];

                    $priorityConfig = [
                        'urgent' => 'bg-rose-50 text-rose-700 border-rose-200',
                        'high'   => 'bg-amber-50 text-amber-700 border-amber-200',
                        'medium' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'low'    => 'bg-slate-50 text-slate-600 border-slate-200',
                    ];
                @endphp

                <div class="project-card bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-xl hover:border-indigo-100 transition-all duration-300 flex flex-col justify-between group"
                     data-title="{{ strtolower($project->title) }}"
                     data-desc="{{ strtolower($project->description ?? '') }}">

                    <div class="space-y-4">
                        {{-- Badges Row --}}
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['border'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                                {{ $sc['label'] }}
                            </span>

                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $priorityConfig[$project->priority] ?? 'bg-slate-50 text-slate-600' }}">
                                    {{ $project->priority }}
                                </span>

                                @if($isOwner)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        Owner
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Project Title & Description --}}
                        <div>
                            <a href="{{ route('projects.show', $project->id) }}"
                               class="text-base sm:text-lg font-black text-slate-800 hover:text-indigo-600 transition block line-clamp-1 group-hover:text-indigo-600">
                                {{ $project->title }}
                            </a>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2 min-h-[32px] leading-relaxed">
                                {{ $project->description ?: 'No detailed summary provided for this project.' }}
                            </p>
                        </div>

                        {{-- Progress Section --}}
                        <div class="bg-slate-50/70 p-3.5 rounded-2xl border border-slate-100/80 space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-slate-600">Task Completion</span>
                                <span class="font-extrabold text-slate-800">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $progress == 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-indigo-500 to-indigo-600' }}" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] text-slate-400 font-medium pt-0.5">
                                <span>{{ $completedTasks }} done</span>
                                <span>{{ $totalTasks }} total</span>
                            </div>
                        </div>

                        {{-- Metadata (Office & Due Date) --}}
                        <div class="flex flex-wrap items-center justify-between text-[11px] text-slate-500 gap-2 pt-1">
                            @if($project->office)
                                <div class="flex items-center gap-1 font-semibold text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    <span class="truncate max-w-[130px]">{{ $project->office->name }}</span>
                                </div>
                            @endif

                            @if($project->due_date)
                                <div class="flex items-center gap-1 {{ $project->due_date->isPast() && $project->status !== 'completed' ? 'text-rose-600 font-bold' : 'text-slate-500 font-medium' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span>{{ $project->due_date->format('d M, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        {{-- Members Avatars Stack --}}
                        <div class="flex items-center -space-x-2 overflow-hidden">
                            @foreach($project->members->take(3) as $m)
                                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center border-2 border-white shadow-xs" title="{{ $m->name }}">
                                    {{ substr($m->name, 0, 1) }}
                                </div>
                            @endforeach
                            @if($project->members->count() > 3)
                                <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px] flex items-center justify-center border-2 border-white">
                                    +{{ $project->members->count() - 3 }}
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('projects.show', $project->id) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white rounded-xl text-xs font-bold transition duration-200">
                            <span>Open Project</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- No results state for search --}}
        <div id="noSearchMatches" class="hidden bg-white rounded-3xl p-10 text-center border border-slate-100">
            <p class="text-sm font-bold text-slate-700">No matching projects found</p>
            <p class="text-xs text-slate-400 mt-1">Try searching with a different term.</p>
        </div>
    @endif

</div>

{{-- Create Project Modal --}}
<div id="createProjectModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-4 sm:p-6">
    <div class="min-h-full flex items-center justify-center py-6">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-start justify-between gap-3 mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-black text-slate-800">Create New Project</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Start a collaborative project and assign tasks to office colleagues.</p>
                </div>
                <button onclick="closeCreateProjectModal()" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Project Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g., Annual Account Audit, Cloud Server Migration..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description / Project Goal</label>
                    <textarea name="description" rows="2.5" placeholder="Outline the main targets, responsibilities and key deliverables..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    {{-- Priority --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Priority</label>
                        <select name="priority" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    {{-- Start Date --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target / Due Date</label>
                        <input type="date" name="due_date" min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-medium">
                    </div>
                </div>

                {{-- Collaborate with Office Staff with Quick Search --}}
                <div class="pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Share With Office Colleagues
                            </label>
                            <p class="text-[11px] text-slate-400">Select colleagues from your office to collaborate on this project:</p>
                        </div>
                        @if($colleagues->isNotEmpty())
                            <button type="button" onclick="toggleSelectAllStaff()" id="selectAllStaffBtn" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">
                                Select All
                            </button>
                        @endif
                    </div>

                    @if($colleagues->isEmpty())
                        <div class="p-4 bg-slate-50 rounded-2xl text-center text-xs text-slate-500 border border-slate-100">
                            No other active staff members found in your office. You can create the project and invite members later.
                        </div>
                    @else
                        {{-- Search inside staff list if more than 5 --}}
                        @if($colleagues->count() > 5)
                            <div class="mb-2">
                                <input type="text" id="staffFilterInput" oninput="filterStaffList()" placeholder="Filter colleague by name..."
                                    class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:border-indigo-500 outline-none">
                            </div>
                        @endif

                        <div class="max-h-48 overflow-y-auto space-y-1.5 border border-slate-200 rounded-2xl p-2.5 bg-slate-50/50" id="staffListContainer">
                            @foreach($colleagues as $staff)
                                <label class="staff-row flex items-center justify-between p-2.5 rounded-xl hover:bg-white transition cursor-pointer border border-transparent hover:border-slate-200"
                                       data-name="{{ strtolower($staff->name) }}">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="member_ids[]" value="{{ $staff->user_id }}" class="colleague-checkbox w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">{{ $staff->name }}</div>
                                            <div class="text-[10px] text-slate-400">
                                                {{ $staff->designation ?? 'Staff' }} • {{ $staff->department->name ?? 'Dept' }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded font-semibold">
                                        {{ $staff->office->name ?? 'Office' }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeCreateProjectModal()" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md transition">
                        Create & Share Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openCreateProjectModal() {
        document.getElementById('createProjectModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateProjectModal() {
        document.getElementById('createProjectModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    let allSelected = false;
    function toggleSelectAllStaff() {
        const checkboxes = document.querySelectorAll('.colleague-checkbox');
        const btn = document.getElementById('selectAllStaffBtn');
        allSelected = !allSelected;
        checkboxes.forEach(cb => cb.checked = allSelected);
        btn.textContent = allSelected ? 'Deselect All' : 'Select All';
    }

    function filterStaffList() {
        const q = (document.getElementById('staffFilterInput')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.staff-row');
        rows.forEach(r => {
            const name = r.getAttribute('data-name') || '';
            r.style.display = name.includes(q) ? 'flex' : 'none';
        });
    }

    function filterProjectCards() {
        const query = (document.getElementById('projectSearchInput')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.project-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const desc = card.getAttribute('data-desc') || '';
            if (title.includes(query) || desc.includes(query)) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noMatch = document.getElementById('noSearchMatches');
        if (noMatch) {
            noMatch.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
        }
    }

    // Escape listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateProjectModal();
        }
    });

    document.getElementById('createProjectModal')?.addEventListener('click', function(e) {
        if (e.target === this || e.target === this.firstElementChild) {
            closeCreateProjectModal();
        }
    });
</script>
@endpush
@endsection
