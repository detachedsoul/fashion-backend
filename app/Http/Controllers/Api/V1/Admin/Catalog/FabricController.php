<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\IndexFabricsRequest;
use App\Http\Requests\Admin\Catalog\StoreFabricRequest;
use App\Http\Requests\Admin\Catalog\UpdateFabricRequest;
use App\Http\Resources\Catalog\FabricResource;
use App\Models\Fabric;
use App\Services\CatalogImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class FabricController extends Controller
{
    public function __construct(protected CatalogImageService $images) {}

    public function index(IndexFabricsRequest $request): JsonResponse
    {
        $fabrics = Fabric::query()
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active')),
            )
            ->when(
                $request->filled('stock_status'),
                fn ($query) => $query->where('stock_status', $request->string('stock_status')),
            )
            ->orderBy('name')
            ->get();

        return response()->success(data: FabricResource::collection($fabrics));
    }

    public function store(StoreFabricRequest $request): JsonResponse
    {
        $fabric = Fabric::create([
            ...$request->safe()->except('image'),
            'slug' => $this->uniqueSlug($request->string('name')->value()),
            'stock_status' => $request->input('stock_status', 'in_stock'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            $fabric->forceFill([
                'swatch_image_path' => $this->images->store($request->file('image'), 'fabrics'),
            ])->save();
        }

        return response()->success(data: new FabricResource($fabric), message: 'Fabric created.', status: 201);
    }

    public function update(UpdateFabricRequest $request, Fabric $fabric): JsonResponse
    {
        $fabric->fill($request->safe()->except(['image', 'name']));

        if ($request->filled('name') && $request->string('name') !== $fabric->name) {
            $fabric->name = $request->string('name');
            $fabric->slug = $this->uniqueSlug($request->string('name')->value(), $fabric->id);
        }

        if ($request->hasFile('image')) {
            $fabric->swatch_image_path = $this->images->replace(
                $fabric->swatch_image_path,
                $request->file('image'),
                'fabrics',
            );
        }

        $fabric->save();

        return response()->success(data: new FabricResource($fabric), message: 'Fabric updated.');
    }

    public function destroy(Fabric $fabric): JsonResponse
    {
        $this->images->delete($fabric->swatch_image_path);
        $fabric->delete();

        return response()->success(message: 'Fabric deleted.');
    }

    protected function uniqueSlug(string $name, ?string $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Fabric::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-".++$i;
        }

        return $slug;
    }
}
