<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5, validated in FormRequest
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('photo_media_id')->nullable(); // "upload photo wearing outfit"
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->foreign('photo_media_id')->references('id')->on('media')->nullOnDelete();
            $table->index(['user_id', 'order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
