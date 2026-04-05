<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',

            'assigned_to'  => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($project) {
                    $isMember = $project->members()->where('user_id', $value)->exists();
                    if (! $isMember) {
                        $fail('This user is not a member of this project.');
                    }
                },
            ],

            'milestone_id' => 'nullable|exists:milestones,id',

            'priority'     => 'required|in:low,medium,high',
            'status'       => 'required|in:pending,started,completed',
            'deadline'     => 'nullable|date',
        ]);

        $project->tasks()->create($validated);

        return back()->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'assigned_to'  => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($project) {
                    $isMember = $project->members()->where('user_id', $value)->exists();
                    if (! $isMember) {
                        $fail('This user is not a member of this project.');
                    }
                },
            ],
            'milestone_id' => 'nullable|exists:milestones,id',
            'priority'     => 'required|in:low,medium,high',
            'status'       => 'required|in:pending,started,completed',
            'deadline'     => 'nullable|date',
        ]);

        $task->update($validated);

        return back()->with('success', 'Task updated.');
    }

    public function destroy(Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            abort(403);
        }

        $task->delete();

        return back()->with('success', 'Task deleted.');
    }
}