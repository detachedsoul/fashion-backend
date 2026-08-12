<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_charts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clothing_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('measurement_json'); // reference chart values per size for this garment type
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_charts');
    }
};
