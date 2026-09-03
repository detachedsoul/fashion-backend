<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fabric_id' => ['required', 'string', 'exists:fabrics,id'],
            'color_id' => ['required', 'string', 'exists:colors,id'],
            'size_id' => ['required', 'string', 'exists:sizes,id'],
            'price_override_kobo' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * No DB-level unique constraint covers this combo, so it's enforced
     * here - a second variant with the exact same fabric+color+size for
     * the same product would just be a confusing duplicate, not a
     * meaningfully different option.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $product = $this->route('product');

            if (! $product || ! $this->filled(['fabric_id', 'color_id', 'size_id'])) {
                return;
            }

            $exists = $product->variants()
                ->where('fabric_id', $this->string('fabric_id')->value())
                ->where('color_id', $this->string('color_id')->value())
                ->where('size_id', $this->string('size_id')->value())
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'fabric_id',
                    'A variant with this fabric, color, and size combination already exists for this product.',
                );
            }
        });
    }
}
