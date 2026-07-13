<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Observability track, Phase A — the "incident reader" step type.
     *
     * Two changes:
     *  1. Move `type` off a DB enum to a plain string. Adding a new step type
     *     (incident, and future ones) no longer needs an enum-altering
     *     migration, which is painful across MySQL and sqlite. Allowed values
     *     are enforced in the API layer (PathStepController::stepRules).
     *  2. Add `evidence` — curated telemetry for a type=incident step. Shape:
     *       { scenario, trace: { root, spans[] }, metrics[], logs[] }
     *     Purely display data (rendered client-side). No answer key lives here:
     *     the graded diagnostic questions reuse the existing `quiz` column, so
     *     grading stays in submitQuiz with the key stripped by the Resource.
     */
    public function up(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            $table->string('type')->default('reading')->change();
            $table->json('evidence')->nullable()->after('quiz');
        });
    }

    public function down(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            $table->dropColumn('evidence');
            // Reverting to the enum fails if any row already holds a value
            // outside the original set (e.g. 'incident') — clear those first.
            $table->enum('type', ['reading', 'lab', 'challenge', 'quiz'])->default('reading')->change();
        });
    }
};
