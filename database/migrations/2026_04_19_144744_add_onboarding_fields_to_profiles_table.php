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
            $table->string('level')->nullable()->after('goal');
            $table->json('stack')->nullable()->after('level');
            $table->string('product_interest')->nullable()->after('stack');
            $table->unsignedTinyInteger('availability_hours')->nullable()->after('product_interest');
            $table->string('timeline')->nullable()->after('availability_hours');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['level', 'stack', 'product_interest', 'availability_hours', 'timeline']);
        });
    }
};
