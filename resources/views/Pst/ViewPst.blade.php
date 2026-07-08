@extends('layouts.app')
@section('title', 'PST - Directory')

@section('content')

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-96 pointer-events-none"></div>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600" id="breadcrumb-label">PST Users</span>
</nav>

<div class="space-y-8 pb-12">
    <div class="card-premium !p-0 overflow-hidden">
        <div class="gradient-bg px-8 py-7 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">PST Directory</h2>
                    <p class="text-indigo-100/70 text-[10px] font-bold uppercase tracking-[0.2em] mt-0.5">Report Viewers</p>
                </div>
            </div>
            <a href="{{ route('pst.create') }}" class="btn-secondary !bg-white/10 !text-white !border-white/20 hover:!bg-white/20 px-6 py-2.5 text-xs">
                Add New PST User
            </a>
        </div>

        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-[10px] text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 font-bold border-b border-slate-100 rounded-tl-xl">ID</th>
                            <th class="px-6 py-4 font-bold border-b border-slate-100">Name</th>
                            <th class="px-6 py-4 font-bold border-b border-slate-100">Email</th>
                            <th class="px-6 py-4 font-bold border-b border-slate-100 text-right rounded-tr-xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pst-table-body" class="divide-y divide-slate-100 text-slate-600">
                        <!-- Data injected via JS -->
                    </tbody>
                </table>
            </div>
            
            <div id="empty-state" class="hidden py-16 flex flex-col items-center justify-center bg-slate-50/50">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-700">No PST Users Found</h3>
                <p class="text-xs text-slate-400 mt-1">Get started by creating a new PST user.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const API_BASE = '/api/psts';
    const HEADERS = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
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

    async function fetchData() {
        try {
            const res = await fetch(API_BASE, { headers: HEADERS });
            const data = await res.json();
            
            if(data.success) {
                renderTable(data.data);
            }
        } catch(e) {
            showToast('Failed to load data', 'error');
        }
    }

    function renderTable(psts) {
        const tbody = document.getElementById('pst-table-body');
        const emptyState = document.getElementById('empty-state');
        
        tbody.innerHTML = '';
        
        if(psts.length === 0) {
            emptyState.classList.remove('hidden');
            return;
        }
        
        emptyState.classList.add('hidden');
        
        psts.forEach((p, index) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/80 transition group';
            tr.innerHTML = `
                <td class="px-6 py-4 text-xs font-bold text-slate-400">#${p.id}</td>
                <td class="px-6 py-4 font-semibold text-slate-700">${p.name}</td>
                <td class="px-6 py-4 text-slate-500">${p.email}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('pst.create') }}?id=${p.id}" class="p-1.5 text-indigo-500 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </a>
                        <button onclick="deletePst(${p.id})" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    async function deletePst(id) {
        if(!confirm('Are you sure you want to delete this PST user?')) return;
        
        try {
            const res = await fetch(`${API_BASE}/${id}`, {
                method: 'DELETE',
                headers: HEADERS
            });
            const data = await res.json();
            
            if(data.success) {
                showToast(data.message, 'success');
                fetchData();
            } else {
                showToast(data.message || 'Error deleting', 'error');
            }
        } catch(e) {
            showToast('Network error', 'error');
        }
    }

    window.addEventListener('DOMContentLoaded', fetchData);
</script>
@endpush
