<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            $table->string('challenge_slug')->nullable()->after('challenge_prompt');
        });
    }

    public function down(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            $table->dropColumn('challenge_slug');
        });
    }
};
