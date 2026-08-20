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

    // No orders() relation yet - the Order model doesn't exist until the
    // Orders module lands. See ProductionTierController::destroy() for how
    // the dependent-check is done in the meantime (raw table query, not a
    // relation). Once Order exists, this should get a real
    // `public function orders(): HasMany` and the controller should switch
    // to using it.
}
