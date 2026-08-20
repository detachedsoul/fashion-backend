<?php

namespace App\Http\Resources\Catalog;

use App\Models\Fabric;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Fabric */
class FabricResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price_modifier_kobo' => $this->price_modifier_kobo,
            'swatch_image_path' => $this->swatch_image_path,
            'stock_status' => $this->stock_status,
        ];
    }
}
