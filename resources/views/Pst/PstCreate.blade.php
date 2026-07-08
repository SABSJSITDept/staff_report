@extends('layouts.app')
@section('title', 'PST - Add / Edit')

@section('content')

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-96 pointer-events-none"></div>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('pst.view') }}" class="hover:text-indigo-600 transition">PST Users</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600" id="breadcrumb-label">New PST User</span>
</nav>

<div class="space-y-8 pb-12 max-w-4xl mx-auto">
    <div class="card-premium !p-0 overflow-hidden">
        <div class="gradient-bg px-8 py-7 flex items-center justify-between bg-indigo-600 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight" id="form-title">PST Registration</h2>
                    <p class="text-indigo-100/70 text-[10px] font-bold uppercase tracking-[0.2em] mt-0.5">Report Viewer Assignment</p>
                </div>
            </div>
            <a href="{{ route('pst.view') }}" class="btn-secondary !bg-white/10 !text-white !border-white/20 hover:!bg-white/20 px-6 py-2.5 text-xs rounded-lg transition border">
                View Directory
            </a>
        </div>

        <form id="pst-form" novalidate class="px-7 py-7 bg-white rounded-b-xl shadow-sm border border-t-0 border-gray-100">
            <input type="hidden" id="pst-id" value="">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                {{-- Name --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" required placeholder="PST Name" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" />
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" required placeholder="email@example.com" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" />
                </div>

                {{-- Password --}}
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Password <span class="text-rose-500" id="password-req">*</span>
                    </label>
                    <input type="password" id="password" placeholder="Enter password" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" />
                    <p class="text-[10px] text-gray-400 mt-1" id="password-help">Leave blank to keep current password when editing.</p>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-slate-100 flex justify-end gap-4">
                <button type="button" onclick="resetForm()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Reset</button>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-lg" id="submit-label">Save PST User</button>
            </div>
        </form>
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

    async function loadEditData(id) {
        try {
            const res = await fetch(`${API_BASE}/${id}`, { headers: HEADERS });
            const data = await res.json();
            if(data.success) {
                const s = data.data;
                document.getElementById('pst-id').value = s.id;
                document.getElementById('name').value = s.name;
                document.getElementById('email').value = s.email;
                document.getElementById('password-req').style.display = 'none';
                document.getElementById('form-title').textContent = 'Edit PST User';
                document.getElementById('submit-label').textContent = 'Update PST User';
            }
        } catch(e) {
            showToast('Failed to load data', 'error');
        }
    }

    function resetForm() {
        document.getElementById('pst-form').reset();
        document.getElementById('pst-id').value = '';
        document.getElementById('password-req').style.display = 'inline';
        document.getElementById('form-title').textContent = 'PST Registration';
        document.getElementById('submit-label').textContent = 'Save PST User';
    }

    document.getElementById('pst-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('pst-id').value;
        const isEdit = !!id;

        const payload = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
        };

        const password = document.getElementById('password').value;
        if(password) {
            payload.password = password;
        } else if(!isEdit) {
            showToast('Password is required', 'error');
            return;
        }

        try {
            const url = isEdit ? `${API_BASE}/${id}` : API_BASE;
            const method = isEdit ? 'PUT' : 'POST';
            
            const res = await fetch(url, {
                method: method,
                headers: HEADERS,
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if(data.success) {
                showToast(data.message, 'success');
                if(!isEdit) resetForm();
                setTimeout(() => window.location.href = "{{ route('pst.view') }}", 1000);
            } else {
                showToast(data.message || 'Error occurred', 'error');
            }
        } catch(e) {
            showToast('Network error', 'error');
        }
    });

    window.addEventListener('DOMContentLoaded', async () => {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        if(id) {
            await loadEditData(id);
        }
    });
</script>
@endpush
