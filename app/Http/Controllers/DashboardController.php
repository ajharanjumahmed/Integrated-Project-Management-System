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
        // Revenue per project — we need this for the earnings breakdown table
        $projects = Project::with(['client:id,name', 'manager:id,name'])
            ->withCount(['tasks', 'members'])
            ->orderByDesc('budget')
            ->get();

        // User counts grouped by role
        $usersByRole = \App\Models\Role::withCount('users')->get()
            ->mapWithKeys(fn($r) => [$r->name => $r->users_count]);

        // Top earners (project managers with most total budget under management)
        $topManagers = User::whereHas('role', fn($q) => $q->where('name', 'Project Manager'))
            ->with('role:id,name')
            ->withSum('managedProjects', 'budget')
            ->withCount('managedProjects')
            ->orderByDesc('managed_projects_sum_budget')
            ->limit(5)
            ->get();

        // Total work hours logged across all projects
        $totalWorkMinutes = \App\Models\WorkSession::whereNotNull('end_time')
            ->sum('duration');

        // Most active team members by total work minutes
        $topWorkers = User::whereHas(
            'role',
            fn($q) =>
            $q->whereIn('name', ['Designer', 'Developer'])
        )
            ->withSum('workSessions', 'duration')
            ->orderByDesc('work_sessions_sum_duration')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard/CeoDashboard', [
            'stats' => [
                'totalUsers'        => User::count(),
                'totalProjects'     => $projects->count(),
                'activeProjects'    => $projects->where('status', 'active')->count(),
                'completedProjects' => $projects->where('status', 'completed')->count(),
                'pendingProjects'   => $projects->where('status', 'pending')->count(),
                'cancelledProjects' => $projects->where('status', 'cancelled')->count(),
                'totalRevenue'      => $projects->sum('budget'),
                'activeRevenue'     => $projects->where('status', 'active')->sum('budget'),
                'completedRevenue'  => $projects->where('status', 'completed')->sum('budget'),
                'totalWorkHours'    => round($totalWorkMinutes / 60, 1),
            ],
            'usersByRole'  => $usersByRole,
            'projects'     => $projects->take(10),   // recent 10 for table
            'topManagers'  => $topManagers,
            'topWorkers'   => $topWorkers,
        ]);
    }

    public function ceoClients()
    {
        $clients = User::whereHas('role', fn($q) => $q->where('name', 'Client'))
            ->with('clientProfile')
            ->withCount([
                'clientProjects',

                'clientProjects as active_projects_count' => function ($q) {
                    $q->where('status', 'active');
                },

                'clientProjects as completed_projects_count' => function ($q) {
                    $q->where('status', 'completed');
                },
            ])
            ->latest()
            ->get();

        return Inertia::render('CEO/Clients', [
            'clients' => $clients,
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

        $alltasks = $user->assignedTasks()
            ->with(['project', 'milestone'])
            ->whereIn('status', ['pending', 'started', 'completed'])
            ->orderBy('deadline')
            ->get();

        return Inertia::render('Dashboard/DesignerDashboard', [
            'projects' => $projects,
            'tasks'    => $tasks,
            'activeTasks'    => $alltasks->where('status', 'started')->count(),
            'completedTasks' => $alltasks->where('status', 'completed')->count(),
            'pendingTasks' => $alltasks->where('status', 'pending')->count(),
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

        $alltasks = $user->assignedTasks()
            ->with(['project', 'milestone'])
            ->whereIn('status', ['pending', 'started', 'completed'])
            ->orderBy('deadline')
            ->get();

        return Inertia::render('Dashboard/DeveloperDashboard', [
            'projects' => $projects,
            'tasks'    => $tasks,
            'activeTasks'    => $alltasks->where('status', 'started')->count(),
            'completedTasks' => $alltasks->where('status', 'completed')->count(),
            'pendingTasks' => $alltasks->where('status', 'pending')->count(),
        ]);
    }

    // Client Dashboard

    public function clientDashboard(Request $request)
    {
        $client   = $request->user();
        $projects = Project::where('client_id', $client->id)->get();

        // Count submissions sent to this client awaiting review
        $pendingReviews = \App\Models\Submission::whereHas(
            'task.project',
            fn($q) =>
            $q->where('client_id', $client->id)
        )
            ->where('client_submitted', true)
            ->where('client_status', 'pending')
            ->count();

        return Inertia::render('Dashboard/ClientDashboard', [
            'stats' => [
                'totalProjects'     => $projects->count(),
                'activeProjects'    => $projects->where('status', 'active')->count(),
                'completedProjects' => $projects->where('status', 'completed')->count(),
                'pendingReviews'    => $pendingReviews,
            ],
        ]);
    }
}
