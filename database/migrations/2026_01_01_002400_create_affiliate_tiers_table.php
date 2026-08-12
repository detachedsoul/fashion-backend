<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_tiers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('key')->unique(); // bronze | silver | gold | platinum
            $table->string('name');
            // Rolling 12-month referred-sales volume (kobo) required to reach this tier.
            // Tiers are assigned by a nightly job based on real sales - never purchased.
            $table->unsignedBigInteger('min_qualifying_sales_kobo');
            $table->unsignedSmallInteger('commission_rate_bps'); // basis points on direct referrals' order revenue, 1000 = 10.00%
            $table->unsignedSmallInteger('passive_rate_bps'); // basis points on repeat orders from the same referral
            $table->unsignedSmallInteger('team_bonus_rate_bps')->default(0);
            $table->unsignedTinyInteger('team_bonus_depth')->default(0); // levels deep team bonus reaches (keep small - see architecture notes)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_tiers');
    }
};
