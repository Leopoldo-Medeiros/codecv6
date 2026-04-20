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
        // path_steps — ordered listing per path
        Schema::table('path_steps', function (Blueprint $table) {
            $table->index(['path_id', 'order'], 'idx_path_steps_path_order');
        });

        // user_step_progress — lookup by user (unique already covers pair, but named index helps explain plans)
        Schema::table('user_step_progress', function (Blueprint $table) {
            $table->index('user_id', 'idx_usp_user');
        });

        // users — search by email (login) and fullname (admin search)
        Schema::table('users', function (Blueprint $table) {
            $existing = collect(Schema::getIndexes('users'))->pluck('name');
            if (! $existing->contains('idx_users_email')) {
                $table->index('email', 'idx_users_email');
            }
            $table->index('fullname', 'idx_users_fullname');
        });

        // courses — soft-delete filter is very common
        Schema::table('courses', function (Blueprint $table) {
            $table->index('deleted_at', 'idx_courses_deleted_at');
        });

        // paths — same
        Schema::table('paths', function (Blueprint $table) {
            $table->index('deleted_at', 'idx_paths_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('path_steps', fn (Blueprint $t) => $t->dropIndex('idx_path_steps_path_order'));
        Schema::table('user_step_progress', fn (Blueprint $t) => $t->dropIndex('idx_usp_user'));
        Schema::table('users', function (Blueprint $t) {
            $t->dropIndex('idx_users_email');
            $t->dropIndex('idx_users_fullname');
        });
        Schema::table('courses', fn (Blueprint $t) => $t->dropIndex('idx_courses_deleted_at'));
        Schema::table('paths', fn (Blueprint $t) => $t->dropIndex('idx_paths_deleted_at'));
    }
};
