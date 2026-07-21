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
        Schema::table('training_modules', function (Blueprint $table) {

            $table->unique([
                'training_id',
                'position',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_modules', function (Blueprint $table) {

            $table->dropUnique([
                'training_id',
                'position',
            ]);

        });
    }
};