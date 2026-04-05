<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['client', 'manager'])
            ->latest()
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        return Inertia::render('Projects/Create', [
            'clients'  => User::whereHas('role', fn($q) => $q->where('name', 'Client'))
                ->select('id', 'name')->latest()->get(),
            'managers' => User::whereHas('role', fn($q) => $q->where('name', 'Project Manager'))
                ->select('id', 'name')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'client_id'          => 'required|exists:users,id',
            'project_manager_id' => 'required|exists:users,id',
            'budget'             => 'nullable|numeric|min:0',
            'start_date'         => 'nullable|date',
            'deadline'           => 'nullable|date|after_or_equal:start_date',
            'status'             => 'in:pending,active,completed,cancelled',
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {

        $project->load([
            'client',
            'manager',
            'milestones',
            'tasks.assignee',
            'tasks.milestone',
            'tasks.submissions.submitter',
            'members.role',
            'workSessions.user',
        ]);

        $existingMemberIds = $project->members->pluck('id');

        $availableMembers = User::whereHas(
            'role',
            fn($q) =>
            $q->whereIn('name', ['Designer', 'Developer'])
        )
            ->whereNotIn('id', $existingMemberIds)
            ->select('id', 'name', 'role_id')
            ->with('role:id,name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Projects/Show', [
            'project'          => $project,
            'availableMembers' => $availableMembers,
        ]);
    }

    public function edit(Project $project)
    {
        return Inertia::render('Projects/Edit', [
            'project'  => $project,
            'clients'  => User::whereHas('role', fn($q) => $q->where('name', 'Client'))
                ->select('id', 'name')->get(),
            'managers' => User::whereHas('role', fn($q) => $q->where('name', 'Project Manager'))
                ->select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'client_id'          => 'required|exists:users,id',
            'project_manager_id' => 'required|exists:users,id',
            'budget'             => 'nullable|numeric|min:0',
            'start_date'         => 'nullable|date',
            'deadline'           => 'nullable|date|after_or_equal:start_date',
            'status'             => 'in:pending,active,completed,cancelled',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted.');
    }
}
