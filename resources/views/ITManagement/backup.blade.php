@extends('layouts.app')

@section('title', 'Staff Backup Sheet (Compact)')

@section('content')
@php
    $selectedMonth = request('month', date('m'));
    $selectedYear = request('year', date('Y'));
    $selectedOffice = request('office_id');
    $specificDate = request('specific_date');
    
    $currentDate = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1);
    
    $saturdays = [];
    if ($specificDate) {
        $saturdays[] = \Carbon\Carbon::parse($specificDate);
    } else {
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();
        $date = $startOfMonth->copy();
        while ($date <= $endOfMonth) {
            if ($date->isSaturday()) {
                $saturdays[] = $date->copy();
            }
            $date->addDay();
        }
    }
    
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
        7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
@endphp

<!-- Compact Header & Filters -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 animate-fade-in">
    <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
            <div class="w-1.5 h-6 bg-indigo-600 rounded-full"></div>
            Backup Records
        </h2>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 ml-3.5">System Administration / IT Sheet</p>
    </div>

    <div class="bg-white px-4 py-3 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap items-center gap-3">
        <form action="{{ route('it-management.backup.index') }}" method="GET" class="flex flex-wrap items-center gap-3" id="filterForm">
            <select name="office_id" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-xl text-[11px] font-bold text-slate-700 px-3 py-2 min-w-[150px]">
                <option value="">All Offices</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" {{ $selectedOffice == $office->id ? 'selected' : '' }}>{{ $office->office_name }}</option>
                @endforeach
            </select>

            @if(!$specificDate)
            <div class="flex gap-1">
                <select name="month" class="bg-slate-50 border-none rounded-xl text-[11px] font-bold text-slate-700 px-3 py-2">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="year" class="bg-slate-50 border-none rounded-xl text-[11px] font-bold text-slate-700 px-3 py-2">
                    @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                        <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            @endif

            <div class="flex items-center gap-2">
                <input type="date" name="specific_date" value="{{ $specificDate }}" class="bg-slate-50 border-none rounded-xl text-[11px] font-bold text-slate-700 px-3 py-2">
                @if($specificDate)
                    <a href="{{ route('it-management.backup.index') }}" class="p-2 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                @endif
            </div>

            <button type="submit" class="p-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="bg-emerald-500 text-white px-5 py-3 rounded-xl relative mb-6 flex items-center justify-between animate-fade-in shadow-lg shadow-emerald-500/20">
    <span class="text-[11px] font-black uppercase tracking-widest">{{ session('success') }}</span>
    <button onclick="this.parentElement.remove()" class="text-white/60 hover:text-white transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
    </button>
</div>
@endif

<form action="{{ route('it-management.backup.store') }}" method="POST" id="bulkBackupForm">
    @csrf
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative isolate">
        <div class="bg-[#FFFF00] py-3 text-center border-b border-black/10">
            <h1 class="text-xs md:text-sm font-black text-black uppercase tracking-[0.2em]">
                STAFF DATA BACKUP RECORD ({{ $selectedOffice ? \App\Models\Office\OfficeModel::find($selectedOffice)->office_name : 'CONSOLIDATED' }})
            </h1>
        </div>
        
        <!-- Synchronized Top Scrollbar -->
        <div class="top-scrollbar overflow-x-auto bg-slate-50 border-b border-slate-100 h-2">
            <div class="top-scrollbar-content"></div>
        </div>

        <div class="overflow-x-auto excel-container" id="mainTableContainer">
            <table class="w-full text-left border-collapse min-w-[1600px] table-fixed">
                <thead>
                    <tr class="bg-slate-50 sticky top-0 z-[60]">
                        <th class="w-[40px] px-1 py-3 text-[9px] font-black text-slate-400 uppercase border-r border-b border-slate-200 sticky left-0 bg-slate-50 z-[70] text-center">SR</th>
                        <th class="w-[50px] px-1 py-3 text-[9px] font-black text-slate-400 uppercase border-r border-b border-slate-200 sticky left-[40px] bg-slate-50 z-[70] text-center">Seq</th>
                        <th class="w-[200px] px-3 py-3 text-[10px] font-black text-slate-700 uppercase border-r border-b border-slate-200 sticky left-[90px] bg-slate-50 z-[70]">Staff Name</th>
                        
                        @foreach($saturdays as $sat)
                            <th colspan="4" class="px-1 py-3 text-[10px] font-black text-slate-800 uppercase border-r border-b border-slate-200 text-center bg-indigo-50/50">
                                {{ $sat->format('d-m-y') }} ({{ substr($sat->format('l'), 0, 3) }})
                            </th>
                        @endforeach
                    </tr>
                    <tr class="bg-white sticky top-[37px] z-[55]">
                        <th class="sticky left-0 bg-white z-[65] border-r border-b"></th>
                        <th class="sticky left-[40px] bg-white z-[65] border-r border-b"></th>
                        <th class="sticky left-[90px] bg-white z-[65] border-r border-b"></th>
                        
                        @foreach($saturdays as $sat)
                            <th class="px-1 py-1 text-[8px] font-black text-slate-300 uppercase border-r border-b text-center w-20">Stat</th>
                            <th class="px-1 py-1 text-[8px] font-black text-slate-300 uppercase border-r border-b text-center w-32">Loc</th>
                            <th class="px-2 py-1 text-[8px] font-black text-slate-300 uppercase border-r border-b text-center min-w-[150px]">Remark</th>
                            <th class="px-1 py-1 text-[8px] font-black text-slate-300 uppercase border-r border-b w-20 text-center">Date</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($staffs as $index => $staff)
                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                        <td class="px-1 py-2 text-[10px] font-bold text-slate-300 border-r border-slate-100 sticky left-0 bg-white group-hover:bg-slate-50 z-40 text-center">
                            {{ $index + 1 }}
                        </td>
                        <td class="p-0 border-r border-slate-100 sticky left-[40px] bg-white group-hover:bg-slate-50 z-40 text-center focus-within:bg-indigo-50">
                            <input type="number" name="sequences[{{ $staff->id }}]" value="{{ $staff->backup_sequence }}" 
                                class="w-full h-8 bg-transparent border-none text-[10px] font-black text-slate-700 text-center focus:ring-0 p-0"
                                placeholder="-">
                        </td>
                        <td class="px-3 py-2 border-r border-slate-100 sticky left-[90px] bg-white group-hover:bg-slate-50 z-40 shadow-[2px_0_5px_rgba(0,0,0,0.01)]">
                            <div class="text-[11px] font-black text-slate-800 uppercase truncate" title="{{ $staff->name }}">{{ $staff->name }}</div>
                            <div class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">{{ $staff->department->dept_name ?? 'N/A' }}</div>
                        </td>
                        
                        @php
                            $backupsByDate = $staff->systemBackups->groupBy(function($item) {
                                return \Carbon\Carbon::parse($item->backup_date)->format('Y-m-d');
                            });
                        @endphp
                        
                        @foreach($saturdays as $sat)
                            @php
                                $dateStr = $sat->format('Y-m-d');
                                $backup = $backupsByDate->has($dateStr) ? $backupsByDate->get($dateStr)->first() : null;
                                $prefix = "backups[{$staff->id}][{$dateStr}]";
                            @endphp
                            
                            <!-- Status -->
                            <td class="p-0 border-r border-slate-100 text-center focus-within:bg-indigo-50">
                                <select name="{{ $prefix }}[status]" class="w-full h-8 bg-transparent border-none text-[10px] font-bold text-center focus:ring-0 p-0 cursor-pointer">
                                    <option value="">-</option>
                                    <option value="YES" {{ ($backup && ($backup->status == 'YES' || $backup->status == 'Completed')) ? 'selected' : '' }}>YES</option>
                                    <option value="NO" {{ ($backup && ($backup->status == 'NO' || $backup->status == 'Failed')) ? 'selected' : '' }}>NO</option>
                                    <option value="NA" {{ ($backup && $backup->status == 'NA') ? 'selected' : '' }}>NA</option>
                                </select>
                            </td>
                            
                            <!-- Location -->
                            <td class="p-0 border-r border-slate-100 focus-within:bg-indigo-50">
                                <select name="{{ $prefix }}[location]" class="w-full h-8 bg-transparent border-none text-[10px] font-medium text-slate-600 focus:ring-0 px-1 cursor-pointer">
                                    <option value="">-</option>
                                    <option value="DRIVE" {{ ($backup && $backup->location == 'DRIVE') ? 'selected' : '' }}>DRIVE</option>
                                    <option value="HDD" {{ ($backup && $backup->location == 'HDD') ? 'selected' : '' }}>HDD</option>
                                    <option value="PENDRIVE" {{ ($backup && $backup->location == 'PENDRIVE') ? 'selected' : '' }}>PEN</option>
                                    <option value="PENDRIVE/DRIVE" {{ ($backup && $backup->location == 'PENDRIVE/DRIVE') ? 'selected' : '' }}>PEN+DR</option>
                                    <option value="LAPTOP" {{ ($backup && $backup->location == 'LAPTOP') ? 'selected' : '' }}>LAPTOP</option>
                                    <option value="SOFTWARE" {{ ($backup && $backup->location == 'SOFTWARE') ? 'selected' : '' }}>SOFT</option>
                                </select>
                            </td>
                            
                            <!-- Remark -->
                            <td class="p-0 border-r border-slate-100 focus-within:bg-indigo-50">
                                <input type="text" name="{{ $prefix }}[remark]" value="{{ $backup->remark ?? '' }}" 
                                    class="w-full h-8 bg-transparent border-none text-[10px] text-slate-500 focus:ring-0 px-2 placeholder:text-slate-200" 
                                    placeholder="...">
                            </td>

                            <!-- Date -->
                            <td class="p-0 border-r border-slate-200 bg-slate-50/10 text-center">
                                <span class="text-[9px] font-bold text-slate-300">{{ $sat->format('d-m') }}</span>
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Compact Save Hub -->
    <div class="fixed bottom-6 right-6 z-[100]">
        <button type="submit" class="group flex items-center gap-3 pl-6 pr-8 py-4 bg-slate-900 text-white rounded-2xl shadow-xl hover:bg-black transition-all active:scale-95 border-4 border-white/20">
            <div class="bg-indigo-500 p-1.5 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v13a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                </svg>
            </div>
            <div class="flex flex-col items-start leading-tight">
                <span class="text-xs font-black uppercase tracking-widest">Update Sheet</span>
            </div>
        </button>
    </div>
</form>

<style>
    /* Compact Excel UI */
    .excel-container {
        scrollbar-width: thin;
        scrollbar-color: #e2e8f0 transparent;
    }
    
    .excel-container::-webkit-scrollbar { height: 8px; width: 8px; }
    .excel-container::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 4px;
        border: 2px solid #ffffff;
    }
    
    /* Exact Sticky Offsets */
    .sticky.left-0 { left: 0 !important; }
    .sticky.left-\[40px\] { left: 40px !important; }
    .sticky.left-\[90px\] { left: 90px !important; }
    
    /* Sticky Top Offset (Nav is 80px) */
    .sticky.top-0 { top: 80px !important; }
    .sticky.top-\[37px\] { top: 117px !important; }

    /* Custom Tiny Dropdown */
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.25rem center;
        background-repeat: no-repeat;
        background-size: 1em 1em;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; margin: 0; 
    }
</style>

<script>
    const mainContainer = document.getElementById('mainTableContainer');
    const topScrollbar = document.querySelector('.top-scrollbar');
    const topScrollbarContent = document.querySelector('.top-scrollbar-content');
    
    function syncScroll() {
        const table = mainContainer.querySelector('table');
        topScrollbarContent.style.width = table.scrollWidth + 'px';
        topScrollbar.scrollLeft = mainContainer.scrollLeft;
    }
    
    window.addEventListener('load', syncScroll);
    window.addEventListener('resize', syncScroll);
    mainContainer.addEventListener('scroll', () => topScrollbar.scrollLeft = mainContainer.scrollLeft);
    topScrollbar.addEventListener('scroll', () => mainContainer.scrollLeft = topScrollbar.scrollLeft);

    document.addEventListener('keydown', function(e) {
        const active = document.activeElement;
        if (!active || (active.tagName !== 'INPUT' && active.tagName !== 'SELECT')) return;
        const cell = active.closest('td');
        const row = cell.closest('tr');
        const colIndex = Array.from(row.children).indexOf(cell);
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            row.nextElementSibling?.children[colIndex].querySelector('input, select')?.focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            row.previousElementSibling?.children[colIndex].querySelector('input, select')?.focus();
        }
    });
</script>
@endsection
