<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Iterations (Exercism-style): every sandbox run of a challenge is recorded as
 * a submission so the learner can revisit, compare, and restore earlier
 * attempts. Capped per (user, challenge) in the controller — see
 * ChallengeController::run — so the table can't grow unbounded.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->mediumText('code');
            $table->boolean('passed');
            $table->unsignedSmallInteger('failed_count')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'challenge_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_submissions');
    }
};
