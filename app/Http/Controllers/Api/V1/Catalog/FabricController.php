<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\FabricResource;
use App\Models\Fabric;
use Illuminate\Http\JsonResponse;

class FabricController extends Controller
{
    public function index(): JsonResponse
    {
        $fabrics = Fabric::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->success(data: FabricResource::collection($fabrics));
    }
}
