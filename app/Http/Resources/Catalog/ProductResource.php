<?php

namespace App\Http\Resources\Catalog;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 *
 * Expects 'clothingType', 'variants.fabric'/'variants.color'/'variants.size',
 * and now 'images' to be eager loaded by the caller - see whenLoaded()
 * note in DesignResource.
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'base_price_kobo' => $this->base_price_kobo,
            'sku' => $this->sku,
            'stock_quantity' => $this->stock_quantity,
            'clothing_type' => new ClothingTypeResource($this->whenLoaded('clothingType')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
