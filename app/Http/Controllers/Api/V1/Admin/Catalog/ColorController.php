<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\IndexColorsRequest;
use App\Http\Requests\Admin\Catalog\StoreColorRequest;
use App\Http\Requests\Admin\Catalog\UpdateColorRequest;
use App\Http\Resources\Catalog\ColorResource;
use App\Models\Color;
use App\Services\CatalogImageService;
use Illuminate\Http\JsonResponse;

class ColorController extends Controller
{
    public function __construct(protected CatalogImageService $images) {}

    public function index(IndexColorsRequest $request): JsonResponse
    {
        $colors = Color::query()
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active')),
            )
            ->orderBy('name')
            ->get();

        return response()->success(data: ColorResource::collection($colors));
    }

    public function store(StoreColorRequest $request): JsonResponse
    {
        $color = Color::create([
            ...$request->safe()->except('image'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            $color->forceFill([
                'swatch_image_path' => $this->images->store($request->file('image'), 'colors'),
            ])->save();
        }

        return response()->success(data: new ColorResource($color), message: 'Color created.', status: 201);
    }

    public function update(UpdateColorRequest $request, Color $color): JsonResponse
    {
        $color->fill($request->safe()->except('image'));

        if ($request->hasFile('image')) {
            $color->swatch_image_path = $this->images->replace(
                $color->swatch_image_path,
                $request->file('image'),
                'colors',
            );
        }

        $color->save();

        return response()->success(data: new ColorResource($color), message: 'Color updated.');
    }

    public function destroy(Color $color): JsonResponse
    {
        $this->images->delete($color->swatch_image_path);
        $color->delete();

        return response()->success(message: 'Color deleted.');
    }
}
