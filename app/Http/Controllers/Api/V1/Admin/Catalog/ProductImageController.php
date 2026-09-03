<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreProductImageRequest;
use App\Http\Requests\Admin\Catalog\UpdateProductImageRequest;
use App\Http\Resources\Catalog\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\CatalogImageService;
use Illuminate\Http\JsonResponse;

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
        $oldPath = null;

        if ($request->hasFile('image')) {
            $oldPath = $image->path;

            $image->path = $this->images->store(
                $request->file('image'),
                'products'
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
            data: new ProductImageResource($image),
            message: 'Image updated.'
        );
    }

    public function destroy(Product $product, ProductImage $productImage): JsonResponse
    {
        abort_unless($productImage->product_id === $product->id, 404);

        $this->images->delete($productImage->path);
        $productImage->delete();

        return response()->success(message: 'Image removed.');
    }
}
