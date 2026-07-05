<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('training_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('registration_number')->unique();

            $table->date('registration_date');

            $table->string('status')->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};