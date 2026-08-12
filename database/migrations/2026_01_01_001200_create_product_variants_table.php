<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('fabric_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('color_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('size_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->unique();
            $table->unsignedInteger('price_override_kobo')->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
