@extends('layouts.app')
@section('title', 'Staff - List')

@section('content')

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-96 pointer-events-none"></div>

{{-- Delete Confirm Modal --}}
<div id="delete-modal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black/50 backdrop-blur-sm flex">
    <div class="bg-white rounded-2xl shadow-2xl p-7 w-full max-w-sm mx-4">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-800">Delete Staff?</h3>
                <p class="text-xs text-gray-400 mt-0.5">Yeh action undo nahi ho sakta</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6 bg-red-50 border border-red-100 rounded-xl px-4 py-3">
            Kya aap sach mein is staff member ko permanently delete karna chahte hain?
        </p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 text-sm rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition font-medium">Cancel</button>
            <button id="confirm-delete-btn" class="flex-1 px-4 py-2.5 text-sm rounded-xl bg-red-500 hover:bg-red-600 text-white transition font-medium flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Karein
            </button>
        </div>
    </div>
</div>

{{-- Mark as Left Modal --}}
<div id="left-modal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black/50 backdrop-blur-sm flex">
    <div class="bg-white rounded-2xl shadow-2xl p-7 w-full max-w-sm mx-4">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-orange-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-800">Staff Left Mark Karein</h3>
                <p class="text-xs text-gray-400 mt-0.5" id="left-modal-staff-name">—</p>
            </div>
        </div>
        <div class="mb-5">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                Left Date <span class="text-red-500">*</span>
            </label>
            <input type="date" id="left-date-input"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition" />
            <p class="text-red-500 text-xs mt-1.5 hidden" id="err-left-date">Left date select karna zaroori hai.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeLeftModal()" class="flex-1 px-4 py-2.5 text-sm rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition font-medium">Cancel</button>
            <button id="confirm-left-btn" class="flex-1 px-4 py-2.5 text-sm rounded-xl bg-orange-500 hover:bg-orange-600 text-white transition font-medium flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Left Mark Karein
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
    <span class="text-gray-600 font-medium">Staff Management</span>
</nav>

{{-- Page Header --}}
<div class="card-premium !p-0 overflow-hidden mb-8">
    <div class="gradient-bg px-8 py-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center shadow-inner border border-white/10">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Staff Management</h1>
                <p class="text-indigo-100/80 text-xs mt-1 uppercase tracking-widest font-semibold">Directory & Permissions</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.create') }}" class="btn-primary !bg-white !text-indigo-600 hover:!bg-indigo-50 shadow-xl border-none px-6 py-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Register Personnel
            </a>
        </div>
    </div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total Staff --}}
    <div class="card-premium">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Personnel</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-0.5" id="stat-total">—</h3>
            </div>
        </div>
    </div>

    {{-- Active Staff --}}
    <div class="card-premium">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Members</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-0.5" id="stat-active">—</h3>
            </div>
        </div>
    </div>

    {{-- Inactive Staff --}}
    <div class="card-premium">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Inactive/Left</p>
                <h3 class="text-2xl font-bold text-rose-600 mt-0.5" id="stat-inactive">—</h3>
            </div>
        </div>
    </div>

    {{-- Last Updated --}}
    <div class="card-premium">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sync Status</p>
                <h3 class="text-sm font-bold text-amber-600 mt-1" id="stat-date">Active Now</h3>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="flex gap-2 mb-4">
    <button id="tab-active" onclick="switchTab('active')"
        class="px-5 py-2 text-sm font-semibold rounded-xl transition tab-btn bg-indigo-600 text-white shadow-sm shadow-indigo-200">
        Active Staff
    </button>
    <button id="tab-inactive" onclick="switchTab('inactive')"
        class="px-5 py-2 text-sm font-semibold rounded-xl transition tab-btn bg-white text-gray-500 border border-gray-200 hover:bg-gray-50">
        Inactive Staff
    </button>
</div>

{{-- Search & Filter Bar --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        {{-- Name Search --}}
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
            </div>
            <input type="text" id="filter-name" placeholder="Search by Name..."
                class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" />
        </div>

        {{-- Mobile Search --}}
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </div>
            <input type="text" id="filter-mobile" placeholder="Search by Mobile..."
                class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" />
        </div>

        {{-- Department Filter --}}
        <select id="filter-dept"
            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 appearance-none
                   focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            <option value="">All Departments</option>
        </select>

        {{-- Office Filter --}}
        <div class="flex items-center gap-2">
            <select id="filter-office"
                class="flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 appearance-none
                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                <option value="">All Offices</option>
            </select>
            <span id="total-count" class="text-xs text-gray-400 whitespace-nowrap bg-gray-100 px-3 py-1.5 rounded-lg"></span>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto max-h-[75vh] relative scroll-smooth shadow-inner">
        <table class="min-w-full text-sm text-left">
            <thead class="sticky top-0 z-20">
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Photo</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mobile</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Designation</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Office</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">DOJ</th>
                    <th class="px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="staff-table-body" class="divide-y divide-gray-50">
                <tr>
                    <td colspan="10" class="px-5 py-12 text-center">
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
    const API_BASE   = '/api/staff';
    const DEPT_API   = '/api/departments';
    const OFFICE_API = '/api/offices';

    const HEADERS = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
    };

    let allStaff        = [];
    let pendingDeleteId = null;
    let pendingLeftId   = null;
    let currentTab      = 'active';

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

    // ─── Format Date ──────────────────────────────────────────
    function fmtDate(d) {
        if (!d) return '—';
        return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    // ─── Update Stats ──────────────────────────────────────────
    function updateStats(staff) {
        document.getElementById('stat-total').textContent    = staff.length;
        document.getElementById('stat-active').textContent   = staff.filter(s => s.status === 'Active').length;
        document.getElementById('stat-inactive').textContent = staff.filter(s => s.status === 'Inactive').length;
        if (staff.length > 0) {
            const latest = staff.reduce((a, b) => new Date(a.updated_at) > new Date(b.updated_at) ? a : b);
            document.getElementById('stat-date').textContent = fmtDate(latest.updated_at);
        } else {
            document.getElementById('stat-date').textContent = '—';
        }
    }

    // ─── Tabs ─────────────────────────────────────────────────
    function switchTab(tab) {
        currentTab = tab;
        document.getElementById('tab-active').className   = tab === 'active'
            ? 'px-5 py-2 text-sm font-semibold rounded-xl transition tab-btn bg-indigo-600 text-white shadow-sm shadow-indigo-200'
            : 'px-5 py-2 text-sm font-semibold rounded-xl transition tab-btn bg-white text-gray-500 border border-gray-200 hover:bg-gray-50';
        document.getElementById('tab-inactive').className = tab === 'inactive'
            ? 'px-5 py-2 text-sm font-semibold rounded-xl transition tab-btn bg-indigo-600 text-white shadow-sm shadow-indigo-200'
            : 'px-5 py-2 text-sm font-semibold rounded-xl transition tab-btn bg-white text-gray-500 border border-gray-200 hover:bg-gray-50';
        applyFilters();
    }

    // ─── Apply Filters ────────────────────────────────────────
    function applyFilters() {
        const name   = document.getElementById('filter-name').value.toLowerCase();
        const mobile = document.getElementById('filter-mobile').value.toLowerCase();
        const dept   = document.getElementById('filter-dept').value;
        const office = document.getElementById('filter-office').value;

        const filtered = allStaff.filter(s => {
            const matchTab    = s.status === (currentTab === 'active' ? 'Active' : 'Inactive');
            const matchName   = !name   || s.name.toLowerCase().includes(name) || s.f_name.toLowerCase().includes(name);
            const matchMobile = !mobile || s.mobile.includes(mobile);
            const matchDept   = !dept   || String(s.dept_id) === dept;
            const matchOffice = !office || String(s.office_id) === office;
            return matchTab && matchName && matchMobile && matchDept && matchOffice;
        });

        renderTable(filtered);
    }

    ['filter-name', 'filter-mobile'].forEach(id =>
        document.getElementById(id).addEventListener('input', applyFilters)
    );
    ['filter-dept', 'filter-office'].forEach(id =>
        document.getElementById(id).addEventListener('change', applyFilters)
    );

    // ─── Render Table ─────────────────────────────────────────
    function renderTable(staff) {
        const tbody = document.getElementById('staff-table-body');
        document.getElementById('total-count').textContent = `${staff.length} result${staff.length !== 1 ? 's' : ''}`;

        if (staff.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="px-5 py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400 font-medium">No staff member found</p>
                            <a href="/staff/create" class="text-xs text-indigo-500 hover:text-indigo-700 transition">+ Add the first staff member</a>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = staff.map((s, index) => `
            <tr class="hover:bg-indigo-50/30 transition group">
                <td class="px-4 py-3 text-xs text-gray-300 font-medium">${String(index + 1).padStart(2, '0')}</td>
                <td class="px-4 py-3">
                    ${s.photo
                        ? `<img src="${s.photo}" alt="${s.name}" class="w-9 h-9 rounded-full object-cover border border-gray-200 shadow-sm" />`
                        : `<div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">${s.name.charAt(0)}</div>`
                    }
                </td>
                <td class="px-4 py-3">
                    <div class="font-semibold text-gray-800 text-sm">${s.name}</div>
                    <div class="text-xs text-gray-400">S/O ${s.f_name}</div>
                </td>
                <td class="px-4 py-3 text-gray-600 text-sm">${s.mobile}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">${s.email ?? '—'}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                        ${s.dept_name ?? '—'}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">${s.designation}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">${s.office_name ?? '—'}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">${fmtDate(s.doj)}</td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        {{-- Edit Button --}}
                        <a href="/staff/create?id=${s.id}" 
                           class="p-1.5 text-indigo-500 hover:bg-indigo-50 rounded-lg transition-all"
                           title="Edit Staff">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </a>

                        {{-- Mark as Left Button --}}
                        ${s.status === 'Active' ? `
                        <button onclick="openLeftModal(${s.id}, '${s.name}')" 
                                class="p-1.5 text-orange-500 hover:bg-orange-50 rounded-lg transition-all"
                                title="Mark as Left">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </button>
                        ` : ''}

                        {{-- Delete Button --}}
                        <button onclick="openDeleteModal(${s.id})" 
                                class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-all"
                                title="Delete Staff">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // ─── Fetch All Staff ──────────────────────────────────────
    async function fetchStaff() {
        try {
            const response = await fetch(API_BASE, { headers: HEADERS });
            const res = await response.json();
            if (res.success) {
                allStaff = res.data;
                updateStats(allStaff);
                applyFilters();
            } else {
                showToast('Staff load karne mein error aaya.', 'error');
            }
        } catch (err) {
            showToast('Server se connect nahi ho saka.', 'error');
            document.getElementById('staff-table-body').innerHTML =
                `<tr><td colspan="10" class="px-5 py-8 text-center text-red-400 text-sm">Data load nahi ho saka.</td></tr>`;
        }
    }

    // ─── Load Filter Dropdowns ────────────────────────────────
    async function loadFilterDropdowns() {
        try {
            const [dRes, oRes] = await Promise.all([
                fetch(DEPT_API,   { headers: HEADERS }),
                fetch(OFFICE_API, { headers: HEADERS }),
            ]);
            const dData = await dRes.json();
            const oData = await oRes.json();

            const dSel = document.getElementById('filter-dept');
            if (dData.success) {
                dData.data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.name;
                    dSel.appendChild(opt);
                });
            }

            const oSel = document.getElementById('filter-office');
            if (oData.success) {
                oData.data.forEach(o => {
                    const opt = document.createElement('option');
                    opt.value = o.id;
                    opt.textContent = o.name;
                    oSel.appendChild(opt);
                });
            }
        } catch (e) {
            // silent - filters are optional
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
                fetchStaff();
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

    // ─── Left Modal ───────────────────────────────────────────
    function openLeftModal(id, name) {
        pendingLeftId = id;
        document.getElementById('left-modal-staff-name').textContent = name;
        document.getElementById('left-date-input').value = '';
        document.getElementById('err-left-date').classList.add('hidden');
        document.getElementById('left-modal').classList.remove('hidden');
    }
    function closeLeftModal() {
        pendingLeftId = null;
        document.getElementById('left-modal').classList.add('hidden');
    }

    document.getElementById('confirm-left-btn').addEventListener('click', async function () {
        const leftDate = document.getElementById('left-date-input').value;
        if (!leftDate) {
            document.getElementById('err-left-date').classList.remove('hidden');
            showToast('Left date select karna zaroori hai.', 'warning');
            return;
        }
        document.getElementById('err-left-date').classList.add('hidden');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Saving...';

        try {
            const response = await fetch(`${API_BASE}/${pendingLeftId}`, {
                method: 'PUT',
                headers: HEADERS,
                body: JSON.stringify({ action: 'mark_left', left_date: leftDate }),
            });
            const res = await response.json();
            if (response.ok && res.success) {
                showToast(res.message, 'success');
                closeLeftModal();
                fetchStaff();
            } else {
                showToast(res.message || 'Left mark karne mein error aaya.', 'error');
            }
        } catch (err) {
            showToast('Network error. Dobara try karein.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg> Left Mark Karein`;
        }
    });

    document.getElementById('left-modal').addEventListener('click', function (e) {
        if (e.target === this) closeLeftModal();
    });

    // ─── Init ─────────────────────────────────────────────────
    loadFilterDropdowns();
    fetchStaff();
</script>
@endpush
