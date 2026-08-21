<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\IndexClothingTypesRequest;
use App\Http\Resources\Catalog\ClothingTypeResource;
use App\Models\ClothingType;
use Illuminate\Http\JsonResponse;

class ClothingTypeController extends Controller
{
    public function index(IndexClothingTypesRequest $request): JsonResponse
    {
        $clothingTypes = ClothingType::query()
            ->where('is_active', true)
            ->when(
                $request->filled('is_custom_only'),
                fn ($query) => $query->where('is_custom_only', $request->boolean('is_custom_only')),
            )
            ->orderBy('name')
            ->get();

        return response()->success(data: ClothingTypeResource::collection($clothingTypes));
    }
}
