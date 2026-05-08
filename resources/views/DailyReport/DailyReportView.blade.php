@extends('layouts.app')
@section('title', 'Daily Reports')

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
        <a href="{{ route('daily-report.create') }}" class="btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Submit New Report
        </a>
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
    <form action="{{ route('daily-report.export') }}" method="GET" id="export-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        @if(Auth::user()->role !== 'staff')
        <div class="space-y-2">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Staff Member</label>
            <div class="relative">
                <select name="staff_id" id="staff-filter" onchange="applyFilters()" class="form-input-modern appearance-none pr-10">
                    <option value="">All Personnel</option>
                    @foreach($allStaff as $s)
                        <option value="{{ $s->id }}" data-name="{{ strtolower($s->name) }}">{{ $s->name }}</option>
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
            <input type="date" name="start_date" id="start-date" onchange="applyFilters()" class="form-input-modern">
        </div>

        <div class="space-y-2">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">End Date</label>
            <input type="date" name="end_date" id="end-date" onchange="applyFilters()" class="form-input-modern">
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
                    <button type="submit" name="type" value="excel" class="w-full px-5 py-4 text-left text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 flex items-center gap-3 transition">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        Excel Sheet
                    </button>
                    <button type="submit" name="type" value="pdf" class="w-full px-5 py-4 text-left text-sm text-slate-700 hover:bg-rose-50 hover:text-rose-700 flex items-center gap-3 transition border-t border-slate-50">
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

{{-- Table Section --}}
<div class="card-premium !p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left" id="reports-table">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest w-12">#</th>
                    @if(Auth::user()->role !== 'staff')
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Personnel</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest">User Role</th>
                    @endif
                    <th class="px-6 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Report Date</th>
                    <th class="px-6 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Tasks Summary</th>
                    <th class="px-6 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Hours Logged</th>
                    <th class="px-6 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Pending Work</th>
                    <th class="px-6 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="reports-tbody" class="divide-y divide-slate-100">
                @forelse($reports as $report)
                @php
                    $taskCount  = $report->tasks->count();
                    $doneCount  = $report->tasks->where('status','completed')->count();
                    $pct        = $taskCount > 0 ? round(($doneCount / $taskCount) * 100) : 0;
                @endphp
                <tr class="hover:bg-gray-50/80 transition-colors report-row group"
                    data-staff-id="{{ $report->staff_id }}"
                    data-name="{{ strtolower($report->staff->name ?? '') }}"
                    data-date="{{ $report->report_date->format('Y-m-d') }}">

                    <td class="px-5 py-4">
                        <span class="text-xs font-mono text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md">
                            #{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>

                    @if(Auth::user()->role !== 'staff')
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-2xl gradient-bg flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-indigo-100 flex-shrink-0">
                                {{ strtoupper(substr($report->staff->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 leading-tight">{{ $report->staff->name ?? '—' }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5 tracking-wide">{{ $report->staff->designation ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php $role = $report->staff->role ?? 'staff'; @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $role === 'admin' ? 'bg-purple-50 text-purple-600' :
                               ($role === 'manager' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-600') }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $role === 'admin' ? 'bg-purple-500' : ($role === 'manager' ? 'bg-indigo-500' : 'bg-slate-400') }}"></span>
                            {{ $role }}
                        </span>
                    </td>
                    @endif

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1.5 text-slate-700 font-medium">
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $report->report_date->format('d M, Y') }}
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-2 min-w-[120px]">
                            <div class="flex items-center justify-between text-[11px] font-bold">
                                <span class="text-slate-400 uppercase tracking-widest">{{ $doneCount }}/{{ $taskCount }} Tasks</span>
                                <span class="{{ $pct === 100 ? 'text-emerald-500' : 'text-indigo-500' }}">{{ $pct }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $pct === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }} rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        @php
                            $totalMinutes = 0;
                            foreach($report->tasks as $task) {
                                $ts = strtolower($task->time_spend);
                                if (preg_match('/(\d+)\s*h/', $ts, $m)) $totalMinutes += $m[1] * 60;
                                if (preg_match('/(\d+)\s*m/', $ts, $m)) $totalMinutes += $m[1];
                                if (preg_match('/(\d+):(\d+)/', $ts, $m)) $totalMinutes += $m[1] * 60 + $m[2];
                            }
                            $h = floor($totalMinutes / 60);
                            $m = $totalMinutes % 60;
                            $timeStr = ($h > 0 ? $h.'h ' : '') . ($m > 0 ? $m.'m' : '');
                        @endphp
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-50 text-slate-700 font-bold text-xs">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $timeStr ?: '—' }}
                        </div>
                    </td>

                    <td class="px-5 py-4 max-w-[220px]">
                        @if($report->pending_task)
                            <p class="text-xs text-gray-600 leading-relaxed line-clamp-2">{{ $report->pending_task }}</p>
                        @else
                            <span class="text-xs text-gray-300 italic">No pending work</span>
                        @endif
                    </td>

                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-1.5">
                            <button data-id="{{ $report->id }}" onclick="viewDetail(this.dataset.id)"
                                    title="View Details"
                                    class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ Auth::user()->role === 'staff' ? '5' : '7' }}"
                        class="px-5 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700">No reports found</p>
                                <p class="text-xs text-gray-400 mt-1">No daily reports have been submitted yet.</p>
                            </div>
                            <a href="{{ route('daily-report.create') }}"
                               class="inline-flex items-center gap-1.5 mt-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Submit First Report
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->count() > 0)
    <div class="px-5 py-3 border-t border-gray-50 bg-gray-50/50 flex items-center justify-between">
        <p class="text-xs text-gray-400">
            Total <span class="font-semibold text-gray-600" id="visible-count">{{ $reports->count() }}</span> reports
        </p>
        <p class="text-xs text-gray-400">{{ now()->format('d M Y, H:i') }} data</p>
    </div>
    @endif
</div>

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
                </div>
                <div class="space-y-3 mb-5">
                    <div class="rounded-xl border border-gray-100 p-4">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Pending Task</p>
                        <p class="text-sm text-gray-700 leading-relaxed">${esc(d.pending_task || '—')}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-4">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Planned Task</p>
                        <p class="text-sm text-gray-700 leading-relaxed">${esc(d.planned_task || '—')}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 p-4">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Comments</p>
                        <p class="text-sm text-gray-700 leading-relaxed">${esc(d.comments || '—')}</p>
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

            const tasksHtml = d.tasks && d.tasks.length > 0
                ? `<div class="flex items-center justify-between mb-3 bg-indigo-50 rounded-lg px-3 py-2 border border-indigo-100">
                       <div>
                           <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wider">Tasks (${d.tasks.length})</p>
                           <p class="text-[10px] text-indigo-500 font-medium">${d.tasks.filter(t=>t.status==='completed').length} completed</p>
                       </div>
                       <div class="text-right">
                           <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wider">Total Time</p>
                           <p class="text-sm font-bold text-indigo-800">${totalStr}</p>
                       </div>
                   </div>
                   <div class="space-y-2.5">
                   ${d.tasks.map((t, i) => `
                    <div class="flex items-start gap-3 border border-gray-100 rounded-xl p-3.5 bg-gray-50 hover:bg-white transition">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">${i+1}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-semibold text-gray-800 text-sm leading-tight">${esc(t.task_title)}</p>
                                <span class="flex-shrink-0 px-2 py-0.5 rounded-lg text-xs font-semibold ${statusBadge[t.status]||''}">${statusLabel[t.status]||t.status}</span>
                            </div>
                            ${t.description ? `<p class="text-xs text-gray-500 mt-1 leading-relaxed">${esc(t.description)}</p>` : ''}
                            ${t.time_spend  ? `<div class="flex items-center gap-1 mt-1.5"><svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="text-xs text-gray-400">${esc(t.time_spend)}</span></div>` : ''}
                        </div>
                    </div>`).join('')}
                   </div>`
                : `<div class="text-center py-8 text-gray-400">
                       <p class="text-xs">No tasks added</p>
                   </div>`;

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
        const staffId = document.getElementById('staff-filter')?.value;
        const start   = document.getElementById('start-date').value;
        const end     = document.getElementById('end-date').value;
        const rows    = document.querySelectorAll('.report-row');
        let visible   = 0;

        rows.forEach(row => {
            const sidMatch = !staffId || row.dataset.staffId === staffId;
            const rDate    = row.dataset.date;
            
            let dateMatch = true;
            if (start && rDate < start) dateMatch = false;
            if (end && rDate > end) dateMatch = false;

            if (sidMatch && dateMatch) { 
                row.style.display = ''; 
                visible++; 
            } else { 
                row.style.display = 'none'; 
            }
        });

        const noRes = document.getElementById('no-results');
        const cnt   = document.getElementById('visible-count');
        if (noRes) noRes.classList.toggle('hidden', visible > 0);
        if (cnt) cnt.textContent = visible;
        
        const rc = document.getElementById('result-count');
        if (rc) rc.textContent = visible + ' report' + (visible !== 1 ? 's' : '');
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
        const sf = document.getElementById('staff-filter');
        if (sf) sf.value = '';
        document.getElementById('start-date').value  = '';
        document.getElementById('end-date').value  = '';
        applyFilters();
    }
</script>
@endpush
