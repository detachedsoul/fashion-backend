<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class IndexClothingTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by auth:admin + permission:products.manage at the route level
    }

    public function rules(): array
    {
        return [
            'is_active' => ['sometimes', 'boolean'],
            'is_custom_only' => ['sometimes', 'boolean'],
        ];
    }
}
