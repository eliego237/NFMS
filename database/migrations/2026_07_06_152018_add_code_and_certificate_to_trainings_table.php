<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {

            $table->string('code')
                  ->nullable()
                  ->unique()
                  ->after('id');

            $table->string('certificate')
                  ->nullable()
                  ->after('duration_months');

        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {

            $table->dropColumn([
                'code',
                'certificate',
            ]);

        });
    }
};