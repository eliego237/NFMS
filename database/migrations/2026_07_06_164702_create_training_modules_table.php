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
        Schema::create('training_modules', function (Blueprint $table) {

            $table->id();

            $table->foreignId('training_id')
                ->constrained()
                ->cascadeOnDelete();

            // Code du module
            $table->string('code')->unique();

            // Nom du module
            $table->string('title');

            // Description
            $table->text('description')->nullable();

            // Ordre d'affichage
            $table->unsignedInteger('position')->default(1);

            // Actif ?
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_modules');
    }
};