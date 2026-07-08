@extends('layouts.app')
@section('title', 'Sanyojak - Add / Edit')

@section('content')

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-96 pointer-events-none"></div>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('sanyojak.view') }}" class="hover:text-indigo-600 transition">Sanyojaks</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600" id="breadcrumb-label">New Sanyojak</span>
</nav>

<div class="space-y-8 pb-12">
    <div class="card-premium !p-0 overflow-hidden">
        <div class="gradient-bg px-8 py-7 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight" id="form-title">Sanyojak Registration</h2>
                    <p class="text-indigo-100/70 text-[10px] font-bold uppercase tracking-[0.2em] mt-0.5">Managerial Assignment</p>
                </div>
            </div>
            <a href="{{ route('sanyojak.view') }}" class="btn-secondary !bg-white/10 !text-white !border-white/20 hover:!bg-white/20 px-6 py-2.5 text-xs">
                View Directory
            </a>
        </div>

        <form id="sanyojak-form" novalidate class="px-7 py-7">
            <input type="hidden" id="sanyojak-id" value="">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                {{-- Name --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" required placeholder="Sanyojak Name" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" />
                </div>

                {{-- Pravarti --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Pravarti
                    </label>
                    <input type="text" id="pravarti" placeholder="e.g. IT Cell" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" />
                </div>

                {{-- Type --}}
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Sanyojak Type <span class="text-rose-500">*</span>
                    </label>
                    <select id="type" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" onchange="toggleStaffAssignment()">
                        <option value="sanyojak">Normal Sanyojak (Staff Assignment Required)</option>
                        <option value="karyalay_sanyojak">Karyalay Sanyojak (Global Access)</option>
                    </select>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" required placeholder="email@example.com" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" />
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Password <span class="text-rose-500" id="password-req">*</span>
                    </label>
                    <input type="password" id="password" placeholder="Enter password" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" />
                    <p class="text-[10px] text-gray-400 mt-1" id="password-help">Leave blank to keep current password when editing.</p>
                </div>

                <div id="staff-assignment-section" class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5 w-full">
                    {{-- Office Selection --}}
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Select Office to Filter Staff <span class="text-rose-500">*</span>
                        </label>
                        <select id="office_id" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            <option value="">-- Select Office --</option>
                        </select>
                    </div>

                    {{-- Assigned Staff Checkboxes --}}
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Assigned Staff
                        </label>
                        <div id="staff-checkbox-container" class="form-input-modern w-full border border-gray-200 rounded-xl px-4 py-3 min-h-[120px] max-h-64 overflow-y-auto bg-gray-50 grid grid-cols-1 md:grid-cols-2 gap-2">
                            <p class="text-sm text-gray-400 col-span-full">Please select an office to view its staff.</p>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2">Check the boxes to assign staff to this Sanyojak. Your selections across different offices will be preserved.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-slate-100 flex justify-end gap-4">
                <button type="button" onclick="resetForm()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Reset</button>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-lg" id="submit-label">Save Sanyojak</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const API_BASE = '/api/sanyojaks';
    const STAFF_API = '/api/staff';
    const OFFICE_API = '/api/offices';
    const HEADERS = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
    };

    let allStaff = [];
    let allOffices = [];
    let selectedStaffIds = new Set();

    function showToast(message, type = 'success') {
        const colors = { success: 'bg-green-500', error: 'bg-red-500' };
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-5 py-3 rounded-xl shadow-lg`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function toggleStaffAssignment() {
        const type = document.getElementById('type').value;
        const section = document.getElementById('staff-assignment-section');
        if (type === 'karyalay_sanyojak') {
            section.style.display = 'none';
        } else {
            section.style.display = 'block';
        }
    }

    async function fetchData() {
        try {
            const [staffRes, officeRes] = await Promise.all([
                fetch(STAFF_API, { headers: HEADERS }),
                fetch(OFFICE_API, { headers: HEADERS })
            ]);
            
            const staffData = await staffRes.json();
            const officeData = await officeRes.json();

            if (staffData.success) allStaff = staffData.data;
            if (officeData.success) {
                allOffices = officeData.data.filter(o => o.status === 'Active');
                renderOffices();
            }
        } catch (e) {
            showToast('Failed to load initial data', 'error');
        }
    }

    function renderOffices() {
        const sel = document.getElementById('office_id');
        sel.innerHTML = '<option value="">-- Select Office --</option>';
        allOffices.forEach(o => {
            const opt = document.createElement('option');
            opt.value = o.id;
            opt.textContent = o.name;
            sel.appendChild(opt);
        });
    }

    function handleCheckboxChange(e) {
        const val = parseInt(e.target.value);
        if(e.target.checked) {
            selectedStaffIds.add(val);
        } else {
            selectedStaffIds.delete(val);
        }
    }

    document.getElementById('office_id').addEventListener('change', function() {
        const officeId = this.value;
        const container = document.getElementById('staff-checkbox-container');
        
        if(!officeId) {
            container.innerHTML = '<p class="text-sm text-gray-400 col-span-full">Please select an office to view its staff.</p>';
            return;
        }

        const filteredStaff = allStaff.filter(s => s.office_id == officeId);
        
        if(filteredStaff.length === 0) {
            container.innerHTML = '<p class="text-sm text-gray-400 col-span-full">No staff members found in this office.</p>';
            return;
        }

        container.innerHTML = '';
        filteredStaff.forEach(s => {
            const isChecked = selectedStaffIds.has(s.id);
            
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg transition';
            
            div.innerHTML = `
                <input type="checkbox" id="staff_${s.id}" value="${s.id}" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500" ${isChecked ? 'checked' : ''}>
                <label for="staff_${s.id}" class="text-sm text-gray-700 cursor-pointer flex-1 font-medium select-none">
                    ${s.name} <span class="text-xs text-gray-400 block">${s.designation || 'No Designation'}</span>
                </label>
            `;
            
            div.querySelector('input').addEventListener('change', handleCheckboxChange);
            container.appendChild(div);
        });
    });

    async function loadEditData(id) {
        try {
            const res = await fetch(`${API_BASE}/${id}`, { headers: HEADERS });
            const data = await res.json();
            if(data.success) {
                const s = data.data;
                document.getElementById('sanyojak-id').value = s.id;
                document.getElementById('name').value = s.name;
                document.getElementById('pravarti').value = s.pravarti || '';
                document.getElementById('email').value = s.email;
                document.getElementById('password-req').style.display = 'none';
                document.getElementById('form-title').textContent = 'Edit Sanyojak';
                document.getElementById('submit-label').textContent = 'Update Sanyojak';
                if (s.type) {
                    document.getElementById('type').value = s.type;
                    toggleStaffAssignment();
                }

                // Initialize selected staff
                selectedStaffIds.clear();
                const assigned = s.staff_assigned || [];
                assigned.forEach(id => selectedStaffIds.add(parseInt(id)));
                
                // If an office is already selected, re-trigger change to show checkboxes
                const officeId = document.getElementById('office_id').value;
                if(officeId) {
                    document.getElementById('office_id').dispatchEvent(new Event('change'));
                }
            }
        } catch(e) {
            showToast('Failed to load data', 'error');
        }
    }

    function resetForm() {
        document.getElementById('sanyojak-form').reset();
        document.getElementById('sanyojak-id').value = '';
        document.getElementById('password-req').style.display = 'inline';
        document.getElementById('form-title').textContent = 'Sanyojak Registration';
        document.getElementById('submit-label').textContent = 'Save Sanyojak';
        document.getElementById('type').value = 'sanyojak';
        toggleStaffAssignment();
        selectedStaffIds.clear();
        document.getElementById('staff-checkbox-container').innerHTML = '<p class="text-sm text-gray-400 col-span-full">Please select an office to view its staff.</p>';
    }

    document.getElementById('sanyojak-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('sanyojak-id').value;
        const isEdit = !!id;

        const payload = {
            name: document.getElementById('name').value,
            pravarti: document.getElementById('pravarti').value,
            email: document.getElementById('email').value,
            type: document.getElementById('type').value,
            staff_assigned: Array.from(selectedStaffIds)
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
                setTimeout(() => window.location.href = "{{ route('sanyojak.view') }}", 1000);
            } else {
                showToast(data.message || 'Error occurred', 'error');
            }
        } catch(e) {
            showToast('Network error', 'error');
        }
    });

    window.addEventListener('DOMContentLoaded', async () => {
        await fetchData();
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        if(id) {
            await loadEditData(id);
        }
    });
</script>
@endpush
