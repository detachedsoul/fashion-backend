<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_redemptions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('loyalty_reward_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('points_spent');
            $table->foreignUlid('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('redeemed_at')->useCurrent();
            $table->timestamp('fulfilled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_redemptions');
    }
};
