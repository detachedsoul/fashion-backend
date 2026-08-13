<?php

namespace App\Http\Resources\Catalog;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProductVariant */
class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'price_override_kobo' => $this->price_override_kobo,
            'stock_quantity' => $this->stock_quantity,
            'fabric' => new FabricResource($this->whenLoaded('fabric')),
            'color' => new ColorResource($this->whenLoaded('color')),
            'size' => new SizeResource($this->whenLoaded('size')),
        ];
    }
}
