<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_redemptions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('sales_milestone_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('pending'); // pending | approved | fulfilled
            $table->timestamp('achieved_at');
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'sales_milestone_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_redemptions');
    }
};
