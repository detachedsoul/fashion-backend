<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreClothingTypeRequest;
use App\Http\Requests\Admin\Catalog\UpdateClothingTypeRequest;
use App\Http\Resources\Catalog\ClothingTypeResource;
use App\Models\ClothingType;
use App\Services\CatalogImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ClothingTypeController extends Controller
{
    public function __construct(protected CatalogImageService $images) {}

    public function index(): JsonResponse
    {
        // Unlike the public browse endpoint, admins see everything,
        // including inactive ones.
        $clothingTypes = ClothingType::query()->orderBy('name')->get();

        return response()->success(data: ClothingTypeResource::collection($clothingTypes));
    }

    public function store(StoreClothingTypeRequest $request): JsonResponse
    {
        $clothingType = ClothingType::create([
            ...$request->safe()->except('image'),
            'slug' => $this->uniqueSlug($request->string('name')->value()),
            'is_custom_only' => $request->boolean('is_custom_only'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            $clothingType->forceFill([
                'image_path' => $this->images->store($request->file('image'), 'clothing-types'),
            ])->save();
        }

        return response()->success(data: new ClothingTypeResource($clothingType), message: 'Clothing type created.', status: 201);
    }

    public function update(UpdateClothingTypeRequest $request, ClothingType $clothingType): JsonResponse
    {
        $clothingType->fill($request->safe()->except(['image', 'name']));

        if ($request->filled('name') && $request->string('name') !== $clothingType->name) {
            $clothingType->name = $request->string('name');
            $clothingType->slug = $this->uniqueSlug($request->string('name')->value(), $clothingType->id);
        }

        if ($request->hasFile('image')) {
            $clothingType->image_path = $this->images->replace(
                $clothingType->image_path,
                $request->file('image'),
                'clothing-types',
            );
        }

        $clothingType->save();

        return response()->success(data: new ClothingTypeResource($clothingType), message: 'Clothing type updated.');
    }

    public function destroy(ClothingType $clothingType): JsonResponse
    {
        // designs/products RESTRICT-delete on clothing_type_id - check first
        // and return a clean 409 rather than letting a raw DB constraint
        // error surface to the client.
        if ($clothingType->designs()->exists() || $clothingType->products()->exists()) {
            return response()->error(
                'Cannot delete: still in use by existing designs or products. Deactivate it instead.',
                null,
                409,
            );
        }

        $this->images->delete($clothingType->image_path);
        $clothingType->delete();

        return response()->success(message: 'Clothing type deleted.');
    }

    protected function uniqueSlug(string $name, ?string $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            ClothingType::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-".++$i;
        }

        return $slug;
    }
}
