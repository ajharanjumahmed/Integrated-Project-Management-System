<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['client', 'manager'])
            ->latest()
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects
        ]);
    }

    public function create()
{
    $clients = User::whereHas('role', function ($query) {
        $query->where('name', 'Client');
    })
    ->latest()
    ->limit(8)
    ->select('id','name')
    ->get();

    $managers = User::whereHas('role', function ($query) {
        $query->where('name', 'Project Manager');
    })
    ->latest()
    ->limit(8)
    ->select('id','name')
    ->get();

    return Inertia::render('Projects/Create', [
        'clients' => $clients,
        'managers' => $managers
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'client_id' => 'required',
            'project_manager_id' => 'required',
            'budget' => 'nullable|numeric',
        ]);

        Project::create($request->all());

        return redirect()->route('projects.index');
    }

    public function edit(Project $project)
    {
        $users = User::select('id', 'name')->get();

        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'users' => $users
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required',
            'client_id' => 'required',
            'project_manager_id' => 'required'
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index');
    }
}
