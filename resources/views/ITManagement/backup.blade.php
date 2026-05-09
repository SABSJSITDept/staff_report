@extends('layouts.app')

@section('title', 'Staff Data Backup Record')

@section('content')
@php
    $selectedMonth = request('month', date('m'));
    $selectedYear = request('year', date('Y'));
    $currentDate = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1);
    
    $startOfMonth = $currentDate->copy()->startOfMonth();
    $endOfMonth = $currentDate->copy()->endOfMonth();
    
    $saturdays = [];
    $date = $startOfMonth->copy();
    while ($date <= $endOfMonth) {
        if ($date->isSaturday()) {
            $saturdays[] = $date->copy();
        }
        $date->addDay();
    }
    
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
        7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
@endphp

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">IT Management</h2>
        <p class="text-slate-500 font-medium mt-1">Staff Data Backup Record & Monitoring</p>
    </div>

    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
        <form action="{{ route('it-management.backup.index') }}" method="GET" class="flex items-center gap-2">
            <select name="month" class="bg-slate-50 border-none rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" class="bg-slate-50 border-none rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                    <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <button type="submit" class="p-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </button>
        </form>
        <div class="h-8 w-px bg-slate-200 mx-1"></div>
        <button onclick="toggleLogForm()" class="flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            New Entry
        </button>
    </div>
</div>

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl relative mb-8 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
    <div class="bg-emerald-100 p-2 rounded-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
    </div>
    <span class="font-semibold">{{ session('success') }}</span>
</div>
@endif

<!-- Quick Log Form (Hidden by default) -->
<div id="quickLogForm" class="hidden mb-8 animate-in fade-in zoom-in duration-200">
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <span class="bg-indigo-100 text-indigo-600 p-2 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                    </svg>
                </span>
                Add Backup Entry
            </h3>
            <button onclick="toggleLogForm()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-8">
            <form action="{{ route('it-management.backup.store') }}" method="POST" id="backupEntryForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Staff Member</label>
                        <select name="staff_id" id="form_staff_id" required class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                            <option value="">-- Select Staff --</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->department->dept_name ?? 'No Dept' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Backup Date</label>
                        <select name="backup_date" id="form_backup_date" required class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                            @foreach($saturdays as $sat)
                                <option value="{{ $sat->format('Y-m-d') }}" {{ $sat->format('Y-m-d') == date('Y-m-d') || ($sat->format('Y-m-d') == end($saturdays)->format('Y-m-d')) ? 'selected' : '' }}>
                                    {{ $sat->format('d M Y (D)') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Status</label>
                        <select name="status" id="form_status" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                            <option value="YES">YES (Completed)</option>
                            <option value="NO">NO (Failed/Pending)</option>
                            <option value="NA">NA (Not Applicable)</option>
                            <option value="Completed">Completed</option>
                            <option value="Pending">Pending</option>
                            <option value="Failed">Failed</option>
                            <option value="Not Taking">Not Taking Backup</option>
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Location</label>
                        <input type="text" name="location" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 transition-all" placeholder="e.g. HDD, Drive, NAS">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Remark</label>
                        <input type="text" name="remark" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 transition-all" placeholder="Any notes...">
                    </div>
                </div>
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-95">
                        Save Backup Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Monthly Grid View -->
<div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
    <div class="bg-[#FFFF00] p-4 text-center border-b border-slate-200">
        <h1 class="text-xl md:text-2xl font-black text-black uppercase tracking-[0.2em]">
            STAFF DATA BACKUP RECORD (MONTHLY REPORT) {{ strtoupper($months[(int)$selectedMonth]) }} {{ $selectedYear }}
        </h1>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[1200px]">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-4 py-4 text-xs font-black text-slate-700 uppercase border-r border-slate-200 sticky left-0 bg-slate-50 z-20 w-12 text-center">SR</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-700 uppercase border-r border-slate-200 sticky left-12 bg-slate-50 z-20 min-w-[200px]">Staff Name</th>
                    
                    @foreach($saturdays as $sat)
                        <th colspan="4" class="px-4 py-4 text-xs font-black text-slate-700 uppercase border-r border-slate-200 text-center bg-slate-100/50">
                            {{ $sat->format('d-m-Y') }} (SATURDAY)
                        </th>
                    @endforeach
                </tr>
                <tr class="bg-slate-50/50">
                    <th class="sticky left-0 bg-slate-50 z-10 border-r border-slate-200"></th>
                    <th class="sticky left-12 bg-slate-50 z-10 border-r border-slate-200"></th>
                    
                    @foreach($saturdays as $sat)
                        <th class="px-4 py-2 text-[10px] font-bold text-slate-500 uppercase border-r border-slate-100 text-center">Status</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-slate-500 uppercase border-r border-slate-100 text-center">Location</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-slate-500 uppercase border-r border-slate-100 text-center">Remark</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-slate-500 uppercase border-r border-slate-200 text-center">Date</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($staffs as $index => $staff)
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="px-4 py-4 text-sm font-bold text-slate-400 border-r border-slate-100 sticky left-0 bg-white group-hover:bg-slate-50 z-10 text-center">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 py-4 border-r border-slate-100 sticky left-12 bg-white group-hover:bg-slate-50 z-10">
                        <div class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $staff->name }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $staff->department->dept_name ?? 'NO DEPT' }}</div>
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
                        @endphp
                        
                        <td class="px-2 py-3 border-r border-slate-100 text-center align-middle">
                            @if($backup)
                                @php
                                    $statusColor = match($backup->status) {
                                        'Completed', 'YES' => 'bg-emerald-100 text-emerald-700',
                                        'Failed', 'NO' => 'bg-rose-100 text-rose-700',
                                        'Pending' => 'bg-amber-100 text-amber-700',
                                        'NA' => 'bg-slate-100 text-slate-500',
                                        default => 'bg-indigo-100 text-indigo-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase {{ $statusColor }}">
                                    {{ $backup->status }}
                                </span>
                            @else
                                <button onclick="quickAdd('{{ $staff->id }}', '{{ $dateStr }}')" class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px] text-indigo-500 font-bold hover:underline">
                                    ADD
                                </button>
                            @endif
                        </td>
                        <td class="px-4 py-3 border-r border-slate-100 text-center text-xs font-medium text-slate-600">
                            {{ $backup->location ?? '-' }}
                        </td>
                        <td class="px-4 py-3 border-r border-slate-100 text-center text-[10px] text-slate-500 italic max-w-[150px] truncate" title="{{ $backup->remark ?? '' }}">
                            {{ $backup->remark ?? '-' }}
                        </td>
                        <td class="px-4 py-3 border-r border-slate-200 text-center text-[10px] font-bold text-slate-400">
                            {{ $backup ? \Carbon\Carbon::parse($backup->backup_date)->format('d-m-Y') : '-' }}
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleLogForm() {
        const form = document.getElementById('quickLogForm');
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function quickAdd(staffId, date) {
        const form = document.getElementById('quickLogForm');
        form.classList.remove('hidden');
        
        document.getElementById('form_staff_id').value = staffId;
        document.getElementById('form_backup_date').value = date;
        
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Add a subtle highlight to the form
        const container = form.querySelector('.bg-white');
        container.classList.add('ring-4', 'ring-indigo-500/10');
        setTimeout(() => container.classList.remove('ring-4', 'ring-indigo-500/10'), 2000);
    }
</script>

<style>
    /* Custom scrollbar for better look */
    .overflow-x-auto::-webkit-scrollbar {
        height: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 5px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 5px;
        border: 2px solid #f1f5f9;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Sticky header styles */
    thead th {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    /* Ensure sticky columns work */
    .sticky {
        position: sticky !important;
    }
</style>
@endsection
