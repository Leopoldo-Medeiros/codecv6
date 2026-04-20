<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            $table->enum('type', ['reading', 'lab', 'challenge', 'quiz'])->default('reading')->after('order');
            $table->string('lab_url')->nullable()->after('type');
            $table->json('instructions')->nullable()->after('lab_url'); // [{ id, text }]
            $table->text('challenge_prompt')->nullable()->after('instructions');
        });
    }

    public function down(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            $table->dropColumn(['type', 'lab_url', 'instructions', 'challenge_prompt']);
        });
    }
};
