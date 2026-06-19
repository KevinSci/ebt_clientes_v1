<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Post;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
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
            'title'                     => ['required', 'string', 'max:255'],
            'description'               => ['required', 'string', 'max:20000'],
            'published_at'              => ['nullable', 'date'],
            'attachments'               => ['nullable', 'array', 'max:100'], // Incrementado el max para admitir carpetas con varios archivos
            'attachments.*'             => ['file', 'max:20480', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip,rar'],
            'attachment_folder_names'   => ['nullable', 'array'],
            'attachment_folder_names.*' => ['nullable', 'string'],
            'attachment_folder_paths'   => ['nullable', 'array'],
            'attachment_folder_paths.*' => ['nullable', 'string'],
        ]);

        $post = $project->posts()->create([
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'published_at' => $validated['published_at'] ?? now(),
        ]);
 
        // Store uploaded file attachments
        if ($request->hasFile('attachments')) {
            $this->storeAttachments(
                $post,
                $request->file('attachments'),
                $request->input('attachment_folder_names', []),
                $request->input('attachment_folder_paths', [])
            );
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
            'title'                     => ['required', 'string', 'max:255'],
            'description'               => ['required', 'string'],
            'published_at'              => ['nullable', 'date'],
            'delete_attachments'        => ['nullable', 'array'],
            'delete_attachments.*'      => ['integer', 'exists:attachments,id'],
            'attachments'               => ['nullable', 'array', 'max:100'],
            'attachments.*'             => ['file', 'max:20480', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip,rar'],
            'attachment_folder_names'   => ['nullable', 'array'],
            'attachment_folder_names.*' => ['nullable', 'string'],
            'attachment_folder_paths'   => ['nullable', 'array'],
            'attachment_folder_paths.*' => ['nullable', 'string'],
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
            $this->storeAttachments(
                $post,
                $request->file('attachments'),
                $request->input('attachment_folder_names', []),
                $request->input('attachment_folder_paths', [])
            );
        }

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Publicación actualizada correctamente.');
    }

    /**
     * Remove the specified post from the database.
     */
    public function destroy(Company $company, Project $project, Post $post): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if($post->project_id !== $project->id, 404);

        $post->attachments()->delete();
        $post->delete();

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Publicación eliminada correctamente.');
    }

    /**
     * Store uploaded file attachments for a given post.
     *
     * Each file is stored in `storage/app/public/attachments/{post_id}/`
     * and registered as an Attachment record with the appropriate type.
     *
     * @param  Post                                        $post
     * @param  array<\Illuminate\Http\UploadedFile>        $files
     * @param  array<string|null>                          $folderNames
     * @param  array<string|null>                          $folderPaths
     */
    private function storeAttachments(Post $post, array $files, array $folderNames = [], array $folderPaths = []): void
    {
        foreach ($files as $index => $file) {
            $directory = "attachments/{$post->id}";
            $path      = $file->store($directory, 'public');
            $mimeType  = $file->getMimeType();

            $folderName = isset($folderNames[$index]) && $folderNames[$index] !== '' ? $folderNames[$index] : null;
            $folderPath = isset($folderPaths[$index]) && $folderPaths[$index] !== '' ? $folderPaths[$index] : null;

            $post->attachments()->create([
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => $path,
                'type'        => str_starts_with($mimeType, 'image/') ? 'image' : 'document',
                'folder_name' => $folderName,
                'folder_path' => $folderPath,
            ]);
        }
    }

    /**
     * Create a post via AJAX (used by the async folder uploader).
     *
     * Accepts only the post metadata + optional individual (non-folder) files.
     * Returns JSON with the new post_id and the redirect URL so JS can continue
     * uploading folder files one-by-one via uploadAttachment().
     */
    public function storeAjax(Request $request, Company $company, Project $project): JsonResponse
    {
        abort_if($project->company_id !== $company->id, 404);

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'max:20000'],
            'published_at'  => ['nullable', 'date'],
            'attachments'   => ['nullable', 'array', 'max:20'],
            'attachments.*' => ['file', 'max:20480', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip,rar'],
        ]);

        $post = $project->posts()->create([
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        if ($request->hasFile('attachments')) {
            $this->storeAttachments($post, $request->file('attachments'));
        }

        return response()->json([
            'post_id'      => $post->id,
            'redirect_url' => route('admin.companies.projects.show', [$company, $project]),
        ], 201);
    }

    /**
     * Upload a single file as an attachment for an existing post.
     *
     * Called sequentially by JS (one request per file) to bypass PHP's
     * max_file_uploads limit when uploading folders.
     */
    public function uploadAttachment(Request $request, Company $company, Project $project, Post $post): JsonResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if($post->project_id !== $project->id, 404);

        $validated = $request->validate([
            'attachment'  => ['required', 'file', 'max:20480', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip,rar'],
            'folder_name' => ['nullable', 'string', 'max:255'],
            'folder_path' => ['nullable', 'string', 'max:500'],
        ]);

        $file      = $request->file('attachment');
        $directory = "attachments/{$post->id}";
        $path      = $file->store($directory, 'public');
        $mimeType  = $file->getMimeType();

        $attachment = $post->attachments()->create([
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'type'        => str_starts_with($mimeType, 'image/') ? 'image' : 'document',
            'folder_name' => $validated['folder_name'] ?: null,
            'folder_path' => $validated['folder_path'] ?: null,
        ]);

        return response()->json(['attachment_id' => $attachment->id], 201);
    }

    /**
     * Update a post via AJAX (used by the async folder/file uploader).
     */
    public function updateAjax(Request $request, Company $company, Project $project, Post $post): JsonResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if($post->project_id !== $project->id, 404);

        $validated = $request->validate([
            'title'              => ['required', 'string', 'max:255'],
            'description'        => ['required', 'string'],
            'published_at'       => ['nullable', 'date'],
            'delete_attachments' => ['nullable', 'array'],
            'delete_attachments.*' => ['integer', 'exists:attachments,id'],
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

        return response()->json([
            'post_id'      => $post->id,
            'redirect_url' => route('admin.companies.projects.show', [$company, $project]),
        ], 200);
    }
}
