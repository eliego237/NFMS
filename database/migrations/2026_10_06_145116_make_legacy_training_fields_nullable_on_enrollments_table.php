<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('training_id')
                ->nullable()
                ->change();

            $table->decimal('training_fee', 12, 2)
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('training_id')
                ->nullable(false)
                ->change();

            $table->decimal('training_fee', 12, 2)
                ->nullable(false)
                ->change();
        });
    }
};