<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Concerns\ConvertsBooleans;
use Illuminate\Foundation\Http\FormRequest;

class IndexDesignsRequest extends FormRequest
{
    use ConvertsBooleans;

    public function authorize(): bool
    {
        return true; // gated by auth:admin + permission:products.manage at the route level
    }

    public function rules(): array
    {
        return [
            'is_active' => ['sometimes', 'boolean'],
            'clothing_type_id' => ['sometimes', 'string', 'exists:clothing_types,id'],
            'is_featured' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
