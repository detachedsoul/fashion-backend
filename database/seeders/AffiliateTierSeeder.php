<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliateTierSeeder extends Seeder
{
    /**
     * Tiers are unlocked by trailing-12-month referred-sales revenue, never
     * by a payment. Rates are basis points (1000 = 10.00%). Adjust the
     * thresholds/rates to your actual margins before going live.
     */
    public function run(): void
    {
        $tiers = [
            [
                'key' => 'bronze',
                'name' => 'Bronze',
                'min_qualifying_sales_kobo' => 0,
                'commission_rate_bps' => 1000, // 10%
                'passive_rate_bps' => 200,      // 2%
                'team_bonus_rate_bps' => 0,
                'team_bonus_depth' => 0,
            ],
            [
                'key' => 'silver',
                'name' => 'Silver',
                'min_qualifying_sales_kobo' => 50_000_00 * 100, // ₦5,000,000 in kobo, tune to your numbers
                'commission_rate_bps' => 1500,
                'passive_rate_bps' => 300,
                'team_bonus_rate_bps' => 200,
                'team_bonus_depth' => 2,
            ],
            [
                'key' => 'gold',
                'name' => 'Gold',
                'min_qualifying_sales_kobo' => 150_000_00 * 100,
                'commission_rate_bps' => 2500,
                'passive_rate_bps' => 400,
                'team_bonus_rate_bps' => 300,
                'team_bonus_depth' => 3,
            ],
            [
                'key' => 'platinum',
                'name' => 'Platinum',
                'min_qualifying_sales_kobo' => 400_000_00 * 100,
                'commission_rate_bps' => 4000,
                'passive_rate_bps' => 500,
                'team_bonus_rate_bps' => 400,
                'team_bonus_depth' => 3,
            ],
        ];

        foreach ($tiers as $tier) {
            DB::table('affiliate_tiers')->updateOrInsert(
                ['key' => $tier['key']],
                $tier + [
                    'id' => (string) Str::ulid(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
