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
                    <div class="flex items-center justify-between pt-2">
                        <div class="text-sm font-semibold text-green-700 flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            Started at: {{ \Carbon\Carbon::parse($activeTask->start_time)->format('h:i A') }}
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="logActiveTaskUpdate({{ $activeTask->id }})" id="log-task-btn"
                                    class="flex items-center gap-2 px-5 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Log Update
                            </button>
                            <button type="button" onclick="pauseActiveTask({{ $activeTask->id }})" id="pause-task-btn"
                                    class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                </svg>
                                Pause Task
                            </button>
                            <button type="submit" id="end-task-btn"
                                    class="flex items-center gap-2 px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                                </svg>
                                End Task
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
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Completed
                                        </span>
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
                                            <button type="button" onclick="endPausedTaskDirect({{ $t->id }})"
                                                    class="inline-flex items-center justify-center p-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg border border-red-200 transition shadow-xs"
                                                    title="End Task">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9A2.25 2.25 0 015.25 16.5v-9z" />
                                                </svg>
                                            </button>
                                        </div>
                                    @elseif($t->status === 'in_progress')
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" onclick="pauseActiveTask({{ $t->id }})"
                                                    class="inline-flex items-center justify-center p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg border border-amber-200 transition shadow-xs"
                                                    title="Pause Task">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                                </svg>
                                            </button>
                                            <button type="button" onclick="endActiveTaskDirect({{ $t->id }})"
                                                    class="inline-flex items-center justify-center p-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg border border-red-200 transition shadow-xs"
                                                    title="End Task">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9A2.25 2.25 0 015.25 16.5v-9z" />
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 font-semibold">—</span>
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
                if(data.success) location.reload();
                else { showToast(data.message, 'error'); btn.disabled = false; btn.innerHTML = 'Start Task'; }
            }).catch(err => {
                showToast('An error occurred.', 'error'); btn.disabled = false; btn.innerHTML = 'Start Task';
            });
        });
    }

    const endForm = document.getElementById('end-task-form');
    if (endForm) {
        endForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('end-task-btn');
            const taskId = document.getElementById('active_task_id').value;
            btn.disabled = true;
            btn.innerHTML = 'Ending...';

            fetch('{{ url("daily-report/task") }}/' + taskId + '/end', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    description: document.getElementById('end_task_desc').value,
                })
            }).then(res => res.json()).then(data => {
                if(data.success) location.reload();
                else { showToast(data.message, 'error'); btn.disabled = false; btn.innerHTML = 'End Task'; }
            }).catch(err => {
                showToast('An error occurred.', 'error'); btn.disabled = false; btn.innerHTML = 'End Task';
            });
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
                if(data.success) location.reload();
                else { showToast(data.message, 'error'); btn.disabled = false; btn.innerHTML = 'Add to Other Task'; }
            }).catch(err => {
                showToast('An error occurred.', 'error'); btn.disabled = false; btn.innerHTML = 'Add to Other Task';
            });
        });
    }

    // Pause Active Task
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

    // Pause Active Task
    function pauseActiveTask(taskId) {
        showConfirmToast('Do you really want to pause this task?', function() {
            const btn = document.getElementById('pause-task-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Pausing...';
            }

            // Get description from end_task_desc if active task widget exists
            const descInput = document.getElementById('end_task_desc');
            const descValue = descInput ? descInput.value : '';

            fetch('{{ url("daily-report/task") }}/' + taskId + '/pause', {
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
                if(data.success) location.reload();
                else { showToast(data.message, 'error'); if (btn) { btn.disabled = false; btn.innerHTML = 'Pause Task'; } }
            }).catch(err => {
                showToast('An error occurred while pausing.', 'error'); if (btn) { btn.disabled = false; btn.innerHTML = 'Pause Task'; }
            });
        });
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
            if(data.success) location.reload();
            else { showToast(data.message, 'error'); }
        }).catch(err => {
            showToast('An error occurred while resuming.', 'error');
        });
    }

    // End Paused Task
    function endPausedTaskDirect(taskId) {
        const desc = prompt('Enter final task description (optional):');
        if (desc === null) return; // User cancelled prompt

        fetch('{{ url("daily-report/task") }}/' + taskId + '/end', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                description: desc
            })
        }).then(res => res.json()).then(data => {
            if(data.success) location.reload();
            else { showToast(data.message, 'error'); }
        }).catch(err => {
            showToast('An error occurred while ending the task.', 'error');
        });
    }

    // End Active Task Directly from Row
    function endActiveTaskDirect(taskId) {
        const desc = prompt('Enter final task description (optional):');
        if (desc === null) return; // User cancelled prompt

        fetch('{{ url("daily-report/task") }}/' + taskId + '/end', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                description: desc
            })
        }).then(res => res.json()).then(data => {
            if(data.success) location.reload();
            else { showToast(data.message, 'error'); }
        }).catch(err => {
            showToast('An error occurred while ending the task.', 'error');
        });
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
</script>
@endpush
