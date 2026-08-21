<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\IndexProductsRequest;
use App\Http\Resources\Catalog\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(IndexProductsRequest $request): JsonResponse
    {
        $products = Product::query()
            ->published()
            ->with(['clothingType', 'variants.fabric', 'variants.color', 'variants.size', 'images'])
            ->when(
                $request->filled('clothing_type_id'),
                fn ($query) => $query->where('clothing_type_id', $request->string('clothing_type_id')),
            )
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%'),
            )
            ->when(
                $request->filled('min_price_kobo'),
                fn ($query) => $query->where('base_price_kobo', '>=', $request->integer('min_price_kobo')),
            )
            ->when(
                $request->filled('max_price_kobo'),
                fn ($query) => $query->where('base_price_kobo', '<=', $request->integer('max_price_kobo')),
            )
            ->when(
                $request->filled('color_id'),
                fn ($query) => $query->whereHas('variants', fn ($v) => $v->where('color_id', $request->string('color_id'))),
            )
            ->when(
                $request->filled('fabric_id'),
                fn ($query) => $query->whereHas('variants', fn ($v) => $v->where('fabric_id', $request->string('fabric_id'))),
            )
            ->when(
                $request->filled('size_id'),
                fn ($query) => $query->whereHas('variants', fn ($v) => $v->where('size_id', $request->string('size_id'))),
            )
            ->tap(fn ($query) => $this->applySort($query, $request->string('sort')->value()))
            ->paginate($request->integer('per_page', 10));

        return response()->success(data: ProductResource::collection($products));
    }

    public function show(Product $product): JsonResponse
    {
        abort_unless($product->is_active && $product->published_at?->isPast(), 404);

        $product->load(['clothingType', 'variants.fabric', 'variants.color', 'variants.size', 'images']);

        return response()->success(data: new ProductResource($product));
    }

    protected function applySort(Builder $query, ?string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('base_price_kobo'),
            'price_desc' => $query->orderByDesc('base_price_kobo'),
            default => $query->latest('published_at'),
        };
    }
}
