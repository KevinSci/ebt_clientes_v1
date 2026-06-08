<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Serve a file from the public storage disk as a secure fallback.
     *
     * @param string $path
     * @return BinaryFileResponse
     */
    public function show(string $path): BinaryFileResponse
    {
        // Prevent directory traversal attacks
        if (str_contains($path, '..')) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            abort(404);
        }

        // Return the file response from the local filesystem
        return response()->file($disk->path($path));
    }
}
