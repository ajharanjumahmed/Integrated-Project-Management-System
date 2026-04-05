<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|in:designer,developer',
        ]);

        $alreadyMember = $project->members()
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($alreadyMember) {
            return back()->with('error', 'This user is already a member of this project.');
        }

        $project->members()->attach($validated['user_id'], [
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'Team member added successfully.');
    }

    public function destroy(Project $project, User $user)
    {
        $project->members()->detach($user->id);

        return back()->with('success', 'Team member removed.');
    }
}