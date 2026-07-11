<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Idempotency ledger for the Stripe webhook: Stripe redelivers events
     * that weren't acknowledged fast enough, so the event id is claimed
     * here (insertOrIgnore) before its handler runs.
     */
    public function up(): void
    {
        Schema::create('processed_stripe_events', function (Blueprint $table) {
            $table->string('event_id')->primary();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processed_stripe_events');
    }
};
