<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_measurements', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Default');
            $table->unsignedSmallInteger('chest')->nullable();
            $table->unsignedSmallInteger('waist')->nullable();
            $table->unsignedSmallInteger('hips')->nullable();
            $table->unsignedSmallInteger('shoulder')->nullable();
            $table->unsignedSmallInteger('sleeve_length')->nullable();
            $table->unsignedSmallInteger('inseam')->nullable();
            $table->unsignedSmallInteger('neck')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('weight')->nullable();
            $table->string('unit', 2)->default('cm'); // cm | in
            $table->string('source')->default('manual'); // manual | ai_assistant
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_measurements');
    }
};
