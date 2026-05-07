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
        $query = DailyReport::with(['staff', 'tasks'])
            ->orderByDesc('report_date')
            ->orderByDesc('created_at');

        // Staff sirf apni khud ki reports dekh sakta hai
        if (Auth::user()->role === 'staff') {
            $query->where('staff_id', Auth::id());
        }

        $reports = $query->get();

        // Fetch staff for dropdown (Admin/Manager only)
        $allStaff = [];
        if (Auth::user()->role !== 'staff') {
            $allStaff = User::whereIn('role', ['staff', 'manager', 'admin'])
                ->orderBy('name')
                ->get();
        }

        return view('DailyReport.DailyReportView', compact('reports', 'allStaff'));
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
        return view('DailyReport.DailyReportCreate');
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_date'  => 'required|date',
            'pending_task' => 'nullable|string',
            'planned_task' => 'nullable|string',
            'comments'     => 'nullable|string',
            'tasks'                => 'nullable|array',
            'tasks.*.task_title'  => 'required_with:tasks.*|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.status'      => 'required_with:tasks.*|in:completed,in_progress,pending,paused',
            'tasks.*.time_spend'  => 'nullable|string|max:100',
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
                        $report->tasks()->create([
                            'task_title'  => $task['task_title'],
                            'description' => $task['description'] ?? null,
                            'status'      => $task['status'] ?? 'pending',
                            'time_spend'  => $task['time_spend'] ?? null,
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
            'report_date'  => $dailyReport->report_date ? $dailyReport->report_date->format('d M Y') : '—',
            'pending_task' => $dailyReport->pending_task,
            'planned_task' => $dailyReport->planned_task,
            'comments'     => $dailyReport->comments,
            'staff'        => $dailyReport->staff ? [
                'name'  => $dailyReport->staff->name,
                'email' => $dailyReport->staff->email,
            ] : null,
            'tasks'        => $dailyReport->tasks->map(fn($t) => [
                'task_title'  => $t->task_title,
                'description' => $t->description,
                'status'      => $t->status,
                'time_spend'  => $t->time_spend,
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
            'report_date'  => 'required|date',
            'pending_task' => 'nullable|string',
            'planned_task' => 'nullable|string',
            'comments'     => 'nullable|string',
            'tasks'                => 'nullable|array',
            'tasks.*.task_title'  => 'required_with:tasks.*|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.status'      => 'required_with:tasks.*|in:completed,in_progress,pending,paused',
            'tasks.*.time_spend'  => 'nullable|string|max:100',
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
                        $dailyReport->tasks()->create([
                            'task_title'  => $task['task_title'],
                            'description' => $task['description'] ?? null,
                            'status'      => $task['status'] ?? 'pending',
                            'time_spend'  => $task['time_spend'] ?? null,
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
}
