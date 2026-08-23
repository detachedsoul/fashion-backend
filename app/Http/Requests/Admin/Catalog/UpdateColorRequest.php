<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'hex_code' => ['sometimes', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=100,min_height=100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
