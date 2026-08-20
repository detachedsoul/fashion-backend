<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Centralizes catalog image storage on the 'r2' disk so every controller
 * that handles an image (single-path columns like ClothingType.image_path,
 * or gallery rows like DesignImage/ProductImage) does it the same way -
 * particularly the "delete the old file when it's replaced" step, which
 * R2/S3 never does automatically. Skipping this silently accumulates
 * orphaned files - and cost - forever.
 */
class CatalogImageService
{
    public function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'r2');
    }

    /**
     * Stores the new file, THEN deletes the old one - in that order, so a
     * failed upload never leaves a record pointing at a file that no
     * longer exists.
     */
    public function replace(?string $oldPath, UploadedFile $newFile, string $directory): string
    {
        $newPath = $this->store($newFile, $directory);

        if ($oldPath) {
            $this->delete($oldPath);
        }

        return $newPath;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('r2')->delete($path);
        }
    }
}
