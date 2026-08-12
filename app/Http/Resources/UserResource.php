<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function __construct(
        mixed $resource,
        private ?string $token = null,
    ) {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'email_verified' => $this->email_verified_at !== null,
                'referral_code' => $this->referral_code,
                'created_at' => $this->created_at?->toIso8601String(),
            ],

            'token' => $this->token,
        ];
    }
}
