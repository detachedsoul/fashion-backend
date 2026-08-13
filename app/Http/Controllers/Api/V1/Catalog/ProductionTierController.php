<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\ProductionTierResource;
use App\Models\ProductionTier;
use Illuminate\Http\JsonResponse;

class ProductionTierController extends Controller
{
    public function index(): JsonResponse
    {
        $tiers = ProductionTier::query()
            ->where('is_active', true)
            ->orderByDesc('production_days_min')
            ->get();

        return response()->success(data: ProductionTierResource::collection($tiers));
    }
}
