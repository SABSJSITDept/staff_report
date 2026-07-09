@extends('layouts.app')
@section('title', 'Sanyojak - List')

@section('content')

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-96 pointer-events-none"></div>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600 font-medium">Sanyojak Management</span>
</nav>

<div class="card-premium !p-0 overflow-hidden mb-8">
    <div class="gradient-bg px-8 py-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Sanyojak Directory</h1>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sanyojak.export') }}" class="btn-primary !bg-green-600 !text-white px-6 py-3 hover:!bg-green-700 transition">
                Export to Excel
            </a>
            <a href="{{ route('sanyojak.create') }}" class="btn-primary !bg-white !text-indigo-600 px-6 py-3">
                Register Sanyojak
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Pravarti</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Staff Assigned</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="sanyojak-table-body" class="divide-y divide-gray-50">
                <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const API_BASE = '/api/sanyojaks';
    const HEADERS = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    };

    function showToast(message, type = 'success') {
        const colors = { success: 'bg-green-500', error: 'bg-red-500' };
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-5 py-3 rounded-xl shadow-lg`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    async function fetchSanyojaks() {
        try {
            const response = await fetch(API_BASE, { headers: HEADERS });
            const res = await response.json();
            const tbody = document.getElementById('sanyojak-table-body');
            
            if (res.success && res.data.length > 0) {
                tbody.innerHTML = res.data.map((s, index) => `
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-400">${index + 1}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">${s.name}</td>
                        <td class="px-4 py-3 text-gray-600">${s.pravarti || '—'}</td>
                        <td class="px-4 py-3 text-gray-600">${s.email}</td>
                        <td class="px-4 py-3 text-gray-600">
                            ${s.type === 'karyalay_sanyojak' ? '<span class="text-xs font-bold text-indigo-600">Karyalay Sanyojak</span>' : '<span class="text-xs text-gray-500">Sanyojak</span>'}
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            <span class="bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded text-xs font-medium">
                                ${s.type === 'karyalay_sanyojak' ? 'All Staff (Global)' : (s.staff_assigned ? s.staff_assigned.length : 0) + ' Assigned'}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="/sanyojak/create?id=${s.id}" class="text-indigo-500 hover:text-indigo-700 mx-2">Edit</a>
                            <button onclick="deleteSanyojak(${s.id})" class="text-red-500 hover:text-red-700 mx-2">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No Sanyojaks found</td></tr>`;
            }
        } catch (e) {
            document.getElementById('sanyojak-table-body').innerHTML = `<tr><td colspan="7" class="px-5 py-8 text-center text-red-500">Failed to load data</td></tr>`;
        }
    }

    async function deleteSanyojak(id) {
        if(!confirm('Are you sure you want to delete this Sanyojak?')) return;
        try {
            const res = await fetch(`${API_BASE}/${id}`, {
                method: 'DELETE',
                headers: HEADERS
            });
            const data = await res.json();
            if(data.success) {
                showToast(data.message);
                fetchSanyojaks();
            } else {
                showToast('Failed to delete', 'error');
            }
        } catch (e) {
            showToast('Network error', 'error');
        }
    }

    window.addEventListener('DOMContentLoaded', fetchSanyojaks);
</script>
@endpush
