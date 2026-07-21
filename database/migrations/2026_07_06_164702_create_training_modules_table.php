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

            // Code unique du module
            $table->string('code')->unique();

            // Intitulé
            $table->string('title');

            // Description
            $table->text('description')->nullable();

            // Durée du module (heures)
            $table->unsignedInteger('duration_hours');

            // Ordre dans la formation
            $table->unsignedInteger('position');

            // Module actif
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->softDeletes();

            // Une position unique par formation
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
        Schema::dropIfExists('training_modules');
    }
};