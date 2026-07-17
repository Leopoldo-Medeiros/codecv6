<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Waitlist / "coming soon" demand-sensing. One row per (email, topic): lets us
 * measure which future track (observability, AI for devs, AI for support) pulls
 * hardest before committing to build it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlist_entries', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('topic')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source')->nullable();
            $table->timestamps();

            // One signup per email per topic — re-submitting is a no-op, not a dupe.
            $table->unique(['email', 'topic']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlist_entries');
    }
};
