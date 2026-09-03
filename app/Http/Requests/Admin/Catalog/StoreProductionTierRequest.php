<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Concerns\ConvertsBooleans;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductionTierRequest extends FormRequest
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
            'production_days_min' => ['required', 'integer', 'min:0'],
            'production_days_max' => ['required', 'integer', 'gte:production_days_min'],
            'fee_type' => ['required', 'in:flat,percentage'],
            // meaning depends on fee_type - flat: kobo amount, percentage: basis points (2000 = 20.00%)
            'fee_value' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
