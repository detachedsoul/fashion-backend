<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (Admin::where('email', 'admin@example.com')->exists()) {
            return;
        }

        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('change-me-immediately'),
            'role' => AdminRole::Admin,
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('admin');
    }
}
