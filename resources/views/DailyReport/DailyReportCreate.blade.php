@extends('layouts.app')
@section('title', isset($dailyReport) ? 'Edit Daily Report' : 'Submit Daily Report')

@section('content')

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-96 pointer-events-none"></div>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : (Auth::user()->role === 'manager' ? route('manager.dashboard') : route('staff.dashboard')) }}"
       class="hover:text-indigo-600 transition">Dashboard</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="{{ route('daily-report.index') }}" class="hover:text-indigo-600 transition">Daily Reports</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600">{{ isset($dailyReport) ? 'Edit Report' : 'New Report' }}</span>
</nav>

<form id="report-form" novalidate>
    @csrf

    {{-- ===================== PART 1: DAILY REPORT ===================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">

        {{-- Card Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-7 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-semibold text-base leading-tight">
                        Part 1 &mdash; Daily Report Info
                    </h2>
                    <p class="text-indigo-200 text-xs mt-0.5">Fill in staff ID and today's details</p>
                </div>
            </div>
            <a href="{{ route('daily-report.index') }}"
               class="flex items-center gap-1.5 text-xs font-medium text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </div>

        <div class="px-7 py-7 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Staff ID (readonly) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Staff ID (Login User)
                </label>
                <input type="text"
                       value="{{ Auth::id() }} &mdash; {{ Auth::user()->name }}"
                       readonly
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 cursor-not-allowed" />
                <input type="hidden" id="staff-id" value="{{ Auth::id() }}">
            </div>

            {{-- Report Date --}}
            <div>
                <label for="report_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Report Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="report_date"
                       value="{{ isset($dailyReport) ? $dailyReport->report_date->format('Y-m-d') : now()->format('Y-m-d') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                              focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition" />
                <p class="text-red-500 text-xs mt-1.5 hidden" id="err-report-date">Report date is required.</p>
            </div>

            {{-- Pending Task --}}
            <div class="sm:col-span-2">
                <label for="pending_task" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                   Yesterday's Pending Task
                </label>
                <textarea id="pending_task" rows="3"
                          placeholder="Tasks pending from yesterday..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                                 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition resize-none">{{ isset($dailyReport) ? $dailyReport->pending_task : '' }}</textarea>
            </div>

            {{-- Planned Task --}}
            <div class="sm:col-span-2">
                <label for="planned_task" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                     Tommorow's Planned Task
                </label>
                <textarea id="planned_task" rows="3"
                          placeholder="What is planned for tommorow..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                                 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition resize-none">{{ isset($dailyReport) ? $dailyReport->planned_task : '' }}</textarea>
            </div>

            {{-- Comments --}}
            <div class="sm:col-span-2">
                <label for="comments" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Comments
                </label>
                <textarea id="comments" rows="3"
                          placeholder="Any other important notes..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                                 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition resize-none">{{ isset($dailyReport) ? $dailyReport->comments : '' }}</textarea>
            </div>

        </div>
    </div>

    {{-- ===================== PART 2: TASKS ===================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">

        <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-7 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-semibold text-base leading-tight">
                        Part 2 &mdash; Task Completed Today
                    </h2>
                    <p class="text-emerald-100 text-xs mt-0.5">You can add multiple tasks</p>
                </div>
            </div>
            <button type="button" onclick="addTask()"
                    class="flex items-center gap-1.5 text-xs font-medium text-white bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Task
            </button>
        </div>

        <div class="px-7 py-6">

            {{-- Task List --}}
            <div id="task-list" class="flex flex-col gap-4">
                {{-- Tasks will be added here via JS --}}
            </div>

            {{-- Empty State --}}
            <div id="task-empty" class="text-center py-12 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm">No task added yet. Click the button above.</p>
            </div>

            {{-- Add Task Button (bottom) --}}
            <div class="mt-5 flex justify-center">
                <button type="button" onclick="addTask()"
                        class="flex items-center gap-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 border-2 border-dashed border-emerald-300 hover:border-emerald-400 px-6 py-3 rounded-xl transition w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Add Another Task
                </button>
            </div>
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="flex justify-end gap-3">
        <a href="{{ route('daily-report.index') }}"
           class="px-6 py-2.5 text-sm rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition font-medium">
            Cancel
        </a>
        <button type="submit" id="submit-btn"
                class="flex items-center gap-2 px-8 py-2.5 text-sm rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white transition font-medium shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span id="submit-label">{{ isset($dailyReport) ? 'Update Report' : 'Submit Report' }}</span>
        </button>
    </div>

</form>

@endsection

@push('scripts')
<div id="page-cfg" hidden
    data-is-edit="{{ isset($dailyReport) ? 'true' : 'false' }}"
    data-report-id="{{ isset($dailyReport) ? $dailyReport->id : '' }}"
    data-existing-tasks="{{ isset($dailyReport) ? e(json_encode($dailyReport->tasks)) : '[]' }}"
    data-store-url="{{ route('daily-report.store') }}"
    data-index-url="{{ route('daily-report.index') }}"
></div>
<script>
    // ---- Config ----
    const _cfg         = document.getElementById('page-cfg').dataset;
    const IS_EDIT      = _cfg.isEdit === 'true';
    const REPORT_ID    = _cfg.reportId || null;
    const EXISTING_TASKS = JSON.parse(_cfg.existingTasks);
    const STORE_URL    = _cfg.storeUrl;
    const INDEX_URL    = _cfg.indexUrl;
    const UPDATE_URL   = IS_EDIT ? '{{ url("/daily-report") }}/' + REPORT_ID : null;
    const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let taskIndex = 0;

    // ---- Toast ----
    function showToast(message, type = 'success') {
        const colors = type === 'success'
            ? 'bg-green-50 border-green-400 text-green-700'
            : 'bg-red-50 border-red-400 text-red-700';
        const icon = type === 'success'
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>';

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-start gap-3 border rounded-xl px-4 py-3 shadow-lg text-sm transition-all duration-300 ${colors}`;
        toast.innerHTML = `
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">${icon}</svg>
            <span>${message}</span>`;
        document.getElementById('toast-container').appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // ---- Task Row Template ----
    function taskRowHtml(index, data = {}) {
        const statusOptions = ['pending', 'in_progress', 'completed', 'paused'];
        const statusLabels  = { pending: 'Pending', in_progress: 'In Progress', completed: 'Completed', paused: 'Paused' };
        const statusColors  = { pending: 'text-yellow-600', in_progress: 'text-blue-600', completed: 'text-green-600', paused: 'text-gray-500' };

        const selectedStatus = data.status || 'pending';
        const optionsHtml = statusOptions.map(s =>
            `<option value="${s}" ${selectedStatus === s ? 'selected' : ''}>${statusLabels[s]}</option>`
        ).join('');

        return `
        <div class="task-row border border-gray-200 rounded-xl p-5 bg-gray-50 relative" data-index="${index}">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Task #<span class="task-num">${index + 1}</span></span>
                <button type="button" onclick="removeTask(${index})"
                        class="text-red-400 hover:text-red-600 transition" title="Remove Task">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Task Title --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Task Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tasks[${index}][task_title]"
                           value="${escHtml(data.task_title || '')}"
                           placeholder="Task title..."
                           class="task-title-input w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white
                                  focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition" />
                    <p class="err-task-title text-red-500 text-xs mt-1.5 hidden">Task title is required.</p>
                </div>

                {{-- Description --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Description
                    </label>
                    <textarea name="tasks[${index}][description]" rows="2"
                              placeholder="Task description..."
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white
                                     focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition resize-none">${escHtml(data.description || '')}</textarea>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="tasks[${index}][status]"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition">
                        ${optionsHtml}
                    </select>
                </div>

                {{-- Time Spend --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Time Spend
                    </label>
                    <input type="text" name="tasks[${index}][time_spend]"
                           value="${escHtml(data.time_spend || '')}"
                           placeholder="e.g. 2 hours, 30 min"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white
                                  focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition" />
                </div>

            </div>
        </div>`;
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    // ---- Add / Remove Task ----
    function addTask(data = {}) {
        const list  = document.getElementById('task-list');
        const empty = document.getElementById('task-empty');
        const div   = document.createElement('div');
        div.innerHTML = taskRowHtml(taskIndex, data);
        list.appendChild(div.firstElementChild);
        empty.classList.add('hidden');
        taskIndex++;
        renumberTasks();
    }

    function removeTask(index) {
        const row = document.querySelector(`.task-row[data-index="${index}"]`);
        if (row) row.remove();
        renumberTasks();
        if (document.querySelectorAll('.task-row').length === 0) {
            document.getElementById('task-empty').classList.remove('hidden');
        }
    }

    function renumberTasks() {
        document.querySelectorAll('.task-row').forEach((row, i) => {
            row.querySelector('.task-num').textContent = i + 1;
        });
    }

    // ---- Validate ----
    function validate() {
        let ok = true;

        const reportDate = document.getElementById('report_date').value;
        const errDate    = document.getElementById('err-report-date');
        if (!reportDate) {
            errDate.classList.remove('hidden'); ok = false;
        } else {
            errDate.classList.add('hidden');
        }

        document.querySelectorAll('.task-row').forEach(row => {
            const titleInput = row.querySelector('.task-title-input');
            const errTitle   = row.querySelector('.err-task-title');
            if (!titleInput.value.trim()) {
                errTitle.classList.remove('hidden'); ok = false;
            } else {
                errTitle.classList.add('hidden');
            }
        });

        return ok;
    }

    // ---- Collect form data ----
    function collectData() {
        const tasks = [];
        document.querySelectorAll('.task-row').forEach(row => {
            const inputs = {};
            row.querySelectorAll('input, textarea, select').forEach(el => {
                const match = el.name && el.name.match(/tasks\[\d+\]\[(.+)\]/);
                if (match) inputs[match[1]] = el.value;
            });
            tasks.push(inputs);
        });

        return {
            staff_id:     document.getElementById('staff-id').value,
            report_date:  document.getElementById('report_date').value,
            pending_task: document.getElementById('pending_task').value,
            planned_task: document.getElementById('planned_task').value,
            comments:     document.getElementById('comments').value,
            tasks:        tasks,
        };
    }

    // ---- Submit ----
    document.getElementById('report-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        if (!validate()) return;

        const btn   = document.getElementById('submit-btn');
        const label = document.getElementById('submit-label');
        btn.disabled = true;
        label.textContent = 'Saving...';

        const data   = collectData();
        const method = IS_EDIT ? 'PUT' : 'POST';
        const url    = IS_EDIT ? UPDATE_URL : STORE_URL;

        try {
            const res  = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept':       'application/json',
                },
                body: JSON.stringify(data),
            });

            if (res.status === 419) {
                showToast('Session expire ho gayi hai. Please page refresh karein.', 'error');
                label.textContent = IS_EDIT ? 'Update Report' : 'Submit Report';
                btn.disabled = false;
                return;
            }

            const json = await res.json();

            if (json.success) {
                showToast(json.message, 'success');
                if (!IS_EDIT) {
                    setTimeout(() => { window.location.href = INDEX_URL; }, 1500);
                } else {
                    label.textContent = 'Update Report';
                    btn.disabled = false;
                }
            } else {
                showToast(json.message || 'Kuch galat hua!', 'error');
                label.textContent = IS_EDIT ? 'Update Report' : 'Submit Report';
                btn.disabled = false;
            }
        } catch (err) {
            console.error(err);
            showToast('Network error: ' + err.message, 'error');
            label.textContent = IS_EDIT ? 'Update Report' : 'Submit Report';
            btn.disabled = false;
        }
    });

    // ---- Init existing tasks on edit ----
    window.addEventListener('DOMContentLoaded', () => {
        if (EXISTING_TASKS && EXISTING_TASKS.length > 0) {
            EXISTING_TASKS.forEach(t => addTask(t));
        }
    });
</script>
@endpush
