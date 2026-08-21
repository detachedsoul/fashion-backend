<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\IndexFabricsRequest;
use App\Http\Resources\Catalog\FabricResource;
use App\Models\Fabric;
use Illuminate\Http\JsonResponse;

class FabricController extends Controller
{
    public function index(IndexFabricsRequest $request): JsonResponse
    {
        $fabrics = Fabric::query()
            ->where('is_active', true)
            ->when(
                $request->filled('stock_status'),
                fn ($query) => $query->where('stock_status', $request->string('stock_status')),
            )
            ->orderBy('name')
            ->get();

        return response()->success(data: FabricResource::collection($fabrics));
    }
}
