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
        ];
    }
}
