<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marks challenges playable anonymously, with no account, from the
     * public teaser surface (practice funnel stage 1 / F2). Curation is a
     * data decision (which rows get this flag), not application logic.
     */
    public function up(): void
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->boolean('is_teaser')->default(false)->after('is_premium');
        });
    }

    public function down(): void
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->dropColumn('is_teaser');
        });
    }
};
