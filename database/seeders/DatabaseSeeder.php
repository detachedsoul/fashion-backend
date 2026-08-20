<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(100)->create();
        Admin::factory(10)->create();

        $this->call([
            RoleAndPermissionSeeder::class,
            AdminSeeder::class,
            AffiliateTierSeeder::class,
            ProductionTierSeeder::class,
            CatalogSeeder::class,
            DesignSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
