<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class IndexClothingTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public browsing endpoint
    }

    public function rules(): array
    {
        return [
            'is_custom_only' => ['sometimes', 'boolean'],
        ];
    }
}
