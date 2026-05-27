<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Store a newly created post with its file attachments.
     *
     * Accepts multiple files (images and PDFs). Each file is stored in
     * `storage/app/public/attachments/{post_id}/` and registered as an
     * Attachment record with the appropriate type.
     */
    public function store(Request $request, User $client, Project $project): RedirectResponse
    {
        abort_if($client->role !== 'client', 404);
        abort_if($project->user_id !== $client->id, 404);

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'published_at'  => ['nullable', 'date'],
            'attachments'   => ['nullable', 'array', 'max:20'],
            'attachments.*' => ['file', 'max:20480', 'mimes:jpg,jpeg,png,gif,webp,pdf'],
        ]);

        $post = $project->posts()->create([
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        // Process and store each uploaded file
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $directory = "attachments/{$post->id}";
                $path      = $file->store($directory, 'public');
                $mimeType  = $file->getMimeType();

                $post->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type'      => str_starts_with($mimeType, 'image/') ? 'image' : 'document',
                ]);
            }
        }

        return redirect()
            ->route('admin.clients.projects.show', [$client, $project])
            ->with('success', 'Publicación creada correctamente.');
    }
}
