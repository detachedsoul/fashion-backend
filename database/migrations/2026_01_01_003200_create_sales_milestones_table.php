<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "Team building" rewards, gated by real cumulative referred-sales
        // revenue (referral_commissions.basis_amount_kobo), never by headcount
        // or signup/registration fees.
        Schema::create('sales_milestones', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name'); // e.g. "Sanetor clothe", "1 bag of rice", "Car"
            $table->text('description')->nullable();
            $table->unsignedBigInteger('required_cumulative_sales_kobo');
            $table->string('reward_type'); // physical_gift | cash | trip
            $table->json('reward_config_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_milestones');
    }
};
