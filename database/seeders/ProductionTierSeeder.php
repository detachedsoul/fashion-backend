<?php

namespace Database\Seeders;

use App\Models\ProductionTier;
use Illuminate\Database\Seeder;

class ProductionTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['key' => 'standard', 'name' => 'Standard', 'production_days_min' => 7, 'production_days_max' => 10, 'fee_type' => 'flat', 'fee_value' => 0],
            ['key' => 'premium', 'name' => 'Premium', 'production_days_min' => 3, 'production_days_max' => 5, 'fee_type' => 'percentage', 'fee_value' => 2000], // 20.00%
            ['key' => 'luxury', 'name' => 'Luxury / Express', 'production_days_min' => 1, 'production_days_max' => 2, 'fee_type' => 'percentage', 'fee_value' => 4000], // 40.00%
        ];

        foreach ($tiers as $tier) {
            ProductionTier::updateOrCreate(['key' => $tier['key']], [...$tier, 'is_active' => true]);
        }
    }
}
