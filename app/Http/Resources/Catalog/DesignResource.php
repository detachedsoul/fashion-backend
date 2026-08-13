<?php

namespace App\Http\Resources\Catalog;

use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Design
 *
 * Expects 'clothingType' and 'images' to be eager loaded by the caller -
 * whenLoaded() means it degrades gracefully (omits the key) rather than
 * triggering a lazy-load violation if a future caller forgets to.
 */
class DesignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'base_price_kobo' => $this->base_price_kobo,
            'is_featured' => $this->is_featured,
            'clothing_type' => new ClothingTypeResource($this->whenLoaded('clothingType')),
            'images' => DesignImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
