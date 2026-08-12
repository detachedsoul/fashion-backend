<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
        ];
    }

    /**
     * Resolve the referral_code input into an actual referring user, if present.
     */
    public function referrer(): ?User
    {
        return $this->filled('referral_code')
            ? User::where('referral_code', $this->string('referral_code'))->first()
            : null;
    }
}
