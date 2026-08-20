<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clothing_type_id' => ['sometimes', 'string', 'exists:clothing_types,id'],
            'color_id' => ['sometimes', 'string', 'exists:colors,id'],
            'fabric_id' => ['sometimes', 'string', 'exists:fabrics,id'],
            'size_id' => ['sometimes', 'string', 'exists:sizes,id'],
            'search' => ['sometimes', 'string', 'max:255'],
            'min_price_kobo' => ['sometimes', 'integer', 'min:0'],
            'max_price_kobo' => ['sometimes', 'integer', 'gte:min_price_kobo'],
            'sort' => ['sometimes', 'in:newest,price_asc,price_desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
