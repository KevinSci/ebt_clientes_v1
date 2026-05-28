<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Store a newly created project in the database for the given client.
     */
    public function store(Request $request, User $client): RedirectResponse
    {
        abort_if($client->role !== 'client', 404);

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'status'              => ['required', 'string', 'in:active,paused,completed'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $client->projects()->create([
            'name'                => $validated['name'],
            'status'              => $validated['status'],
            'progress_percentage' => $validated['progress_percentage'],
        ]);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Remove the specified project from the database.
     */
    public function destroy(User $client, Project $project): RedirectResponse
    {
        abort_if($client->role !== 'client', 404);
        abort_if($project->user_id !== $client->id, 404);

        $project->delete();

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Proyecto eliminado correctamente.');
    }
    /**
     * Display a specific project with its posts and attachments.
     *
     * The client ownership is verified to ensure data isolation.
     */
    public function show(User $client, Project $project): View
    {
        abort_if($client->role !== 'client', 404);
        abort_if($project->user_id !== $client->id, 404);

        $project->load([
            'posts' => fn ($q) => $q->with('attachments')->latest('published_at'),
        ]);

        return view('admin.projects.show', compact('client', 'project'));
    }
}
