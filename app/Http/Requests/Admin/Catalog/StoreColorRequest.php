<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Concerns\ConvertsBooleans;
use Illuminate\Foundation\Http\FormRequest;

class StoreColorRequest extends FormRequest
{
    use ConvertsBooleans;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'hex_code' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=100,min_height=100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
