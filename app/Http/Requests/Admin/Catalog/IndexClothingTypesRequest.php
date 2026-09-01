<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class IndexClothingTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by auth:admin + permission:products.manage at the route level
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('is_active')) {
            $data['is_active'] = $this->boolean('is_active');
        }

        if ($this->has('is_custom_only')) {
            $data['is_custom_only'] = $this->boolean('is_custom_only');
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'is_active' => ['sometimes', 'boolean'],
            'is_custom_only' => ['sometimes', 'boolean'],
        ];
    }
}
