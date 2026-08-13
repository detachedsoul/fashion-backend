<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'image_path', 'is_custom_only', 'is_active'])]
class ClothingType extends Model
{
    use HasUlids;

    protected function casts(): array
    {
        return [
            'is_custom_only' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function designs(): HasMany
    {
        return $this->hasMany(Design::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
