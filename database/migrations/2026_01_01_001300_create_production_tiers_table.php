<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_tiers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('key')->unique(); // standard | premium | luxury
            $table->string('name');
            $table->unsignedSmallInteger('production_days_min');
            $table->unsignedSmallInteger('production_days_max');
            $table->string('fee_type'); // flat | percentage
            // meaning depends on fee_type: flat -> kobo amount, percentage -> basis points (2000 = 20.00%)
            $table->unsignedInteger('fee_value');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_tiers');
    }
};
