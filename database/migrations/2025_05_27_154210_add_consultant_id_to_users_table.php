<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if the column exists
            if (!Schema::hasColumn('users', 'consultant_id')) {
                // If column doesn't exist, create it with foreign key
                $table->foreignId('consultant_id')
                    ->nullable()
                    ->after('email')
                    ->constrained('users')
                    ->nullOnDelete();
            } else {
                // If column exists, check if foreign key exists
                $db = DB::getDatabaseName();
                $constraint = DB::selectOne("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.TABLE_CONSTRAINTS
                    WHERE TABLE_SCHEMA = '{$db}'
                    AND TABLE_NAME = 'users'
                    AND CONSTRAINT_NAME = 'users_consultant_id_foreign'
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                ");

                if (!$constraint) {
                    // Only add foreign key if it doesn't exist
                    $table->foreign('consultant_id')
                        ->references('id')
                        ->on('users')
                        ->nullOnDelete();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint if it exists
            $table->dropForeign(['consultant_id']);
        });
    }
};
