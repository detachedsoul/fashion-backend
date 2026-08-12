<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\AdminRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
])]
#[Hidden([
    'password',
    'remember_token',
    'two_factor_secret',
])]
class Admin extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, HasUlids, Notifiable, SoftDeletes;

    protected $guard_name = 'admin';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => AdminRole::class,
            'status' => AccountStatus::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === AdminRole::Admin;
    }
}
