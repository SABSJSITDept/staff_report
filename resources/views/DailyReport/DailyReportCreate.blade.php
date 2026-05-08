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
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                              focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition" />
                <p class="text-red-500 text-xs mt-1.5 hidden" id="err-report-date">Report date is required and cannot be in the future.</p>
            </div>

            {{-- Pending Task --}}
            <div class="sm:col-span-2">
                <label for="pending_task" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                   Yesterday's Pending Task <span class="text-red-500">*</span>
                </label>
                <textarea id="pending_task" rows="3"
                          placeholder="Tasks pending from yesterday..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                                 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition resize-none">{{ isset($dailyReport) ? $dailyReport->pending_task : '' }}</textarea>
                <p class="text-red-500 text-[10px] mt-1 hidden font-semibold uppercase tracking-tight" id="err-pending-task">Pending task details are required</p>
            </div>

            {{-- Planned Task --}}
            <div class="sm:col-span-2">
                <label for="planned_task" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                     Tommorow's Planned Task <span class="text-red-500">*</span>
                </label>
                <textarea id="planned_task" rows="3"
                          placeholder="What is planned for tommorow..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                                 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition resize-none">{{ isset($dailyReport) ? $dailyReport->planned_task : '' }}</textarea>
                <p class="text-red-500 text-[10px] mt-1 hidden font-semibold uppercase tracking-tight" id="err-planned-task">Planned task details are required</p>
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
    data-last-tasks-url="{{ route('daily-report.last-tasks') }}"
    data-store-url="{{ route('daily-report.store') }}"
    data-index-url="{{ route('daily-report.index') }}"
></div>
<script>
    // ---- Config ----
    const _cfg         = document.getElementById('page-cfg').dataset;
    const IS_EDIT      = _cfg.isEdit === 'true';
    const REPORT_ID    = _cfg.reportId || null;
    
    // Safety injection from PHP
    const EXISTING_TASKS = @json(isset($dailyReport) ? $dailyReport->tasks : []);
    const CARRY_TASKS    = @json(isset($lastReport) ? $lastReport->tasks : []);
    const CARRY_PENDING  = @json(isset($lastReport) ? $lastReport->planned_task : '');

    const LAST_TASKS_URL = _cfg.lastTasksUrl;
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
    function parseTimeStr(str) {
        if (!str) return { hours: '', minutes: '' };
        const hMatch = str.match(/(\d+)\s*h/);
        const mMatch = str.match(/(\d+)\s*m/);
        return {
            hours:   hMatch ? hMatch[1] : '',
            minutes: mMatch ? mMatch[1] : ''
        };
    }

    function taskRowHtml(index, data = {}) {
        const statusOptions = ['pending', 'in_progress', 'completed', 'paused'];
        const statusLabels  = { pending: 'Pending', in_progress: 'In Progress', completed: 'Completed', paused: 'Paused' };
        
        const isCarry = !!data.is_carry;
        const time = parseTimeStr(data.time_spend);
        const hVal = data.hours   !== undefined ? data.hours   : time.hours;
        const mVal = data.minutes !== undefined ? data.minutes : time.minutes;

        const selectedStatus = data.status || 'pending';
        const optionsHtml = statusOptions.map(s =>
            `<option value="${s}" ${selectedStatus === s ? 'selected' : ''}>${statusLabels[s]}</option>`
        ).join('');

        return `
        <div class="task-row border ${isCarry ? 'border-amber-200 bg-amber-50/30' : 'border-gray-200 bg-gray-50'} rounded-xl p-5 relative mb-4" data-index="${index}">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Task #<span class="task-num">${index + 1}</span></span>
                    ${isCarry ? '<span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[9px] font-bold rounded uppercase tracking-tighter border border-amber-200">Continued Task</span>' : ''}
                </div>
                <button type="button" onclick="removeTask(${index})"
                        class="text-red-400 hover:text-red-600 transition" title="Remove Task">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Task Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tasks[${index}][task_title]"
                           value="${escHtml(data.task_title || '')}"
                           ${isCarry ? 'readonly' : ''}
                           placeholder="Task title..."
                           class="task-title-input w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm ${isCarry ? 'bg-gray-100/50 cursor-not-allowed text-gray-600' : 'bg-white'}
                                  focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition" />
                    <p class="err-task-title text-red-500 text-[10px] mt-1 hidden font-semibold uppercase tracking-tight">Required</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="tasks[${index}][description]" rows="2"
                              ${isCarry ? 'readonly' : ''}
                              placeholder="Describe what you did..."
                              class="task-desc-input w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm ${isCarry ? 'bg-gray-100/50 cursor-not-allowed text-gray-600' : 'bg-white'}
                                     focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition resize-none">${escHtml(data.description || '')}</textarea>
                    <p class="err-task-desc text-red-500 text-[10px] mt-1 hidden font-semibold uppercase tracking-tight">Required</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="tasks[${index}][status]"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition ring-2 ring-indigo-100">
                        ${optionsHtml}
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Time Spend <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <input type="number" name="tasks[${index}][hours]"
                                   value="${hVal}" min="0" placeholder="HH"
                                   class="task-hours-input w-full pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-400 focus:outline-none transition" />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] text-gray-400 font-bold uppercase pointer-events-none">hrs</span>
                        </div>
                        <div class="flex-1 relative">
                            <input type="number" name="tasks[${index}][minutes]"
                                   value="${mVal}" min="0" max="59" placeholder="MM"
                                   class="task-mins-input w-full pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-emerald-400 focus:outline-none transition" />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] text-gray-400 font-bold uppercase pointer-events-none">min</span>
                        </div>
                    </div>
                    <p class="err-task-time text-red-500 text-[10px] mt-1 hidden font-semibold uppercase tracking-tight">Time is required</p>
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
    window.addTask = function(data = {}) {
        try {
            const list  = document.getElementById('task-list');
            const empty = document.getElementById('task-empty');
            const html  = taskRowHtml(taskIndex, data);
            list.insertAdjacentHTML('beforeend', html);
            if (empty) empty.classList.add('hidden');
            taskIndex++;
            renumberTasks();
        } catch (e) {
            console.error('Error adding task:', e);
        }
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
        const today = new Date().toISOString().split('T')[0];

        if (!reportDate || reportDate > today) {
            errDate.classList.remove('hidden'); ok = false;
        } else {
            errDate.classList.add('hidden');
        }

        const pendingInput = document.getElementById('pending_task');
        const errPending   = document.getElementById('err-pending-task');
        if (!pendingInput.value.trim()) {
            errPending.classList.remove('hidden'); ok = false;
        } else {
            errPending.classList.add('hidden');
        }

        const plannedInput = document.getElementById('planned_task');
        const errPlanned   = document.getElementById('err-planned-task');
        if (!plannedInput.value.trim()) {
            errPlanned.classList.remove('hidden'); ok = false;
        } else {
            errPlanned.classList.add('hidden');
        }

        const taskRows = document.querySelectorAll('.task-row');
        if (taskRows.length === 0) {
            showToast('Kam se kam ek task add karna zaroori hai.', 'error');
            ok = false;
        }

        taskRows.forEach(row => {
            const titleInput = row.querySelector('.task-title-input');
            const errTitle   = row.querySelector('.err-task-title');
            if (!titleInput.value.trim()) {
                errTitle.classList.remove('hidden'); ok = false;
            } else {
                errTitle.classList.add('hidden');
            }

            const descInput = row.querySelector('.task-desc-input');
            const errDesc   = row.querySelector('.err-task-desc');
            if (!descInput.value.trim()) {
                errDesc.classList.remove('hidden'); ok = false;
            } else {
                errDesc.classList.add('hidden');
            }

            const hInput = row.querySelector('.task-hours-input');
            const mInput = row.querySelector('.task-mins-input');
            const errTime = row.querySelector('.err-task-time');
            if (!hInput.value && !mInput.value) {
                errTime.classList.remove('hidden'); ok = false;
            } else {
                errTime.classList.add('hidden');
            }
        });

        return ok;
    }

    // ---- Collect form data ----
    function collectData() {
        const tasks = [];
        document.querySelectorAll('.task-row').forEach(row => {
            const rowData = {};
            row.querySelectorAll('input, textarea, select').forEach(el => {
                const match = el.name && el.name.match(/tasks\[\d+\]\[(.+)\]/);
                if (match) rowData[match[1]] = el.value;
            });
            
            // Format time string from split inputs
            let timeStr = '';
            if (rowData.hours)   timeStr += rowData.hours + 'h ';
            if (rowData.minutes) timeStr += rowData.minutes + 'm';
            rowData.time_spend = timeStr.trim();
            
            tasks.push(rowData);
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

    // ---- Date Change Handler (Carry Forward) ----
    function processCarryTasks(data) {
        const taskList = document.getElementById('task-list');
        const emptyState = document.getElementById('task-empty');
        
        taskList.innerHTML = '';
        taskIndex = 0;
        
        if (data.pending_task !== undefined) {
             document.getElementById('pending_task').value = data.pending_task || '';
        }

        if (data.tasks && data.tasks.length > 0) {
            taskList.insertAdjacentHTML('beforeend', `
                <div class="carry-header flex items-center gap-3 mb-2 mt-2">
                    <div class="h-px flex-1 bg-amber-200"></div>
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                        Continued From Previous Report
                    </span>
                    <div class="h-px flex-1 bg-amber-200"></div>
                </div>
            `);
            data.tasks.forEach(t => addTask({ ...t, is_carry: true }));
            
            taskList.insertAdjacentHTML('beforeend', `
                <div class="carry-divider flex items-center gap-3 mb-6 mt-8">
                    <div class="h-px flex-1 bg-gray-200"></div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Today's New Tasks</span>
                    <div class="h-px flex-1 bg-gray-200"></div>
                </div>
            `);
            if (emptyState) emptyState.classList.add('hidden');
        } else {
            if (emptyState) emptyState.classList.remove('hidden');
            addTask(); // Always add one empty task if nothing carried
        }
    }

    async function fetchCarryTasks(date) {
        if (IS_EDIT) return;
        try {
            const res = await fetch(`${LAST_TASKS_URL}?date=${date}`);
            const data = await res.json();
            if (data.success) {
                processCarryTasks(data);
                if (data.tasks && data.tasks.length > 0) {
                    showToast('Pichle report ke incomplete tasks load kar diye gaye hain.', 'success');
                }
            }
        } catch (e) {
            console.error('Error fetching carry tasks:', e);
            addTask(); // Fallback
        }
    }

    document.getElementById('report_date').addEventListener('change', function(e) {
        if (!IS_EDIT) {
            const selectedDate = e.target.value;
            const today = new Date().toISOString().split('T')[0];
            
            if (selectedDate > today) {
                showToast('Future date ki report fill nahi kar sakte.', 'error');
                e.target.value = today;
                return;
            }

            if (confirm('Date badalne par us date ke hisab se pichle pending tasks load honge aur current entry reset ho jayegi. Kya aap aage badhna chahte hain?')) {
                fetchCarryTasks(selectedDate);
            }
        }
    });

    // ---- Init ----
    window.addEventListener('DOMContentLoaded', () => {
        if (IS_EDIT) {
            if (EXISTING_TASKS && EXISTING_TASKS.length > 0) {
                EXISTING_TASKS.forEach(t => addTask(t));
            } else {
                addTask();
            }
        } else {
            // New report: Use injected carry tasks immediately
            if ((CARRY_TASKS && CARRY_TASKS.length > 0) || CARRY_PENDING) {
                processCarryTasks({ tasks: CARRY_TASKS, pending_task: CARRY_PENDING });
            } else {
                addTask();
            }
        }
    });
</script>
@endpush
