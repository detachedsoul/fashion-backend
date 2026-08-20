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
            'image' => ['sometimes', 'nullable', 'image', 'max:4096'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
