<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\IndexProductsRequest;
use App\Http\Resources\Catalog\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(IndexProductsRequest $request): JsonResponse
    {
        $products = Product::query()
            ->published()
            ->with(['clothingType', 'variants.fabric', 'variants.color', 'variants.size'])
            ->when(
                $request->filled('clothing_type_id'),
                fn ($query) => $query->where('clothing_type_id', $request->string('clothing_type_id')),
            )
            ->latest('published_at')
            ->paginate(24);

        return response()->success(data: ProductResource::collection($products));
    }

    public function show(Product $product): JsonResponse
    {
        abort_unless($product->is_active && $product->published_at?->isPast(), 404);

        $product->load(['clothingType', 'variants.fabric', 'variants.color', 'variants.size']);

        return response()->success(data: new ProductResource($product));
    }
}
