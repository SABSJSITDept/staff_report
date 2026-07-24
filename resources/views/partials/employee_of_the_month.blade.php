{{-- Employee of the Month --}}
@if(isset($featuredEmployee) && $featuredEmployee)
<div class="mb-8 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between border border-indigo-500">
    <div class="relative z-10 flex items-center gap-5 w-full">
        @if($featuredEmployee->staff && $featuredEmployee->staff->photo)
            <img src="{{ asset('storage/' . $featuredEmployee->staff->photo) }}" class="w-20 h-20 rounded-full border-4 border-white shadow-lg object-cover">
        @else
            <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl shadow-inner border-2 border-white/50">🏆</div>
        @endif
        <div class="flex-1">
            @if(Auth::check() && Auth::user()->staff && $featuredEmployee->staff_id == Auth::user()->staff->id)
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-gradient-to-r from-yellow-400 to-amber-500 text-yellow-900 text-xs font-extrabold rounded-full tracking-wider uppercase shadow-md mb-2">
                    <span>🌟 Congratulations! You are the Star of {{ date('F', mktime(0, 0, 0, $featuredEmployee->month, 1)) }}! 🌟</span>
                </div>
                <h3 class="font-extrabold text-3xl mt-1 text-transparent bg-clip-text bg-gradient-to-r from-white to-indigo-100">Outstanding Work, {{ Auth::user()->name }}!</h3>
                <p class="text-indigo-50 text-sm mt-2 leading-relaxed max-w-2xl border-l-4 border-yellow-400 pl-3 italic">
                    "{{ $featuredEmployee->description }}"
                </p>
            @else
                <span class="px-3 py-1 bg-yellow-400 text-yellow-900 text-xs font-extrabold rounded-full tracking-wider uppercase shadow-sm">
                    Employee of the Month - {{ date('F', mktime(0, 0, 0, $featuredEmployee->month, 1)) }} ({{ $featuredEmployee->office->name ?? '' }})
                </span>
                <h3 class="font-bold text-2xl mt-2">{{ $featuredEmployee->staff->name ?? '' }}</h3>
                <p class="text-indigo-100 text-sm mt-1 leading-relaxed max-w-2xl">
                    "{{ $featuredEmployee->description }}"
                </p>
            @endif
        </div>
    </div>
    
    {{-- Previous Winners for the Year --}}
    @if(isset($otherEmployees) && $otherEmployees->count() > 0)
    <div class="relative z-10 mt-6 md:mt-0 md:ml-6 pl-0 md:pl-6 border-t md:border-t-0 md:border-l border-white/20 w-full md:w-auto">
        <h4 class="text-xs text-indigo-200 font-semibold uppercase tracking-wider mb-3">Other Winners in {{ now()->year }}</h4>
        <div class="flex flex-col gap-2">
            @foreach($otherEmployees as $other)
                <div class="flex items-center gap-3 bg-white/10 rounded-lg p-2 border border-white/10 hover:bg-white/20 transition">
                    @if($other->staff && $other->staff->photo)
                        <img src="{{ asset('storage/' . $other->staff->photo) }}" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-indigo-800 flex items-center justify-center text-xs">🏅</div>
                    @endif
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold leading-tight">{{ $other->staff->name ?? '' }}</span>
                        <span class="text-[10px] text-indigo-200 uppercase">{{ date('M', mktime(0, 0, 0, $other->month, 1)) }} - {{ $other->office->name ?? '' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="absolute -right-10 -bottom-10 opacity-10 transform -rotate-12 scale-150 pointer-events-none">
        <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
    </div>
</div>
@endif
