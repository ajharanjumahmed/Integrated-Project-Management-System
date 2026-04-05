<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\WorkSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KanbanController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user();

    $tasks = Task::where('assigned_to', $user->id)
    ->with([
        'project:id,title,status',
        'milestone:id,title',
        'submissions' => fn($q) => $q->latest()
            ->select('id', 'task_id', 'status', 'pm_note', 'created_at'),
    ])
    ->orderBy('deadline')
    ->get();

    $projects = $tasks
        ->groupBy('project_id')
        ->map(function ($projectTasks) {
            $first = $projectTasks->first();
            return [
                'id'        => $first->project->id,
                'title'     => $first->project->title,
                'status'    => $first->project->status,
                'pending'   => $projectTasks->where('status', 'pending')->values(),
                'started'   => $projectTasks->where('status', 'started')->values(),
                'completed' => $projectTasks->where('status', 'completed')->values(),
            ];
        })
        ->values();

    // Find if the user has any currently active (not signed out) session.
    // We pass the project_id so the frontend knows WHICH project button
    // to show as "active / signed in".
    $activeSession = WorkSession::active()
        ->where('user_id', $user->id)
        ->select('id', 'project_id', 'start_time')
        ->first();

    return Inertia::render('Kanban/Index', [
        'projects'      => $projects,
        'activeSession' => $activeSession,
    ]);
}


    public function updateStatus(Request $request, Task $task)
    {
        if ($task->assigned_to !== $request->user()->id) {
            abort(403, 'You can only update your own tasks.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,started,completed',
        ]);

        $task->update(['status' => $validated['status']]);

        return back()->with('success', 'Task status updated.');
    }
}