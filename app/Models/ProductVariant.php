<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'fabric_id', 'color_id', 'size_id', 'sku', 'price_override_kobo', 'stock_quantity'])]
class ProductVariant extends Model
{
    use HasUlids;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fabric(): BelongsTo
    {
        return $this->belongsTo(Fabric::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
}
