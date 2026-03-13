<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function ceoDashboard()
    {
        return Inertia::render('Dashboard/CeoDashboard', [
            'totalUsers' => User::count(),
            'totalProjects' => Project::count(),
            'activeProjects' => Project::where('status', 'active')->count(),
            'completedProjects' => Project::where('status', 'completed')->count(),
            'totalRevenue' => Project::sum('budget'),
        ]);
    }
}