<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Append-only ledger. Rows are never updated except the `status`
        // transition (pending -> payable -> paid, or -> reversed on refund).
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('order_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('beneficiary_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('referral_relationship_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('level'); // 1 = direct, 2+ = team bonus
            $table->unsignedBigInteger('basis_amount_kobo'); // the order revenue this % was calculated from
            $table->unsignedSmallInteger('rate_bps_applied');
            $table->unsignedBigInteger('commission_amount_kobo');
            $table->string('status')->default('pending'); // pending | payable | paid | reversed
            $table->timestamp('payable_at')->nullable(); // e.g. order delivered + return-window
            $table->timestamps();

            $table->index(['beneficiary_user_id', 'status']);
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_commissions');
    }
};
