@extends('layouts.app')
@section('title', 'Department - Create / Edit')

@section('content')

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-96 pointer-events-none"></div>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('department.view') }}" class="hover:text-indigo-600 transition">Departments</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600" id="breadcrumb-label">New Department</span>
</nav>

<div class="flex flex-col lg:flex-row gap-8">

    {{-- Left: Form Card --}}
    <div class="flex-1 min-w-0">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-7 py-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-semibold text-base leading-tight" id="form-heading">New Department Add Karein</h2>
                        <p class="text-indigo-200 text-xs mt-0.5">All starred fields are required</p>
                    </div>
                </div>
                <a href="{{ route('department.view') }}"
                    class="flex items-center gap-1.5 text-xs font-medium text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to List
                </a>
            </div>

            {{-- Form --}}
            <form id="department-form" novalidate class="px-7 py-7">
                @csrf
                <input type="hidden" id="department-id" value="">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

                    {{-- Department Name --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Department Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="name" name="name"
                                placeholder="e.g. HUMAN RESOURCES"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm uppercase bg-gray-50
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                       transition placeholder:normal-case" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden flex items-center gap-1" id="err-name">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Status --}}
                    <div class="sm:col-span-2">
                        <label for="status" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <select id="status" name="status"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 appearance-none
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                <option value="">-- Select Status --</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden flex items-center gap-1" id="err-status">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- HOD --}}
                    <div class="sm:col-span-2">
                        <label for="hod_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Head of Department (HOD)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <select id="hod_id" name="hod_id"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 appearance-none
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                <option value="">-- No HOD Assigned --</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden flex items-center gap-1" id="err-hod_id">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                </div>

                {{-- Submit Button --}}
                <div class="flex gap-3 pt-2">
                    <button type="submit" id="submit-btn"
                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition disabled:opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="submit-label">Save Department</span>
                    </button>
                    <button type="button" onclick="resetForm()"
                        class="text-sm font-medium text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl border border-gray-200 hover:border-gray-300 transition">
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const API_BASE = '/api/departments';
    const HEADERS = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
    };

    // ─── Toast ────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const colors = { success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-yellow-500', info: 'bg-blue-500' };
        const icons  = { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' };
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 transition-all duration-300 pointer-events-auto`;
        toast.innerHTML = `
            <span class="text-lg font-bold">${icons[type]}</span>
            <span class="text-sm flex-1">${message}</span>
            <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white text-lg leading-none">&times;</button>
        `;
        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 4000);
    }

    // ─── Show/Clear field error ───────────────────────────────
    function setError(field, message) {
        const el = document.getElementById(`err-${field}`);
        if (!el) return;
        if (message) {
            el.classList.remove('hidden');
            el.querySelector('span').textContent = message;
        } else {
            el.classList.add('hidden');
            el.querySelector('span').textContent = '';
        }
    }
    
    function clearErrors() {
        ['name', 'status', 'hod_id'].forEach(f => setError(f, ''));
    }

    // ─── Auto-uppercase name input ────────────────────────────
    document.getElementById('name').addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });

    // ─── Reset Form ───────────────────────────────────────────
    function resetForm() {
        document.getElementById('department-form').reset();
        document.getElementById('department-id').value = '';
        document.getElementById('form-heading').textContent = 'New Department Add Karein';
        document.getElementById('breadcrumb-label').textContent = 'New Department';
        document.getElementById('submit-label').textContent = 'Save Department';
        document.getElementById('hod_id').value = '';
        clearErrors();
    }

    async function loadForEdit(id) {
        try {
            const res = await fetch(`${API_BASE}/${id}`, { headers: HEADERS });
            const data = await res.json();
            if (!data.success) { showToast('Department load nahi ho saka.', 'error'); return; }
            const d = data.data;
            document.getElementById('department-id').value = d.id;
            document.getElementById('name').value = d.name;
            document.getElementById('status').value = d.status;
            document.getElementById('hod_id').value = d.hod_id || '';
            document.getElementById('form-heading').textContent = 'Department Update Karein';
            document.getElementById('breadcrumb-label').textContent = 'Edit Department';
            document.getElementById('submit-label').textContent = 'Update Department';
        } catch {
            showToast('Server se connect nahi ho saka.', 'error');
        }
    }

    // ─── Load Staff List for HOD Dropdown ─────────────────────
    async function fetchStaffList() {
        try {
            const res = await fetch('/api/staff', { headers: HEADERS });
            const data = await res.json();
            if (data.success) {
                const hodSelect = document.getElementById('hod_id');
                // Keep the first option
                const firstOption = hodSelect.options[0];
                hodSelect.innerHTML = '';
                hodSelect.appendChild(firstOption);
                
                data.data.forEach(staff => {
                    const opt = document.createElement('option');
                    opt.value = staff.id;
                    opt.textContent = `${staff.name} (${staff.designation || 'Staff'})`;
                    hodSelect.appendChild(opt);
                });
            }
        } catch {
            console.error('Failed to load staff list for HOD dropdown');
        }
    }

    // ─── On page load ─────────────────────────────────────────
    fetchStaffList().then(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('id');
        if (editId) loadForEdit(editId);
    });

    // ─── Submit (Create / Update) ─────────────────────────────
    document.getElementById('department-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        const id     = document.getElementById('department-id').value;
        const name   = document.getElementById('name').value.trim();
        const status = document.getElementById('status').value; 
        const hod_id = document.getElementById('hod_id').value;

        // Client-side validation
        let hasError = false;
        if (!name)   { setError('name', 'Department name required hai.'); hasError = true; }
        if (!status) { setError('status', 'Status select karna zaroori hai.'); hasError = true; }
        if (hasError) { showToast('Kripya sare required fields fill karein.', 'warning'); return; }

        const btn = document.getElementById('submit-btn');
        btn.disabled = true;

        const isEdit  = id !== '';
        const url     = isEdit ? `${API_BASE}/${id}` : API_BASE;
        const method  = isEdit ? 'PUT' : 'POST';
        const payload = { name, status, hod_id: hod_id || null };

        try {
            const response = await fetch(url, {
                method,
                headers: HEADERS,
                body: JSON.stringify(payload),
            });

            const res = await response.json();

            if (response.ok && res.success) {
                showToast(res.message, 'success');
                if (!isEdit) resetForm();
            } else if (response.status === 422 && res.errors) {
                // Server-side validation errors
                Object.entries(res.errors).forEach(([field, messages]) => {
                    setError(field, messages[0]);
                });
                showToast('Validation errors hain, please check karein.', 'warning');
            } else {
                showToast(res.message || 'Kuch galat ho gaya.', 'error');
            }
        } catch {
            showToast('Server se connect nahi ho saka.', 'error');
        } finally {
            btn.disabled = false;
        }
    });
</script>
@endpush
