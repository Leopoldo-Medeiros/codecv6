<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Quiz questions for a type=quiz step (practice funnel F5). Shape:
     *   [{ id, question, options: [string], correct_index, explanation? }]
     * The correct_index/explanation stay server-side — grading happens in
     * PathStepController::submitQuiz, mirroring how challenges never send
     * their answer key to the client.
     */
    public function up(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            $table->json('quiz')->nullable()->after('challenge_slug');
        });
    }

    public function down(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            $table->dropColumn('quiz');
        });
    }
};
