<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductionTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tierId = $this->route('production_tier')?->id;

        return [
            'key' => ['sometimes', 'string', 'max:50', Rule::unique('production_tiers', 'key')->ignore($tierId)],
            'name' => ['sometimes', 'string', 'max:255'],
            'production_days_min' => ['sometimes', 'integer', 'min:0'],
            'production_days_max' => ['sometimes', 'integer', 'gte:production_days_min'],
            'fee_type' => ['sometimes', 'in:flat,percentage'],
            'fee_value' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
