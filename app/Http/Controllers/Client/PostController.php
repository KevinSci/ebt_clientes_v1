<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Post;
use App\Models\Project;
use App\Models\Company;
use App\Http\Concerns\StoresPostAttachments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use StoresPostAttachments;

    public function store(Request $request, Company $company, Project $project): RedirectResponse
    {
        abort_unless(auth()->user()->canPublish(), 403, 'No tienes permiso para publicar.');

        $validated = $request->validate([
            'title'                     => ['required', 'string', 'max:255'],
            'description'               => ['required', 'string', 'max:20000'],
            'published_at'              => ['nullable', 'date'],
            'attachments'               => ['nullable', 'array', 'max:100'],
            'attachments.*'             => ['file', 'max:20480', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip,rar'],
            'attachment_folder_names'   => ['nullable', 'array'],
            'attachment_folder_names.*' => ['nullable', 'string'],
            'attachment_folder_paths'   => ['nullable', 'array'],
            'attachment_folder_paths.*' => ['nullable', 'string'],
        ]);

        $post = $project->posts()->create([
            'user_id'      => auth()->id(),
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'published_at' => $validated['published_at'] ?? now(),
        ]);
 
        if ($request->hasFile('attachments')) {
            $this->storeAttachments(
                $post,
                $request->file('attachments'),
                $request->input('attachment_folder_names', []),
                $request->input('attachment_folder_paths', [])
            );
        }

        return redirect()
            ->route('client.companies.projects.show', [$company, $project])
            ->with('success', 'Publicación creada correctamente.');
    }

    public function storeAjax(Request $request, Company $company, Project $project): JsonResponse
    {
        abort_unless(auth()->user()->canPublish(), 403, 'No tienes permiso para publicar.');

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'max:20000'],
            'published_at'  => ['nullable', 'date'],
            'attachments'   => ['nullable', 'array', 'max:20'],
            'attachments.*' => ['file', 'max:20480', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip,rar'],
        ]);

        $post = $project->posts()->create([
            'user_id'      => auth()->id(),
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        if ($request->hasFile('attachments')) {
            $this->storeAttachments($post, $request->file('attachments'));
        }

        return response()->json([
            'post_id'      => $post->id,
            'redirect_url' => route('client.companies.projects.show', [$company, $project]),
        ], 201);
    }

    public function uploadAttachment(Request $request, Company $company, Project $project, Post $post): JsonResponse
    {
        abort_unless(auth()->user()->canPublish(), 403, 'No tienes permiso para publicar.');
        abort_if($post->user_id !== auth()->id(), 403, 'No puedes modificar este post.');

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

    public function update(Request $request, Company $company, Project $project, Post $post): RedirectResponse
    {
        abort_unless(auth()->user()->canPublish(), 403, 'No tienes permiso para publicar.');
        abort_if($post->user_id !== auth()->id(), 403, 'No puedes editar este post.');

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

        if ($request->has('delete_attachments')) {
            $attachmentsToDelete = $post->attachments()->whereIn('id', $request->input('delete_attachments'))->get();
            foreach ($attachmentsToDelete as $attachment) {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }
        }

        if ($request->hasFile('attachments')) {
            $this->storeAttachments(
                $post,
                $request->file('attachments'),
                $request->input('attachment_folder_names', []),
                $request->input('attachment_folder_paths', [])
            );
        }

        return redirect()
            ->route('client.companies.projects.show', [$company, $project])
            ->with('success', 'Publicación actualizada correctamente.');
    }

    public function updateAjax(Request $request, Company $company, Project $project, Post $post): JsonResponse
    {
        abort_unless(auth()->user()->canPublish(), 403, 'No tienes permiso para publicar.');
        abort_if($post->user_id !== auth()->id(), 403, 'No puedes editar este post.');

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
            'redirect_url' => route('client.companies.projects.show', [$company, $project]),
        ], 200);
    }

    public function destroy(Company $company, Project $project, Post $post): RedirectResponse
    {
        abort_unless(auth()->user()->canPublish(), 403, 'No tienes permiso para publicar.');
        abort_if($post->user_id !== auth()->id(), 403, 'No puedes eliminar este post.');

        $post->attachments()->delete();
        $post->delete();

        return redirect()
            ->route('client.companies.projects.show', [$company, $project])
            ->with('success', 'Publicación eliminada correctamente.');
    }
}
