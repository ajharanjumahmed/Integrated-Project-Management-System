<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Task;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * Designer/Developer submits work for a task.
     */
    public function store(Request $request, Task $task)
    {
        if ($task->assigned_to !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'file'    => 'required|file|max:20480',
            'comment' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('file')->store('submissions', 'public');

        Submission::create([
            'task_id'      => $task->id,
            'submitted_by' => $request->user()->id,
            'file_path'    => $path,
            'comment'      => $request->comment,
            'status'       => 'pending_review',
        ]);

        $task->update(['status' => 'completed']);

        return back()->with('success', 'Work submitted. Awaiting PM review.');
    }

    /**
     * PM approves designer submission.
     */
    public function approve(Request $request, Submission $submission)
    {
        $this->authorizePM($request, $submission);
        $submission->update(['status' => 'approved']);
        return back()->with('success', 'Submission approved.');
    }

    /**
     * PM requests revision from designer.
     */
    public function requestRevision(Request $request, Submission $submission)
    {
        $this->authorizePM($request, $submission);

        $request->validate(['pm_note' => 'required|string|max:1000']);

        $submission->update([
            'status'  => 'revision_requested',
            'pm_note' => $request->pm_note,
        ]);

        // Reopen the task so designer can rework it
        $submission->task->update(['status' => 'started']);

        return back()->with('success', 'Revision requested from designer.');
    }

    /**
     * PM submits approved work to client — with their own comment and optional file.
     */
    public function submitToClient(Request $request, Submission $submission)
    {
        $this->authorizePM($request, $submission);

        if ($submission->status !== 'approved') {
            return back()->with('error', 'Only approved submissions can be sent to client.');
        }

        $request->validate([
            'pm_to_client_comment' => 'nullable|string|max:1000',
            'pm_to_client_file'    => 'nullable|file|max:20480',
        ]);

        $data = [
            'client_submitted'     => true,
            'client_status'        => 'pending',   // reset for fresh client review
            'pm_to_client_comment' => $request->pm_to_client_comment,
        ];

        if ($request->hasFile('pm_to_client_file')) {
            $data['pm_to_client_file'] = $request->file('pm_to_client_file')
                ->store('submissions', 'public');
        }

        $submission->update($data);

        return back()->with('success', 'Work submitted to client.');
    }

    /**
     * PM resubmits to client after client requested a revision.
     */
    public function resubmitToClient(Request $request, Submission $submission)
    {
        $this->authorizePM($request, $submission);

        $request->validate([
            'pm_to_client_comment' => 'nullable|string|max:1000',
            'pm_to_client_file'    => 'nullable|file|max:20480',
        ]);

        $data = [
            'client_status'        => 'pending',   // reset so client can review again
            'client_note'          => null,         // clear previous client note
            'pm_to_client_comment' => $request->pm_to_client_comment,
        ];

        if ($request->hasFile('pm_to_client_file')) {
            $data['pm_to_client_file'] = $request->file('pm_to_client_file')
                ->store('submissions', 'public');
        }

        $submission->update($data);

        return back()->with('success', 'Resubmitted to client.');
    }

    private function authorizePM(Request $request, Submission $submission): void
    {
        $pmId = $submission->task->project->project_manager_id;
        if ($request->user()->id !== $pmId) {
            abort(403);
        }
    }
}