<?php

namespace App\Http\Resources\Catalog;

use App\Models\ClothingType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ClothingType */
class ClothingTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_path' => $this->image_path,
            'is_custom_only' => $this->is_custom_only,
        ];
    }
}
