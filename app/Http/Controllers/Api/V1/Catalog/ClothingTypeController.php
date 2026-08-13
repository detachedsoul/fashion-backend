<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\ClothingTypeResource;
use App\Models\ClothingType;
use Illuminate\Http\JsonResponse;

class ClothingTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $clothingTypes = ClothingType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->success(
            data: ClothingTypeResource::collection($clothingTypes),
            message: 'Clothing types retrieved successfully.'
        );
    }
}
