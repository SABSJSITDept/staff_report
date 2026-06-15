@extends('layouts.app')

@section('title', 'Defaulters List')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 animate-fade-in">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-4">
                <span class="w-1.5 h-8 bg-red-600 rounded-full"></span>
                Defaulters List
            </h2>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-5">Staff members who have continuously missed their daily backups.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" form="bulk-mail-form" id="bulk-send-btn"
                onclick="handleBulkSend(event)"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-5 rounded-xl border border-indigo-700 transition-all flex items-center gap-2 text-sm shadow-sm">
                <svg id="bulk-mail-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                <svg id="bulk-spin-icon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span id="bulk-send-text">Send to Selected</span>
            </button>
            <a href="{{ route('it-management.backup-defaulters.mail-logs') }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold py-2 px-5 rounded-xl border border-indigo-200 transition-all flex items-center gap-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Mail Logs
            </a>
            <a href="{{ route('it-management.backup-defaulters.export-pdf') }}" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold py-2 px-5 rounded-xl border border-red-200 transition-all flex items-center gap-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export PDF
            </a>
            <a href="{{ route('it-management.backup-defaulters.export-excel') }}" class="bg-green-50 hover:bg-green-100 text-green-600 font-bold py-2 px-5 rounded-xl border border-green-200 transition-all flex items-center gap-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- ===================== TOAST NOTIFICATION ===================== --}}
    @if(session('success'))
    <div id="toast-success"
         class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-white border border-green-200 shadow-2xl shadow-green-500/10 px-5 py-4 rounded-2xl max-w-sm transition-all duration-500"
         style="animation: slideInRight 0.4s ease;">
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
         class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-white border border-red-200 shadow-2xl shadow-red-500/10 px-5 py-4 rounded-2xl max-w-sm"
         style="animation: slideInRight 0.4s ease;">
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

    {{-- ===================== SENDING OVERLAY ===================== --}}
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
        <div class="bg-red-50 px-8 py-5 border-b border-red-100 flex items-center gap-3">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <h3 class="text-lg font-bold text-red-800">Action Required: Continuous Missing Backups (3+ Days)</h3>
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
                            <th class="px-8 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Staff Name</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-center">Consecutive Days Missed</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Last Backup Dates</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($defaulters as $defaulter)
                        <tr class="hover:bg-red-50/30 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <input type="checkbox" name="staff_ids[]" value="{{ $defaulter['staff']->id }}" class="staff-checkbox rounded border-slate-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $defaulter['staff']->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $defaulter['staff']->email }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-slate-600">
                                {{ $defaulter['staff']->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <span class="bg-red-100 text-red-800 font-black px-4 py-1.5 rounded-xl border border-red-200">
                                    {{ $defaulter['consecutive_missed'] }} Days
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                @if($defaulter['recent_backups']->isEmpty())
                                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-lg">Never taken a backup</span>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($defaulter['recent_backups'] as $date)
                                            <span class="text-xs font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-1 rounded-md">
                                                {{ \Carbon\Carbon::parse($date)->format('d M') }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right">
                                <button type="submit"
                                    formaction="{{ route('it-management.backup-defaulters.send-mail', $defaulter['staff']->id) }}"
                                    onclick="handleSingleSend(event, this, '{{ $defaulter['staff']->name }}')"
                                    data-name="{{ $defaulter['staff']->name }}"
                                    class="single-send-btn bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold py-1.5 px-4 rounded-lg border border-indigo-200 transition-all text-xs shadow-sm flex items-center gap-1.5 ml-auto">
                                    <svg class="send-icon w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="btn-label">Send Mail</span>
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
                                    <p class="text-slate-500 text-sm mt-1">No staff members have missed their backup for 3 or more consecutive days.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes slideInRight {
    from { transform: translateX(100px); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
@keyframes fadeOut {
    from { opacity: 1; }
    to   { opacity: 0; transform: translateX(30px); }
}
</style>

<script>
    function toggleAllCheckboxes(source) {
        const checkboxes = document.querySelectorAll('.staff-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }

    function dismissToast(id) {
        const el = document.getElementById(id);
        el.style.animation = 'fadeOut 0.4s ease forwards';
        setTimeout(() => el.remove(), 400);
    }

    // Auto-dismiss toast after 5 seconds
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
        // Show sending overlay
        showSendingOverlay();
        // Update button
        const btn = document.getElementById('bulk-send-btn');
        document.getElementById('bulk-mail-icon').classList.add('hidden');
        document.getElementById('bulk-spin-icon').classList.remove('hidden');
        document.getElementById('bulk-send-text').textContent = 'Sending...';
        setTimeout(() => {
            btn.disabled = true;
        }, 50);
    }

    function handleSingleSend(e, btn, staffName) {
        // Show overlay
        showSendingOverlay();
        // Update clicked button
        btn.querySelector('.send-icon').classList.add('hidden');
        btn.classList.add('opacity-75');
        btn.querySelector('.btn-label').textContent = 'Sending...';
        setTimeout(() => {
            btn.disabled = true;
        }, 50);
    }
</script>
@endsection
