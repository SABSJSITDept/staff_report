@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in pb-10">
    <!-- Compact Top Bar -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('it-tickets.index') }}" class="p-2 bg-white border rounded-xl hover:bg-slate-50 transition-all shadow-sm group">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ticket #{{ $itTicket->id }}</span>
                    @php
                        $statusColors = [
                            'Pending' => 'bg-amber-500',
                            'In Progress' => 'bg-indigo-600',
                            'Completed' => 'bg-emerald-500',
                            'Paused' => 'bg-slate-400',
                        ];
                        $bg = $statusColors[$itTicket->status] ?? 'bg-slate-400';
                    @endphp
                    <span class="w-1.5 h-1.5 rounded-full {{ $bg }} animate-pulse"></span>
                    <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $itTicket->status }}</span>
                </div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight leading-tight">{{ $itTicket->subject }}</h1>
            </div>
        </div>
        
        @if(Auth::user()->canAccessIT())
        <button onclick="document.getElementById('status-modal').classList.remove('hidden')" 
                class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-indigo-600 transition-all shadow-lg shadow-slate-100 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Update
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-bold animate-slide-in">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Main Panel -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Issue Info -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-[9px] font-black bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md uppercase tracking-wider">{{ $itTicket->category }} Issue</span>
                        <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-wrap">{{ $itTicket->issue_description }}</p>
                    </div>
                </div>

                @if($itTicket->photos && count($itTicket->photos) > 0)
                <div class="pt-4 border-t border-slate-50">
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                        @foreach($itTicket->photos as $photo)
                        <div class="aspect-square relative group rounded-lg overflow-hidden border border-slate-100 shadow-sm cursor-pointer" onclick="window.open('{{ asset('storage/' . $photo) }}', '_blank')">
                            <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Discussion -->
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Activity Stream</h3>
                
                <div class="space-y-4">
                    @forelse($itTicket->replies as $reply)
                    <div class="flex gap-4 {{ $reply->user_id === Auth::id() ? 'flex-row-reverse' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black shadow-sm
                                 {{ $reply->user->canAccessIT() ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-white' }}">
                                {{ substr($reply->user->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="space-y-1 max-w-[80%]">
                            <div class="flex items-center gap-2 {{ $reply->user_id === Auth::id() ? 'justify-end' : '' }}">
                                <span class="text-[10px] font-black text-slate-800">{{ $reply->user->name }}</span>
                                <span class="text-[9px] text-slate-400 font-bold">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="p-4 rounded-2xl shadow-sm text-sm {{ $reply->user_id === Auth::id() ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white border border-slate-200 text-slate-700 rounded-tl-none' }}">
                                <p class="leading-relaxed whitespace-pre-wrap">{{ $reply->message }}</p>
                                @if($reply->attachment)
                                <div class="mt-3 pt-3 border-t {{ $reply->user_id === Auth::id() ? 'border-white/10' : 'border-slate-50' }}">
                                    <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="inline-flex items-center gap-2 text-[10px] font-black uppercase {{ $reply->user_id === Auth::id() ? 'text-white' : 'text-indigo-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        View File
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <p class="text-xs font-bold text-slate-400 italic">No updates yet.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Quick Reply -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                    <form action="{{ route('it-tickets.reply', $itTicket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <textarea name="message" id="message" rows="2" required placeholder="Write a message..."
                                  class="w-full px-4 py-3 bg-slate-50 border rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition-all text-sm font-medium resize-none"></textarea>
                        <div class="flex items-center justify-between">
                            <label for="attachment" class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-600 text-[10px] font-black uppercase rounded-lg cursor-pointer hover:bg-slate-200 transition-all border border-slate-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span id="file-name">Attach</span>
                                <input type="file" name="attachment" id="attachment" class="sr-only" onchange="document.getElementById('file-name').innerText = this.files[0].name">
                            </label>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-black uppercase text-[10px] rounded-lg hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95">
                                Send Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Panel -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Requester Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Requester</h4>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black text-xs">
                        {{ substr($itTicket->staff->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-800 leading-none">{{ $itTicket->staff->name }}</p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1">{{ $itTicket->staff->staff->department->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Schedule Card -->
            <div class="bg-indigo-600 rounded-2xl p-5 text-white shadow-xl shadow-indigo-100 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-indigo-200">IT Visit</h4>
                    <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                
                @if($itTicket->expected_arrival_time)
                <div>
                    <p class="text-lg font-black tracking-tight leading-none">{{ $itTicket->expected_arrival_time->format('d M, h:i A') }}</p>
                    @if($itTicket->itStaff)
                    <p class="text-[10px] font-bold text-indigo-300 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Eng: {{ $itTicket->itStaff->name }}
                    </p>
                    @endif
                </div>
                @else
                <div class="py-2">
                    <p class="text-xs font-bold text-indigo-100 italic">Not scheduled yet.</p>
                    @if(Auth::user()->canAccessIT())
                    <button onclick="document.getElementById('assign-modal').classList.remove('hidden')" 
                            class="mt-3 w-full bg-white text-indigo-600 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-indigo-50 transition-all">
                        Schedule Now
                    </button>
                    @endif
                </div>
                @endif

                @if($itTicket->expected_arrival_time && Auth::user()->canAccessIT())
                <button onclick="document.getElementById('assign-modal').classList.remove('hidden')" 
                        class="w-full bg-white/10 text-white py-2 rounded-lg text-[10px] font-black uppercase border border-white/10 hover:bg-white/20 transition-all">
                    Reschedule
                </button>
                @endif
            </div>

            <!-- Remarks -->
            @if($itTicket->remarks)
            <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100 space-y-2">
                <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-widest">IT Remarks</h4>
                <p class="text-xs text-amber-900 font-bold leading-relaxed">{{ $itTicket->remarks }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Compact Modals -->
<div id="status-modal" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm animate-pop-in overflow-hidden border border-slate-100">
        <div class="p-6 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-black text-slate-900 uppercase">Update Status</h3>
                <button onclick="document.getElementById('status-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <form action="{{ route('it-tickets.update-status', $itTicket->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Current State</label>
                    <select name="status" required class="w-full px-4 py-2.5 bg-slate-50 border rounded-xl focus:bg-white focus:border-indigo-500 outline-none text-sm font-bold text-slate-700 appearance-none">
                        <option value="Pending" {{ $itTicket->status === 'Pending' ? 'selected' : '' }}>PENDING</option>
                        <option value="In Progress" {{ $itTicket->status === 'In Progress' ? 'selected' : '' }}>IN PROGRESS</option>
                        <option value="Completed" {{ $itTicket->status === 'Completed' ? 'selected' : '' }}>COMPLETED</option>
                        <option value="Paused" {{ $itTicket->status === 'Paused' ? 'selected' : '' }}>PAUSED</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Resolution Remark</label>
                    <textarea name="remarks" rows="3" placeholder="What's the update?"
                              class="w-full px-4 py-3 bg-slate-50 border rounded-xl focus:bg-white focus:border-indigo-500 outline-none resize-none text-sm font-medium">{{ $itTicket->remarks }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white font-black uppercase text-xs rounded-xl hover:bg-indigo-700 transition-all active:scale-95">Update Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="assign-modal" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm animate-pop-in overflow-hidden border border-slate-100">
        <div class="p-6 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-black text-slate-900 uppercase">Set Visit Time</h3>
                <button onclick="document.getElementById('assign-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <form action="{{ route('it-tickets.assign-time', $itTicket->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Expected Arrival</label>
                    <input type="datetime-local" name="expected_arrival_time" required 
                           min="{{ now()->format('Y-m-d\TH:i') }}"
                           value="{{ $itTicket->expected_arrival_time ? $itTicket->expected_arrival_time->format('Y-m-d\TH:i') : '' }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border rounded-xl focus:bg-white focus:border-indigo-500 outline-none text-sm font-bold text-slate-700">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white font-black uppercase text-xs rounded-xl hover:bg-indigo-700 transition-all active:scale-95">Schedule Visit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes pop-in { from { opacity: 0; transform: scale(0.9) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
@keyframes slide-in { from { transform: translateX(-10px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.animate-pop-in { animation: pop-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.animate-slide-in { animation: slide-in 0.4s ease-out; }
.animate-fade-in { animation: fade-in 0.5s ease-out; }
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
</style>
@endsection
