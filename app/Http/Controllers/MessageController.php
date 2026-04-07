<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Dedicated chat page — for Designers/Developers.
     * Shows the full message thread for a project.
     */
    public function show(Request $request, Project $project)
    {
        $user = $request->user();

        // Verify the user is allowed to chat on this project.
        // Designers/Developers must be a project member.
        // PM and Client are checked separately.
        $this->authorizeChat($user, $project);

        $messages = Message::where('project_id', $project->id)
            ->with('sender:id,name,role_id')
            ->with('sender.role:id,name')
            ->oldest()
            ->get();

        return Inertia::render('Chat/Project', [
            'project'  => $project->only('id', 'title', 'status'),
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message in a project chat.
     */
    public function store(Request $request, Project $project)
    {
        $user = $request->user();

        $this->authorizeChat($user, $project);

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        Message::create([
            'project_id' => $project->id,
            'sender_id'  => $user->id,
            'message'    => $request->message,
        ]);

        // back() returns to whatever page sent this request —
        // works for both the Show page panels and the dedicated chat page.
        return back();
    }

    /**
     * Check if the user is authorized to send/read messages in this project.
     *
     * Allowed:
     *   - The project's assigned PM
     *   - The project's client
     *   - Any designer/developer who is a member of the project
     */
    private function authorizeChat($user, Project $project): void
    {
        $isManager = $project->project_manager_id === $user->id;
        $isClient  = $project->client_id === $user->id;
        $isMember  = $project->members()->where('user_id', $user->id)->exists();

        if (! $isManager && ! $isClient && ! $isMember) {
            abort(403, 'You don\'t have access to this project chat.');
        }
    }
}