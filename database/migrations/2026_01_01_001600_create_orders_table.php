<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('order_number')->unique(); // e.g. FB-2026-000123
            $table->foreignUlid('user_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('pending_payment');
            // pending_payment | confirmed | in_production | quality_check | shipped | delivered | cancelled | refunded
            $table->foreignUlid('production_tier_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('subtotal_kobo');
            $table->unsignedInteger('service_fee_kobo')->default(0);
            $table->unsignedInteger('discount_total_kobo')->default(0);
            $table->unsignedInteger('shipping_fee_kobo')->default(0);
            $table->unsignedInteger('tax_total_kobo')->default(0);
            $table->unsignedInteger('grand_total_kobo');
            $table->string('currency', 3)->default('NGN');
            $table->foreignUlid('shipping_address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->foreignUlid('billing_address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('promised_at')->nullable(); // computed from production tier SLA
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
