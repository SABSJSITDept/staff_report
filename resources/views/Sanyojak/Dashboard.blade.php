@extends('layouts.app')
@section('title', 'Sanyojak Dashboard')

@section('content')

{{-- ===================== DETAIL MODAL ===================== --}}
<div id="detail-modal" class="fixed inset-0 z-[100] hidden items-center justify-center">
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

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="{{ route('sanyojak.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600 font-medium">Assigned Staff Reports</span>
</nav>

<div class="card-premium !p-0 overflow-hidden mb-8">
    <div class="gradient-bg px-8 py-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Assigned Staff Reports</h1>
                <p class="text-indigo-100/70 text-[10px] font-bold uppercase tracking-[0.2em] mt-0.5">Sanyojak View - {{ $sanyojak->pravarti ?? 'General' }}</p>
            </div>
        </div>
        <form method="GET" action="{{ route('sanyojak.dashboard') }}" class="flex items-center gap-3">
            <input type="date" name="date" value="{{ $date }}" class="form-input-modern border-white/20 bg-white/10 text-white placeholder-indigo-200 rounded-xl px-4 py-2.5 text-sm" onchange="this.form.submit()">
            <button type="submit" class="btn-primary !bg-white !text-indigo-600 px-6 py-2.5">
                Filter
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if(count($reports) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase">Staff Member</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase">Office / Dept</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase">Total Tasks</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase">Completed</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($reports as $report)
                        @php
                            $totalTasks = $report->tasks->count();
                            $completedTasks = $report->tasks->where('status', 'Completed')->count();
                            $staffName = $report->staff->name ?? 'Unknown';
                            $staffDetails = $report->staff->staff;
                            $officeName = $staffDetails && $staffDetails->office ? $staffDetails->office->name : 'N/A';
                            $deptName = $staffDetails && $staffDetails->department ? $staffDetails->department->name : 'N/A';
                        @endphp
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-5 py-4 font-semibold text-gray-800">{{ $staffName }}</td>
                            <td class="px-5 py-4 text-gray-600">
                                <span class="block text-xs font-medium text-gray-800">{{ $officeName }}</span>
                                <span class="block text-[10px] text-gray-400 uppercase tracking-wide mt-0.5">{{ $deptName }}</span>
                            </td>
                            <td class="px-5 py-4 text-gray-600">
                                <span class="bg-blue-50 text-blue-700 font-bold px-2.5 py-1 rounded-md text-xs">{{ $totalTasks }} Tasks</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($totalTasks > 0 && $completedTasks === $totalTasks)
                                    <span class="bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-md text-xs">{{ $completedTasks }} Completed</span>
                                @elseif($completedTasks > 0)
                                    <span class="bg-amber-50 text-amber-700 font-bold px-2.5 py-1 rounded-md text-xs">{{ $completedTasks }} / {{ $totalTasks }} Completed</span>
                                @else
                                    <span class="bg-gray-100 text-gray-500 font-bold px-2.5 py-1 rounded-md text-xs">0 Completed</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button type="button" data-id="{{ $report->id }}" onclick="viewDetail(this.dataset.id)" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg text-xs font-bold transition">
                                    View Report
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-gray-800 font-bold text-lg mb-1">No Reports Found</h3>
            <p class="text-gray-500 text-sm max-w-sm">No daily reports have been submitted by your assigned staff for the selected date.</p>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
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
</script>
@endpush
