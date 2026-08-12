<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('order_id')->constrained()->cascadeOnDelete();
            $table->string('orderable_type'); // product_variant | custom
            $table->ulid('orderable_id')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('unit_price_kobo');
            $table->unsignedInteger('line_total_kobo');
            $table->json('meta_json')->nullable(); // snapshot of options chosen at time of order
            $table->timestamps();

            $table->index(['orderable_type', 'orderable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
