@extends('layouts.app')

@section('title', 'Allotment History')

@section('content')
<!-- Header Section -->
<div class="mb-8 relative overflow-hidden bg-gradient-to-r from-slate-800 to-indigo-900 rounded-3xl p-8 text-white shadow-xl">
    <div class="relative z-10">
        <h2 class="text-3xl font-extrabold tracking-tight">Allotment History</h2>
        <p class="text-indigo-100 mt-2 text-sm max-w-2xl">View all IT assets and resources distributed to staff members. Access detailed history, track temporary allotments, and manage returns.</p>
    </div>
    <div class="absolute -right-10 -top-10 opacity-20 transform rotate-12">
        <svg class="w-64 h-64" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-xl shadow-sm" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Staff Allotment Summary
        </h3>
        <div class="flex gap-2">
            <a href="{{ route('stock-management.allotments.export-pdf') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg text-xs font-bold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                PDF
            </a>
            <a href="{{ route('stock-management.allotments.export-excel') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border border-emerald-200 rounded-lg text-xs font-bold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Excel
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100">
                    <th class="p-4 pl-6 text-xs font-bold text-gray-500 uppercase tracking-wider w-16">#</th>
                    <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Staff Details</th>
                    <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Total Items</th>
                    <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Active Items</th>
                    <th class="p-4 pr-6 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($staffsWithAllotments as $index => $staff)
                @php
                    $totalItems = $staff->stockAllotments->count();
                    $activeItems = $staff->stockAllotments->where('status', '!=', 'Returned')->count();
                @endphp
                <tr class="hover:bg-indigo-50/30 transition-colors group">
                    <td class="p-4 pl-6 align-middle text-sm font-bold text-gray-400">
                        {{ $index + 1 }}
                    </td>
                    <td class="p-4 align-middle">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm border border-indigo-200/50">
                                {{ substr($staff->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-800">{{ $staff->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $staff->designation ?? 'Staff Member' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 align-middle text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gray-100 text-gray-800 font-bold text-sm border border-gray-200">
                            {{ $totalItems }}
                        </span>
                    </td>
                    <td class="p-4 align-middle text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl {{ $activeItems > 0 ? 'bg-indigo-50 text-indigo-700 border-indigo-200 border' : 'bg-gray-50 text-gray-400' }} font-bold text-sm">
                            {{ $activeItems }}
                        </span>
                    </td>
                    <td class="p-4 pr-6 align-middle text-center">
                        <button onclick="openStaffModal({{ $staff->id }})" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 hover:shadow-sm transition-all group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            View Details
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-16 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <p class="text-gray-500 font-medium">No allotment history found.</p>
                        <a href="{{ route('stock-management.allotments.index') }}" class="inline-block mt-3 text-sm text-indigo-600 font-bold hover:text-indigo-800">Allot items to a staff member &rarr;</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Render Modals for Each Staff -->
@foreach($staffsWithAllotments as $staff)
<div id="staffModal_{{ $staff->id }}" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[100] transition-opacity">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg shadow-sm border border-indigo-200">
                        {{ substr($staff->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $staff->name }}'s Allotments</h3>
                        <p class="text-sm text-gray-500">{{ $staff->designation ?? 'Staff Member' }}</p>
                    </div>
                </div>
                <button onclick="closeStaffModal({{ $staff->id }})" class="p-2 text-gray-400 hover:text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto bg-slate-50/30 flex-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100">
                                    <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Info</th>
                                    <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Item Details</th>
                                    <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="p-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($staff->stockAllotments as $allot)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="p-4 align-top">
                                        <div class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($allot->allotment_date)->format('d M, Y') }}</div>
                                        <div class="mt-1.5 flex flex-wrap gap-1">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $allot->allotment_type == 'Temporary' ? 'bg-orange-50 text-orange-600 border-orange-200' : 'bg-blue-50 text-blue-600 border-blue-200' }}">
                                                {{ $allot->allotment_type }}
                                            </span>
                                            @if($allot->return_date)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border bg-red-50 text-red-600 border-red-200" title="Expected Return Date">
                                                Till: {{ \Carbon\Carbon::parse($allot->return_date)->format('d M') }}
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4 align-top">
                                        <div class="text-sm font-semibold text-gray-800">{{ $allot->brand->item->name ?? 'Deleted Item' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $allot->brand->name ?? 'N/A' }}</div>
                                        <div class="text-[10px] font-medium text-indigo-500 mt-0.5">{{ $allot->brand->item->category->name ?? 'N/A' }}</div>
                                        @if($allot->remark)
                                            <div class="text-xs text-gray-400 mt-1.5 italic w-48 truncate" title="{{ $allot->remark }}">"{{ $allot->remark }}"</div>
                                        @endif
                                    </td>
                                    <td class="p-4 align-top">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 text-gray-800 font-bold text-sm">
                                            {{ $allot->quantity }}
                                        </span>
                                    </td>
                                    <td class="p-4 align-top">
                                        @if($allot->status === 'Returned')
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                                Returned
                                            </div>
                                            @if($allot->returned_date)
                                            <div class="text-[10px] text-gray-400 mt-1.5 font-medium ml-1">{{ \Carbon\Carbon::parse($allot->returned_date)->format('d M, y') }}</div>
                                            @endif
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold">
                                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                                                Active
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4 align-top text-center">
                                        <div class="flex items-center justify-center gap-2 opacity-100 sm:opacity-50 group-hover:opacity-100 transition-opacity">
                                            @if($allot->status !== 'Returned')
                                            <form action="{{ route('stock-management.allotments.return', $allot->id) }}" method="POST" onsubmit="return confirm('Confirm returning this item to normal inventory?');" class="inline">
                                                @csrf
                                                <input type="hidden" name="return_type" value="inventory">
                                                <button type="submit" title="Return to Inventory" class="p-1.5 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 hover:border-emerald-200 rounded-lg transition-colors shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('stock-management.allotments.return', $allot->id) }}" method="POST" onsubmit="return confirm('Confirm moving this item to DUMP STOCK?');" class="inline ml-1">
                                                @csrf
                                                <input type="hidden" name="return_type" value="dump">
                                                <button type="submit" title="Move to Dump Stock" class="p-1.5 text-orange-600 bg-orange-50 hover:bg-orange-100 border border-orange-100 hover:border-orange-200 rounded-lg transition-colors shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                                </button>
                                            </form>
                                            @endif
                                            
                                            <button onclick="editAllotment({{ json_encode($allot) }})" title="Edit Details" class="p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-100 hover:border-blue-200 rounded-lg transition-colors shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            
                                            <form action="{{ route('stock-management.allotments.destroy', $allot->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this allotment? If not returned, stock will be added back.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete Allotment" class="p-1.5 text-red-600 bg-red-50 hover:bg-red-100 border border-red-100 hover:border-red-200 rounded-lg transition-colors shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 shrink-0 flex justify-end">
                <button onclick="closeStaffModal({{ $staff->id }})" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl text-sm font-bold transition-colors">Close View</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Edit Allotment Modal (Shared) -->
<div id="editAllotmentModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-md overflow-y-auto h-full w-full hidden z-[200] transition-opacity">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 transform transition-all">
            <div class="absolute top-4 right-4">
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Allotment
            </h3>
            
            <form id="editAllotmentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Expected Return Date</label>
                        <input type="date" name="return_date" id="edit_return_date" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 block p-2.5 transition-all duration-200">
                    </div>
                    <div class="form-group">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Remark</label>
                        <textarea name="remark" id="edit_remark" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 block p-2.5 transition-all duration-200" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl text-sm font-bold transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-md shadow-indigo-200 transition-colors">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Staff View Modals
    function openStaffModal(staffId) {
        const modal = document.getElementById('staffModal_' + staffId);
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        setTimeout(() => {
            modal.querySelector('div').classList.add('opacity-100');
        }, 10);
    }

    function closeStaffModal(staffId) {
        const modal = document.getElementById('staffModal_' + staffId);
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }

    // Edit Allotment Modal (Inner Modal)
    function editAllotment(allotment) {
        const modal = document.getElementById('editAllotmentModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('div').classList.add('opacity-100');
        }, 10);
        
        document.getElementById('editAllotmentForm').action = '/stock-management/allotments/' + allotment.id;
        document.getElementById('edit_return_date').value = allotment.return_date ? allotment.return_date.split(' ')[0] : '';
        document.getElementById('edit_remark').value = allotment.remark || '';
    }

    function closeEditModal() {
        const modal = document.getElementById('editAllotmentModal');
        modal.classList.add('hidden');
    }
</script>
@endpush
