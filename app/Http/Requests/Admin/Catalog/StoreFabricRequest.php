<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreFabricRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_modifier_kobo' => ['sometimes', 'integer', 'min:0'],
            'stock_status' => ['sometimes', 'in:in_stock,low_stock,out_of_stock'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
