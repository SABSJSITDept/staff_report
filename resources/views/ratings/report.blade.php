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
            <p class="text-sm text-gray-500 mt-1">View ratings given to staff members.</p>
        </div>
        
        <div class="flex items-center gap-3 flex-wrap">
            <form method="GET" action="{{ route('ratings.report') }}" class="flex gap-2">
                <select name="office_id" class="form-input-modern border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-700 bg-white" onchange="this.form.submit()">
                    <option value="">All Offices</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}
                        </option>
                    @endforeach
                </select>

                <select name="staff_id" class="form-input-modern border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-700 bg-white">
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
        </div>
    </div>

    {{-- Ratings Reports --}}
    @forelse($groupedData as $staffId => $staffData)
    @php
        $totalStaffSum = 0;
        $totalStaffCount = 0;
        foreach($staffData['categories'] as $catData) {
            foreach($catData['questions'] as $qData) {
                foreach($staffData['raters'] as $rater) {
                    $r = $qData['ratings_by_rater'][$rater] ?? null;
                    if ($r) {
                        $totalStaffSum += $r['rating'];
                        $totalStaffCount++;
                    }
                }
            }
        }
        $overallAvg = $totalStaffCount > 0 ? round($totalStaffSum / $totalStaffCount, 1) : '-';
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Staff: <span class="text-indigo-700">{{ $staffData['staff_name'] }}</span>
                    @if(!empty($staffData['financial_years']))
                        <span class="text-xs font-normal text-gray-500 bg-gray-200 px-2 py-0.5 rounded-md ml-2">Session: {{ implode(', ', $staffData['financial_years']) }}</span>
                    @endif
                </h3>
                <div class="bg-green-100 border border-green-200 text-green-800 px-3 py-1.5 rounded-lg flex items-center gap-2 shadow-sm font-bold">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    Overall Average: <span class="text-xl leading-none text-green-700">{{ $overallAvg }}</span>
                </div>
            </div>
            <div class="text-sm bg-white border border-gray-200 rounded-lg px-4 py-3 shadow-sm max-w-lg w-full md:w-auto">
                <div class="text-gray-500 font-bold mb-2 uppercase text-[10px] tracking-wider border-b border-gray-100 pb-1">Overall Remarks</div> 
                <div class="space-y-1.5 max-h-32 overflow-y-auto pr-2">
                    @forelse($staffData['overall_remarks'] as $rmk)
                        <div class="flex flex-col sm:flex-row sm:items-start gap-1">
                            <span class="font-bold text-gray-700 whitespace-nowrap">{{ $rmk['rater'] }}:</span> 
                            <span class="text-gray-600 italic">"{{ $rmk['remark'] }}"</span>
                        </div>
                    @empty
                        <div class="text-gray-400 italic">No overall remarks.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 border-r border-gray-200 min-w-[150px] w-1/5">Category</th>
                        <th class="px-4 py-3 border-r border-gray-200 min-w-[200px] w-1/3">Question</th>
                        @foreach($staffData['raters'] as $rater)
                            <th class="px-4 py-3 border-r border-gray-200 text-center bg-indigo-50/50 min-w-[100px]">{{ $rater }} (Rating)</th>
                            <th class="px-4 py-3 border-r border-gray-200 bg-indigo-50/50 min-w-[150px]">{{ $rater }} (Remark)</th>
                        @endforeach
                        <th class="px-4 py-3 text-center min-w-[80px]">Average</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php $sr = 1; @endphp
                    @foreach($staffData['categories'] as $categoryId => $catData)
                        @php 
                            $isFirstQuestionInCategory = true; 
                            $rowspan = count($catData['questions']);
                        @endphp
                        @foreach($catData['questions'] as $questionId => $qData)
                            <tr class="hover:bg-gray-50/50 transition bg-white">
                                @if($isFirstQuestionInCategory)
                                    <td rowspan="{{ $rowspan }}" class="px-4 py-3 border-r border-gray-200 font-bold text-gray-700 align-top bg-white">
                                        {{ $catData['category_name'] }}
                                    </td>
                                @endif
                                
                                <td class="px-4 py-3 border-r border-gray-200 text-gray-700 font-medium">
                                    <span class="text-gray-400 mr-1">{{ $sr++ }}.</span> {{ $qData['question_text'] }}
                                </td>

                                @php
                                    $sum = 0;
                                    $count = 0;
                                @endphp
                                @foreach($staffData['raters'] as $rater)
                                    @php
                                        $r = $qData['ratings_by_rater'][$rater] ?? null;
                                        if ($r) {
                                            $sum += $r['rating'];
                                            $count++;
                                        }
                                    @endphp
                                    <td class="px-4 py-3 border-r border-gray-200 text-center font-bold {{ $r ? 'text-indigo-600' : 'text-gray-300' }}">
                                        {{ $r ? $r['rating'] : '-' }}
                                    </td>
                                    <td class="px-4 py-3 border-r border-gray-200 text-xs text-gray-500 italic">
                                        {{ $r && $r['remark'] ? $r['remark'] : '-' }}
                                    </td>
                                @endforeach

                                <td class="px-4 py-3 text-center font-bold text-green-600 bg-gray-50/30">
                                    {{ $count > 0 ? round($sum / $count, 1) : '-' }}
                                </td>
                            </tr>
                            @php $isFirstQuestionInCategory = false; @endphp
                        @endforeach
                    @endforeach
                    
                    {{-- Overall Average Row --}}
                    <tr class="bg-green-50/50 border-t-2 border-green-100">
                        @php
                            $cols = 2 + (count($staffData['raters']) * 2);
                        @endphp
                        <td colspan="{{ $cols }}" class="px-4 py-4 text-right font-bold text-gray-700 uppercase tracking-widest text-xs">
                            Overall Average Rating (out of 5):
                        </td>
                        <td class="px-4 py-4 text-center font-bold text-green-700 text-lg bg-green-100/50">
                            {{ $overallAvg }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-800">No Ratings Found</h3>
        <p class="text-sm text-gray-500 mt-1">There are no ratings available for the selected filters.</p>
    </div>
    @endforelse
</div>

@endsection
