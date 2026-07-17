@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">IT Recharges (Bills)</h1>
            <p class="text-sm text-slate-500">Manage recurring IT bills and their due dates.</p>
        </div>
        <a href="{{ route('it-management.recharges.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Recharge
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Name & Purpose</th>
                        <th class="px-6 py-4">Billing Cycle</th>
                        <th class="px-6 py-4">Upcoming Bill Date</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Type & Mode</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recharges as $recharge)
                        @php
                            $isClosed = $recharge->status === 'closed';
                            $isOverdue = !$isClosed && \Carbon\Carbon::parse($recharge->last_date)->lt(now());
                            $isDueSoon = !$isClosed && !$isOverdue && \Carbon\Carbon::parse($recharge->last_date)->lte(now()->addDays(7));
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors {{ $isClosed ? 'opacity-60 bg-slate-100/50 grayscale-[0.5]' : ($isOverdue ? 'bg-red-50/30' : ($isDueSoon ? 'bg-amber-50/30' : '')) }}">
                            <td class="px-6 py-4 text-sm text-slate-600 font-medium">{{ $recharge->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-bold text-slate-900">{{ $recharge->name }}</div>
                                    @if($isClosed)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-600">CLOSED</span>
                                    @endif
                                </div>
                                @if($recharge->purpose)
                                    <div class="text-xs text-slate-500">{{ $recharge->purpose }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                @if($recharge->duration_months == 1) Monthly 
                                @elseif($recharge->duration_months == 3) Quarterly
                                @elseif($recharge->duration_months == 6) Half-Yearly
                                @elseif($recharge->duration_months == 12) Yearly
                                @else {{ $recharge->duration_months }} Months @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold {{ $isOverdue ? 'text-red-600' : ($isDueSoon ? 'text-amber-600' : 'text-slate-700') }}">
                                    {{ $recharge->last_date->format('d M, Y') }}
                                </div>
                                @if($isOverdue)
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700">OVERDUE</span>
                                @elseif($isDueSoon)
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">DUE SOON</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-700">₹{{ number_format($recharge->amount, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($recharge->payment_type == 'Auto Pay')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 mb-1">AUTO PAY</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 mb-1">MANUAL</span>
                                @endif
                                <div class="text-xs text-slate-500">{{ $recharge->mode ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$isClosed)
                                        <button onclick="document.getElementById('markPaidModal{{ $recharge->id }}').classList.remove('hidden')" 
                                                class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded-lg text-xs font-bold transition">
                                            Mark Paid
                                        </button>
                                    @endif
                                    <a href="{{ route('it-management.recharges.history', $recharge->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 transition" title="History">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </a>
                                    <a href="{{ route('it-management.recharges.edit', $recharge->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    @if(!$isClosed)
                                    <form action="{{ route('it-management.recharges.close', $recharge->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to close this recharge? It will stop generating future bills.');">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-amber-600 transition" title="Close Recharge">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('it-management.recharges.destroy', $recharge->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this recharge?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Mark Paid Modal -->
                        <div id="markPaidModal{{ $recharge->id }}" class="fixed inset-0 z-50 hidden">
                            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                                <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-slate-200">
                                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                                        <h3 class="text-lg font-bold text-slate-800">Mark Payment - {{ $recharge->name }}</h3>
                                        <button onclick="document.getElementById('markPaidModal{{ $recharge->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('it-management.recharges.mark-paid', $recharge->id) }}" method="POST" class="p-6" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-6 bg-indigo-50 border border-indigo-100 p-4 rounded-xl">
                                            <p class="text-sm text-indigo-900 mb-1">Upcoming Bill Date: <strong>{{ $recharge->last_date->format('d M, Y') }}</strong></p>
                                            <p class="text-sm text-indigo-900">Billing Cycle: <strong>
                                                @if($recharge->duration_months == 1) Monthly 
                                                @elseif($recharge->duration_months == 3) Quarterly
                                                @elseif($recharge->duration_months == 6) Half-Yearly
                                                @elseif($recharge->duration_months == 12) Yearly
                                                @else {{ $recharge->duration_months }} Months @endif
                                            </strong></p>
                                        </div>
                                        
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Amount Paid</label>
                                                <input type="number" step="0.01" name="amount_paid" value="{{ $recharge->amount }}" 
                                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Payment Date</label>
                                                <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" 
                                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Notes (Optional)</label>
                                                <textarea name="notes" rows="2"
                                                          class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium resize-none"></textarea>
                                            </div>
                                            
                                            <div class="relative py-2">
                                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                                    <div class="w-full border-t border-slate-200"></div>
                                                </div>
                                                <div class="relative flex justify-center">
                                                    <span class="bg-white px-2 text-xs text-slate-400 font-medium">OR BULK IMPORT (CSV)</span>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Upload Payment History (.csv)</label>
                                                <input type="file" name="payment_history_csv" accept=".csv"
                                                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition border border-slate-200 rounded-xl bg-slate-50">
                                                <p class="text-[10px] text-slate-400 mt-1">Format: Date (YYYY-MM-DD), Amount, Notes. This overrides single payment fields above.</p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                            <button type="button" onclick="document.getElementById('markPaidModal{{ $recharge->id }}').classList.add('hidden')" 
                                                    class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition">
                                                Cancel
                                            </button>
                                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                                                Save Payment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <p class="text-sm font-medium">No recharges found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
