<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        self::autoPauseMidnightTasks(Auth::id());

        $query = DailyReport::with(['staff', 'tasks' => function($q) {
            $q->withCount('comments');
        }])
            ->orderByDesc('report_date')
            ->orderByDesc('created_at');

        // Staff sirf apni khud ki reports dekh sakta hai
        if (Auth::user()->role === 'staff') {
            $query->where('staff_id', Auth::id());
        }

        $reports = $query->paginate(5);

        // Statistics for dashboard
        $statsQuery = DailyReport::query();
        if (Auth::user()->role === 'staff') {
            $statsQuery->where('staff_id', Auth::id());
        }

        $totalReports = (clone $statsQuery)->count();
        $todayReports = (clone $statsQuery)->whereDate('report_date', now()->toDateString())->count();
        
        $reportIds = (clone $statsQuery)->pluck('id');
        $totalTasks = DailyReportTask::whereIn('daily_report_id', $reportIds)->count();
        $doneTasks = DailyReportTask::whereIn('daily_report_id', $reportIds)->where('status', 'completed')->count();
        
        $completionRate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

        // Fetch staff for dropdown (Admin/Manager only)
        $allStaff = [];
        $offices = [];
        if (Auth::user()->role !== 'staff') {
            $allStaff = User::with('staff.office')
                ->whereIn('role', ['staff', 'manager', 'admin'])
                ->orderBy('name')
                ->get();
            $offices = \App\Models\Office\OfficeModel::orderBy('name')->get();
        }

        return view('DailyReport.DailyReportView', compact(
            'reports', 
            'allStaff',
            'offices',
            'totalReports', 
            'todayReports', 
            'totalTasks', 
            'completionRate'
        ));
    }

    public function export(Request $request)
    {
        $type   = $request->get('type', 'excel'); // excel or pdf
        $staffId = $request->get('staff_id');
        $start  = $request->get('start_date');
        $end    = $request->get('end_date');

        $query = DailyReport::with(['staff', 'tasks'])
            ->orderBy('report_date');

        if (Auth::user()->role === 'staff') {
            $query->where('staff_id', Auth::id());
        } elseif ($staffId) {
            $query->where('staff_id', $staffId);
        }

        if ($start) $query->where('report_date', '>=', $start);
        if ($end)   $query->where('report_date', '<=', $end);

        $reports = $query->get();

        if ($reports->isEmpty()) {
            return back()->with('error', 'Chune gaye dates ke liye koi data nahi mila.');
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('DailyReport.DailyReportPdf', compact('reports', 'start', 'end'));
            return $pdf->download('Daily_Report_' . now()->format('YmdHis') . '.pdf');
        }

        // For Excel, we'll use a custom export class
        return Excel::download(new \App\Exports\DailyReportExport($reports), 'Daily_Report_' . now()->format('YmdHis') . '.xlsx');
    }

    public function create()
    {
        $today = now()->toDateString();
        $staffId = Auth::id();

        // Run self-healing backfill for legacy tasks
        self::backfillLegacySourceTaskIds($staffId);

        // Get the latest report to carry forward planned_task
        $lastReport = DailyReport::where('staff_id', $staffId)
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->first();

        // Get all outstanding incomplete tasks across all reports before today
        $incompleteTasks = DailyReportTask::select('daily_report_tasks.*')
            ->join('daily_reports', 'daily_report_tasks.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.staff_id', $staffId)
            ->where('daily_reports.report_date', '<', $today)
            ->where('daily_report_tasks.status', '!=', 'completed')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('daily_report_tasks as t2')
                    ->whereColumn('t2.source_task_id', 'daily_report_tasks.id');
            })
            ->get();

        if ($lastReport) {
            $lastReport->setRelation('tasks', $incompleteTasks);
        } else {
            $lastReport = new DailyReport();
            $lastReport->planned_task = '';
            $lastReport->setRelation('tasks', $incompleteTasks);
        }

        return view('DailyReport.DailyReportCreate', compact('lastReport'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_date'  => 'required|date|before_or_equal:today',
            'pending_task' => 'nullable|string',
            'planned_task' => 'nullable|string',
            'comments'     => 'nullable|string',
            'tasks'                => 'nullable|array',
            'tasks.*.task_title'  => 'required_with:tasks.*|string|max:255',
            'tasks.*.description' => 'required_with:tasks.*|string',
            'tasks.*.status'      => 'required_with:tasks.*|in:completed,in_progress,pending,paused',
            'tasks.*.time_spend'  => 'nullable|string|max:100',
            'tasks.*.start_time'  => 'nullable|string',
            'tasks.*.end_time'    => 'nullable|string',
            'tasks.*.source_task_id' => 'nullable|integer|exists:daily_report_tasks,id',
        ]);

        DB::beginTransaction();
        try {
            $report = DailyReport::create([
                'staff_id'     => Auth::id(),
                'report_date'  => $request->report_date,
                'pending_task' => $request->pending_task,
                'planned_task' => $request->planned_task,
                'comments'     => $request->comments,
            ]);

            if ($request->has('tasks') && is_array($request->tasks)) {
                foreach ($request->tasks as $task) {
                    if (!empty($task['task_title'])) {
                        $startTime = null;
                        if (!empty($task['start_time'])) {
                            try {
                                $startTime = \Carbon\Carbon::parse($request->report_date . ' ' . $task['start_time']);
                            } catch (\Exception $e) {}
                        }
                        $endTime = null;
                        if (!empty($task['end_time'])) {
                            try {
                                $endTime = \Carbon\Carbon::parse($request->report_date . ' ' . $task['end_time']);
                            } catch (\Exception $e) {}
                        }

                        $timeSpend = $task['time_spend'] ?? null;
                        if ($startTime && $endTime) {
                            $diffInMinutes = $endTime->diffInMinutes($startTime);
                            $hours = floor($diffInMinutes / 60);
                            $minutes = $diffInMinutes % 60;
                            $calculatedTime = '';
                            if ($hours > 0) $calculatedTime .= $hours . 'h ';
                            if ($minutes > 0) $calculatedTime .= $minutes . 'm';
                            if ($calculatedTime === '') $calculatedTime = '1m';
                            $timeSpend = trim($calculatedTime);
                        }

                        $report->tasks()->create([
                            'source_task_id'=> isset($task['source_task_id']) && $task['source_task_id'] !== '' ? $task['source_task_id'] : null,
                            'task_title'   => $task['task_title'],
                            'description'  => $task['description'] ?? null,
                            'is_carry'     => isset($task['is_carry']) && ($task['is_carry'] === 'true' || $task['is_carry'] === true),
                            'previous_time' => $task['previous_time'] ?? null,
                            'status'       => $task['status'] ?? 'pending',
                            'time_spend'   => $timeSpend,
                            'start_time'   => $startTime,
                            'end_time'     => $endTime,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Report successfully save ho gayi!', 'id' => $report->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Kuch galat hua: ' . $e->getMessage()], 500);
        }
    }

    public function show(DailyReport $dailyReport)
    {
        if (Auth::user()->role === 'staff' && $dailyReport->staff_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $dailyReport->load(['staff', 'tasks']);

        return response()->json([
            'id'           => $dailyReport->id,
            'can_edit'     => Auth::id() === $dailyReport->staff_id || Auth::user()->role === 'admin',
            'report_date'  => $dailyReport->report_date ? $dailyReport->report_date->format('d M Y') : '—',
            'pending_task' => $dailyReport->pending_task,
            'planned_task' => $dailyReport->planned_task,
            'comments'     => $dailyReport->comments,
            'staff'        => $dailyReport->staff ? [
                'name'  => $dailyReport->staff->name,
                'email' => $dailyReport->staff->email,
            ] : null,
            'tasks'        => $dailyReport->tasks->map(fn($t) => [
                'id'            => $t->id,
                'source_task_id'=> $t->source_task_id,
                'task_title'    => $t->task_title,
                'description'   => $t->description,
                'is_carry'      => $t->is_carry,
                'previous_time'  => $t->previous_time,
                'status'        => $t->status,
                'time_spend'    => $t->time_spend,
                'start_time'    => $t->start_time,
                'end_time'      => $t->end_time,
            ])->values()->toArray(),
        ]);
    }

    public function edit(DailyReport $dailyReport)
    {
        if (Auth::user()->role === 'staff' && $dailyReport->staff_id !== Auth::id()) {
            abort(403, 'Aap sirf apni khud ki report edit kar sakte hain.');
        }
        $dailyReport->load('tasks');
        return view('DailyReport.DailyReportCreate', compact('dailyReport'));
    }

    public function update(Request $request, DailyReport $dailyReport)
    {
        if (Auth::user()->role === 'staff' && $dailyReport->staff_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'report_date'  => 'required|date|before_or_equal:today',
            'pending_task' => 'nullable|string',
            'planned_task' => 'nullable|string',
            'comments'     => 'nullable|string',
            'tasks'                => 'nullable|array',
            'tasks.*.task_title'  => 'required_with:tasks.*|string|max:255',
            'tasks.*.description' => 'required_with:tasks.*|string',
            'tasks.*.status'      => 'required_with:tasks.*|in:completed,in_progress,pending,paused',
            'tasks.*.time_spend'  => 'nullable|string|max:100',
            'tasks.*.start_time'  => 'nullable|string',
            'tasks.*.end_time'    => 'nullable|string',
            'tasks.*.source_task_id' => 'nullable|integer|exists:daily_report_tasks,id',
        ]);

        DB::beginTransaction();
        try {
            $dailyReport->update([
                'report_date'  => $request->report_date,
                'pending_task' => $request->pending_task,
                'planned_task' => $request->planned_task,
                'comments'     => $request->comments,
            ]);

            // Delete existing tasks and re-create
            $dailyReport->tasks()->delete();

            if ($request->has('tasks') && is_array($request->tasks)) {
                foreach ($request->tasks as $task) {
                    if (!empty($task['task_title'])) {
                        $startTime = null;
                        if (!empty($task['start_time'])) {
                            try {
                                $startTime = \Carbon\Carbon::parse($request->report_date . ' ' . $task['start_time']);
                            } catch (\Exception $e) {}
                        }
                        $endTime = null;
                        if (!empty($task['end_time'])) {
                            try {
                                $endTime = \Carbon\Carbon::parse($request->report_date . ' ' . $task['end_time']);
                            } catch (\Exception $e) {}
                        }

                        $timeSpend = $task['time_spend'] ?? null;
                        if ($startTime && $endTime) {
                            $diffInMinutes = $endTime->diffInMinutes($startTime);
                            $hours = floor($diffInMinutes / 60);
                            $minutes = $diffInMinutes % 60;
                            $calculatedTime = '';
                            if ($hours > 0) $calculatedTime .= $hours . 'h ';
                            if ($minutes > 0) $calculatedTime .= $minutes . 'm';
                            if ($calculatedTime === '') $calculatedTime = '1m';
                            $timeSpend = trim($calculatedTime);
                        }

                        $dailyReport->tasks()->create([
                            'source_task_id'=> isset($task['source_task_id']) && $task['source_task_id'] !== '' ? $task['source_task_id'] : null,
                            'task_title'    => $task['task_title'],
                            'description'   => $task['description'] ?? null,
                            'is_carry'      => isset($task['is_carry']) && ($task['is_carry'] === 'true' || $task['is_carry'] === true),
                            'previous_time'  => $task['previous_time'] ?? null,
                            'status'        => $task['status'] ?? 'pending',
                            'time_spend'    => $timeSpend,
                            'start_time'    => $startTime,
                            'end_time'      => $endTime,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Report update ho gayi!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Kuch galat hua: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(DailyReport $dailyReport)
    {
        if (Auth::user()->role === 'staff' && $dailyReport->staff_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $dailyReport->tasks()->delete();
        $dailyReport->delete();
        return response()->json(['success' => true, 'message' => 'Report delete ho gayi!']);
    }

    public function getLastTasks(Request $request)
    {
        $date = $request->get('date') ?: now()->toDateString();
        $staffId = Auth::id();

        // Run self-healing backfill for legacy tasks
        self::backfillLegacySourceTaskIds($staffId);

        // Get the latest report BEFORE the selected date to carry forward planned_task
        $lastReport = DailyReport::where('staff_id', $staffId)
            ->where('report_date', '<', $date)
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->first();

        // Get all outstanding incomplete tasks across all reports before the selected date
        $incompleteTasks = DailyReportTask::select('daily_report_tasks.*')
            ->join('daily_reports', 'daily_report_tasks.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.staff_id', $staffId)
            ->where('daily_reports.report_date', '<', $date)
            ->where('daily_report_tasks.status', '!=', 'completed')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('daily_report_tasks as t2')
                    ->whereColumn('t2.source_task_id', 'daily_report_tasks.id');
            })
            ->get();

        return response()->json([
            'success' => true,
            'pending_task' => $lastReport ? $lastReport->planned_task : '',
            'tasks' => $incompleteTasks->map(fn($t) => [
                'source_task_id'=> $t->id,
                'task_title'    => $t->task_title,
                'description'   => $t->description,
                'status'        => $t->status,
                'is_carry'      => true,
                'previous_time' => $this->sumTimeStrings($t->previous_time, $t->time_spend),
                'time_spend'    => '' 
            ])
        ]);
    }

    private function sumTimeStrings($time1, $time2)
    {
        $totalMinutes = 0;
        foreach ([$time1, $time2] as $timeStr) {
            if (!$timeStr) continue;
            $ts = strtolower($timeStr);
            if (preg_match('/(\d+)\s*h/', $ts, $m)) $totalMinutes += (int)$m[1] * 60;
            if (preg_match('/(\d+)\s*m/', $ts, $m)) $totalMinutes += (int)$m[1];
            if (preg_match('/(\d+):(\d+)/', $ts, $m)) $totalMinutes += (int)$m[1] * 60 + (int)$m[2];
        }

        if ($totalMinutes === 0) return '';
        
        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;
        
        return ($h > 0 ? $h . 'h ' : '') . ($m > 0 ? $m . 'm' : '');
    }

    public function startTask(Request $request)
    {
        $request->validate([
            'task_title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Prevent starting if another task is already in_progress
        $activeTask = DailyReportTask::whereHas('dailyReport', function($q) {
            $q->where('staff_id', Auth::id());
        })->where('status', 'in_progress')->first();

        if ($activeTask) {
            return response()->json([
                'success' => false, 
                'message' => 'Aapka ek task pehle se live chal raha hai. Usse pause ya end karein pehle!'
            ], 400);
        }

        // Find today's report for the staff, or create one
        $today = now()->toDateString();
        $report = DailyReport::firstOrCreate(
            ['staff_id' => Auth::id(), 'report_date' => $today],
            ['pending_task' => 'Live Task Tracking', 'planned_task' => 'Live Task Tracking']
        );

        $task = $report->tasks()->create([
            'task_title' => $request->task_title,
            'description' => $request->description,
            'status' => 'in_progress',
            'start_time' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Task started successfully', 'task' => $task]);
    }

    public function endTask(Request $request, DailyReportTask $task)
    {
        $request->validate([
            'description' => 'nullable|string',
        ]);

        if ($task->dailyReport->staff_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $endTime = now();
        
        if ($task->status === 'in_progress') {
            $startTime = $task->start_time ?: $task->created_at;
            $diffInMinutes = $endTime->diffInMinutes($startTime);
            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            
            $segmentTime = '';
            if ($hours > 0) $segmentTime .= $hours . 'h ';
            if ($minutes > 0) $segmentTime .= $minutes . 'm';
            if ($segmentTime === '') $segmentTime = '1m';

            $timeSpend = $this->sumTimeStrings($task->time_spend, $segmentTime);
        } else {
            $timeSpend = $task->time_spend;
        }

        $newDescription = $task->description;
        if ($request->description) {
            $timestamp = now()->format('d M, h:i A');
            $newDescription = $newDescription ? $newDescription . "\n[" . $timestamp . "] " . $request->description : "[" . $timestamp . "] " . $request->description;
        }

        $task->update([
            'description' => $newDescription,
            'status' => 'completed',
            'end_time' => $endTime,
            'time_spend' => trim($timeSpend)
        ]);

        return response()->json(['success' => true, 'message' => 'Task completed successfully', 'task' => $task]);
    }

    public function assignTask(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'task_title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $staffId = $request->staff_id;
        
        if (Auth::user()->role === 'manager') {
            $staffUser = User::with('staff')->find($staffId);
            if (!$staffUser || !$staffUser->staff || $staffUser->staff->office_id !== Auth::user()->staff->office_id) {
                return response()->json(['success' => false, 'message' => 'Staff not found or not in your office'], 403);
            }
        }

        $today = now()->toDateString();
        $report = DailyReport::firstOrCreate(
            ['staff_id' => $staffId, 'report_date' => $today],
            ['pending_task' => 'Assigned Task Tracking', 'planned_task' => 'Assigned Task Tracking']
        );

        $task = $report->tasks()->create([
            'task_title' => $request->task_title,
            'description' => $request->description,
            'status' => 'paused',
            'assigned_by' => Auth::id()
        ]);
        
        \App\Models\Notification::create([
            'user_id' => $staffId,
            'title' => 'New Task Assigned',
            'message' => Auth::user()->name . " assigned a new task: '{$task->task_title}'",
            'url' => route('staff.track-task'),
            'type' => 'info',
            'is_read' => false
        ]);

        return response()->json(['success' => true, 'message' => 'Task assigned successfully to the staff.', 'task' => $task]);
    }

    public static function autoCarryForwardPaused($staffId)
    {
        $today = now()->toDateString();

        // Run self-healing backfill for legacy tasks
        self::backfillLegacySourceTaskIds($staffId);

        // Find all paused tasks across all previous reports that have not been superseded by any subsequent task
        $pausedTasks = DailyReportTask::select('daily_report_tasks.*')
            ->join('daily_reports', 'daily_report_tasks.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.staff_id', $staffId)
            ->where('daily_reports.report_date', '<', $today)
            ->where('daily_report_tasks.status', 'paused')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('daily_report_tasks as t2')
                    ->whereColumn('t2.source_task_id', 'daily_report_tasks.id');
            })
            ->get();

        if ($pausedTasks->isEmpty()) {
            return false;
        }

        // Get or create today's report
        $todayReport = DailyReport::firstOrCreate(
            ['staff_id' => $staffId, 'report_date' => $today],
            ['pending_task' => 'Live Task Tracking', 'planned_task' => 'Live Task Tracking']
        );

        $instance = new self();

        foreach ($pausedTasks as $pTask) {
            // Check if already carried forward today
            $alreadyExists = $todayReport->tasks()->where('source_task_id', $pTask->id)->exists();
            if (!$alreadyExists) {
                // Calculate accumulated previous time
                $accumulatedTime = $instance->sumTimeStrings($pTask->previous_time, $pTask->time_spend);
                
                $todayReport->tasks()->create([
                    'source_task_id' => $pTask->id,
                    'task_title' => $pTask->task_title,
                    'description' => $pTask->description,
                    'status' => 'paused', // carry forward as paused
                    'is_carry' => true,
                    'previous_time' => $accumulatedTime,
                    'time_spend' => null
                ]);
            }
        }
        return true;
    }

    public function getTaskHistory(DailyReportTask $task)
    {
        // Find all tasks with the same title for this staff
        $staffId = $task->dailyReport->staff_id;
        $title = $task->task_title;

        $historyTasks = DailyReportTask::whereHas('dailyReport', function($q) use ($staffId) {
                $q->where('staff_id', $staffId);
            })
            ->where('task_title', $title)
            ->join('daily_reports', 'daily_report_tasks.daily_report_id', '=', 'daily_reports.id')
            ->orderBy('daily_reports.report_date', 'asc')
            ->select('daily_report_tasks.*', 'daily_reports.report_date')
            ->get();

        $historyData = [];
        $totalMinutes = 0;

        foreach ($historyTasks as $hTask) {
            $timeStr = $hTask->time_spend;
            $mins = 0;
            if ($timeStr) {
                $ts = strtolower($timeStr);
                if (preg_match('/(\d+)\s*h/', $ts, $m)) $mins += (int)$m[1] * 60;
                if (preg_match('/(\d+)\s*m/', $ts, $m)) $mins += (int)$m[1];
                if (preg_match('/(\d+):(\d+)/', $ts, $m)) $mins += (int)$m[1] * 60 + (int)$m[2];
            }
            
            // Even if mins is 0, we show it if they worked on it or if it was created
            if ($mins > 0 || $hTask->id === $task->id) {
                $totalMinutes += $mins;
                $historyData[] = [
                    'date' => \Carbon\Carbon::parse($hTask->report_date)->format('d M Y'),
                    'time_spend' => $timeStr ?: '0m',
                    'status' => $hTask->status,
                    'description' => $hTask->description
                ];
            }
        }

        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;
        $totalTimeFormatted = ($h > 0 ? $h . 'h ' : '') . ($m > 0 ? $m . 'm' : '0m');

        return response()->json([
            'success' => true,
            'task_title' => $title,
            'history' => $historyData,
            'total_time' => $totalTimeFormatted
        ]);
    }

    public function pauseTask(Request $request, DailyReportTask $task)
    {
        if ($task->dailyReport->staff_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($task->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Only in-progress tasks can be paused'], 400);
        }

        $endTime = now();
        $startTime = $task->start_time ?: $task->created_at;
        
        $diffInMinutes = $endTime->diffInMinutes($startTime);
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;
        
        $segmentTime = '';
        if ($hours > 0) $segmentTime .= $hours . 'h ';
        if ($minutes > 0) $segmentTime .= $minutes . 'm';
        if ($segmentTime === '') $segmentTime = '1m';

        // Sum with existing time_spend
        $totalTime = $this->sumTimeStrings($task->time_spend, $segmentTime);

        $newDescription = $task->description;
        if ($request->description) {
            $timestamp = now()->format('d M, h:i A');
            $newDescription = $newDescription ? $newDescription . "\n[" . $timestamp . "] " . $request->description : "[" . $timestamp . "] " . $request->description;
        }

        $task->update([
            'description' => $newDescription,
            'status' => 'paused',
            'time_spend' => trim($totalTime),
            'end_time' => null, // Just paused, not finished
        ]);

        return response()->json(['success' => true, 'message' => 'Task paused successfully', 'task' => $task]);
    }

    public function resumeTask(Request $request, DailyReportTask $task)
    {
        if ($task->dailyReport->staff_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($task->status !== 'paused') {
            return response()->json(['success' => false, 'message' => 'Only paused tasks can be resumed'], 400);
        }

        // Before resuming, ensure there is no other live/in_progress task!
        $activeTask = DailyReportTask::whereHas('dailyReport', function($q) {
            $q->where('staff_id', Auth::id());
        })->where('status', 'in_progress')->first();

        if ($activeTask) {
            return response()->json([
                'success' => false, 
                'message' => 'Aapka ek task pehle se live chal raha hai. Usse pause ya end karein pehle!'
            ], 400);
        }

        $task->update([
            'status' => 'in_progress',
            'start_time' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Task resumed successfully', 'task' => $task]);
    }

    public function addOtherTask(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $today = now()->toDateString();
        $report = DailyReport::firstOrCreate(
            ['staff_id' => Auth::id(), 'report_date' => $today],
            ['pending_task' => 'Other Task Tracking', 'planned_task' => 'Other Task Tracking']
        );

        $task = $report->tasks()->where('task_title', 'Other Task')->first();
        
        $timestamp = now()->format('d M, h:i A');
        $appendDesc = "[" . $timestamp . "] " . $request->description;

        if ($task) {
            $newDescription = $task->description ? $task->description . "\n" . $appendDesc : $appendDesc;
            $task->update([
                'description' => $newDescription
            ]);
        } else {
            $task = $report->tasks()->create([
                'task_title' => 'Other Task',
                'description' => $appendDesc,
                'status' => 'completed',
                'time_spend' => null,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Other task updated successfully', 'task' => $task]);
    }

    public function updateTaskDescription(Request $request, DailyReportTask $task)
    {
        if ($task->dailyReport->staff_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'description' => 'required|string',
        ]);

        $newDescription = $task->description;
        $timestamp = now()->format('d M, h:i A');
        $newDescription = $newDescription ? $newDescription . "\n[" . $timestamp . "] " . $request->description : "[" . $timestamp . "] " . $request->description;

        $task->update([
            'description' => $newDescription,
        ]);

        return response()->json(['success' => true, 'message' => 'Task update logged successfully', 'task' => $task]);
    }


    public function liveTasks()
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            return abort(403, 'Unauthorized action.');
        }

        self::autoPauseMidnightTasks(null);

        // Fetch all active staff
        $query = User::with(['staff.department', 'staff.office'])
            ->where('role', 'staff')
            ->whereHas('staff', function($q) {
                $q->where('status', 'active');
            });
            
        // If manager, restrict to their office
        if (Auth::user()->role === 'manager' && Auth::user()->staff) {
            $officeId = Auth::user()->staff->office_id;
            $query->whereHas('staff', function($q) use ($officeId) {
                $q->where('office_id', $officeId);
            });
        }

        // To calculate overall stats:
        $allStaffIds = (clone $query)->pluck('id');
        $totalStaffCount = $allStaffIds->count();

        // Fetch today's tasks grouped by staff_id (both live and completed)
        $todayTasks = DailyReportTask::whereHas('dailyReport', function($q) {
                $q->whereDate('report_date', now()->toDateString());
            })
            ->with('dailyReport')
            ->withCount('comments')
            ->get()
            ->groupBy(function($item) {
                return $item->dailyReport->staff_id;
            });

        $liveCount = 0;
        $pausedCount = 0;
        $completedStaffCount = 0;

        foreach ($allStaffIds as $staffId) {
            $tasks = $todayTasks->get($staffId) ?? collect();
            if ($tasks->where('status', 'in_progress')->isNotEmpty()) {
                $liveCount++;
            } elseif ($tasks->where('status', 'paused')->isNotEmpty()) {
                $pausedCount++;
            } elseif ($tasks->where('status', 'completed')->isNotEmpty()) {
                $completedStaffCount++;
            }
        }
        $idleCount = $totalStaffCount - ($liveCount + $pausedCount + $completedStaffCount);

        $staffListForDropdown = (clone $query)->orderBy('name', 'asc')->get();
        $allStaff = $query->orderBy('name', 'asc')->paginate(5);

        return view('DailyReport.LiveTasks', compact('allStaff', 'staffListForDropdown', 'todayTasks', 'totalStaffCount', 'liveCount', 'pausedCount', 'idleCount'));
    }

    public static function autoPauseMidnightTasks($userId = null)
    {
        $query = DailyReportTask::whereHas('dailyReport', function($q) use ($userId) {
            if ($userId) {
                $q->where('staff_id', $userId);
            }
            $q->whereDate('report_date', '<', today());
        })->where('status', 'in_progress');

        $overdueTasks = $query->get();

        foreach ($overdueTasks as $task) {
            $reportDate = $task->dailyReport->report_date;
            // Target end time is 18:30 (6:30 PM) of the report date
            $endTime = \Carbon\Carbon::parse($reportDate)->setTime(18, 30, 0);
            $startTime = $task->start_time ?: $task->created_at;

            if ($endTime->greaterThan($startTime)) {
                $diffInMinutes = $endTime->diffInMinutes($startTime);
            } else {
                $diffInMinutes = 1;
            }

            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            
            $segmentTime = '';
            if ($hours > 0) $segmentTime .= $hours . 'h ';
            if ($minutes > 0) $segmentTime .= $minutes . 'm';
            if ($segmentTime === '') $segmentTime = '1m';

            // Sum with existing time_spend
            $totalMinutes = 0;
            foreach ([$task->time_spend, $segmentTime] as $timeStr) {
                if (!$timeStr) continue;
                $ts = strtolower($timeStr);
                if (preg_match('/(\d+)\s*h/', $ts, $m)) $totalMinutes += (int)$m[1] * 60;
                if (preg_match('/(\d+)\s*m/', $ts, $m)) $totalMinutes += (int)$m[1];
                if (preg_match('/(\d+):(\d+)/', $ts, $m)) $totalMinutes += (int)$m[1] * 60 + (int)$m[2];
            }

            $h = floor($totalMinutes / 60);
            $m = $totalMinutes % 60;
            
            $totalTime = ($h > 0 ? $h . 'h ' : '') . ($m > 0 ? $m . 'm' : '');
            if (trim($totalTime) === '') $totalTime = '1m';

            $task->update([
                'status' => 'paused',
                'time_spend' => trim($totalTime),
                'end_time' => null, // Just paused
            ]);
        }
    }

    private static function backfillLegacySourceTaskIds($staffId)
    {
        $carryTasks = DailyReportTask::select('daily_report_tasks.id', 'daily_report_tasks.task_title', 'daily_reports.report_date', 'daily_reports.id as report_id')
            ->join('daily_reports', 'daily_report_tasks.daily_report_id', '=', 'daily_reports.id')
            ->where('daily_reports.staff_id', $staffId)
            ->where('daily_report_tasks.is_carry', true)
            ->whereNull('daily_report_tasks.source_task_id')
            ->orderBy('daily_reports.report_date', 'asc')
            ->orderBy('daily_reports.id', 'asc')
            ->orderBy('daily_report_tasks.id', 'asc')
            ->get();

        foreach ($carryTasks as $ct) {
            $sourceTask = DailyReportTask::select('daily_report_tasks.id')
                ->join('daily_reports', 'daily_report_tasks.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.staff_id', $staffId)
                ->where('daily_report_tasks.task_title', $ct->task_title)
                ->where(function($q) use ($ct) {
                    $q->where('daily_reports.report_date', '<', $ct->report_date)
                      ->orWhere(function($q2) use ($ct) {
                          $q2->where('daily_reports.report_date', '=', $ct->report_date)
                             ->where('daily_reports.id', '<', $ct->report_id);
                      });
                })
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('daily_report_tasks as t3')
                        ->whereColumn('t3.source_task_id', 'daily_report_tasks.id');
                })
                ->orderByDesc('daily_reports.report_date')
                ->orderByDesc('daily_reports.id')
                ->orderByDesc('daily_report_tasks.id')
                ->first();

            if ($sourceTask) {
                DailyReportTask::where('id', $ct->id)->update(['source_task_id' => $sourceTask->id]);
            }
        }
    }

    public function taskReport(DailyReportTask $task)
    {
        $staffId = $task->dailyReport->staff_id;
        $title = $task->task_title;

        $historyTasks = DailyReportTask::whereHas('dailyReport', function($q) use ($staffId) {
                $q->where('staff_id', $staffId);
            })
            ->where('task_title', $title)
            ->join('daily_reports', 'daily_report_tasks.daily_report_id', '=', 'daily_reports.id')
            ->orderBy('daily_reports.report_date', 'asc')
            ->select('daily_report_tasks.*', 'daily_reports.report_date')
            ->get();

        $historyData = [];
        $totalMinutes = 0;

        foreach ($historyTasks as $hTask) {
            $timeStr = $hTask->time_spend;
            $mins = 0;
            if ($timeStr) {
                $ts = strtolower($timeStr);
                if (preg_match('/(\d+)\s*h/', $ts, $m)) $mins += (int)$m[1] * 60;
                if (preg_match('/(\d+)\s*m/', $ts, $m)) $mins += (int)$m[1];
                if (preg_match('/(\d+):(\d+)/', $ts, $m)) $mins += (int)$m[1] * 60 + (int)$m[2];
            }
            
            if ($mins > 0 || $hTask->id === $task->id || $hTask->status == 'completed' || $hTask->status == 'idle') {
                $totalMinutes += $mins;
                $historyData[] = [
                    'date' => \Carbon\Carbon::parse($hTask->report_date)->format('d M Y'),
                    'time_spend' => $timeStr ?: '0m',
                    'status' => $hTask->status,
                    'description' => $hTask->description
                ];
            }
        }

        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;
        $totalTimeFormatted = ($h > 0 ? $h . 'h ' : '') . ($m > 0 ? $m . 'm' : '0m');

        return view('DailyReport.TaskReport', compact('task', 'title', 'historyData', 'totalTimeFormatted'));
    }

    public function exportTaskReport(Request $request, DailyReportTask $task, $format)
    {
        $staffId = $task->dailyReport->staff_id;
        $title = $task->task_title;

        $historyTasks = DailyReportTask::whereHas('dailyReport', function($q) use ($staffId) {
                $q->where('staff_id', $staffId);
            })
            ->where('task_title', $title)
            ->join('daily_reports', 'daily_report_tasks.daily_report_id', '=', 'daily_reports.id')
            ->orderBy('daily_reports.report_date', 'asc')
            ->select('daily_report_tasks.*', 'daily_reports.report_date')
            ->get();

        $historyData = [];
        $totalMinutes = 0;

        foreach ($historyTasks as $hTask) {
            $timeStr = $hTask->time_spend;
            $mins = 0;
            if ($timeStr) {
                $ts = strtolower($timeStr);
                if (preg_match('/(\d+)\s*h/', $ts, $m)) $mins += (int)$m[1] * 60;
                if (preg_match('/(\d+)\s*m/', $ts, $m)) $mins += (int)$m[1];
                if (preg_match('/(\d+):(\d+)/', $ts, $m)) $mins += (int)$m[1] * 60 + (int)$m[2];
            }
            
            if ($mins > 0 || $hTask->id === $task->id || $hTask->status == 'completed' || $hTask->status == 'idle') {
                $totalMinutes += $mins;
                $historyData[] = [
                    'date' => \Carbon\Carbon::parse($hTask->report_date)->format('d M Y'),
                    'time_spend' => $timeStr ?: '0m',
                    'status' => $hTask->status,
                    'description' => $hTask->description
                ];
            }
        }

        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;
        $totalTimeFormatted = ($h > 0 ? $h . 'h ' : '') . ($m > 0 ? $m . 'm' : '0m');

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('DailyReport.TaskReportPDF', compact('task', 'title', 'historyData', 'totalTimeFormatted'));
            return $pdf->download('Task_Report_' . str_replace(' ', '_', $title) . '.pdf');
        } elseif ($format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TaskReportExport($title, $historyData, $totalTimeFormatted, $task->dailyReport->staff->name ?? ''), 'Task_Report_' . str_replace(' ', '_', $title) . '.xlsx');
        }

        abort(404);
    }
}
