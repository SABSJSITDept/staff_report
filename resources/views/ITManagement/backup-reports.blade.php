@extends('layouts.app')

@section('title', 'Backup Reports')

@section('content')
<div class="max-w-[100rem] mx-auto px-4 py-8 animate-fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-4">
                <span class="w-1.5 h-8 bg-indigo-600 rounded-full"></span>
                IT Backup Reports (Monthly Matrix)
            </h2>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-5">
                Staff wise monthly backup attendance matrix (YES / NO / NO-FILL).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button type="button" onclick="exportReport('pdf')" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold py-2.5 px-5 rounded-xl border border-red-200 transition-all flex items-center gap-2 text-sm shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export PDF
            </button>
            <button type="button" onclick="exportReport('excel')" class="bg-green-50 hover:bg-green-100 text-green-600 font-bold py-2.5 px-5 rounded-xl border border-green-200 transition-all flex items-center gap-2 text-sm shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Filters Form -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="bg-slate-50 px-8 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                Report Filters
            </h3>
            <div class="flex items-center gap-4 text-xs font-bold text-slate-500">
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> YES = Backup Taken</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-rose-500 inline-block"></span> NO = Backup Failed</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-400 inline-block"></span> NO-FILL = Missing / Not Entered</span>
            </div>
        </div>
        
        <form method="GET" action="{{ route('it-management.backup-reports.index') }}" class="p-6 md:p-8" id="filterForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <!-- Month Filter -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Month</label>
                    <select name="month" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 font-bold text-slate-700 bg-slate-50/50">
                        @for($i = 1; $i <= 12; $i++)
                            @php $mVal = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $mVal }}" {{ (string)$month === $mVal ? 'selected' : '' }}>
                                {{ date("F", mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Year Filter -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Year</label>
                    <select name="year" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 font-bold text-slate-700 bg-slate-50/50">
                        @for($y = (int)date('Y'); $y >= (int)date('Y') - 4; $y--)
                            <option value="{{ $y }}" {{ (int)$year === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Office Filter (Office Wise) -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Office Wise</label>
                    <select name="office_id" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 font-bold text-slate-700 bg-slate-50/50">
                        <option value="">All Offices</option>
                        @foreach($offices as $off)
                            <option value="{{ $off->id }}" {{ request('office_id') == $off->id ? 'selected' : '' }}>
                                {{ $off->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Particular Staff Filter -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Particular Staff</label>
                    <select name="staff_id" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 font-bold text-slate-700 bg-slate-50/50">
                        <option value="">All Staff Members</option>
                        @foreach($allStaffList as $st)
                            <option value="{{ $st->id }}" {{ request('staff_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->name }} {{ $st->office ? '(' . $st->office->name . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Month View Scope -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Days To Show</label>
                    <select name="full_month" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 font-bold text-slate-700 bg-slate-50/50">
                        <option value="0" {{ !$showFullMonth ? 'selected' : '' }}>Up to Today (Current Progress)</option>
                        <option value="1" {{ $showFullMonth ? 'selected' : '' }}>Full Month (1 to {{ $daysInMonth }})</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-5 mt-6">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-7 rounded-xl shadow-lg shadow-indigo-600/30 transition-all text-sm active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Apply Filter
                </button>
                <a href="{{ route('it-management.backup-reports.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 px-6 rounded-xl transition-all text-sm">
                    Reset
                </a>

                <div class="ml-auto text-xs font-bold text-slate-500 bg-slate-100 px-4 py-2 rounded-xl">
                    Showing {{ count($reportData) }} Staff Members | {{ count($dates) }} Date Columns
                </div>
            </div>
        </form>
    </div>

    <!-- Matrix Table Container with 2-Way Sticky Freeze (Left, Right, Up, Down) -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="matrix-scroll-container scrollbar-thin">
            <table class="w-full text-left matrix-table text-xs">
                <thead>
                    <tr>
                        <!-- SR Column (Sticky top-0 & left-0, z-50) -->
                        <th class="sticky-sr-th py-3.5 px-3 font-black text-center uppercase tracking-wider border-b-2 border-r border-slate-300 text-slate-800">
                            SR
                        </th>
                        <!-- Staff Name Column (Sticky top-0 & left-[56px], z-50) -->
                        <th class="sticky-name-th py-3.5 px-4 font-black uppercase tracking-wider border-b-2 border-r-2 border-slate-300 text-slate-800">
                            STAFF NAME
                        </th>
                        <!-- Date Columns (Sticky top-0, z-40) -->
                        @foreach($dates as $dateObj)
                        <th class="sticky-date-th py-3 px-2 font-black text-center uppercase tracking-wider min-w-[96px] border-b-2 border-r border-slate-200 {{ $dateObj['is_sunday'] ? '!bg-red-50 text-red-700' : 'text-slate-700' }}">
                            <div class="text-[11px] font-black">{{ $dateObj['display'] }}</div>
                            <div class="text-[9px] font-bold uppercase tracking-widest {{ $dateObj['is_sunday'] ? 'text-red-500' : 'text-slate-400' }}">
                                {{ $dateObj['day_name'] }}
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $row)
                    <tr class="matrix-row transition-colors">
                        <!-- SR Cell (Sticky left-0, z-30) -->
                        <td class="sticky-sr-td py-3 px-3 text-center font-bold text-slate-500 border-b border-r border-slate-200">
                            {{ $row['sr'] }}
                        </td>
                        <!-- Staff Name Cell (Sticky left-[56px], z-30) -->
                        <td class="sticky-name-td py-3 px-4 font-bold text-slate-800 border-b border-r-2 border-slate-300">
                            <div class="font-extrabold text-slate-900 text-xs truncate max-w-[210px]" title="{{ $row['staff']->name }}">
                                {{ $row['staff']->name }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-medium flex items-center gap-1.5 mt-0.5">
                                @if($row['staff']->office)
                                    <span class="inline-block px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-semibold text-[9px]">
                                        {{ $row['staff']->office->name }}
                                    </span>
                                @endif
                                @if($row['staff']->department)
                                    <span class="text-slate-400 text-[9px]">{{ $row['staff']->department->name }}</span>
                                @endif
                            </div>
                        </td>
                        <!-- Date Cells -->
                        @foreach($dates as $dateObj)
                            @php
                                $dStr = $dateObj['date_str'];
                                $val = $row['days'][$dStr] ?? '-';
                            @endphp
                            <td class="py-2.5 px-2 text-center border-b border-r border-slate-100 {{ $dateObj['is_sunday'] ? 'bg-red-50/25' : '' }}">
                                @if($val === 'YES')
                                    <span class="inline-block px-2.5 py-1 text-[11px] font-black rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm" title="Backup taken on {{ $dateObj['display'] }}">
                                        YES
                                    </span>
                                @elseif($val === 'NO')
                                    <span class="inline-block px-2.5 py-1 text-[11px] font-black rounded-lg bg-rose-100 text-rose-800 border border-rose-200 shadow-sm" title="Backup failed / entered NO on {{ $dateObj['display'] }}">
                                        NO
                                    </span>
                                @elseif($val === 'NO-FILL')
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-amber-50 text-amber-700 border border-amber-200" title="Missing / No entry on {{ $dateObj['display'] }}">
                                        NO-FILL
                                    </span>
                                @else
                                    <span class="text-slate-300 font-bold text-sm">-</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($dates) + 2 }}" class="py-16 text-center text-slate-500 bg-white">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <h4 class="font-bold text-base text-slate-700">No Backup Records Found</h4>
                            <p class="text-xs text-slate-400 mt-1">Try selecting a different month, year, or office filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* 2-Way Freeze / Sticky Matrix CSS */
    .matrix-scroll-container {
        overflow: auto;
        max-height: calc(100vh - 210px);
        min-height: 460px;
        position: relative;
    }

    .matrix-table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }

    /* Fixed Top-Left Corner 1: SR Header */
    .sticky-sr-th {
        position: sticky !important;
        top: 0 !important;
        left: 0 !important;
        z-index: 50 !important;
        width: 56px !important;
        min-width: 56px !important;
        max-width: 56px !important;
        background-color: #f1f5f9 !important; /* solid slate-100 */
        box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.05);
    }

    /* Fixed Top-Left Corner 2: STAFF NAME Header */
    .sticky-name-th {
        position: sticky !important;
        top: 0 !important;
        left: 56px !important;
        z-index: 50 !important;
        width: 220px !important;
        min-width: 220px !important;
        max-width: 220px !important;
        background-color: #f1f5f9 !important; /* solid slate-100 */
        box-shadow: 4px 2px 8px -2px rgba(0, 0, 0, 0.12);
    }

    /* Fixed Top: Date Headers */
    .sticky-date-th {
        position: sticky !important;
        top: 0 !important;
        z-index: 40 !important;
        background-color: #f1f5f9 !important; /* solid slate-100 */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    /* Fixed Left: SR Body Cells */
    .sticky-sr-td {
        position: sticky !important;
        left: 0 !important;
        z-index: 30 !important;
        width: 56px !important;
        min-width: 56px !important;
        max-width: 56px !important;
        background-color: #ffffff !important;
        box-shadow: 1px 0 3px rgba(0, 0, 0, 0.04);
    }

    /* Fixed Left: STAFF NAME Body Cells */
    .sticky-name-td {
        position: sticky !important;
        left: 56px !important;
        z-index: 30 !important;
        width: 220px !important;
        min-width: 220px !important;
        max-width: 220px !important;
        background-color: #ffffff !important;
        box-shadow: 4px 0 8px -2px rgba(0, 0, 0, 0.12);
    }

    /* Row Hover Effect without breaking sticky cell backgrounds */
    .matrix-row:hover td {
        background-color: #f8faff !important;
    }
    .matrix-row:hover .sticky-sr-td,
    .matrix-row:hover .sticky-name-td {
        background-color: #f1f5f9 !important;
    }
</style>

<script>
    function exportReport(type) {
        const form = document.getElementById('filterForm');
        const originalAction = form.action;

        if (type === 'pdf') {
            form.action = '{{ route('it-management.backup-reports.export-pdf') }}';
        } else if (type === 'excel') {
            form.action = '{{ route('it-management.backup-reports.export-excel') }}';
        }

        form.submit();
        form.action = originalAction;
    }
</script>
@endsection
