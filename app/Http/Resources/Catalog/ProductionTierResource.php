<?php

namespace App\Http\Resources\Catalog;

use App\Models\ProductionTier;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProductionTier */
class ProductionTierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'name' => $this->name,
            'production_days_min' => $this->production_days_min,
            'production_days_max' => $this->production_days_max,
            'fee_type' => $this->fee_type,
            'fee_value' => $this->fee_value,
        ];
    }
}
