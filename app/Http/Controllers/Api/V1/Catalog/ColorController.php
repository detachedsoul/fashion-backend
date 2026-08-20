<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\ColorResource;
use App\Models\Color;
use Illuminate\Http\JsonResponse;

class ColorController extends Controller
{
    public function index(): JsonResponse
    {
        $colors = Color::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->success(data: ColorResource::collection($colors));
    }
}
