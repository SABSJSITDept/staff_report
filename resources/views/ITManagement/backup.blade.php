@extends('layouts.app')

@section('title', 'Professional Backup Management')

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

<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Staff Backup <span class="text-indigo-600">Vault</span></h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em]">Enterprise IT Asset Continuity Management</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 bg-white p-2 rounded-2xl shadow-xl shadow-slate-100 border border-slate-100">
            <form action="{{ route('it-management.backup.index') }}" method="GET" class="flex flex-wrap items-center gap-2" id="filterForm">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <select name="office_id" onchange="this.form.submit()" class="pl-9 pr-8 py-2.5 bg-slate-50/50 border-transparent rounded-xl text-xs font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all min-w-[160px] appearance-none cursor-pointer hover:bg-slate-100">
                        <option value="">Global Infrastructure</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ $selectedOffice == $office->id ? 'selected' : '' }}>{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>

                @if(!$specificDate)
                <div class="flex items-center gap-2">
                    <select name="month" class="px-4 py-2.5 bg-slate-50/50 border-transparent rounded-xl text-xs font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all appearance-none cursor-pointer hover:bg-slate-100">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ strtoupper($name) }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="px-4 py-2.5 bg-slate-50/50 border-transparent rounded-xl text-xs font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all appearance-none cursor-pointer hover:bg-slate-100">
                        @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                @endif

                <div class="relative group">
                    <input type="date" name="specific_date" value="{{ $specificDate }}" class="pl-4 pr-4 py-2.5 bg-slate-50/50 border-transparent rounded-xl text-xs font-black text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all hover:bg-slate-100">
                </div>
                
                <button type="submit" class="p-2.5 bg-slate-900 text-white rounded-xl hover:bg-black transition-all shadow-lg shadow-slate-200 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <form action="{{ route('it-management.backup.store') }}" method="POST" id="bulkBackupForm" class="pb-32">
        @csrf
        <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
            <!-- Table Info Bar -->
            <div class="bg-slate-900 px-8 py-3 flex justify-between items-center border-b border-white/10">
                <div class="flex items-center gap-4">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-[10px] font-black text-white/70 uppercase tracking-[0.3em]">
                        Sequence Protocol: {{ $selectedOffice ? strtoupper(\App\Models\Office\OfficeModel::find($selectedOffice)->office_name) : 'GLOBAL ASSET NETWORK' }}
                    </h2>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-[9px] font-bold text-white/50 uppercase tracking-widest text-nowrap">Status: Synchronized</span>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto excel-container relative" id="mainTableContainer">
                <table class="w-full text-left border-collapse min-w-[1500px]">
                    <thead>
                        <tr class="bg-slate-50/80 backdrop-blur-md sticky top-0 z-[100] border-b border-slate-200/60">
                            <th class="sticky left-0 bg-slate-50 z-[110] w-12 px-3 py-4 text-[9px] font-black text-slate-400 uppercase border-r border-slate-200 text-center">ID</th>
                            <th class="sticky left-12 bg-slate-50 z-[110] w-14 px-2 py-4 text-[9px] font-black text-slate-400 uppercase border-r border-slate-200 text-center">SEQ</th>
                            <th class="sticky left-[104px] bg-slate-50 z-[110] w-72 px-6 py-4 text-[11px] font-black text-slate-800 uppercase border-r border-slate-200 shadow-[4px_0_15px_-10px_rgba(0,0,0,0.1)]">
                                Resource Identification
                            </th>
                            
                            @foreach($saturdays as $sat)
                                <th colspan="4" class="px-4 py-4 text-[11px] font-black text-slate-800 uppercase border-r border-slate-200 text-center bg-indigo-50/30">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z" /></svg>
                                        {{ $sat->format('d M, Y') }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                        <tr class="bg-white/90 backdrop-blur-sm sticky top-[49px] z-[90] border-b border-slate-200 shadow-sm">
                            <th class="sticky left-0 bg-white z-[110] border-r border-slate-100 h-10"></th>
                            <th class="sticky left-12 bg-white z-[110] border-r border-slate-100 h-10"></th>
                            <th class="sticky left-[104px] bg-white z-[110] border-r border-slate-100 h-10 shadow-[4px_0_15px_-10px_rgba(0,0,0,0.1)]"></th>
                            
                            @foreach($saturdays as $sat)
                                <th class="px-2 py-2 text-[9px] font-black text-slate-400 uppercase border-r border-slate-100 text-center w-24">Compliance</th>
                                <th class="px-2 py-2 text-[9px] font-black text-slate-400 uppercase border-r border-slate-100 text-center w-32">Repository</th>
                                <th class="px-2 py-2 text-[9px] font-black text-slate-400 uppercase border-r border-slate-100 text-center min-w-[180px]">Operational Remark</th>
                                <th class="px-2 py-2 text-[9px] font-black text-slate-400 uppercase border-r border-slate-100 w-24 text-center">Timestamp</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($staffs as $index => $staff)
                        <tr class="hover:bg-indigo-50/30 transition-all group duration-200">
                            <td class="sticky left-0 bg-white group-hover:bg-slate-50 z-40 px-3 py-3.5 text-[10px] font-bold text-slate-300 border-r border-slate-100 text-center">
                                #{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="sticky left-12 bg-white group-hover:bg-slate-50 z-40 p-0 border-r border-slate-100 text-center">
                                <input type="number" name="sequences[{{ $staff->id }}]" value="{{ $staff->backup_sequence }}" 
                                    class="w-full h-12 bg-transparent border-none text-[12px] font-black text-slate-700 text-center focus:ring-2 focus:ring-indigo-500/20 focus:bg-white p-0 transition-all" placeholder="-">
                            </td>
                            <td class="sticky left-[104px] bg-white group-hover:bg-slate-50 z-40 px-6 py-3.5 border-r border-slate-100 shadow-[6px_0_20px_-12px_rgba(0,0,0,0.15)]">
                                <div class="flex flex-col">
                                    <span class="text-[12px] font-black text-slate-900 uppercase tracking-tight truncate group-hover:text-indigo-700 transition-colors" title="{{ $staff->name }}">
                                        {{ $staff->name }}
                                    </span>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-[8px] font-black text-slate-500 rounded uppercase tracking-tighter">
                                            {{ $staff->department->dept_name ?? 'UNIT' }}
                                        </span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">• {{ $staff->office->office_name ?? 'HQ' }}</span>
                                    </div>
                                </div>
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
                                    $status = $backup ? $backup->status : '';
                                @endphp
                                
                                <td class="p-0 border-r border-slate-100 text-center group/cell">
                                    <div class="relative w-full h-12">
                                        <select name="{{ $prefix }}[status]" class="w-full h-full bg-transparent border-none text-[11px] font-black text-center focus:ring-2 focus:ring-indigo-500/20 focus:bg-white p-0 cursor-pointer appearance-none z-10 relative
                                            {{ $status == 'YES' ? 'text-emerald-600' : ($status == 'NO' ? 'text-rose-600' : 'text-slate-400') }}">
                                            <option value="" class="text-slate-400 font-bold">-</option>
                                            <option value="YES" {{ $status == 'YES' ? 'selected' : '' }} class="text-emerald-600 font-black">COMPLIANT</option>
                                            <option value="NO" {{ $status == 'NO' ? 'selected' : '' }} class="text-rose-600 font-black">NON-COMPLIANT</option>
                                            <option value="NA" {{ $status == 'NA' ? 'selected' : '' }} class="text-slate-500 font-black">EXEMPTED</option>
                                        </select>
                                        @if($status == 'YES')
                                            <div class="absolute right-2 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-emerald-500 opacity-50"></div>
                                        @elseif($status == 'NO')
                                            <div class="absolute right-2 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-rose-500 opacity-50"></div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="p-0 border-r border-slate-100 group/cell">
                                    <select name="{{ $prefix }}[location]" class="w-full h-12 bg-transparent border-none text-[10px] font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white px-3 cursor-pointer appearance-none transition-all">
                                        <option value="">UNCATEGORIZED</option>
                                        @foreach(['DRIVE' => 'CLOUD DRIVE', 'HDD' => 'EXTERNAL HDD', 'PENDRIVE' => 'USB FLASH', 'PENDRIVE/DRIVE' => 'DUAL SYNC', 'LAPTOP' => 'LOCAL ASSET', 'SOFTWARE' => 'SYSTEM CLOUD'] as $val => $label)
                                            <option value="{{ $val }}" {{ ($backup && $backup->location == $val) ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                
                                <td class="p-0 border-r border-slate-100 group/cell">
                                    <input type="text" name="{{ $prefix }}[remark]" value="{{ $backup->remark ?? '' }}" 
                                        class="w-full h-12 bg-transparent border-none text-[11px] font-medium text-slate-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white px-4 transition-all" placeholder="Enter logs...">
                                </td>

                                <td class="p-0 border-r border-slate-100 bg-slate-50/30 text-center">
                                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">{{ $sat->format('d-M') }}</span>
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Professional Action Bar -->
        <div class="fixed bottom-0 left-0 right-0 z-[200]">
            <div class="bg-white/80 backdrop-blur-xl border-t border-slate-200/60 shadow-[0_-20px_50px_rgba(0,0,0,0.1)] px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-6">
                    <div class="flex -space-x-3">
                        @foreach($staffs->take(3) as $s)
                            <div class="h-10 w-10 rounded-full border-4 border-white bg-slate-200 flex items-center justify-center text-[10px] font-black text-slate-500 shadow-sm uppercase">
                                {{ substr($s->name, 0, 2) }}
                            </div>
                        @endforeach
                        @if($staffs->count() > 3)
                            <div class="h-10 w-10 rounded-full border-4 border-white bg-indigo-600 flex items-center justify-center text-[10px] font-black text-white shadow-lg uppercase">
                                +{{ $staffs->count() - 3 }}
                            </div>
                        @endif
                    </div>
                    <div class="h-10 w-[1px] bg-slate-200 hidden md:block"></div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Asset Configuration</p>
                        <p class="text-sm font-black text-slate-900">{{ count($staffs) }} Active Protocol Chains</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button type="button" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="p-3 bg-slate-100 text-slate-400 rounded-2xl hover:bg-slate-200 transition-all active:scale-90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                    </button>
                    <button type="submit" class="group relative flex items-center gap-4 px-10 py-4 bg-slate-900 text-white rounded-[1.5rem] shadow-2xl shadow-indigo-200 hover:bg-black hover:-translate-y-1 transition-all duration-300 active:scale-95 overflow-hidden">
                        <div class="absolute inset-0 bg-indigo-600 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                        <div class="relative flex items-center gap-3">
                            <div class="p-2 bg-white/10 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-black uppercase tracking-[0.2em]">Execute Master Commit</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    /* Custom Scrollbar */
    .excel-container { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
    .excel-container::-webkit-scrollbar { height: 8px; width: 8px; }
    .excel-container::-webkit-scrollbar-track { background: transparent; }
    .excel-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; border: 2px solid transparent; background-clip: content-box; }
    .excel-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    /* Sticky Offsets - Optimized for professional spacing */
    .sticky.left-0 { left: 0 !important; }
    .sticky.left-12 { left: 48px !important; }
    .sticky.left-\[104px\] { left: 104px !important; }
    
    /* Global Nav Height is ~80px */
    .sticky.top-0 { top: 64px !important; }
    .sticky.top-\[49px\] { top: 113px !important; }

    /* Premium Input Styling */
    input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    
    /* Stealth Select Indicators */
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 0.8em 0.8em;
    }

    /* Column Shadows for depth */
    .sticky.left-\[104px\] {
        z-index: 50;
    }

    /* Animation Keyframes */
    @keyframes pulse-indigo {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
</style>

<script>
    // Keyboard Navigation System (Enterprise Grade)
    document.addEventListener('keydown', function(e) {
        const active = document.activeElement;
        if (!active || (active.tagName !== 'INPUT' && active.tagName !== 'SELECT')) return;
        
        const cell = active.closest('td');
        const row = cell.closest('tr');
        const cells = Array.from(row.children);
        const colIndex = cells.indexOf(cell);
        
        const focusNext = (target) => {
            if (target) {
                const element = target.querySelector('input, select');
                if (element) {
                    element.focus();
                    element.select?.();
                }
            }
        };

        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                focusNext(row.nextElementSibling?.children[colIndex]);
                break;
            case 'ArrowUp':
                e.preventDefault();
                focusNext(row.previousElementSibling?.children[colIndex]);
                break;
            case 'ArrowRight':
                if (active.tagName === 'SELECT' || (active.tagName === 'INPUT' && active.selectionEnd === active.value.length)) {
                    // Logic to move right only if cursor at end or it's a select
                    // focusNext(cells[colIndex + 1]);
                }
                break;
            case 'ArrowLeft':
                if (active.tagName === 'SELECT' || (active.tagName === 'INPUT' && active.selectionStart === 0)) {
                    // focusNext(cells[colIndex - 1]);
                }
                break;
        }
    });

    // Auto-save notification simulation or status updates can be added here
</script>
@endsection
