<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Concerns\ConvertsBooleans;
use Illuminate\Foundation\Http\FormRequest;

class StoreClothingTypeRequest extends FormRequest
{
    use ConvertsBooleans;

    public function authorize(): bool
    {
        return true; // gated by auth:admin + permission:products.manage at the route level
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=100,min_height=100'],
            'is_custom_only' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
