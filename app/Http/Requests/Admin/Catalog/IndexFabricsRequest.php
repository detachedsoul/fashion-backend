<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class IndexFabricsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('is_active')) {
            $data['is_active'] = $this->boolean('is_active');
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'is_active' => ['sometimes', 'boolean'],
            'stock_status' => ['sometimes', 'in:in_stock,low_stock,out_of_stock'],
        ];
    }
}
