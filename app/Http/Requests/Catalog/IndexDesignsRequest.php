<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class IndexDesignsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public browsing endpoint
    }

    public function rules(): array
    {
        return [
            'clothing_type_id' => ['sometimes', 'string', 'exists:clothing_types,id'],
            'featured' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'string', 'max:255'],
            'min_price_kobo' => ['sometimes', 'integer', 'min:0'],
            'max_price_kobo' => ['sometimes', 'integer', 'gte:min_price_kobo'],
            'sort' => ['sometimes', 'in:newest,price_asc,price_desc,featured_first'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
