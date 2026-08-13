<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'hex_code', 'swatch_image_path', 'is_active'])]
class Color extends Model
{
    use HasUlids;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
