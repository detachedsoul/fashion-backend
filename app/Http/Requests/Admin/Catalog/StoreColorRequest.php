<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'hex_code' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
