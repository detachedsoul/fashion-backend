<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'name', 'production_days_min', 'production_days_max', 'fee_type', 'fee_value', 'is_active'])]
class ProductionTier extends Model
{
    use HasUlids;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
