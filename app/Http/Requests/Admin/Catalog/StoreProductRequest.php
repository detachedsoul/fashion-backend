<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Concerns\ConvertsBooleans;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    use ConvertsBooleans;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clothing_type_id' => ['required', 'string', 'exists:clothing_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'base_price_kobo' => ['required', 'integer', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'published_at' => ['sometimes', 'nullable', 'date'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=100,min_height=100'],
        ];
    }
}
