<?php

use App\Enums\ChallengeDifficulty;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->enum('difficulty', ChallengeDifficulty::values())->default(ChallengeDifficulty::Beginner->value);
            $table->longText('boilerplate_code');
            $table->longText('tests_code');
            $table->boolean('is_premium')->default(false);
            $table->unsignedInteger('price_eur')->nullable()->comment('Price in euro cents (minor units)');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
