<?php

namespace App\Jobs;

use App\Models\DesignImage;
use App\Models\ProductImage;
use App\Services\CatalogImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupOrphanedFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $path) {}

    public function handle(CatalogImageService $images): void
    {
        try {
            // Only delete if no DB record references this path
            $referenced = DesignImage::where('path', $this->path)->exists() ||
                ProductImage::where('path', $this->path)->exists();

            if ($referenced) {
                Log::info('CleanupOrphanedFileJob: path still referenced, skipping delete', ['path' => $this->path]);
                return;
            }

            $images->delete($this->path);
            Log::info('CleanupOrphanedFileJob: deleted orphaned file', ['path' => $this->path]);
        } catch (\Throwable $e) {
            Log::error('CleanupOrphanedFileJob: failed to delete orphaned file', ['path' => $this->path, 'error' => $e->getMessage()]);

            // Re-throwing will let the job retry based on queue retry policy
            throw $e;
        }
    }
}
