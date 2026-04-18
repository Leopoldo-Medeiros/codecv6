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
        Schema::create('user_step_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('path_step_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['not_started', 'in_progress', 'done'])->default('not_started');
            $table->timestamps();
            $table->unique(['user_id', 'path_step_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_step_progress');
    }
};
