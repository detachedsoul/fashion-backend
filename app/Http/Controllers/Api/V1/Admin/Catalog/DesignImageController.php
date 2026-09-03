<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreDesignImageRequest;
use App\Http\Requests\Admin\Catalog\UpdateDesignImageRequest;
use App\Http\Resources\Catalog\DesignImageResource;
use App\Models\Design;
use App\Models\DesignImage;
use App\Services\CatalogImageService;
use Illuminate\Http\JsonResponse;

class DesignImageController extends Controller
{
    public function __construct(
        protected CatalogImageService $images
    ) {}

    public function store(
        StoreDesignImageRequest $request,
        Design $design
    ): JsonResponse {
        $image = DesignImage::create([
            'design_id' => $design->id,
            'path' => $this->images->store(
                $request->file('image'),
                'designs'
            ),
            'sort_order' => $request->integer(
                'sort_order',
                ($design->images()->max('sort_order') ?? -1) + 1
            ),
        ]);

        return response()->success(
            data: new DesignImageResource($image),
            message: 'Image added.',
            status: 201
        );
    }

    public function update(
        UpdateDesignImageRequest $request,
        Design $design,
        DesignImage $image
    ): JsonResponse {
        $oldPath = null;

        if ($request->hasFile('image')) {
            $oldPath = $image->path;

            $image->path = $this->images->store(
                $request->file('image'),
                'designs'
            );
        }

        if ($request->has('sort_order')) {
            $image->sort_order = $request->integer('sort_order');
        }

        $image->save();

        if ($oldPath) {
            $this->images->delete($oldPath);
        }

        return response()->success(
            data: new DesignImageResource($image),
            message: 'Image updated.'
        );
    }

    public function destroy(
        Design $design,
        DesignImage $image
    ): JsonResponse {
        $path = $image->path;

        // delete file first, then DB record to avoid orphan files on failure
        $this->images->delete($path);
        $image->delete();

        return response()->success(
            message: 'Image removed.'
        );
    }
}
