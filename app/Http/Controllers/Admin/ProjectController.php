<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\View\View;

class ProjectController extends Controller
{
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
