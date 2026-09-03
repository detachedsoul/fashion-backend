<?php

namespace App\Http\Requests\Catalog;

use App\Http\Requests\Concerns\ConvertsBooleans;
use Illuminate\Foundation\Http\FormRequest;

class IndexColorsRequest extends FormRequest
{
    use ConvertsBooleans;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['sometimes', 'boolean'],
            'hex_code' => ['sometimes', 'string'],
        ];
    }
}
