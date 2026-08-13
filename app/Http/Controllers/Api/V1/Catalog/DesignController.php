<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\IndexDesignsRequest;
use App\Http\Resources\Catalog\DesignResource;
use App\Models\Design;
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
            ->latest()
            ->paginate(24);

        return response()->success(data: DesignResource::collection($designs));
    }

    public function show(Design $design): JsonResponse
    {
        abort_if(! $design->is_active, 404);

        $design->load(['clothingType', 'images']);

        return response()->success(data: new DesignResource($design));
    }
}
