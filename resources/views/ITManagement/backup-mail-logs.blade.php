@extends('layouts.app')

@section('title', 'Mail Logs')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 animate-fade-in">

    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-4">
                <span class="w-1.5 h-8 bg-indigo-600 rounded-full"></span>
                Defaulter Mail Logs
            </h2>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-5">History of all emails sent to backup defaulters.</p>
        </div>
        <div>
            <a href="{{ route('it-management.backup-defaulters.index') }}"
               class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold py-2.5 px-6 rounded-xl shadow-sm transition-all text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Defaulters
            </a>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-2xl px-6 py-4 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $totalCount }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">
                    {{ request()->hasAny(['staff_id','date']) ? 'Filtered Results' : 'Total Mails Sent' }}
                </p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl px-6 py-4 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $logs->total() }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Matching Records</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl px-6 py-4 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-black text-slate-900">{{ now()->format('d M Y') }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Today's Date</p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm px-6 py-5 mb-6">
        <form method="GET" action="{{ route('it-management.backup-defaulters.mail-logs') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-wide mb-1.5">Filter by Staff</label>
                <select name="staff_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-slate-50">
                    <option value="">All Staff</option>
                    @foreach($staffList as $s)
                        <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-wide mb-1.5">Filter by Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-slate-50">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl text-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                    Apply Filter
                </button>
                @if(request()->hasAny(['staff_id','date']))
                <a href="{{ route('it-management.backup-defaulters.mail-logs') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 px-4 rounded-xl text-sm transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Active Filters Badge --}}
    @if(request()->hasAny(['staff_id','date']))
    <div class="mb-4 flex flex-wrap gap-2 items-center">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Active filters:</span>
        @if(request('staff_id'))
            @php $selectedStaff = $staffList->firstWhere('id', request('staff_id')); @endphp
            <span class="inline-flex items-center gap-1.5 bg-indigo-100 text-indigo-700 font-bold text-xs px-3 py-1.5 rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $selectedStaff->name ?? 'Unknown' }}
            </span>
        @endif
        @if(request('date'))
            <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 font-bold text-xs px-3 py-1.5 rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ \Carbon\Carbon::parse(request('date'))->format('d M Y') }}
            </span>
        @endif
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-auto">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-8 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Date &amp; Time</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Recipient (Staff)</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Sent By</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5 whitespace-nowrap text-sm font-bold text-slate-400">
                            {{ $logs->firstItem() + $loop->index }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="font-bold text-slate-800">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $log->created_at->format('h:i A') }}
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-black text-indigo-600">
                                        {{ strtoupper(substr($log->staff->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $log->staff->name ?? 'Unknown Staff' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $log->staff->email ?? 'No email' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-black text-slate-500">
                                        {{ strtoupper(substr($log->sender->name ?? 'S', 0, 1)) }}
                                    </span>
                                </div>
                                <span class="text-sm font-bold text-slate-600">
                                    {{ $log->sender->name ?? 'System' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-800 font-bold px-3 py-1.5 rounded-xl text-xs border border-green-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Mail Sent
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-16 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 text-indigo-500 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">No Mail Logs Found</h3>
                            <p class="text-slate-500 text-sm mt-1">
                                @if(request()->hasAny(['staff_id','date']))
                                    No records match your filter. <a href="{{ route('it-management.backup-defaulters.mail-logs') }}" class="text-indigo-600 underline">Clear filters</a>
                                @else
                                    No emails have been sent to backup defaulters yet.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between">
            <p class="text-sm text-slate-500">
                Showing <span class="font-bold text-slate-700">{{ $logs->firstItem() }}</span>–<span class="font-bold text-slate-700">{{ $logs->lastItem() }}</span>
                of <span class="font-bold text-slate-700">{{ $logs->total() }}</span> records
            </p>
            {{ $logs->links() }}
        </div>
        @else
            @if($logs->count() > 0)
            <div class="px-8 py-4 border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    Showing all <span class="font-bold text-slate-700">{{ $logs->total() }}</span> records
                </p>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
