<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Public skill profile (practice funnel stage 5 / F3): opt-in,
     * off by default. public_slug is generated on first opt-in and kept
     * stable afterwards, so a shared link survives toggling visibility.
     */
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('facebook');
            $table->string('public_slug')->nullable()->unique()->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'public_slug']);
        });
    }
};
