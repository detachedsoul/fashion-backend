<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\IndexColorsRequest;
use App\Http\Resources\Catalog\ColorResource;
use App\Models\Color;
use Illuminate\Http\JsonResponse;

class ColorController extends Controller
{
    public function index(IndexColorsRequest $request): JsonResponse
    {
        $colors = Color::query()
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active')),
            )
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->value().'%'),
            )
            ->when(
                $request->filled('hex_code'),
                fn ($query) => $query->where('hex_code', 'like', '%'.$request->string('hex_code')->value().'%'),
            )
            ->orderBy('name')
            ->get();

        return response()->success(data: ColorResource::collection($colors));
    }
}
