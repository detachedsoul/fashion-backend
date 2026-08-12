<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Append-only, like referral_commissions - never mutate a row, only insert.
        Schema::create('loyalty_ledger', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('action_key');
            $table->string('reference_type')->nullable(); // polymorphic: order, review, etc.
            $table->ulid('reference_id')->nullable();
            $table->integer('points_delta'); // + earn / - redeem
            $table->integer('balance_after');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_ledger');
    }
};
