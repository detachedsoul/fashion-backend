<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Concerns\ConvertsBooleans;
use Illuminate\Foundation\Http\FormRequest;

class IndexFabricsRequest extends FormRequest
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
            'stock_status' => ['sometimes', 'in:in_stock,low_stock,out_of_stock'],
        ];
    }
}
