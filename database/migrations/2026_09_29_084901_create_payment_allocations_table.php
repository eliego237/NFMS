<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_allocations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Paiement global
            |--------------------------------------------------------------------------
            */

            $table->foreignId('payment_id')
                ->constrained('payments')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Inscription concernée
            |--------------------------------------------------------------------------
            */

            $table->foreignId('enrollment_id')
                ->constrained('enrollments')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Montant affecté à cette inscription
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 12, 2);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Une inscription ne peut apparaître qu'une seule fois
            | dans un même paiement.
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'payment_id',
                'enrollment_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};