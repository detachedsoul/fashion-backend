<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('cart_id')->constrained()->cascadeOnDelete();
            // Polymorphic: points at either a product_variant (ready-to-wear)
            // or a custom_order_draft-style payload captured in meta_json.
            $table->string('orderable_type');
            $table->ulid('orderable_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('unit_price_kobo');
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['orderable_type', 'orderable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
