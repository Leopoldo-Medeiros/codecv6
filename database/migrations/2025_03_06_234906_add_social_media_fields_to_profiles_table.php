<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('website')->nullable()->after('profession');
            $table->string('github')->nullable()->after('website');
            $table->string('linkedin')->nullable()->after('github');
            $table->string('instagram')->nullable()->after('linkedin');
            $table->string('facebook')->nullable()->after('instagram');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['website', 'github', 'linkedin', 'instagram', 'facebook']);
        });
    }
};
