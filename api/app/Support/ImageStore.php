<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores uploaded pictures on the "public" disk (served at /storage/...).
 */
class ImageStore
{
    public const RULE = 'image|mimes:jpeg,jpg,png,gif,webp|max:2048';

    /** Save $file in $directory, delete the $previous file, return the new relative path. */
    public static function replace(UploadedFile $file, string $directory, ?string $previous = null): string
    {
        $path = $file->store($directory, 'public');
        self::delete($previous);

        return $path;
    }

    public static function delete(?string $path): void
    {
        // External avatars (e.g. "https://...") are not ours to delete.
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }
}
