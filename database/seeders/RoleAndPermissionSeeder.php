<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'orders.view',
            'orders.manage',
            'products.manage',
            'referrals.view',
            'referrals.manage',
            'withdrawals.view',
            'withdrawals.approve',
            'loyalty.manage',
            'content.manage',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'admin']);
        $staff->syncPermissions(['orders.view', 'orders.manage', 'referrals.view', 'withdrawals.view']);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $admin->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }
}
