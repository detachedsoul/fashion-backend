<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug', 'description', 'price_modifier_kobo', 'swatch_image_path', 'stock_status', 'is_active'])]
class Fabric extends Model
{
    use HasUlids;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
