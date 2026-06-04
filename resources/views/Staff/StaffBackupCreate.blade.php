@extends('layouts.app')

@section('title', 'Log Daily Backup')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 animate-fade-in">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-4">
                <span class="w-1.5 h-8 bg-indigo-600 rounded-full"></span>
                Log Daily Backup
            </h2>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-5">Log your daily backup status to ensure data safety.</p>
        </div>
        <div>
            <a href="{{ route('staff.daily-backup.index') }}" class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold py-2.5 px-6 rounded-xl shadow-sm transition-all text-sm">
                View History
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-500 text-white px-6 py-3 rounded-2xl mb-6 flex items-center justify-between shadow-xl shadow-red-500/10">
        <span class="font-bold uppercase text-[10px] tracking-widest italic">{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="opacity-40 hover:opacity-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>
    </div>
    @endif

    <!-- Submission Form -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden mb-10">
        <div class="bg-slate-50 px-8 py-5 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">Today's Entry ({{ now()->format('d M Y') }})</h3>
        </div>
        <form action="{{ route('staff.daily-backup.store') }}" method="POST" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Backup Taken?</label>
                    <select name="status" id="status" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 border font-medium" required onchange="toggleLocation()">
                        <option value="">-- Select Status --</option>
                        <option value="YES" {{ ($todayBackup && $todayBackup->status == 'YES') ? 'selected' : '' }}>YES</option>
                        <option value="NO" {{ ($todayBackup && $todayBackup->status == 'NO') ? 'selected' : '' }}>NO</option>
                    </select>
                </div>

                <div id="location-container" style="{{ ($todayBackup && $todayBackup->status == 'YES') ? '' : 'display: none;' }}">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Backup Location (Hold Ctrl to select multiple)</label>
                    @php
                        $selectedLocations = $todayBackup && $todayBackup->location ? array_map('trim', explode(',', $todayBackup->location)) : [];
                    @endphp
                    <select name="location[]" id="location" multiple class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 border font-medium h-32">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->name }}" {{ in_array($loc->name, $selectedLocations) ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-slate-700 mb-2">Remark / Note</label>
                <textarea name="remark" rows="2" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3 border" placeholder="Any specific details...">{{ $todayBackup->remark ?? '' }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-600/30 transition-all active:scale-95">
                    {{ $todayBackup ? 'Update Entry' : 'Submit Entry' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleLocation() {
        const status = document.getElementById('status').value;
        const locationContainer = document.getElementById('location-container');
        const locationSelect = document.getElementById('location');
        
        if (status === 'YES') {
            locationContainer.style.display = 'block';
            locationSelect.setAttribute('required', 'required');
        } else {
            locationContainer.style.display = 'none';
            locationSelect.removeAttribute('required');
            locationSelect.value = '';
        }
    }
</script>
@endsection
