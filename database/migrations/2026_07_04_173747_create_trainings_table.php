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
        Schema::create('trainings', function (Blueprint $table) {

            $table->id();

            // Code unique de la formation
            $table->string('code')->unique();

            // Nom de la formation
            $table->string('title');

            // Catégorie
            $table->string('category');

            // Description
            $table->text('description')->nullable();

            // Prix de base
            $table->decimal('price', 12, 2);

            // Durée en mois
            $table->integer('duration_months');

            // Certificat délivré
            $table->string('certificate')->nullable();

            // Formation active ou non
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
        Schema::dropIfExists('trainings');
    }
};