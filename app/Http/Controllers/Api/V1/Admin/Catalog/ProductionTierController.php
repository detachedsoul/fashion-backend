<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\IndexProductionTiersRequest;
use App\Http\Requests\Admin\Catalog\StoreProductionTierRequest;
use App\Http\Requests\Admin\Catalog\UpdateProductionTierRequest;
use App\Http\Resources\Catalog\ProductionTierResource;
use App\Models\ProductionTier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductionTierController extends Controller
{
    public function index(IndexProductionTiersRequest $request): JsonResponse
    {
        $tiers = ProductionTier::query()
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active')),
            )
            ->orderByDesc('production_days_min')
            ->get();

        return response()->success(data: ProductionTierResource::collection($tiers));
    }

    public function store(StoreProductionTierRequest $request): JsonResponse
    {
        $tier = ProductionTier::create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->success(data: new ProductionTierResource($tier), message: 'Production tier created.', status: 201);
    }

    public function update(UpdateProductionTierRequest $request, ProductionTier $productionTier): JsonResponse
    {
        $productionTier->fill($request->validated())->save();

        return response()->success(data: new ProductionTierResource($productionTier), message: 'Production tier updated.');
    }

    public function destroy(ProductionTier $productionTier): JsonResponse
    {
        $hasOrders = DB::table('orders')->where('production_tier_id', $productionTier->id)->exists();

        if ($hasOrders) {
            return response()->error(
                'Cannot delete: still in use by existing orders. Deactivate it instead.',
                null,
                409,
            );
        }

        $productionTier->delete();

        return response()->success(message: 'Production tier deleted.');
    }
}
