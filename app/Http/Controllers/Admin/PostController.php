<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Post;
use App\Models\Project;
use App\Models\Company;
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
    public function store(Request $request, Company $company, Project $project): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'published_at'  => ['nullable', 'date'],
            'attachments'   => ['nullable', 'array', 'max:20'],
            'attachments.*' => ['file', 'max:20480', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip,rar'],
        ]);

        $post = $project->posts()->create([
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        // Store uploaded file attachments
        if ($request->hasFile('attachments')) {
            $this->storeAttachments($post, $request->file('attachments'));
        }

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Publicación creada correctamente.');
    }

    /**
     * Update the specified post in the database.
     */
    public function update(Request $request, Company $company, Project $project, Post $post): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if($post->project_id !== $project->id, 404);

        $validated = $request->validate([
            'title'                => ['required', 'string', 'max:255'],
            'description'          => ['required', 'string'],
            'published_at'         => ['nullable', 'date'],
            'delete_attachments'   => ['nullable', 'array'],
            'delete_attachments.*' => ['integer', 'exists:attachments,id'],
            'attachments'          => ['nullable', 'array', 'max:20'],
            'attachments.*'        => ['file', 'max:20480', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip,rar'],
        ]);

        $post->fill([
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'published_at' => $validated['published_at'] ?? now(),
        ])->save();

        // Process attachments marked for deletion
        if ($request->has('delete_attachments')) {
            $attachmentsToDelete = $post->attachments()->whereIn('id', $request->input('delete_attachments'))->get();
            foreach ($attachmentsToDelete as $attachment) {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }
        }

        // Store newly uploaded file attachments
        if ($request->hasFile('attachments')) {
            $this->storeAttachments($post, $request->file('attachments'));
        }

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Publicación actualizada correctamente.');
    }

    /**
     * Store uploaded file attachments for a given post.
     *
     * Each file is stored in `storage/app/public/attachments/{post_id}/`
     * and registered as an Attachment record with the appropriate type.
     *
     * @param  Post                                        $post
     * @param  array<\Illuminate\Http\UploadedFile>        $files
     */
    private function storeAttachments(Post $post, array $files): void
    {
        foreach ($files as $file) {
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
}
