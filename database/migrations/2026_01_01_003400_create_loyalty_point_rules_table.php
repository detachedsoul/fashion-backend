<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_point_rules', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('action_key')->unique();
            // register_account | complete_profile | place_order | write_review |
            // upload_outfit_photo | referral_first_purchase
            $table->unsignedInteger('points_flat')->nullable();
            $table->unsignedInteger('points_per_kobo_spent')->nullable(); // e.g. 1 point per 10,000 kobo (=1 point per NGN100)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_rules');
    }
};
