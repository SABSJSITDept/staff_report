@extends('layouts.app')
@section('title', 'Office - List')

@section('content')

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-96 pointer-events-none"></div>

{{-- Delete Confirm Modal --}}
<div id="delete-modal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black/50 backdrop-blur-sm flex">
    <div class="bg-white rounded-2xl shadow-2xl p-7 w-full max-w-sm mx-4 transform transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-800">Delete Office?</h3>
                <p class="text-xs text-gray-400 mt-0.5">Yeh action undo nahi ho sakta</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6 bg-red-50 border border-red-100 rounded-xl px-4 py-3">
            Kya aap sach mein is office ko permanently delete karna chahte hain?
        </p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()"
                class="flex-1 px-4 py-2.5 text-sm rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition font-medium">
                Cancel
            </button>
            <button id="confirm-delete-btn"
                class="flex-1 px-4 py-2.5 text-sm rounded-xl bg-red-500 hover:bg-red-600 text-white transition font-medium flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Karein
            </button>
        </div>
    </div>
</div>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600 font-medium">Offices</span>
</nav>

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Office Management</h1>
            <p class="text-gray-400 text-xs mt-0.5">Manage all registered offices</p>
        </div>
    </div>
    <a href="{{ route('office.create') }}"
        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold
               px-5 py-2.5 rounded-xl transition shadow-sm shadow-indigo-200 self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Add New Office
    </a>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total Offices</p>
        <p class="text-2xl font-bold text-gray-800" id="stat-total">—</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Active</p>
        <p class="text-2xl font-bold text-green-600" id="stat-active">—</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Inactive</p>
        <p class="text-2xl font-bold text-red-400" id="stat-inactive">—</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Last Updated</p>
        <p class="text-sm font-semibold text-indigo-600 pt-1" id="stat-date">—</p>
    </div>
</div>

{{-- Search & Filter Bar --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 mb-4 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <div class="relative w-full sm:w-80">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
        </div>
        <input type="text" id="search-input" placeholder="Search by Name, City, or Status..."
            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                   focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" />
    </div>
    <div class="flex items-center gap-3">
        <select id="status-filter" onchange="filterTable()"
            class="text-sm border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            <option value="">All Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
        <span id="total-count" class="text-xs text-gray-400 whitespace-nowrap bg-gray-100 px-3 py-1.5 rounded-lg"></span>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Office Name</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">City</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Address</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="office-table-body" class="divide-y divide-gray-50">
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-8 h-8 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <span class="text-sm text-gray-400">Data load ho raha hai...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const API_BASE = '/api/offices';
    const HEADERS = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
    };

    let allOffices = [];
    let pendingDeleteId = null;

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

    // ─── Update Stats ──────────────────────────────────────────
    function updateStats(offices) {
        document.getElementById('stat-total').textContent    = offices.length;
        document.getElementById('stat-active').textContent   = offices.filter(o => o.status === 'Active').length;
        document.getElementById('stat-inactive').textContent = offices.filter(o => o.status === 'Inactive').length;
        if (offices.length > 0) {
            const latest = offices.reduce((a, b) => new Date(a.updated_at) > new Date(b.updated_at) ? a : b);
            document.getElementById('stat-date').textContent = new Date(latest.updated_at).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        } else {
            document.getElementById('stat-date').textContent = '—';
        }
    }

    // ─── Render Table ─────────────────────────────────────────
    function renderTable(offices) {
        const tbody = document.getElementById('office-table-body');
        document.getElementById('total-count').textContent = `${offices.length} result${offices.length !== 1 ? 's' : ''}`;

        if (offices.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-5 py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400 font-medium">Koi office nahi mili</p>
                            <a href="/office/create" class="text-xs text-indigo-500 hover:text-indigo-700 transition">+ Pehli office add karein</a>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = offices.map((office, index) => `
            <tr class="hover:bg-indigo-50/30 transition group">
                <td class="px-5 py-4 text-xs text-gray-300 font-medium">${String(index + 1).padStart(2, '0')}</td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-800 text-sm">${office.name}</span>
                    </div>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-1.5 text-gray-500 text-sm">
                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        ${office.city}
                    </div>
                </td>
                <td class="px-5 py-4 text-gray-400 text-xs max-w-xs truncate" title="${office.address}">${office.address}</td>
                <td class="px-5 py-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                        ${office.status === 'Active'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-500'}">
                        <span class="w-1.5 h-1.5 rounded-full ${office.status === 'Active' ? 'bg-green-500' : 'bg-red-400'}"></span>
                        ${office.status}
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-2 opacity-70 group-hover:opacity-100 transition">
                        <a href="/staff/create?office_id=${office.id}&role=manager"
                            class="inline-flex items-center gap-1.5 bg-green-50 hover:bg-green-100 text-green-700
                                   text-xs font-medium px-3 py-1.5 rounded-lg transition" title="Add Manager for this office">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            + Mgr
                        </a>
                        <a href="/office/create?id=${office.id}"
                            class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700
                                   text-xs font-medium px-3 py-1.5 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        <button onclick="openDeleteModal(${office.id})"
                            class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-500
                                   text-xs font-medium px-3 py-1.5 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // ─── Filter ───────────────────────────────────────────────
    function filterTable() {
        const q      = document.getElementById('search-input').value.toLowerCase();
        const status = document.getElementById('status-filter').value;
        const filtered = allOffices.filter(o =>
            (!q || o.name.toLowerCase().includes(q) || o.city.toLowerCase().includes(q) || o.address.toLowerCase().includes(q) || o.status.toLowerCase().includes(q)) &&
            (!status || o.status === status)
        );
        renderTable(filtered);
    }

    document.getElementById('search-input').addEventListener('input', filterTable);

    // ─── Fetch All Offices ────────────────────────────────────
    async function fetchOffices() {
        try {
            const response = await fetch(API_BASE, { headers: HEADERS });
            const res = await response.json();
            if (res.success) {
                allOffices = res.data;
                updateStats(allOffices);
                renderTable(allOffices);
            } else {
                showToast('Offices load karne mein error aaya.', 'error');
            }
        } catch (err) {
            showToast('Server se connect nahi ho saka.', 'error');
            document.getElementById('office-table-body').innerHTML = `
                <tr><td colspan="6" class="px-5 py-8 text-center text-red-400 text-sm">Data load nahi ho saka.</td></tr>`;
        }
    }

    // ─── Delete Modal ─────────────────────────────────────────
    function openDeleteModal(id) {
        pendingDeleteId = id;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        pendingDeleteId = null;
        document.getElementById('delete-modal').classList.add('hidden');
    }

    document.getElementById('confirm-delete-btn').addEventListener('click', async function () {
        if (!pendingDeleteId) return;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Deleting...';
        try {
            const response = await fetch(`${API_BASE}/${pendingDeleteId}`, {
                method: 'DELETE',
                headers: HEADERS,
            });
            const res = await response.json();
            if (response.ok && res.success) {
                showToast(res.message, 'success');
                closeDeleteModal();
                fetchOffices();
            } else {
                showToast(res.message || 'Delete karne mein error aaya.', 'error');
            }
        } catch (err) {
            showToast('Network error. Dobara try karein.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Delete Karein`;
        }
    });

    document.getElementById('delete-modal').addEventListener('click', function (e) {
        if (e.target === this) closeDeleteModal();
    });

    // ─── Init ─────────────────────────────────────────────────
    fetchOffices();
</script>
@endpush
