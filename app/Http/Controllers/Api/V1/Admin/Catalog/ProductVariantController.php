<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreProductVariantRequest;
use App\Http\Requests\Admin\Catalog\UpdateProductVariantRequest;
use App\Http\Resources\Catalog\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
{
    public function store(
        StoreProductVariantRequest $request,
        Product $product
    ): JsonResponse {
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'fabric_id' => $request->string('fabric_id')->value(),
            'color_id' => $request->string('color_id')->value(),
            'size_id' => $request->string('size_id')->value(),
            'sku' => $this->generateSku($product),
            'price_override_kobo' => $request->input('price_override_kobo'),
            'stock_quantity' => $request->integer('stock_quantity'),
        ]);

        $variant->load(['fabric', 'color', 'size']);

        return response()->success(
            data: new ProductVariantResource($variant),
            message: 'Variant added.',
            status: 201
        );
    }

    public function update(
        UpdateProductVariantRequest $request,
        Product $product,
        ProductVariant $variant
    ): JsonResponse {
        $variant->update($request->validated());

        $variant->load(['fabric', 'color', 'size']);

        return response()->success(
            data: new ProductVariantResource($variant),
            message: 'Variant updated.'
        );
    }

    public function destroy(
        Product $product,
        ProductVariant $variant
    ): JsonResponse {
        $variant->delete();

        return response()->success(
            message: 'Variant removed.'
        );
    }

    protected function generateSku(Product $product): string
    {
        do {
            $sku = $product->sku.'-'.Str::upper(Str::random(4));
        } while (ProductVariant::where('sku', $sku)->exists());

        return $sku;
    }
}
