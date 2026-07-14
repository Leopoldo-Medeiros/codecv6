<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Certification seals (observability track). A path may confer a badge on
     * full completion (`paths.badge_key`), and badges carry a `category` so
     * the public profile can render certifications as a prominent seal,
     * distinct from achievement badges (streaks, first challenge, etc.).
     */
    public function up(): void
    {
        Schema::table('paths', function (Blueprint $table) {
            $table->string('badge_key')->nullable()->after('description');
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->string('category')->default('achievement')->after('key');
        });
    }

    public function down(): void
    {
        Schema::table('paths', function (Blueprint $table) {
            $table->dropColumn('badge_key');
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
