<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\IndexDesignsRequest;
use App\Http\Resources\Catalog\DesignResource;
use App\Models\Design;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class DesignController extends Controller
{
    public function index(IndexDesignsRequest $request): JsonResponse
    {
        $designs = Design::query()
            ->with(['clothingType', 'images'])
            ->where('is_active', true)
            ->when(
                $request->filled('clothing_type_id'),
                fn ($query) => $query->where('clothing_type_id', $request->string('clothing_type_id')),
            )
            ->when(
                $request->filled('featured'),
                fn ($query) => $query->where('is_featured', $request->boolean('featured')),
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
            ->tap(fn ($query) => $this->applySort($query, $request->string('sort')->value()))
            ->paginate($request->integer('per_page', 10));

        return response()->success(data: DesignResource::collection($designs));
    }

    public function show(Design $design): JsonResponse
    {
        abort_if(! $design->is_active, 404);

        $design->load(['clothingType', 'images']);

        return response()->success(data: new DesignResource($design));
    }

    /**
     * Search uses a plain LIKE - fine at current catalog size, but a
     * leading-wildcard LIKE can't use an index efficiently. Worth
     * revisiting (e.g. a dedicated search index) if the design catalog
     * grows into the thousands.
     */
    protected function applySort(Builder $query, ?string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('base_price_kobo'),
            'price_desc' => $query->orderByDesc('base_price_kobo'),
            'featured_first' => $query->orderByDesc('is_featured')->latest(),
            default => $query->latest(),
        };
    }
}
