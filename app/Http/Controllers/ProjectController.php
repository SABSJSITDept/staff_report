<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectMessage;
use App\Models\ProjectMessageRead;
use App\Models\Staff\StaffModel;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $filter = $request->query('filter', 'all');

        $query = Project::with(['creator', 'office', 'members', 'tasks']);

        if (!$user->isAdmin()) {
            // Must be creator or member
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhereHas('projectMembers', function ($sub) use ($userId) {
                      $sub->where('user_id', $userId);
                  });
            });
        }

        if ($filter === 'my_projects') {
            $query->where('created_by', $userId);
        } elseif ($filter === 'shared_with_me') {
            $query->where('created_by', '!=', $userId)
                  ->whereHas('projectMembers', function ($sub) use ($userId) {
                      $sub->where('user_id', $userId);
                  });
        } elseif ($filter === 'completed') {
            $query->where('status', 'completed');
        } elseif ($filter === 'in_progress') {
            $query->where('status', 'in_progress');
        }

        $projects = $query->orderByDesc('created_at')->get();

        // Get available staff members for project sharing (Same Office Staff)
        $userOfficeId = $user->staff?->office_id;
        $officeStaffQuery = StaffModel::where('status', 'Active')
            ->whereNotNull('user_id')
            ->where('user_id', '!=', $userId)
            ->with(['user', 'department', 'office']);

        if ($userOfficeId && !$user->isAdmin()) {
            $officeStaffQuery->where('office_id', $userOfficeId);
        }

        $colleagues = $officeStaffQuery->orderBy('name')->get();

        // Project statistics
        $allUserProjects = Project::where(function ($q) use ($userId, $user) {
            if ($user->isAdmin()) return;
            $q->where('created_by', $userId)
              ->orWhereHas('projectMembers', function ($sub) use ($userId) {
                  $sub->where('user_id', $userId);
              });
        })->get();

        $stats = [
            'total' => $allUserProjects->count(),
            'my_created' => $allUserProjects->where('created_by', $userId)->count(),
            'shared' => $allUserProjects->where('created_by', '!=', $userId)->count(),
            'in_progress' => $allUserProjects->where('status', 'in_progress')->count(),
            'completed' => $allUserProjects->where('status', 'completed')->count(),
        ];

        return view('projects.index', compact('projects', 'colleagues', 'stats', 'filter', 'userOfficeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $user = Auth::user();
        $userOfficeId = $user->staff?->office_id;

        $project = Project::create([
            'created_by' => $user->id,
            'office_id' => $userOfficeId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'in_progress',
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'] ?? today(),
            'due_date' => $validated['due_date'] ?? null,
        ]);

        // Add creator as owner
        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        // Add selected office colleagues
        if (!empty($validated['member_ids'])) {
            foreach ($validated['member_ids'] as $memberId) {
                if ($memberId != $user->id) {
                    ProjectMember::firstOrCreate([
                        'project_id' => $project->id,
                        'user_id' => $memberId,
                    ], [
                        'role' => 'member',
                    ]);

                    // Send notification to member
                    Notification::create([
                        'user_id' => $memberId,
                        'title' => 'New Project Shared',
                        'message' => $user->name . ' added you to project "' . $project->title . '".',
                        'url' => route('projects.show', $project->id),
                        'type' => 'project',
                        'is_read' => false,
                    ]);
                }
            }
        }

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Project created successfully and shared with team members!');
    }

    public function show(Project $project, Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$user->isAdmin() && !$project->isMember($userId)) {
            abort(403, 'You do not have permission to view this project.');
        }

        $project->load(['creator.staff', 'office', 'members.staff.department']);

        $taskFilter = $request->query('task_filter', 'all');

        $tasksQuery = $project->tasks()->with(['creator.staff', 'assignedTo.staff', 'comments.user.staff']);

        if ($taskFilter === 'assigned_to_me') {
            $tasksQuery->where('assigned_to', $userId);
        } elseif ($taskFilter === 'created_by_me') {
            $tasksQuery->where('created_by', $userId);
        } elseif ($taskFilter === 'pending') {
            $tasksQuery->where('status', 'pending');
        } elseif ($taskFilter === 'in_progress') {
            $tasksQuery->where('status', 'in_progress');
        } elseif ($taskFilter === 'review') {
            $tasksQuery->where('status', 'review');
        } elseif ($taskFilter === 'completed') {
            $tasksQuery->where('status', 'completed');
        }

        $tasks = $tasksQuery->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
                            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
                            ->orderBy('due_date', 'asc')
                            ->orderByDesc('created_at')
                            ->get();

        $allTasks = $project->tasks()->get();
        $taskStats = [
            'total' => $allTasks->count(),
            'pending' => $allTasks->where('status', 'pending')->count(),
            'in_progress' => $allTasks->where('status', 'in_progress')->count(),
            'review' => $allTasks->where('status', 'review')->count(),
            'completed' => $allTasks->where('status', 'completed')->count(),
            'assigned_to_me' => $allTasks->where('assigned_to', $userId)->count(),
            'created_by_me' => $allTasks->where('created_by', $userId)->count(),
            'progress' => $project->progressPercentage(),
        ];

        // Available colleagues in same office to add more members if owner
        $userOfficeId = $project->office_id ?? $user->staff?->office_id;
        $currentMemberIds = $project->members()->pluck('users.id')->toArray();
        $availableStaff = StaffModel::where('status', 'Active')
            ->whereNotNull('user_id')
            ->whereNotIn('user_id', $currentMemberIds);

        if ($userOfficeId && !$user->isAdmin()) {
            $availableStaff->where('office_id', $userOfficeId);
        }
        $availableStaff = $availableStaff->with(['user', 'department'])->orderBy('name')->get();

        // Load project group messages for WhatsApp-style chat feed
        // Automatically record seen status for all incoming messages in this project
        $unseenMessageIds = $project->messages()
            ->where('user_id', '!=', $userId)
            ->whereDoesntHave('reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->pluck('id');

        if ($unseenMessageIds->isNotEmpty()) {
            $now = now();
            $readRecords = [];
            foreach ($unseenMessageIds as $mId) {
                $readRecords[] = [
                    'project_message_id' => $mId,
                    'user_id' => $userId,
                    'seen_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            ProjectMessageRead::insert($readRecords);
        }

        // Load project group messages for WhatsApp-style chat feed with read receipts history
        $messages = $project->messages()
            ->with(['user.staff', 'task.assignedTo.staff', 'task.creator.staff', 'reads.user.staff'])
            ->oldest()
            ->get();

        return view('projects.show', compact('project', 'tasks', 'taskStats', 'taskFilter', 'availableStaff', 'messages'));
    }

    public function sendMessage(Request $request, Project $project)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$user->isAdmin() && !$project->isMember($userId)) {
            abort(403, 'Unauthorized access to project.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'project_task_id' => 'nullable|exists:project_tasks,id',
        ]);

        ProjectMessage::create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'project_task_id' => $validated['project_task_id'] ?? null,
            'type' => 'chat',
            'message' => $validated['message'],
        ]);

        // Notify other team members
        $allMemberIds = $project->members()->pluck('users.id')->toArray();
        if (!in_array($project->created_by, $allMemberIds)) {
            $allMemberIds[] = $project->created_by;
        }

        foreach ($allMemberIds as $memberId) {
            if ($memberId != $userId) {
                Notification::create([
                    'user_id' => $memberId,
                    'title' => 'New message in ' . $project->title,
                    'message' => $user->name . ': ' . substr($validated['message'], 0, 80),
                    'url' => route('projects.show', $project->id),
                    'type' => 'project_message',
                    'is_read' => false,
                ]);
            }
        }

        return redirect()->route('projects.show', $project->id . '#chat-bottom');
    }

    public function update(Request $request, Project $project)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$user->isAdmin() && !$project->isOwner($userId)) {
            abort(403, 'Only project owner or admin can update project details.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:planning,in_progress,on_hold,completed',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        $project->update($validated);

        return redirect()->back()->with('success', 'Project details updated successfully!');
    }

    public function addMembers(Request $request, Project $project)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$user->isAdmin() && !$project->isOwner($userId)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        foreach ($validated['member_ids'] as $memberId) {
            ProjectMember::firstOrCreate([
                'project_id' => $project->id,
                'user_id' => $memberId,
            ], [
                'role' => 'member',
            ]);

            Notification::create([
                'user_id' => $memberId,
                'title' => 'New Project Shared',
                'message' => $user->name . ' added you to project "' . $project->title . '".',
                'url' => route('projects.show', $project->id),
                'type' => 'project',
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'New team members added to project successfully!');
    }

    public function removeMember(Project $project, User $member)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$user->isAdmin() && !$project->isOwner($userId)) {
            abort(403, 'Unauthorized action.');
        }

        if ($member->id == $project->created_by) {
            return redirect()->back()->with('error', 'Project creator cannot be removed.');
        }

        ProjectMember::where('project_id', $project->id)
            ->where('user_id', $member->id)
            ->delete();

        return redirect()->back()->with('success', 'Member removed from project successfully.');
    }

    public function updateStatus(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => 'required|in:planning,in_progress,on_hold,completed',
        ]);

        $project->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Project status updated successfully!');
    }

    public function destroy(Project $project)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$user->isAdmin() && !$project->isOwner($userId)) {
            abort(403, 'Only project owner or admin can delete this project.');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function messageReadInfo(Project $project, ProjectMessage $message)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$user->isAdmin() && !$project->isMember($userId)) {
            abort(403, "Unauthorized");
        }

        // Load reads with users
        $message->load(['reads.user.staff', 'user.staff']);

        $readUserIds = $message->reads->pluck('user_id')->toArray();

        $readBy = $message->reads->map(function ($read) {
            return [
                'user_id' => $read->user_id,
                'name' => $read->user->name ?? "Unknown",
                'role' => $read->user->role ?? "staff",
                'seen_at' => $read->seen_at ? $read->seen_at->format('d M, h:i A') : "",
                'seen_at_relative' => $read->seen_at ? $read->seen_at->diffForHumans() : "",
            ];
        });

        // Other project members who haven't seen it yet
        $otherMembers = $project->members
            ->where('id', '!=', $message->user_id)
            ->whereNotIn('id', $readUserIds)
            ->map(function ($m) {
                return [
                    'user_id' => $m->id,
                    'name' => $m->name,
                    'role' => $m->role,
                ];
            })
            ->values();

        return response()->json([
            'message_id' => $message->id,
            'message_text' => $message->message,
            'sender_name' => $message->user ? $message->user->name : "System",
            'sent_at' => $message->created_at->format('d M Y, h:i A'),
            'read_by' => $readBy,
            'not_read_yet' => $otherMembers,
            'total_members' => $project->members->where('id', '!=', $message->user_id)->count(),
        ]);
    }

    public function markMessagesSeen(Request $request, Project $project)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$user->isAdmin() && !$project->isMember($userId)) {
            return response()->json(['success' => false], 403);
        }

        $unseenMessageIds = $project->messages()
            ->where('user_id', '!=', $userId)
            ->whereDoesntHave('reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->pluck('id');

        if ($unseenMessageIds->isNotEmpty()) {
            $now = now();
            $readRecords = [];
            foreach ($unseenMessageIds as $mId) {
                $readRecords[] = [
                    'project_message_id' => $mId,
                    'user_id' => $userId,
                    'seen_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            ProjectMessageRead::insert($readRecords);
        }

        return response()->json(['success' => true, 'marked_count' => $unseenMessageIds->count()]);
    }

}