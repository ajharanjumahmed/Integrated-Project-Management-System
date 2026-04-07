<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Client projects list page.
     */
    public function projects(Request $request)
    {
        $projects = Project::where('client_id', $request->user()->id)
            ->with([
                'manager:id,name',
                'milestones',
                'tasks.submissions' => fn($q) => $q->where('client_submitted', true),
            ])
            ->withCount('tasks')
            ->latest()
            ->get();

        return Inertia::render('Client/Projects', [
            'projects' => $projects,
        ]);
    }

    /**
     * Client single project detail view.
     */
    public function show(Request $request, Project $project)
    {
        if ($project->client_id !== $request->user()->id) {
            abort(403);
        }

        $project->load([
            'manager:id,name',
            'milestones',
            'tasks.submissions' => fn($q) => $q
                ->where('client_submitted', true)
                ->with('submitter:id,name')
                ->latest(),
            // Load messages for this project
            'messages' => fn($q) => $q->with('sender:id,name,role_id')
                ->with('sender.role:id,name')
                ->oldest(),
        ]);

        return Inertia::render('Client/ProjectShow', [
            'project' => $project,
        ]);
    }

    public function approveSubmission(Request $request, Submission $submission)
    {
        $this->authorizeClient($request, $submission);
        $submission->update(['client_status' => 'approved']);
        return back()->with('success', 'Submission approved.');
    }

    public function requestRevision(Request $request, Submission $submission)
    {
        $this->authorizeClient($request, $submission);

        $request->validate(['client_note' => 'required|string|max:1000']);

        $submission->update([
            'client_status' => 'revision_requested',
            'client_note'   => $request->client_note,
        ]);

        return back()->with('success', 'Revision requested. PM has been notified.');
    }

    private function authorizeClient(Request $request, Submission $submission): void
    {
        if ($request->user()->id !== $submission->task->project->client_id) {
            abort(403);
        }
    }
}
