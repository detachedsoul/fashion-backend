<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreProductImageRequest;
use App\Http\Requests\Admin\Catalog\UpdateProductImageRequest;
use App\Http\Resources\Catalog\ProductImageResource;
use App\Jobs\CleanupOrphanedFileJob;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\CatalogImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductImageController extends Controller
{
    public function __construct(protected CatalogImageService $images) {}

    public function store(
        StoreProductImageRequest $request,
        Product $product
    ): JsonResponse {
        $image = ProductImage::create([
            'product_id' => $product->id,
            'path' => $this->images->store(
                $request->file('image'),
                'products'
            ),
            'sort_order' => $request->integer(
                'sort_order',
                ($product->images()->max('sort_order') ?? -1) + 1
            ),
        ]);

        return response()->success(
            data: new ProductImageResource($image),
            message: 'Image added.',
            status: 201
        );
    }

    public function update(
        UpdateProductImageRequest $request,
        Product $product,
        ProductImage $image
    ): JsonResponse {
        $newPath = null;
        $oldPath = $image->path;

        if ($request->hasFile('image')) {
            try {
                $newPath = $this->images->store($request->file('image'), 'products');
            } catch (\Throwable $e) {
                Log::error('ProductImageController::update - upload failed', ['error' => $e->getMessage()]);
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

                // Try to delete the newly uploaded file (compensating action)
                try {
                    $this->images->delete($newPath);
                } catch (\Throwable $delEx) {
                    Log::error('Failed to delete orphaned new image after DB rollback', [
                        'newPath' => $newPath,
                        'error' => $delEx->getMessage()
                    ]);
                    CleanupOrphanedFileJob::dispatch($newPath)->delay(now()->addMinutes(1));
                }

                Log::error('ProductImageController::update - DB save failed', ['error' => $e->getMessage()]);
                return response()->error('Failed to save image record', 500);
            }

            // Post-commit: try to delete the old path; schedule cleanup if delete fails
            if ($oldPath) {
                try {
                    $this->images->delete($oldPath);
                } catch (\Throwable $delEx) {
                    Log::warning('Failed to delete old product image after DB update', [
                        'oldPath' => $oldPath, 'newPath' => $newPath, 'error' => $delEx->getMessage()
                    ]);
                    CleanupOrphanedFileJob::dispatch($oldPath);
                }
            }
        } else {
            // No new file, just a sort_order update
            $image->save();
        }

        $image->refresh();
        return response()->success(
            data: new ProductImageResource($image),
            message: 'Image updated.'
        );
    }

    public function destroy(Product $product, ProductImage $image): JsonResponse
    {
        // With route scopeBindings the $image is already scoped to the $product,
        // so there's no need to manually verify ownership here.
        $path = $image->path;

        try {
            $this->images->delete($path);
        } catch (\Throwable $e) {
            Log::warning('Failed to delete product image in destroy; scheduling cleanup', ['path' => $path, 'error' => $e->getMessage()]);
            CleanupOrphanedFileJob::dispatch($path)->delay(now()->addMinutes(1));
        }

        $image->delete();

        return response()->success(message: 'Image removed.');
    }
}
