<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_order_details', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('clothing_type_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('fabric_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('color_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('design_id')->nullable()->constrained()->nullOnDelete(); // set if customer chose an existing design
            $table->string('measurement_source')->default('saved_profile'); // saved_profile | manual_entry | ai_assistant
            $table->foreignUlid('user_measurement_id')->nullable()->constrained()->nullOnDelete();
            $table->text('special_instructions')->nullable();
            $table->string('status')->default('awaiting_review'); // awaiting_review | measurements_confirmed | in_production | ready
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_order_details');
    }
};
