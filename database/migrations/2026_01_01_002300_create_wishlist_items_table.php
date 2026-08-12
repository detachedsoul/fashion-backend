<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('wishlist_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['wishlist_id', 'product_id', 'product_variant_id'], 'wishlist_items_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};
