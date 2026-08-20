<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['design_id', 'path', 'sort_order'])]
class DesignImage extends Model
{
    use HasUlids;

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }
}
