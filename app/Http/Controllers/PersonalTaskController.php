<?php

namespace App\Http\Controllers;

use App\Models\PersonalTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $filter = $request->query('filter', 'all');

        $query = PersonalTask::where('user_id', $userId);

        if ($filter === 'daily') {
            $query->where('type', 'daily');
        } elseif ($filter === 'scheduled') {
            $query->where('type', 'scheduled')->where('status', 'pending');
        } elseif ($filter === 'completed') {
            $query->where(function ($q) {
                $q->where('status', 'completed')
                  ->orWhere(function ($sub) {
                      $sub->where('type', 'daily')
                          ->whereDate('last_completed_at', today());
                  });
            });
        }

        $tasks = $query->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
                       ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
                       ->orderBy('due_date', 'asc')
                       ->orderByDesc('created_at')
                       ->get();

        // Statistics
        $allTasks = PersonalTask::where('user_id', $userId)->get();
        $dailyTasks = $allTasks->where('type', 'daily');
        $dailyCompletedToday = $dailyTasks->filter(fn($t) => $t->last_completed_at && $t->last_completed_at->isToday())->count();
        $scheduledTasks = $allTasks->where('type', 'scheduled');
        $upcomingCount = $scheduledTasks->where('status', 'pending')->where('due_date', '>=', today())->count();
        $overdueCount = $scheduledTasks->where('status', 'pending')->where('due_date', '<', today())->count();
        $completedCount = $allTasks->filter(function ($t) {
            return $t->status === 'completed' || ($t->type === 'daily' && $t->last_completed_at && $t->last_completed_at->isToday());
        })->count();

        $stats = [
            'total' => $allTasks->count(),
            'daily_total' => $dailyTasks->count(),
            'daily_completed_today' => $dailyCompletedToday,
            'scheduled_total' => $scheduledTasks->count(),
            'upcoming' => $upcomingCount,
            'overdue' => $overdueCount,
            'completed' => $completedCount,
        ];

        return view('personal_tasks.index', compact('tasks', 'stats', 'filter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:daily,scheduled',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ]);

        if ($validated['type'] === 'scheduled' && empty($validated['due_date'])) {
            $validated['due_date'] = today()->toDateString();
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        PersonalTask::create($validated);

        return redirect()->route('personal-tasks.index')
            ->with('success', 'Task created successfully!');
    }

    public function update(Request $request, PersonalTask $personalTask)
    {
        if ($personalTask->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:daily,scheduled',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pending,completed',
        ]);

        $personalTask->update($validated);

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    public function toggle(PersonalTask $personalTask)
    {
        if ($personalTask->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($personalTask->isDaily()) {
            $isDoneToday = $personalTask->last_completed_at && $personalTask->last_completed_at->isToday();
            if ($isDoneToday) {
                $personalTask->update([
                    'last_completed_at' => null,
                    'status' => 'pending',
                ]);
                $msg = 'Daily task marked as pending for today.';
            } else {
                $personalTask->update([
                    'last_completed_at' => today(),
                    'status' => 'completed',
                ]);
                $msg = 'Daily task marked as completed for today!';
            }
        } else {
            if ($personalTask->status === 'completed') {
                $personalTask->update([
                    'status' => 'pending',
                ]);
                $msg = 'Task marked as pending.';
            } else {
                $personalTask->update([
                    'status' => 'completed',
                    'last_completed_at' => today(),
                ]);
                $msg = 'Task marked as completed!';
            }
        }

        return redirect()->back()->with('success', $msg);
    }

    public function destroy(PersonalTask $personalTask)
    {
        if ($personalTask->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $personalTask->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
