<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'deadline' => 'nullable|date',
            'status'   => 'in:pending,running,completed',
        ]);

        $project->milestones()->create($validated);

        return back()->with('success', 'Milestone created.');
    }

    public function update(Request $request, Project $project, Milestone $milestone)
    {
        if ($milestone->project_id !== $project->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'deadline' => 'nullable|date',
            'status'   => 'required|in:pending,running,completed',
        ]);

        $milestone->update($validated);

        return back()->with('success', 'Milestone updated.');
    }

    public function destroy(Project $project, Milestone $milestone)
    {
        if ($milestone->project_id !== $project->id) {
            abort(403);
        }

        $milestone->delete();

        return back()->with('success', 'Milestone deleted.');
    }
}