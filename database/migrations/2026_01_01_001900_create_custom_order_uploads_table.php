<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_order_uploads', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('custom_order_detail_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // design_image | sketch | inspiration_photo | pdf
            // References the polymorphic `media` table (Spatie Media Library) -
            // see 2026_01_01_003800_create_media_table.php
            $table->unsignedBigInteger('media_id');
            $table->timestamps();

            $table->foreign('media_id')->references('id')->on('media')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_order_uploads');
    }
};
