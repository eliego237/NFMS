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
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            // Inscription concernée
            $table->foreignId('enrollment_id')
                ->constrained()
                ->cascadeOnDelete();

            // Numéro du reçu
            $table->string('receipt_number')->unique();

            // Montant payé
            $table->decimal('amount', 12, 2);

            // Moyen de paiement
            $table->foreignId('payment_method_id')
                ->constrained()
                ->cascadeOnDelete();

            // Date du paiement
            $table->date('payment_date');

            // Référence de transaction
            $table->string('reference')->nullable();

            // Observations
            $table->text('notes')->nullable();

            // Utilisateur ayant enregistré le paiement
            $table->foreignId('received_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};