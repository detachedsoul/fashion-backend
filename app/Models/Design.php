<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['clothing_type_id', 'name', 'slug', 'description', 'base_price_kobo', 'is_featured', 'is_active'])]
class Design extends Model
{
    use HasUlids;

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function clothingType(): BelongsTo
    {
        return $this->belongsTo(ClothingType::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(DesignImage::class)->orderBy('sort_order');
    }
}
