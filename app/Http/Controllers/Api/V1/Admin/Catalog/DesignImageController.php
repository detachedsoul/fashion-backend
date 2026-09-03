<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreDesignImageRequest;
use App\Http\Requests\Admin\Catalog\UpdateDesignImageRequest;
use App\Http\Resources\Catalog\DesignImageResource;
use App\Jobs\CleanupOrphanedFileJob;
use App\Models\Design;
use App\Models\DesignImage;
use App\Services\CatalogImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DesignImageController extends Controller
{
    public function __construct(protected CatalogImageService $images) {}

    public function store(StoreDesignImageRequest $request, Design $design): JsonResponse
    {
        $image = DesignImage::create([
            'design_id' => $design->id,
            'path' => $this->images->store($request->file('image'), 'designs'),
            'sort_order' => $request->integer('sort_order', ($design->images()->max('sort_order') ?? -1) + 1),
        ]);

        return response()->success(data: new DesignImageResource($image), message: 'Image added.', status: 201);
    }

    public function update(UpdateDesignImageRequest $request, Design $design, DesignImage $image): JsonResponse
    {
        $newPath = null;
        $oldPath = $image->path;

        if ($request->hasFile('image')) {
            try {
                $newPath = $this->images->store($request->file('image'), 'designs');
            } catch (\Throwable $e) {
                Log::error(
                    'DesignImageController::update - upload failed',
                    ['error' => $e->getMessage()]
                );

                return response()->error('Failed to upload file', 500);
            }
        }

        if ($request->has('sort_order')) {
            $image->sort_order = $request->integer('sort_order');
        }

        if ($newPath) {
            DB::beginTransaction();

            try {
                $image->path = $newPath;

                $image->save();

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                try {
                    $this->images->delete($newPath);
                } catch (\Throwable $delEx) {
                    Log::error(
                        'Failed to delete orphaned new image after DB rollback',
                        ['newPath' => $newPath, 'error' => $delEx->getMessage()]
                    );

                    CleanupOrphanedFileJob::dispatch($newPath)->delay(now()->addMinutes(1));
                }

                Log::error(
                    'DesignImageController::update - DB save failed',
                    ['error' => $e->getMessage()]
                );

                return response()->error('Failed to save image record', 500);
            }

            if ($oldPath) {
                try {
                    $this->images->delete($oldPath);
                } catch (\Throwable $delEx) {
                    Log::warning(
                        'Failed to delete old design image after DB update',
                        ['oldPath' => $oldPath, 'newPath' => $newPath, 'error' => $delEx->getMessage()]
                    );

                    CleanupOrphanedFileJob::dispatch($oldPath);
                }
            }
        } else {
            $image->save();
        }

        $image->refresh();

        return response()->success(data: new DesignImageResource($image), message: 'Image updated.');
    }

    public function destroy(Design $design, DesignImage $image): JsonResponse
    {
        $path = $image->path;

        try {
            $this->images->delete($path);
        } catch (\Throwable $e) {
            Log::warning(
                'Failed to delete design image in destroy; scheduling cleanup',
                ['path' => $path, 'error' => $e->getMessage()]
            );

            CleanupOrphanedFileJob::dispatch($path)->delay(now()->addMinutes(1));
        }

        $image->delete();

        return response()->success(message: 'Image removed.');
    }
}
