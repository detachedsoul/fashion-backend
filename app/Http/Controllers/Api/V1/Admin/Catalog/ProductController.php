<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\IndexProductsRequest;
use App\Http\Requests\Admin\Catalog\StoreProductRequest;
use App\Http\Requests\Admin\Catalog\UpdateProductRequest;
use App\Http\Resources\Catalog\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\CatalogImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(
        protected CatalogImageService $images
    ) {}

    public function index(IndexProductsRequest $request): JsonResponse
    {
        $products = Product::query()
            ->with([
                'clothingType',
                'variants.fabric',
                'variants.color',
                'variants.size',
                'images',
            ])
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->value().'%'),
            )
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where(
                    'is_active',
                    $request->boolean('is_active')
                ),
            )
            ->when(
                $request->filled('clothing_type_id'),
                fn ($query) => $query->where(
                    'clothing_type_id',
                    $request->string('clothing_type_id')->value()
                ),
            )
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->success(
            data: ProductResource::collection($products)
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create([
                ...$request->safe()->except(['images', 'sku']),
                'slug' => $this->uniqueSlug(
                    $request->string('name')->value()
                ),
                'sku' => $this->generateSku(),
                'stock_quantity' => $request->integer('stock_quantity'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            foreach ($request->file('images') as $index => $file) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $this->images->store($file, 'products'),
                    'sort_order' => $index,
                ]);
            }

            return $product;
        });

        $product->load([
            'clothingType',
            'variants.fabric',
            'variants.color',
            'variants.size',
            'images',
        ]);

        return response()->success(
            data: new ProductResource($product),
            message: 'Product created.',
            status: 201
        );
    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ): JsonResponse {
        $product->fill($request->safe()->except('name'));

        if (
            $request->filled('name') &&
            $request->string('name')->value() !== $product->name
        ) {
            $product->name = $request->string('name')->value();
            $product->slug = $this->uniqueSlug(
                $product->name,
                $product->id
            );
        }

        $product->save();

        $product->load([
            'clothingType',
            'variants.fabric',
            'variants.color',
            'variants.size',
            'images',
        ]);

        return response()->success(
            data: new ProductResource($product),
            message: 'Product updated.'
        );
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->images->each(
            fn (ProductImage $image) => $this->images->delete($image->path)
        );

        $product->delete();

        return response()->success(
            message: 'Product deleted.'
        );
    }

    protected function uniqueSlug(
        string $name,
        ?string $ignoreId = null
    ): string {
        $base = Str::slug($name);

        if (! $this->slugExists($base, $ignoreId)) {
            return $base;
        }

        do {
            $slug = "{$base}-".Str::lower(Str::random(6));
        } while ($this->slugExists($slug, $ignoreId));

        return $slug;
    }

    protected function slugExists(
        string $slug,
        ?string $ignoreId = null
    ): bool {
        return Product::query()
            ->where('slug', $slug)
            ->when(
                $ignoreId,
                fn ($query) => $query->where('id', '!=', $ignoreId)
            )
            ->exists();
    }

    protected function generateSku(): string
    {
        do {
            $sku = 'SKU-'.Str::upper(Str::random(8));
        } while (
            Product::query()
                ->where('sku', $sku)
                ->exists()
        );

        return $sku;
    }
}
