<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Populated by a nightly scheduled job so the admin analytics dashboard
        // never has to aggregate raw orders/commissions on every page load.
        Schema::create('daily_sales_summary', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->date('summary_date')->unique();
            $table->unsignedInteger('orders_count')->default(0);
            $table->unsignedBigInteger('gross_revenue_kobo')->default(0);
            $table->unsignedBigInteger('discount_total_kobo')->default(0);
            $table->unsignedBigInteger('referral_commissions_kobo')->default(0);
            $table->unsignedInteger('new_customers_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_sales_summary');
    }
};
