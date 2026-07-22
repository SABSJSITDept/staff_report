@php
    $today = now()->format('m-d');
    
    $todaysBirthdays = \App\Models\Staff\StaffModel::where('status', 'Active')
        ->whereNotNull('dob')
        ->get()
        ->filter(function($staff) use ($today) {
            return \Carbon\Carbon::parse($staff->dob)->format('m-d') === $today;
        });

    $todaysAnniversaries = \App\Models\Staff\StaffModel::where('status', 'Active')
        ->whereNotNull('doj')
        ->get()
        ->filter(function($staff) use ($today) {
            return \Carbon\Carbon::parse($staff->doj)->format('m-d') === $today;
        })->map(function($staff) {
            $staff->yearsOfService = \Carbon\Carbon::parse($staff->doj)->diffInYears(now());
            return $staff;
        });
@endphp

@if($todaysBirthdays->count() > 0 || $todaysAnniversaries->count() > 0)
<div class="grid grid-cols-1 {{ ($todaysBirthdays->count() > 0 && $todaysAnniversaries->count() > 0) ? 'md:grid-cols-2' : '' }} gap-4 mb-6">
    
    @if($todaysBirthdays->count() > 0)
    <div class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-xl shadow-inner">🎂</div>
                <h3 class="font-bold text-lg">Today's Birthdays</h3>
            </div>
            <div class="flex flex-col gap-3">
                @foreach($todaysBirthdays as $bdayStaff)
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20 hover:bg-white/20 transition">
                        @if($bdayStaff->photo)
                            <img src="{{ asset('storage/' . $bdayStaff->photo) }}" class="w-12 h-12 rounded-full object-cover border-2 border-white/50 shadow-sm" alt="{{ $bdayStaff->name }}">
                        @else
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-2xl shadow-inner">👤</div>
                        @endif
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-base">{{ $bdayStaff->name }}</span>
                                @if(Auth::check() && $bdayStaff->id === Auth::id())
                                    <span class="bg-white text-rose-500 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase shadow-sm">You</span>
                                @endif
                            </div>
                            <span class="text-xs text-white/90 mt-0.5">Wishing you a very Happy Birthday! 🎉 Have a great day.</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="absolute -right-6 -bottom-6 opacity-10 transform rotate-12 scale-150">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M19 15v2h2v2h-2v2h-2v-2h-2v-2h2v-2h2zM7 9a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 110-8 4 4 0 010 8zm10-2a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 110-8 4 4 0 010 8zM7 17a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 110-8 4 4 0 010 8zm10-2a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 110-8 4 4 0 010 8z"/></svg>
        </div>
    </div>
    @endif

    @if($todaysAnniversaries->count() > 0)
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-xl shadow-inner">🎊</div>
                <h3 class="font-bold text-lg">Work Anniversaries</h3>
            </div>
            <div class="flex flex-col gap-3">
                @foreach($todaysAnniversaries as $anniStaff)
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20 hover:bg-white/20 transition">
                        @if($anniStaff->photo)
                            <img src="{{ asset('storage/' . $anniStaff->photo) }}" class="w-12 h-12 rounded-full object-cover border-2 border-white/50 shadow-sm" alt="{{ $anniStaff->name }}">
                        @else
                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-2xl shadow-inner">💼</div>
                        @endif
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-base">{{ $anniStaff->name }}</span>
                                <span class="bg-white/20 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm">{{ $anniStaff->yearsOfService }} yr(s)</span>
                                @if(Auth::check() && $anniStaff->id === Auth::id())
                                    <span class="bg-white text-orange-500 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase shadow-sm">You</span>
                                @endif
                            </div>
                            <span class="text-xs text-white/90 mt-0.5">Happy Work Anniversary! Thank you for your dedication. 🚀</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="absolute -right-6 -bottom-6 opacity-10 transform rotate-12 scale-150">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.27 14.3H3.73L12 5.45zM11 11v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
        </div>
    </div>
    @endif

</div>
@endif
