<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per user: cumulative XP and daily-practice streak state.
     * Updated by GamificationService whenever a challenge or path step
     * is freshly completed (never on re-completion).
     */
    public function up(): void
    {
        Schema::create('user_progress_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('xp_points')->default(0);
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('longest_streak')->default(0);
            $table->timestamp('last_practiced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress_stats');
    }
};
