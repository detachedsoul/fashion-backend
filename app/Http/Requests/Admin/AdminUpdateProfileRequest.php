<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by auth:admin middleware at the route level
    }

    public function rules(): array
    {
        $adminId = $this->user()->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($adminId),
            ],
            'current_password' => ['required_with:email', 'current_password:admin'],
        ];
    }
}
