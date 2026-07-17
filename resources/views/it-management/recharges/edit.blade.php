@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Recharge: {{ $recharge->name }}</h1>
            <p class="text-sm text-slate-500">Update the details for this IT recharge or bill.</p>
        </div>
        <a href="{{ route('it-management.recharges.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('it-management.recharges.update', $recharge->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Name</label>
                    <input type="text" name="name" required value="{{ old('name', $recharge->name) }}"
                           placeholder="e.g. Jio Fiber, AWS Bill"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                    @error('name') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Purpose -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Purpose</label>
                    <input type="text" name="purpose" value="{{ old('purpose', $recharge->purpose) }}"
                           placeholder="Optional purpose"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                    @error('purpose') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Billing Cycle -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Billing Cycle</label>
                    <select name="duration_months" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                        <option value="1" {{ old('duration_months', $recharge->duration_months) == 1 ? 'selected' : '' }}>Monthly</option>
                        <option value="3" {{ old('duration_months', $recharge->duration_months) == 3 ? 'selected' : '' }}>Quarterly</option>
                        <option value="6" {{ old('duration_months', $recharge->duration_months) == 6 ? 'selected' : '' }}>Half-Yearly</option>
                        <option value="12" {{ old('duration_months', $recharge->duration_months) == 12 ? 'selected' : '' }}>Yearly</option>
                    </select>
                    @error('duration_months') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Payment Type -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Payment Type</label>
                    <select name="payment_type" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                        <option value="Manual" {{ old('payment_type', $recharge->payment_type) == 'Manual' ? 'selected' : '' }}>Manual</option>
                        <option value="Auto Pay" {{ old('payment_type', $recharge->payment_type) == 'Auto Pay' ? 'selected' : '' }}>Auto Pay</option>
                    </select>
                    @error('payment_type') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Billing Day -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Billing Day</label>
                    <select name="billing_day" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                        @for($i=1; $i<=31; $i++)
                            <option value="{{ $i }}" {{ old('billing_day', $recharge->last_date->day) == $i ? 'selected' : '' }}>{{ $i }}{{ in_array($i, [1, 21, 31]) ? 'st' : (in_array($i, [2, 22]) ? 'nd' : (in_array($i, [3, 23]) ? 'rd' : 'th')) }} of the month</option>
                        @endfor
                    </select>
                    <p class="text-[10px] text-slate-400 font-medium ml-1">On this day, the bill will automatically generate.</p>
                </div>

                <!-- Amount -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Amount</label>
                    <input type="number" step="0.01" name="amount" required value="{{ old('amount', $recharge->amount) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                    @error('amount') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Mode of Payment -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Mode of Payment</label>
                    <input type="text" name="mode" value="{{ old('mode', $recharge->mode) }}" placeholder="e.g. Credit Card, UPI"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                    @error('mode') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Details -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Details</label>
                <textarea name="details" rows="3"
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium resize-none leading-relaxed">{{ old('details', $recharge->details) }}</textarea>
                @error('details') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Update Recharge
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
