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
        Schema::create('enrollments', function (Blueprint $table) {

            $table->id();

            // Numéro d'inscription
            $table->string('enrollment_number')->unique();

            // Étudiant
            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            // Formation
            $table->foreignId('training_id')
                ->constrained()
                ->cascadeOnDelete();

            // Frais figés au moment de l'inscription
            $table->decimal('registration_fee', 12, 2);

            $table->decimal('training_fee', 12, 2);

            // Réduction éventuelle
            $table->decimal('discount', 12, 2)->default(0);

            // Total à payer
            $table->decimal('total_amount', 12, 2);

            // Déjà payé
            $table->decimal('amount_paid', 12, 2)->default(0);

            // Solde restant
            $table->decimal('balance', 12, 2);

            // Statut
            $table->enum('status', [
                'pending',
                'partial',
                'paid',
                'cancelled'
            ])->default('pending');

            // Date d'inscription
            $table->date('enrolled_at');

            // Utilisateur ayant créé l'inscription
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Observations
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->string('academic_year')->default('2026-2027');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};