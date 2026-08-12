<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clothing_types', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name'); // Senator, Agbada, Suit, Kaftan, Native Wear, Shirt, Trouser, Gown, Jacket, Skirt, Custom Design
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_custom_only')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clothing_types');
    }
};
