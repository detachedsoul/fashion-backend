<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_affiliate_status', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignUlid('affiliate_tier_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('qualifying_sales_kobo')->default(0); // cached, recalculated nightly
            $table->timestamp('tier_since')->nullable();
            $table->timestamp('next_review_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_affiliate_status');
    }
};
