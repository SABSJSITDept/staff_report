@extends('layouts.app')
@section('title', 'Rating Reports')

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="#" class="hover:text-indigo-600 transition">Dashboard</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600">Rating Reports</span>
</nav>

@include('partials.employee_of_the_month')

<div class="space-y-6 max-w-7xl mx-auto">
    {{-- Header & Filter --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Staff Rating Reports</h2>
            @if(request()->filled('office_id'))
                @php
                    $selectedOffice = $offices->firstWhere('id', request('office_id'));
                @endphp
                <p class="text-sm text-gray-500 mt-1">Viewing reports for office: <span class="font-bold text-indigo-600">{{ $selectedOffice->name ?? '' }}</span></p>
            @else
                <p class="text-sm text-gray-500 mt-1">Select an office below to view reports.</p>
            @endif
        </div>
        
        <div class="flex items-center gap-3 flex-wrap">
            @if(request()->filled('office_id'))
                <a href="{{ route('ratings.report') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium rounded-xl text-sm transition shadow-sm gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Offices
                </a>

                <form method="GET" action="{{ route('ratings.report') }}" class="flex gap-2">
                    <input type="hidden" name="office_id" value="{{ request('office_id') }}">
                    <select name="staff_id" class="form-input-modern border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-700 bg-white" onchange="this.form.submit()">
                        <option value="">All Staff</option>
                        @foreach($allStaff as $staff)
                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm">
                        Filter
                    </button>
                </form>

                <a href="{{ route('ratings.report.export-excel', ['staff_id' => request('staff_id'), 'office_id' => request('office_id')]) }}" class="bg-green-50 text-green-700 hover:bg-green-100 hover:text-green-800 border border-green-200 px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-1 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Export Excel
                </a>
            @endif
        </div>
    </div>

    {{-- Ratings Reports --}}
    @if(!request()->filled('office_id'))
        <div class="my-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($offices as $office)
                    <a href="{{ route('ratings.report', ['office_id' => $office->id]) }}" class="group bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-xl hover:border-indigo-500 hover:scale-[1.02] transition-all duration-300 flex flex-col items-center justify-center text-center cursor-pointer min-h-[160px]">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors duration-300 text-base leading-snug">{{ $office->name }}</h4>
                        <span class="text-xs text-slate-400 mt-2 bg-slate-50 px-3 py-1 rounded-full group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors duration-300 font-semibold uppercase tracking-wider">View Reports</span>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        @if(!empty($groupedData))
        @php
            // Calculate overall averages and sort staff descending
            $sortedGroupedData = collect($groupedData)->map(function($sData, $sId) {
                $sum = 0;
                $count = 0;
                foreach($sData['categories'] as $cat) {
                    foreach($cat['questions'] as $q) {
                        foreach($sData['raters'] as $rater) {
                            $r = $q['ratings_by_rater'][$rater] ?? null;
                            if ($r) {
                                $sum += $r['rating'];
                                $count++;
                            }
                        }
                    }
                }
                $sData['calculated_avg'] = $count > 0 ? round($sum / $count, 2) : 0;
                $sData['staff_id'] = $sId;
                return $sData;
            })->sortByDesc('calculated_avg')->values();
        @endphp

        {{-- Summary Table Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-indigo-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-100 font-bold">
                        🏆
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Staff Ratings Leaderboard</h3>
                        <p class="text-xs text-slate-500">Ranked by overall average rating score</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full w-fit">
                    Total Staff Rated: {{ count($sortedGroupedData) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-center border-r border-slate-200 w-28">Rank</th>
                            <th class="px-4 py-3 border-r border-slate-200">Staff Member</th>
                            <th class="px-4 py-3 text-center bg-emerald-50/60 border-r border-slate-200 text-emerald-800 min-w-[160px]">
                                Average Rating
                            </th>
                            <th class="px-4 py-3 text-center w-32">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sortedGroupedData as $index => $sData)
                            @php
                                $rank = $index + 1;
                                $staffAvg = $sData['calculated_avg'];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                {{-- Rank Position Badge --}}
                                <td class="px-4 py-3 text-center border-r border-slate-100 font-bold">
                                    @if($rank == 1)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-amber-300 via-amber-400 to-yellow-500 text-amber-950 border border-amber-300 shadow-sm gap-1">
                                            🥇 1st
                                        </span>
                                    @elseif($rank == 2)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-slate-200 via-slate-300 to-slate-400 text-slate-900 border border-slate-300 shadow-sm gap-1">
                                            🥈 2nd
                                        </span>
                                    @elseif($rank == 3)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-amber-700/20 via-amber-800/30 to-amber-900/40 text-amber-900 border border-amber-700/30 shadow-sm gap-1">
                                            🥉 3rd
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $rank }}th
                                        </span>
                                    @endif
                                </td>

                                {{-- Staff Member Name --}}
                                <td class="px-4 py-3 font-semibold text-slate-800 border-r border-slate-100">
                                    <button type="button" onclick="openStaffModal('{{ $sData['staff_id'] }}')" class="hover:text-indigo-600 text-left transition font-bold">
                                        {{ $sData['staff_name'] }}
                                    </button>
                                </td>

                                {{-- Overall Average Rating --}}
                                <td class="px-4 py-3 text-center border-r border-slate-100 bg-emerald-50/30">
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs">
                                        ⭐ {{ number_format($staffAvg, 1) }} / 5
                                    </span>
                                </td>

                                {{-- Details Action Button --}}
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="openStaffModal('{{ $sData['staff_id'] }}')" class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white rounded-xl transition shadow-2xs gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Rating Details Modal Dialog --}}
        <div id="staffRatingModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
            <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200">
                {{-- Modal Header --}}
                <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-indigo-500/30">
                            📋
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                Staff Rating Details
                            </h3>
                            <p class="text-xs text-slate-400">Complete category & question-wise rating card</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeStaffModal()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 overflow-y-auto space-y-6 flex-1 bg-slate-50/50">
                    @foreach($sortedGroupedData as $index => $staffData)
                        @php
                            $staffId = $staffData['staff_id'];
                            $overallAvg = $staffData['calculated_avg'];
                            $rank = $index + 1;
                        @endphp
                        <div id="modal-card-{{ $staffId }}" class="modal-staff-card hidden space-y-6">
                            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h4 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                                        👤 {{ $staffData['staff_name'] }}
                                        @if(!empty($staffData['financial_years']))
                                            <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 rounded-full">Session: {{ implode(', ', $staffData['financial_years']) }}</span>
                                        @endif
                                    </h4>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="bg-emerald-100 border border-emerald-200 text-emerald-900 px-4 py-2 rounded-xl flex items-center gap-2 font-black text-base shadow-2xs">
                                        <span class="text-xs font-bold text-emerald-700">Overall Average:</span> ⭐ {{ $overallAvg }} / 5
                                    </div>
                                </div>
                            </div>

                            {{-- Overall Remarks Card --}}
                            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                                <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Overall Remarks</h5>
                                <div class="space-y-2">
                                    @forelse($staffData['overall_remarks'] as $rmk)
                                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-start gap-2">
                                            <span class="font-bold text-slate-800 text-xs whitespace-nowrap bg-white px-2.5 py-1 rounded-md border border-slate-200 shadow-2xs">{{ $rmk['rater'] }}:</span>
                                            <span class="text-slate-700 text-sm italic">"{{ $rmk['remark'] }}"</span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400 italic">No overall remarks provided.</p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Detailed Question Table --}}
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left border-collapse">
                                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                            <tr>
                                                <th class="px-4 py-3 border-r border-slate-200 min-w-[150px] w-1/5">Category</th>
                                                <th class="px-4 py-3 border-r border-slate-200 min-w-[200px] w-1/3">Question</th>
                                                @foreach($staffData['raters'] as $rater)
                                                    <th class="px-4 py-3 border-r border-slate-200 text-center bg-indigo-50/50 min-w-[100px]">{{ $rater }} (Rating)</th>
                                                    <th class="px-4 py-3 border-r border-slate-200 bg-indigo-50/50 min-w-[150px]">{{ $rater }} (Remark)</th>
                                                @endforeach
                                                <th class="px-4 py-3 text-center min-w-[80px]">Average</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200">
                                            @php $sr = 1; @endphp
                                            @foreach($staffData['categories'] as $categoryId => $catData)
                                                @php 
                                                    $isFirstQuestionInCategory = true; 
                                                    $rowspan = count($catData['questions']);
                                                @endphp
                                                @foreach($catData['questions'] as $questionId => $qData)
                                                    <tr class="hover:bg-slate-50/50 transition bg-white">
                                                        @if($isFirstQuestionInCategory)
                                                            <td rowspan="{{ $rowspan }}" class="px-4 py-3 border-r border-slate-200 font-bold text-slate-700 align-top bg-white">
                                                                {{ $catData['category_name'] }}
                                                            </td>
                                                        @endif
                                                        
                                                        <td class="px-4 py-3 border-r border-slate-200 text-slate-700 font-medium">
                                                            <span class="text-slate-400 mr-1">{{ $sr++ }}.</span> {{ $qData['question_text'] }}
                                                        </td>

                                                        @php
                                                            $sum = 0;
                                                            $count = 0;
                                                            foreach($staffData['raters'] as $rater) {
                                                                $r = $qData['ratings_by_rater'][$rater] ?? null;
                                                                if ($r) {
                                                                    $sum += $r['rating'];
                                                                    $count++;
                                                                }
                                                            }
                                                        @endphp

                                                        @foreach($staffData['raters'] as $rater)
                                                            @php
                                                                $r = $qData['ratings_by_rater'][$rater] ?? null;
                                                            @endphp
                                                            <td class="px-4 py-3 border-r border-slate-200 text-center font-bold {{ $r ? 'text-indigo-600' : 'text-slate-300' }}">
                                                                {{ $r ? $r['rating'] : '-' }}
                                                            </td>
                                                            <td class="px-4 py-3 border-r border-slate-200 text-xs text-slate-500 italic">
                                                                {{ $r && $r['remark'] ? $r['remark'] : '-' }}
                                                            </td>
                                                        @endforeach

                                                        <td class="px-4 py-3 text-center font-bold text-emerald-600 bg-slate-50/30">
                                                            {{ $count > 0 ? round($sum / $count, 1) : '-' }}
                                                        </td>
                                                    </tr>
                                                        @php $isFirstQuestionInCategory = false; @endphp
                                                    @endforeach
                                                @endforeach
                                                
                                                <tr class="bg-emerald-50/60 border-t-2 border-emerald-100">
                                                    @php
                                                        $cols = 2 + (count($staffData['raters']) * 2);
                                                    @endphp
                                                    <td colspan="{{ $cols }}" class="px-4 py-4 text-right font-bold text-slate-700 uppercase tracking-widest text-xs border-r border-slate-200">
                                                        Overall Average Rating (out of 5):
                                                    </td>
                                                    <td class="px-4 py-4 text-center font-black text-emerald-800 text-base bg-emerald-100/80">
                                                        {{ $overallAvg }}
                                                    </td>
                                                </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-3 bg-slate-100 border-t border-slate-200 flex justify-end">
                    <button type="button" onclick="closeStaffModal()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>

<script>
function openStaffModal(staffId) {
    // Hide all modal cards
    document.querySelectorAll('.modal-staff-card').forEach(el => el.classList.add('hidden'));
    
    // Show selected staff modal card
    const targetCard = document.getElementById('modal-card-' + staffId);
    if (targetCard) {
        targetCard.classList.remove('hidden');
    }
    
    const modal = document.getElementById('staffRatingModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function closeStaffModal() {
    const modal = document.getElementById('staffRatingModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Close modal on backdrop click
document.getElementById('staffRatingModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeStaffModal();
    }
});

@if(request()->filled('staff_id'))
    document.addEventListener('DOMContentLoaded', function() {
        openStaffModal('{{ request('staff_id') }}');
    });
@endif
</script>

@endsection
