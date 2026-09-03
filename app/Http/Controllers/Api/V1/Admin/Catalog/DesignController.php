<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\IndexDesignsRequest;
use App\Http\Requests\Admin\Catalog\StoreDesignRequest;
use App\Http\Requests\Admin\Catalog\UpdateDesignRequest;
use App\Http\Resources\Catalog\DesignResource;
use App\Models\Design;
use App\Models\DesignImage;
use App\Services\CatalogImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    public function __construct(protected CatalogImageService $images) {}

    public function index(IndexDesignsRequest $request): JsonResponse
    {
        $designs = Design::query()
            ->with(['clothingType', 'images'])
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->value().'%'),
            )
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active')),
            )
            ->when(
                $request->filled('clothing_type_id'),
                fn ($query) => $query->where('clothing_type_id', $request->string('clothing_type_id')->value()),
            )
            ->when(
                $request->filled('is_featured'),
                fn ($query) => $query->where('is_featured', $request->boolean('is_featured')),
            )
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->success(data: DesignResource::collection($designs));
    }

    public function store(StoreDesignRequest $request): JsonResponse
    {
        $design = DB::transaction(function () use ($request) {
            $design = Design::create([
                ...$request->safe()->except('images'),
                'slug' => $this->uniqueSlug($request->string('name')->value()),
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            foreach ($request->file('images') as $index => $file) {
                DesignImage::create([
                    'design_id' => $design->id,
                    'path' => $this->images->store($file, 'designs'),
                    'sort_order' => $index,
                ]);
            }

            return $design;
        });

        $design->load(['clothingType', 'images']);

        return response()->success(data: new DesignResource($design), message: 'Design created.', status: 201);
    }

    public function update(UpdateDesignRequest $request, Design $design): JsonResponse
    {
        $design->fill($request->safe()->except('name'));

        if ($request->filled('name') && $request->string('name')->value() !== $design->name) {
            $design->name = $request->string('name')->value();
            $design->slug = $this->uniqueSlug($request->string('name')->value(), $design->id);
        }

        $design->save();
        $design->load(['clothingType', 'images']);

        return response()->success(data: new DesignResource($design), message: 'Design updated.');
    }

    public function destroy(Design $design): JsonResponse
    {
        $design->images->each(fn (DesignImage $image) => $this->images->delete($image->path));
        $design->delete();

        return response()->success(message: 'Design deleted.');
    }

    protected function uniqueSlug(string $name, ?string $ignoreId = null): string
    {
        $base = Str::slug($name);

        if (! $this->slugExists($base, $ignoreId)) {
            return $base;
        }

        do {
            $slug = "{$base}-".Str::lower(Str::random(6));
        } while ($this->slugExists($slug, $ignoreId));

        return $slug;
    }

    protected function slugExists(string $slug, ?string $ignoreId = null): bool
    {
        return Design::where('slug', $slug)
            ->when(
                $ignoreId,
                fn ($query) => $query->where('id', '!=', $ignoreId)
            )
            ->exists();
    }
}
