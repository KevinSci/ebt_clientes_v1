<?php

namespace App\Http\Concerns;

use App\Models\Post;

trait StoresPostAttachments
{
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
    protected function storeAttachments(Post $post, array $files, array $folderNames = [], array $folderPaths = []): void
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
}
