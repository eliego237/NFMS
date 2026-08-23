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
        Schema::create('cash_transactions', function (Blueprint $table) {

            $table->id();

            // Numéro de transaction
            $table->string('transaction_number')->unique();

            // Entrée ou Sortie
            $table->enum('type', [
                'Entrée',
                'Sortie',
            ]);

            // Catégorie
            $table->string('category');

            // Montant
            $table->decimal('amount', 12, 2);

            // Moyen de paiement
            $table->foreignId('payment_method_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Paiement lié
            $table->foreignId('payment_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Dépense liée
            $table->foreignId('expense_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Description
            $table->string('description');

            // Date de l'opération
            $table->date('transaction_date');

            // Utilisateur ayant enregistré
            $table->foreignId('recorded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // Observations
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};