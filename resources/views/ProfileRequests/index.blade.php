@extends('layouts.app')
@section('title', 'Profile Update Requests')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Profile Update Requests</h2>
    <p class="text-gray-500 text-sm mt-1">Review and approve staff profile update requests.</p>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    @if($requests->count() > 0)
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Staff Name</th>
                <th class="px-4 py-3">Office</th>
                <th class="px-4 py-3">Requested Updates</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($requests as $req)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $req->staff->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $req->staff->office->name ?? 'N/A' }}</td>
                <td class="px-4 py-3">
                    <div class="space-y-3">
                        @foreach($req->requested_data as $key => $newValue)
                            @php
                                $displayKey = ucfirst(str_replace('_', ' ', $key));
                                $oldValue = $req->staff->$key;
                            @endphp
                            <div class="border-l-2 border-indigo-100 pl-2 py-1">
                                <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">{{ $displayKey }}</div>
                                <div class="flex items-center gap-4 mt-1">
                                    {{-- Old Value --}}
                                    <div class="flex-1">
                                        <div class="text-[9px] text-gray-400">Current:</div>
                                        @if($key === 'photo')
                                            <img src="{{ $oldValue ? asset($oldValue) : asset('assets/images/default-user.png') }}" class="w-10 h-10 rounded-md object-cover border border-gray-100 opacity-60" alt="Current Photo">
                                        @else
                                            <div class="text-xs text-gray-400 line-through truncate max-w-[150px]">{{ $oldValue ?? 'N/A' }}</div>
                                        @endif
                                    </div>
                                    
                                    <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>

                                    {{-- New Value --}}
                                    <div class="flex-1">
                                        <div class="text-[9px] text-indigo-500 font-medium">Requested:</div>
                                        @if($key === 'photo')
                                            <img src="{{ asset($newValue) }}" class="w-10 h-10 rounded-md object-cover border border-indigo-200 shadow-sm" alt="Requested Photo">
                                        @else
                                            <div class="text-xs text-indigo-700 font-bold truncate max-w-[150px]">{{ $newValue }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $req->created_at->format('d M Y h:i A') }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <form action="{{ route('profile.requests.approve', $req->id) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded shadow-sm transition">Approve</button>
                    </form>
                    <form action="{{ route('profile.requests.reject', $req->id) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded shadow-sm transition">Reject</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="p-8 text-center text-gray-500">
        No pending profile update requests found.
    </div>
    @endif
</div>
@endsection
