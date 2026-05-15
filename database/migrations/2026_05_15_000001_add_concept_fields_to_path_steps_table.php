<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            // Headline + body for the "reading" portion of a step.
            $table->string('tldr', 500)->nullable()->after('description');
            $table->longText('concept_content')->nullable()->after('tldr');

            // Sidebar metadata.
            $table->unsignedSmallInteger('estimated_minutes')->nullable()->after('concept_content');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->nullable()->after('estimated_minutes');
            $table->json('prerequisites')->nullable()->after('difficulty'); // [{ id, title }]
            $table->json('concepts')->nullable()->after('prerequisites');   // ['variables', 'types', ...]

            // Scratch playground (separate from `challenge_slug`, which is a graded exercise).
            $table->boolean('has_playground')->default(false)->after('concepts');
            $table->text('playground_starter_code')->nullable()->after('has_playground');
        });
    }

    public function down(): void
    {
        Schema::table('path_steps', function (Blueprint $table) {
            $table->dropColumn([
                'tldr',
                'concept_content',
                'estimated_minutes',
                'difficulty',
                'prerequisites',
                'concepts',
                'has_playground',
                'playground_starter_code',
            ]);
        });
    }
};
