@extends('layouts.app')

@section('title', 'Defaulters List')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 animate-fade-in">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-4">
                <span class="w-1.5 h-8 bg-red-600 rounded-full"></span>
                Daily Backup Defaulters
            </h2>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-5">Staff members who have continuously missed their daily backups.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" form="bulk-mail-form" id="bulk-send-btn"
                onclick="handleBulkSend(event)"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl border border-indigo-700 transition-all flex items-center gap-2 text-sm shadow-sm active:scale-95">
                <svg id="bulk-mail-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                <svg id="bulk-spin-icon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span id="bulk-send-text">Send Mail to Selected</span>
            </button>
            <a href="{{ route('it-management.backup-defaulters.mail-logs') }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold py-2.5 px-5 rounded-xl border border-indigo-200 transition-all flex items-center gap-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Mail Logs
            </a>
            <a href="{{ route('it-management.backup-defaulters.export-pdf', ['min_days' => $minDays, 'office_id' => $officeId, 'exclude_mailed' => $excludeMailed ? 1 : 0]) }}" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold py-2.5 px-5 rounded-xl border border-red-200 transition-all flex items-center gap-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export PDF
            </a>
            <a href="{{ route('it-management.backup-defaulters.export-excel', ['min_days' => $minDays, 'office_id' => $officeId, 'exclude_mailed' => $excludeMailed ? 1 : 0]) }}" class="bg-green-50 hover:bg-green-100 text-green-600 font-bold py-2.5 px-5 rounded-xl border border-green-200 transition-all flex items-center gap-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-6 mb-8">
        <form method="GET" action="{{ route('it-management.backup-defaulters.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-5 items-end">
            <!-- Filter: Days Not Taken -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                    Consecutive Days Missed
                </label>
                <select name="min_days" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 font-bold text-slate-700 bg-slate-50/50">
                    <option value="1" {{ $minDays == 1 ? 'selected' : '' }}>1+ Days Missed</option>
                    <option value="2" {{ $minDays == 2 ? 'selected' : '' }}>2+ Days Missed</option>
                    <option value="3" {{ $minDays == 3 ? 'selected' : '' }}>3+ Days Missed (Default)</option>
                    <option value="5" {{ $minDays == 5 ? 'selected' : '' }}>5+ Days Missed</option>
                    <option value="7" {{ $minDays == 7 ? 'selected' : '' }}>7+ Days Missed (1 Week)</option>
                    <option value="10" {{ $minDays == 10 ? 'selected' : '' }}>10+ Days Missed</option>
                    <option value="15" {{ $minDays == 15 ? 'selected' : '' }}>15+ Days Missed</option>
                </select>
            </div>

            <!-- Filter: Office Wise -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                    Office Wise Filter
                </label>
                <select name="office_id" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 font-bold text-slate-700 bg-slate-50/50">
                    <option value="">All Offices</option>
                    @foreach($offices as $off)
                        <option value="{{ $off->id }}" {{ $officeId == $off->id ? 'selected' : '' }}>
                            {{ $off->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter: Mail Grace Status -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                    Mail Grace Rule
                </label>
                <select name="exclude_mailed" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 font-bold text-slate-700 bg-slate-50/50">
                    <option value="1" {{ $excludeMailed ? 'selected' : '' }}>Hide Mailed (7 Days Grace)</option>
                    <option value="0" {{ !$excludeMailed ? 'selected' : '' }}>Show All (Include Mailed)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2.5">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-xl shadow-md transition-all text-sm active:scale-95 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                    Apply Filter
                </button>
                <a href="{{ route('it-management.backup-defaulters.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 px-4 rounded-xl transition text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Recently Mailed Grace Notice --}}
    @if($recentlyMailedCount > 0 && $excludeMailed)
    <div class="mb-6 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200/80 rounded-2xl px-6 py-4 flex items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-xs text-indigo-950 font-bold">
                    7-Day Post-Email Grace Active: <span class="text-indigo-600 font-black">{{ $recentlyMailedCount }} staff member(s)</span> are currently hidden from this list because they received a reminder email in the past 7 days.
                </p>
                <p class="text-[11px] text-indigo-700/80 mt-0.5">
                    If they take backup (YES), they will stay off the list. If they do not take backup within 7 days, they will automatically reappear here.
                </p>
            </div>
        </div>
        <a href="{{ request()->fullUrlWithQuery(['exclude_mailed' => 0]) }}" class="text-xs font-black text-indigo-600 hover:text-indigo-800 bg-white border border-indigo-200 px-3.5 py-1.5 rounded-lg whitespace-nowrap shadow-sm transition">
            View All Staff
        </a>
    </div>
    @endif

    {{-- Toast Notification --}}
    @if(session('success'))
    <div id="toast-success"
         class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-white border border-green-200 shadow-2xl shadow-green-500/10 px-5 py-4 rounded-2xl max-w-sm transition-all duration-500">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div>
            <p class="font-black text-slate-800 text-sm">Mail Sent Successfully!</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ session('success') }}</p>
        </div>
        <button onclick="dismissToast('toast-success')" class="ml-2 text-slate-300 hover:text-slate-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div id="toast-error"
         class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-white border border-red-200 shadow-2xl shadow-red-500/10 px-5 py-4 rounded-2xl max-w-sm">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
        <div>
            <p class="font-black text-slate-800 text-sm">Mail Failed!</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ session('error') }}</p>
        </div>
        <button onclick="dismissToast('toast-error')" class="ml-2 text-slate-300 hover:text-slate-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    @endif

    {{-- Sending Overlay --}}
    <div id="sending-overlay"
         class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-2xl px-10 py-8 flex flex-col items-center gap-4 min-w-[260px]">
            <div class="relative">
                <div class="w-16 h-16 rounded-full border-4 border-indigo-100 border-t-indigo-600 animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <p class="font-black text-slate-800 text-lg">Sending Mail...</p>
                <p class="text-sm text-slate-500 mt-1">Please wait, do not close this page.</p>
            </div>
        </div>
    </div>

    <!-- Defaulters Table -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-red-50 px-8 py-5 border-b border-red-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <h3 class="text-lg font-bold text-red-800">
                    Defaulters List ({{ $minDays }}+ Consecutive Days Missed)
                </h3>
            </div>
            <span class="bg-red-100 text-red-800 text-xs font-black px-3 py-1 rounded-full">
                {{ count($defaulters) }} Defaulters Found
            </span>
        </div>
        
        <form id="bulk-mail-form" action="{{ route('it-management.backup-defaulters.send-bulk-mail') }}" method="POST"
              onsubmit="showSendingOverlay()">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-auto">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-center w-12">
                                <input type="checkbox" id="select-all" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onclick="toggleAllCheckboxes(this)">
                            </th>
                            <th class="px-8 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">
                                Staff Name & Action
                            </th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Office & Department</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-center">Days Not Taken</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Recent Backup History</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-right">Direct Email</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($defaulters as $defaulter)
                        <tr class="hover:bg-red-50/30 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap text-center align-top">
                                <input type="checkbox" name="staff_ids[]" value="{{ $defaulter['staff']->id }}" class="staff-checkbox rounded border-slate-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mt-1">
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap align-top">
                                <!-- Staff Name -->
                                <div class="font-bold text-slate-900 text-base">{{ $defaulter['staff']->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $defaulter['staff']->email }}</div>

                                @if(!empty($defaulter['last_mail']) && \Carbon\Carbon::parse($defaulter['last_mail']->created_at)->isAfter(now()->subDays(7)))
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            Mailed {{ \Carbon\Carbon::parse($defaulter['last_mail']->created_at)->diffForHumans() }} (Grace Active)
                                        </span>
                                    </div>
                                @endif

                                <!-- Send Email Button RIGHT UNDER THE NAME -->
                                <div class="mt-2.5">
                                    <button type="submit"
                                        formaction="{{ route('it-management.backup-defaulters.send-mail', $defaulter['staff']->id) }}"
                                        onclick="handleSingleSend(event, this, '{{ $defaulter['staff']->name }}')"
                                        data-name="{{ $defaulter['staff']->name }}"
                                        class="single-send-btn bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-3.5 rounded-lg border border-indigo-700 transition-all text-xs shadow-sm flex items-center gap-1.5 active:scale-95">
                                        <svg class="send-icon w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="btn-label">Send Email</span>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm align-top">
                                <div class="font-bold text-slate-700">
                                    {{ $defaulter['staff']->office->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $defaulter['staff']->department->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center align-top">
                                <span class="bg-red-100 text-red-800 font-black px-4 py-1.5 rounded-xl border border-red-200 inline-block">
                                    {{ $defaulter['consecutive_missed'] }} Days Missed
                                </span>
                            </td>
                            <td class="px-6 py-5 align-top">
                                @if($defaulter['recent_backups']->isEmpty())
                                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-lg">Never taken a backup</span>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($defaulter['recent_backups'] as $date)
                                            <span class="text-xs font-bold text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-md">
                                                {{ \Carbon\Carbon::parse($date)->format('d M') }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right align-top">
                                <button type="submit"
                                    formaction="{{ route('it-management.backup-defaulters.send-mail', $defaulter['staff']->id) }}"
                                    onclick="handleSingleSend(event, this, '{{ $defaulter['staff']->name }}')"
                                    class="text-indigo-600 hover:text-indigo-800 font-bold text-xs p-1.5 hover:bg-indigo-50 rounded-lg transition inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Mail Staff
                                </button>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 text-green-500 mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-800">All Good!</h3>
                                    <p class="text-slate-500 text-sm mt-1">No staff members have missed their backup for {{ $minDays }}+ consecutive days.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAllCheckboxes(source) {
        const checkboxes = document.querySelectorAll('.staff-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }

    function dismissToast(id) {
        const el = document.getElementById(id);
        if (el) {
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        ['toast-success', 'toast-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) setTimeout(() => dismissToast(id), 5000);
        });
    });

    function showSendingOverlay() {
        document.getElementById('sending-overlay').classList.remove('hidden');
        document.getElementById('sending-overlay').classList.add('flex');
    }

    function handleBulkSend(e) {
        const checked = document.querySelectorAll('.staff-checkbox:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Please select at least one staff member to send mail.');
            return;
        }
        showSendingOverlay();
        const btn = document.getElementById('bulk-send-btn');
        document.getElementById('bulk-mail-icon').classList.add('hidden');
        document.getElementById('bulk-spin-icon').classList.remove('hidden');
        document.getElementById('bulk-send-text').textContent = 'Sending...';
        setTimeout(() => {
            btn.disabled = true;
        }, 50);
    }

    function handleSingleSend(e, btn, staffName) {
        showSendingOverlay();
        const sendIcon = btn.querySelector('.send-icon');
        if (sendIcon) sendIcon.classList.add('hidden');
        btn.classList.add('opacity-75');
        const label = btn.querySelector('.btn-label');
        if (label) label.textContent = 'Sending...';
        setTimeout(() => {
            btn.disabled = true;
        }, 50);
    }
</script>
@endsection
