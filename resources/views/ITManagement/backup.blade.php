@extends('layouts.app')

@section('title', 'Backup Master Sheet')

@section('content')
@php
    $selectedMonth = request('month', date('m'));
    $selectedYear = request('year', date('Y'));
    $selectedOffice = request('office_id');
    $specificDate = request('specific_date');
    $viewMode = request('view_mode', 'single');
    
    $currentDate = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1);
    
    $saturdays = [];
    if ($specificDate) {
        $saturdays[] = \Carbon\Carbon::parse($specificDate);
    } elseif ($viewMode === 'monthly') {
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();
        $date = $startOfMonth->copy();
        while ($date <= $endOfMonth) {
            if ($date->isSaturday()) {
                $saturdays[] = $date->copy();
            }
            $date->addDay();
        }
    } else {
        $today = \Carbon\Carbon::today();
        $saturdays[] = $today->isSaturday() ? $today : $today->previous(\Carbon\Carbon::SATURDAY);
    }
    
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
        7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
@endphp

<div class="max-w-[1800px] mx-auto px-4 animate-fade-in">
    <!-- Main Toolbar -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-4">
                <span class="w-1.5 h-8 bg-indigo-600 rounded-full"></span>
                Backup Intelligence
            </h2>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1 ml-5">Protocol: redundant audit</p>
        </div>

        <div class="bg-white p-1.5 rounded-2xl shadow-sm border border-slate-200 flex flex-wrap items-center gap-2">
            <form action="{{ route('it-management.backup.index') }}" method="GET" class="flex flex-wrap items-center gap-2" id="filterForm">
                <select name="view_mode" onchange="this.form.submit()" class="bg-indigo-50 border-none rounded-xl text-xs font-black text-indigo-700 px-4 py-2">
                    <option value="single" {{ $viewMode === 'single' ? 'selected' : '' }}>Latest Saturday</option>
                    <option value="monthly" {{ $viewMode === 'monthly' ? 'selected' : '' }}>Monthly Grid</option>
                </select>

                <div class="h-6 w-px bg-slate-200"></div>

                <select name="office_id" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 px-4 py-2 min-w-[140px]">
                    <option value="">All Offices</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ $selectedOffice == $office->id ? 'selected' : '' }}>{{ $office->office_name }}</option>
                    @endforeach
                </select>

                <input type="date" name="specific_date" value="{{ $specificDate }}" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 px-4 py-2">
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-indigo-600 text-white px-6 py-3 rounded-2xl mb-6 flex items-center justify-between shadow-xl shadow-indigo-600/10">
        <span class="font-bold uppercase text-[10px] tracking-widest italic">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="opacity-40 hover:opacity-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>
    </div>
    @endif

    <form action="{{ route('it-management.backup.store') }}" method="POST" id="bulkBackupForm" class="mb-20">
        @csrf
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden relative isolate">
            <!-- Header Identity (Banner) -->
            <div class="bg-slate-900 py-3 text-center border-b border-white/5 flex items-center justify-center gap-4">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">Master Sheet</span>
                <div class="h-3 w-px bg-slate-700"></div>
                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">
                    Entry: {{ $viewMode === 'single' ? $saturdays[0]->format('d M Y') : $months[(int)$selectedMonth] }}
                </span>
            </div>
            
            <!-- Table Header (Moved to the very top of the content area) -->
            <div class="overflow-hidden">
                <table class="w-full text-left border-collapse table-auto">
                    <thead>
                        <!-- THEAD IS NOW STICKY AT TOP: 0 (Inside the White Box) -->
                        <tr class="bg-slate-50 sticky top-0 z-[100] border-b border-slate-200 h-[50px] shadow-sm">
                            <th class="w-[60px] px-2 py-0 text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 text-center">SR</th>
                            <th class="w-[70px] px-2 py-0 text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 text-center">SEQ</th>
                            <th class="px-8 py-0 text-[10px] font-black text-slate-700 uppercase border-r border-slate-200 tracking-widest">Staff Identifier</th>
                            
                            @foreach($saturdays as $sat)
                                <th class="px-2 py-0 text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 text-center w-32 bg-indigo-50/10">Status</th>
                                <th class="px-2 py-0 text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 text-center w-48 bg-indigo-50/10">Location</th>
                                <th class="px-6 py-0 text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 text-center bg-indigo-50/10">Remarks</th>
                                <th class="px-2 py-0 text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 w-24 text-center bg-indigo-50/30">Date</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($staffs as $index => $staff)
                        <tr class="hover:bg-indigo-50/10 transition-colors group">
                            <td class="px-2 py-3 text-xs font-black text-slate-300 border-r border-slate-100 text-center">
                                {{ $index + 1 }}
                            </td>
                            <td class="p-0 border-r border-slate-100 text-center">
                                <input type="number" name="sequences[{{ $staff->id }}]" value="{{ $staff->backup_sequence }}" 
                                    class="w-full h-14 bg-transparent border-none text-xs font-black text-slate-700 text-center focus:ring-0 p-0" placeholder="-">
                            </td>
                            <td class="px-8 py-3 border-r border-slate-100">
                                <div class="text-[13px] font-black text-slate-800 uppercase tracking-tight">{{ $staff->name }}</div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $staff->department->dept_name ?? 'N/A' }}</div>
                            </td>
                            
                            @php
                                $backupsByDate = $staff->systemBackups->groupBy(function($item) {
                                    return \Carbon\Carbon::parse($item->backup_date)->format('Y-m-d');
                                });
                            @endphp
                            
                            @foreach($saturdays as $sat)
                                @php
                                    $dateStr = $sat->format('Y-m-d');
                                    $backup = $backupsByDate->get($dateStr)?->first();
                                    $prefix = "backups[{$staff->id}][{$dateStr}]";
                                @endphp
                                
                                <td class="p-0 border-r border-slate-100 text-center focus-within:bg-indigo-50/50">
                                    <select name="{{ $prefix }}[status]" class="w-full h-14 bg-transparent border-none text-[11px] font-black text-center focus:ring-0 p-0 cursor-pointer text-slate-600">
                                        <option value="">-</option>
                                        <option value="YES" {{ ($backup && ($backup->status == 'YES' || $backup->status == 'Completed')) ? 'selected' : '' }}>YES</option>
                                        <option value="NO" {{ ($backup && ($backup->status == 'NO' || $backup->status == 'Failed')) ? 'selected' : '' }}>NO</option>
                                        <option value="NA" {{ ($backup && $backup->status == 'NA') ? 'selected' : '' }}>NA</option>
                                    </select>
                                </td>
                                
                                <td class="p-0 border-r border-slate-100 focus-within:bg-indigo-50/50">
                                    <select name="{{ $prefix }}[location]" class="w-full h-14 bg-transparent border-none text-[10px] font-bold text-slate-500 focus:ring-0 px-4 cursor-pointer uppercase">
                                        <option value="">-</option>
                                        <option value="DRIVE" {{ ($backup && $backup->location == 'DRIVE') ? 'selected' : '' }}>DRIVE</option>
                                        <option value="HDD" {{ ($backup && $backup->location == 'HDD') ? 'selected' : '' }}>HARD DISK</option>
                                        <option value="PENDRIVE" {{ ($backup && $backup->location == 'PENDRIVE') ? 'selected' : '' }}>PENDRIVE</option>
                                        <option value="PENDRIVE/DRIVE" {{ ($backup && $backup->location == 'PENDRIVE/DRIVE') ? 'selected' : '' }}>PEN+DRIVE</option>
                                        <option value="LAPTOP" {{ ($backup && $backup->location == 'LAPTOP') ? 'selected' : '' }}>LOCAL MACHINE</option>
                                        <option value="SOFTWARE" {{ ($backup && $backup->location == 'SOFTWARE') ? 'selected' : '' }}>SOFTWARE</option>
                                    </select>
                                </td>
                                
                                <td class="p-0 border-r border-slate-100 focus-within:bg-indigo-50/50">
                                    <input type="text" name="{{ $prefix }}[remark]" value="{{ $backup->remark ?? '' }}" 
                                        class="w-full h-14 bg-transparent border-none text-[11px] text-slate-500 font-medium focus:ring-0 px-6 placeholder:text-slate-200" placeholder="...">
                                </td>

                                <td class="p-0 border-r border-slate-100 bg-slate-50/5 text-center">
                                    <span class="text-[9px] font-black text-slate-300 italic">{{ $sat->format('d/m') }}</span>
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Form Footer -->
            <div class="bg-slate-50 border-t border-slate-200 p-10 flex flex-col items-center justify-center text-center">
                <button type="submit" class="group relative flex items-center gap-8 pl-10 pr-14 py-6 bg-slate-900 text-white rounded-3xl shadow-2xl hover:bg-black transition-all active:scale-95 border-4 border-white/20">
                    <div class="bg-indigo-500 p-3 rounded-2xl shadow-xl group-hover:rotate-12 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v13a2 2 0 0 1-2 2z" />
                        </svg>
                    </div>
                    <div class="flex flex-col items-start leading-tight">
                        <span class="text-xs font-black uppercase tracking-[0.3em] opacity-60 mb-1">Final Submission</span>
                        <span class="text-2xl font-black uppercase tracking-tight">Submit All Records</span>
                    </div>
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    /* Fixed Table Header Offset (Now 0px as it sticks within the container area) */
    .sticky.top-0 { top: 0px !important; z-index: 100; }

    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.1em 1.1em;
        -webkit-appearance: none; appearance: none;
    }
    input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<script>
    document.addEventListener('keydown', function(e) {
        const active = document.activeElement;
        if (!active || (active.tagName !== 'INPUT' && active.tagName !== 'SELECT')) return;
        const cell = active.closest('td');
        const row = cell.closest('tr');
        const colIndex = Array.from(row.children).indexOf(cell);
        if (e.key === 'ArrowDown') { e.preventDefault(); row.nextElementSibling?.children[colIndex].querySelector('input, select')?.focus(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); row.previousElementSibling?.children[colIndex].querySelector('input, select')?.focus(); }
    });
</script>
@endsection
