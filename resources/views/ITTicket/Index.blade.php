@extends('layouts.app')

@section('title', 'IT Troubleshooting - Elite Command Center')

@section('content')
<div class="space-y-10 animate-fade-in pb-20">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 bg-white p-10 rounded-[3rem] border border-slate-100 shadow-2xl shadow-indigo-100/50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full -mr-32 -mt-32 blur-3xl opacity-60"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-900 text-white rounded-full mb-4 border border-slate-800">
                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></span>
                <span class="text-[9px] font-black uppercase tracking-[0.2em]">Live Support Dashboard</span>
            </div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight leading-none">IT Support Hub</h1>
            <p class="text-slate-500 mt-3 font-medium max-w-md">Streamline your technical workflow and track resolution progress in real-time.</p>
        </div>

        <div class="relative z-10 flex flex-wrap gap-4">
            <a href="{{ route('it-tickets.create') }}" 
               class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-[0.15em] rounded-[1.5rem] shadow-xl shadow-indigo-100 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 gap-3 group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Raise Ticket
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500 text-white px-6 py-4 rounded-3xl flex items-center gap-3 animate-slide-in shadow-xl shadow-emerald-100">
        <div class="p-1.5 bg-white/20 rounded-lg">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        </div>
        <p class="font-bold text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Data Table Card -->
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-10 py-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Ticket Information</th>
                        @if(Auth::user()->canAccessIT())
                        <th class="px-10 py-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Requester</th>
                        @endif
                        <th class="px-10 py-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Domain</th>
                        <th class="px-10 py-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Current State</th>
                        <th class="px-10 py-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">SLA Window</th>
                        <th class="px-10 py-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-slate-50/50 transition-all duration-300 group">
                        <td class="px-10 py-6">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-black text-slate-400 font-mono tracking-tighter">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-base font-black text-slate-900 group-hover:text-indigo-600 transition-colors leading-tight">{{ $ticket->subject }}</span>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $ticket->created_at->format('d M, h:i A') }}</span>
                                    @if($ticket->photos && count($ticket->photos) > 0)
                                        <span class="inline-flex items-center gap-1.5 text-[9px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-lg font-black uppercase tracking-wider border border-indigo-100/50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ count($ticket->photos) }} Assets
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @if(Auth::user()->canAccessIT())
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-slate-900 flex items-center justify-center text-white font-black text-xs shadow-lg group-hover:rotate-6 transition-transform">
                                    {{ substr($ticket->staff->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-800">{{ $ticket->staff->name }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $ticket->staff->staff->department->name ?? 'Dept' }}</span>
                                </div>
                            </div>
                        </td>
                        @endif
                        <td class="px-10 py-6">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-[1rem] text-[10px] font-black uppercase tracking-widest border-2 {{ $ticket->category === 'Hardware' ? 'bg-orange-50 text-orange-600 border-orange-100 shadow-orange-50' : 'bg-blue-50 text-blue-600 border-blue-100 shadow-blue-50' }} shadow-lg">
                                {{ $ticket->category }}
                            </span>
                        </td>
                        <td class="px-10 py-6">
                            @php
                                $statusClasses = [
                                    'Pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                                    'In Progress' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                                    'Completed' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                    'Paused' => 'bg-slate-50 text-slate-600 border-slate-200',
                                ];
                                $cls = $statusClasses[$ticket->status] ?? 'bg-gray-50 text-gray-600';
                            @endphp
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-[0.15em] border-2 {{ $cls }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                {{ $ticket->status }}
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            @if($ticket->expected_arrival_time)
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-black text-slate-800 tracking-tight">{{ $ticket->expected_arrival_time->format('d M, h:i A') }}</span>
                                    @if($ticket->itStaff)
                                        <span class="text-[9px] font-bold text-indigo-500 uppercase tracking-widest flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ $ticket->itStaff->name }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-300 text-xs font-black uppercase tracking-widest italic opacity-50">Not Scheduled</span>
                            @endif
                        </td>
                        <td class="px-10 py-6">
                            <a href="{{ route('it-tickets.show', $ticket->id) }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-100 text-slate-800 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300 shadow-sm active:scale-95 group/btn">
                                Intelligence
                                <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->canAccessIT() ? 6 : 5 }}" class="px-10 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-6">
                                <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center text-slate-200 border-4 border-dashed border-slate-100">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-slate-400 font-black text-lg tracking-tight uppercase">Operational Silence</p>
                                    <p class="text-slate-300 text-sm font-medium mt-1">No active support tickets found in the system.</p>
                                </div>
                                <a href="{{ route('it-tickets.create') }}" 
                                   class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all">
                                    Initialize First Ticket
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes slide-in {
    from { transform: translateX(-10px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.animate-fade-in { animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-slide-in { animation: slide-in 0.5s ease-out; }
</style>
@endsection
