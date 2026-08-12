<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Every inbound webhook is recorded here BEFORE any processing happens,
        // keyed uniquely on (gateway, gateway_reference/event id) so a retried
        // webhook can never double-process.
        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('gateway');
            $table->string('event_type');
            $table->string('gateway_reference');
            $table->json('payload_json');
            $table->boolean('signature_verified')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['gateway', 'gateway_reference', 'event_type'], 'payment_webhook_events_idempotency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');
    }
};
