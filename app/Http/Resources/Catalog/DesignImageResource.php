<?php

namespace App\Http\Resources\Catalog;

use App\Models\DesignImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DesignImage */
class DesignImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'path' => $this->path,
            'sort_order' => $this->sort_order,
        ];
    }
}
