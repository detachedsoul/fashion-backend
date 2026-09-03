<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Deliberately does NOT allow changing fabric_id/color_id/size_id -
     * those define what the variant IS. Wanting a different combination
     * means creating a new variant and removing the old one, not mutating
     * this one in place.
     */
    public function rules(): array
    {
        return [
            'price_override_kobo' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
