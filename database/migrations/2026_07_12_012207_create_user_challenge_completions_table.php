<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * First-pass completion record for a challenge. Only the first
     * successful run per (user, challenge) is stored — this is what
     * makes challenge XP idempotent (re-running a solved challenge
     * doesn't farm more XP) and doubles as the practice-history source
     * for the public skill profile (funnel stage 5).
     */
    public function up(): void
    {
        Schema::create('user_challenge_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at');

            $table->unique(['user_id', 'challenge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_challenge_completions');
    }
};
