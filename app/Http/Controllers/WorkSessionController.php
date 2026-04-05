<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\WorkSession;
use Illuminate\Http\Request;

class WorkSessionController extends Controller
{
    public function signIn(Request $request, Project $project)
    {
        $user = $request->user();

        $alreadyActive = WorkSession::active()
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyActive) {
            return back()->with('error', 'You are already signed in to a project. Sign out first.');
        }

        $isMember = $project->members()->where('user_id', $user->id)->exists();

        if (! $isMember) {
            abort(403, 'You are not a member of this project.');
        }

        WorkSession::create([
            'user_id'    => $user->id,
            'project_id' => $project->id,
            'start_time' => now(),
        ]);

        return back()->with('success', "Signed in to {$project->title}.");
    }

    public function signOut(Request $request, Project $project)
    {
        $user = $request->user();

        $session = WorkSession::active()
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        if (! $session) {
            return back()->with('error', 'No active session found for this project.');
        }

        $endTime = now();

        $duration = $session->start_time->diffInMinutes($endTime);

        $session->update([
            'end_time' => $endTime,
            'duration' => max(1, $duration),
        ]);

        return back()->with('success', "Signed out. Session: {$session->duration} minute(s).");
    }
}