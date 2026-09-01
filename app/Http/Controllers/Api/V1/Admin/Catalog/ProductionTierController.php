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
use Illuminate\Support\Str;

class ProductionTierController extends Controller
{
    public function index(IndexProductionTiersRequest $request): JsonResponse
    {
        $tiers = ProductionTier::query()
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active')),
            )
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->value().'%'),
            )
            ->when(
                $request->filled('fee_type'),
                fn ($query) => $query->where('fee_type', 'like', '%'.$request->string('fee_type')->value().'%'),
            )
            ->orderByDesc('production_days_min')
            ->get();

        return response()->success(data: ProductionTierResource::collection($tiers));
    }

    public function store(StoreProductionTierRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['key'] = $this->generateUniqueKey($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        $tier = ProductionTier::create($data);

        return response()->success(
            data: new ProductionTierResource($tier),
            message: 'Production tier created.',
            status: 201,
        );
    }

    public function update(
        UpdateProductionTierRequest $request,
        ProductionTier $productionTier
    ): JsonResponse {
        $data = $request->validated();

        if (isset($data['name']) && $data['name'] !== $productionTier->name) {
            $data['key'] = $this->generateUniqueKey(
                $data['name']
            );
        }

        $productionTier->fill($data)->save();

        return response()->success(
            data: new ProductionTierResource($productionTier),
            message: 'Production tier updated.',
        );
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

    private function generateUniqueKey(string $name): string
    {
        $baseKey = Str::slug($name);

        if (! ProductionTier::where('key', $baseKey)->exists()) {
            return $baseKey;
        }

        do {
            $key = $baseKey.'-'.Str::random(8);
        } while (ProductionTier::where('key', $key)->exists());

        return $key;
    }
}
