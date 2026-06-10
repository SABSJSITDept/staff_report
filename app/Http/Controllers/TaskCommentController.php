<?php

namespace App\Http\Controllers;

use App\Models\DailyReportTask;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(DailyReportTask $task)
    {
        // Check access
        $user = Auth::user();
        if ($user->role === 'staff' && $task->dailyReport->staff_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sessions = $task->sessions;
        $systemEvents = collect();

        foreach ($sessions as $session) {
            if ($session->start_time) {
                $systemEvents->push([
                    'id' => 's_start_' . $session->id,
                    'user_name' => 'System',
                    'user_role' => 'system',
                    'comment' => '▶ Task Started / Resumed',
                    'created_at_raw' => $session->start_time,
                    'created_at' => $session->start_time->format('d M Y, h:i A'),
                    'is_own' => false,
                    'is_system' => true
                ]);
            }
            if ($session->end_time) {
                $systemEvents->push([
                    'id' => 's_end_' . $session->id,
                    'user_name' => 'System',
                    'user_role' => 'system',
                    'comment' => '⏸ Task Paused / Completed',
                    'created_at_raw' => $session->end_time,
                    'created_at' => $session->end_time->format('d M Y, h:i A'),
                    'is_own' => false,
                    'is_system' => true
                ]);
            }
        }

        $comments = $task->comments()->with('user')->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'user_name' => $c->user->name ?? 'Unknown',
                'user_role' => $c->user->role ?? 'staff',
                'comment' => $c->comment,
                'created_at_raw' => $c->created_at,
                'created_at' => $c->created_at->format('d M Y, h:i A'),
                'is_own' => $c->user_id === Auth::id(),
                'is_system' => false
            ];
        });

        $allActivity = $comments->concat($systemEvents)->sortBy('created_at_raw')->values()->map(function ($item) {
            unset($item['created_at_raw']);
            return $item;
        });

        return response()->json([
            'success' => true,
            'task_title' => $task->task_title,
            'staff_name' => $task->dailyReport->staff->name ?? 'Staff',
            'comments' => $allActivity
        ]);
    }

    public function store(Request $request, DailyReportTask $task)
    {
        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        if ($user->role === 'staff' && $task->dailyReport->staff_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment = $task->comments()->create([
            'user_id' => $user->id,
            'comment' => $request->comment
        ]);

        // Create Notification
        $notifyUserIds = [];
        $url = '';
        if ($user->role === 'staff') {
            $url = route('daily-report.index'); // Admin/Manager views daily reports
            
            // Notify the person who assigned it
            if ($task->assigned_by) {
                $notifyUserIds[] = $task->assigned_by;
            }
            
            // Notify admins/managers who commented on this task
            $commentedUserIds = $task->comments()
                ->where('user_id', '!=', $user->id)
                ->whereHas('user', function($q) {
                    $q->whereIn('role', ['admin', 'manager']);
                })
                ->pluck('user_id')
                ->toArray();
                
            $notifyUserIds = array_merge($notifyUserIds, $commentedUserIds);
            
            // If still no one to notify (e.g., staff comments first on their own task), maybe notify admins?
            // User requested "admin ko bhi", let's notify all admins just to be safe if no one is explicitly linked.
            if (empty($notifyUserIds)) {
                $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                $notifyUserIds = array_merge($notifyUserIds, $adminIds);
            }
        } else {
            // Admin/Manager commented, notify staff
            $notifyUserIds[] = $task->dailyReport->staff_id;
            $url = route('staff.track-task'); // Staff views track task
        }

        $notifyUserIds = array_unique($notifyUserIds);

        foreach ($notifyUserIds as $notifyUserId) {
            if ($notifyUserId && $notifyUserId !== $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $notifyUserId,
                    'title' => 'New Comment on Task',
                    'message' => "{$user->name} commented on '{$task->task_title}'",
                    'url' => $url,
                    'type' => 'info',
                    'is_read' => false
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at->format('d M Y, h:i A'),
                'is_own' => true
            ]
        ]);
    }
}
