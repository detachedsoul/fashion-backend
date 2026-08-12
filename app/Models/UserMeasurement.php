<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'label',
    'chest',
    'waist',
    'hips',
    'shoulder',
    'sleeve_length',
    'inseam',
    'neck',
    'height',
    'weight',
    'unit',
    'source',
    'notes',
    'is_default',
])]
class UserMeasurement extends Model
{
    use HasUlids;

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
