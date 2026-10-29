<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectTaskComment;
use App\Models\ProjectMessage;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Project $project)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$user->isAdmin() && !$project->isMember($userId)) {
            abort(403, 'You do not have permission to add tasks to this project.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,review,completed',
        ]);

        $assignedTo = !empty($validated['assigned_to']) ? $validated['assigned_to'] : $userId;

        $task = ProjectTask::create([
            'project_id' => $project->id,
            'created_by' => $userId,
            'assigned_to' => $assignedTo,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'] ?? 'pending',
            'completed_at' => ($validated['status'] ?? 'pending') === 'completed' ? now() : null,
        ]);

        // Send notification if assigned to another staff member
        if ($assignedTo != $userId) {
            Notification::create([
                'user_id' => $assignedTo,
                'title' => 'New Task Assigned',
                'message' => $user->name . ' assigned you task "' . $task->title . '" in project "' . $project->title . '".',
                'url' => route('projects.show', $project->id),
                'type' => 'project_task',
                'is_read' => false,
            ]);
        }

        // Post interactive Task Card to WhatsApp-style project chat feed
        $assignedUser = $task->assignedTo;
        $assigneeLabel = $assignedTo == $userId ? 'themselves' : ($assignedUser ? $assignedUser->name : 'colleague');
        ProjectMessage::create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'project_task_id' => $task->id,
            'type' => 'task_card',
            'message' => 'created task "' . $task->title . '" and assigned it to ' . $assigneeLabel . '.',
            'metadata' => [
                'priority' => $task->priority,
                'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                'status' => $task->status,
            ],
        ]);

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Task added to project successfully!');
    }

    public function update(Request $request, ProjectTask $projectTask)
    {
        $userId = Auth::id();
        $user = Auth::user();

        // Only the task creator (or Admin) can edit task details
        $canEdit = $user->isAdmin() || $projectTask->created_by == $userId;

        if (!$canEdit) {
            abort(403, 'Only the person who created this task can edit its details.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,review,completed',
        ]);

        $oldAssignedTo = $projectTask->assigned_to;
        $newAssignedTo = !empty($validated['assigned_to']) ? $validated['assigned_to'] : null;

        if ($validated['status'] === 'completed' && $projectTask->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $projectTask->update($validated);

        // Notify new assignee if changed
        if ($newAssignedTo && $newAssignedTo != $oldAssignedTo && $newAssignedTo != $userId) {
            Notification::create([
                'user_id' => $newAssignedTo,
                'title' => 'Task Re-assigned',
                'message' => $user->name . ' assigned you task "' . $projectTask->title . '" in project "' . $projectTask->project->title . '".',
                'url' => route('projects.show', $projectTask->project_id),
                'type' => 'project_task',
                'is_read' => false,
            ]);
        }

        // Notify task creator if someone else changed status to completed
        if ($validated['status'] === 'completed' && $projectTask->created_by != $userId) {
            Notification::create([
                'user_id' => $projectTask->created_by,
                'title' => 'Task Completed',
                'message' => $user->name . ' marked task "' . $projectTask->title . '" as completed.',
                'url' => route('projects.show', $projectTask->project_id),
                'type' => 'project_task',
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    public function updateStatus(Request $request, ProjectTask $projectTask)
    {
        $userId = Auth::id();
        $user = Auth::user();

        // Only the assigned user (or Admin) can update status
        if (!$user->isAdmin() && $projectTask->assigned_to != $userId) {
            abort(403, 'Only the assigned staff member can update this task.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,review,completed',
        ]);

        $oldStatus = $projectTask->status;
        $status = $validated['status'];
        $completedAt = $status === 'completed' ? now() : null;

        $projectTask->update([
            'status' => $status,
            'completed_at' => $completedAt,
        ]);

        // Post status update event in project group chat
        ProjectMessage::create([
            'project_id' => $projectTask->project_id,
            'user_id' => $userId,
            'project_task_id' => $projectTask->id,
            'type' => 'status_update',
            'message' => 'changed status of "' . $projectTask->title . '" to ' . ucfirst(str_replace('_', ' ', $status)) . '.',
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $status,
            ],
        ]);

        // Notify creator if completed by another person
        if ($status === 'completed' && $projectTask->created_by != $userId) {
            Notification::create([
                'user_id' => $projectTask->created_by,
                'title' => 'Task Completed',
                'message' => $user->name . ' marked task "' . $projectTask->title . '" as completed.',
                'url' => route('projects.show', $projectTask->project_id),
                'type' => 'project_task',
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Task status updated to: ' . ucfirst(str_replace('_', ' ', $status)));
    }

    public function addComment(Request $request, ProjectTask $projectTask)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $project = $projectTask->project;

        if (!$user->isAdmin() && !$project->isMember($userId)) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        ProjectTaskComment::create([
            'project_task_id' => $projectTask->id,
            'user_id' => $userId,
            'comment' => $validated['comment'],
        ]);

        // Post comment to group chat
        ProjectMessage::create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'project_task_id' => $projectTask->id,
            'type' => 'chat',
            'message' => $validated['comment'],
        ]);

        // Notify assignee or creator if not current user
        $notifyTargets = array_unique(array_filter([$projectTask->created_by, $projectTask->assigned_to]));
        foreach ($notifyTargets as $targetId) {
            if ($targetId != $userId) {
                Notification::create([
                    'user_id' => $targetId,
                    'title' => 'New Update on Task',
                    'message' => $user->name . ' posted an update on task "' . $projectTask->title . '".',
                    'url' => route('projects.show', $project->id),
                    'type' => 'project_task',
                    'is_read' => false,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Update posted successfully!');
    }

    public function destroy(ProjectTask $projectTask)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $project = $projectTask->project;

        // Can delete if admin, project owner, or task creator
        $canDelete = $user->isAdmin() ||
                     $project->isOwner($userId) ||
                     $projectTask->created_by == $userId;

        if (!$canDelete) {
            abort(403, 'You do not have permission to delete this task.');
        }

        $projectTask->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
