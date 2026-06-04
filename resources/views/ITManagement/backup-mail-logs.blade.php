@extends('layouts.app')

@section('title', 'Mail Logs')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 animate-fade-in">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-4">
                <span class="w-1.5 h-8 bg-indigo-600 rounded-full"></span>
                Defaulter Mail Logs
            </h2>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-5">View history of emails sent to backup defaulters.</p>
        </div>
        <div>
            <a href="{{ route('it-management.backup-defaulters.index') }}" class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold py-2.5 px-6 rounded-xl shadow-sm transition-all text-sm">
                Back to Defaulters
            </a>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-auto">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-8 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Recipient (Staff)</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Sent By</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5 whitespace-nowrap">
                            <div class="font-bold text-slate-800">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $log->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="font-bold text-slate-800">{{ $log->staff->name ?? 'Unknown Staff' }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $log->staff->email ?? 'No email' }}</div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="text-sm font-bold text-slate-600">
                                {{ $log->sender->name ?? 'System/Unknown' }}
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="bg-green-100 text-green-800 font-bold px-3 py-1 rounded-lg text-xs border border-green-200">
                                Sent
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-16 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 text-indigo-500 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">No Mail Logs Found</h3>
                            <p class="text-slate-500 text-sm mt-1">No emails have been sent to backup defaulters yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-8 py-5 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
