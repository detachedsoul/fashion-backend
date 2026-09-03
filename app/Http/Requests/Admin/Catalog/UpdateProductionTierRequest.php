<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Concerns\ConvertsBooleans;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductionTierRequest extends FormRequest
{
    use ConvertsBooleans;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'production_days_min' => ['sometimes', 'integer', 'min:0'],
            'production_days_max' => ['sometimes', 'integer', 'gte:production_days_min'],
            'fee_type' => ['sometimes', 'in:flat,percentage'],
            'fee_value' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
