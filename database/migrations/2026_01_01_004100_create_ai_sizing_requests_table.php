<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_sizing_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('input_type'); // questionnaire | photo
            $table->json('input_payload_json')->nullable();
            $table->unsignedBigInteger('photo_media_id')->nullable();
            $table->string('status')->default('pending'); // pending | completed | failed
            $table->json('recommended_measurement_json')->nullable();
            $table->foreignUlid('consumed_by_user_measurement_id')->nullable()->constrained('user_measurements')->nullOnDelete();
            $table->boolean('consent_given')->default(false);
            $table->timestamp('consent_given_at')->nullable();
            $table->timestamps();

            $table->foreign('photo_media_id')->references('id')->on('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_sizing_requests');
    }
};
