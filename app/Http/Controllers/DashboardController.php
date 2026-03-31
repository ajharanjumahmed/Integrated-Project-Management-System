<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * This is called when any logged-in user hits /dashboard.
     * We read their role and redirect them to the correct dashboard.
     * Each role has a completely different experience in this app.
     */
    public function index(Request $request)
    {
        $role = $request->user()->role?->name;

        return match ($role) {
            'CEO'             => redirect()->route('ceo.dashboard'),
            'Project Manager' => redirect()->route('manager.dashboard'),
            'Designer'        => redirect()->route('designer.dashboard'),
            'Developer'       => redirect()->route('developer.dashboard'),
            'Client'          => redirect()->route('client.dashboard'),
            // If role is null or unknown, show a plain fallback page
            default           => Inertia::render('Dashboard/Default'),
        };
    }

    // CEO Dashboard
    public function ceoDashboard()
    {
        return Inertia::render('Dashboard/CeoDashboard', [
            'stats' => [
                'totalUsers'        => User::count(),
                'totalProjects'     => Project::count(),
                'activeProjects'    => Project::where('status', 'active')->count(),
                'completedProjects' => Project::where('status', 'completed')->count(),
                'totalRevenue'      => Project::sum('budget'),
            ],
            // Recent projects for the dashboard table
            'recentProjects' => Project::with(['client', 'manager'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    // Project Manager Dashboard

    public function managerDashboard(Request $request)
    {
        $manager = $request->user();

        // Only load projects where THIS manager is assigned
        $projects = Project::where('project_manager_id', $manager->id)
            ->withCount(['tasks', 'milestones'])
            ->with(['client'])
            ->latest()
            ->get();

        return Inertia::render('Dashboard/ManagerDashboard', [
            'projects'          => $projects,
            'totalProjects'     => $projects->count(),
            'activeProjects'    => $projects->where('status', 'active')->count(),
            'completedProjects' => $projects->where('status', 'completed')->count(),
            'pendingProjects' => $projects->where('status', 'pending')->count(),
        ]);
    }

    // Designer Dashboard

    public function designerDashboard(Request $request)
    {
        $user = $request->user();

        // Get projects this designer is a member of
        $projects = $user->memberProjects()
            ->withCount(['tasks' => function ($q) use ($user) {
                // Only count tasks assigned to this user
                $q->where('assigned_to', $user->id);
            }])
            ->get();

        // Get tasks assigned to this user, with project info
        $tasks = $user->assignedTasks()
            ->with(['project', 'milestone'])
            ->whereIn('status', ['pending', 'started'])
            ->orderBy('deadline')
            ->get();

        return Inertia::render('Dashboard/DesignerDashboard', [
            'projects' => $projects,
            'tasks'    => $tasks,
            'activeTasks'    => $tasks->where('status', 'started')->count(),
            'completedTasks' => $tasks->where('status', 'completed')->count(),
            'pendingTasks' => $tasks->where('status', 'pending')->count(),
        ]);
    }

    // Developer Dashboard

    public function developerDashboard(Request $request)
    {
        $user = $request->user();

        $projects = $user->memberProjects()
            ->withCount(['tasks' => function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            }])
            ->get();

        $tasks = $user->assignedTasks()
            ->with(['project', 'milestone'])
            ->whereIn('status', ['pending', 'started'])
            ->orderBy('deadline')
            ->get();

        return Inertia::render('Dashboard/DeveloperDashboard', [
            'projects' => $projects,
            'tasks'    => $tasks,
            'activeTasks'    => $tasks->where('status', 'started')->count(),
            'completedTasks' => $tasks->where('status', 'completed')->count(),
            'pendingTasks' => $tasks->where('status', 'pending')->count(),
        ]);
    }

    // Client Dashboard

    public function clientDashboard(Request $request)
    {
        $client = $request->user();

        // Only load projects where this user is the client
        $projects = Project::where('client_id', $client->id)
            ->with(['manager', 'milestones'])
            ->withCount(['tasks'])
            ->latest()
            ->get();

        return Inertia::render('Dashboard/ClientDashboard', [
            'projects' => $projects,
        ]);
    }
}