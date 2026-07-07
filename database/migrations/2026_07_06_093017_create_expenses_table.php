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
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Identification
            |--------------------------------------------------------------------------
            */

            $table->string('expense_number')->unique();

            /*
            |--------------------------------------------------------------------------
            | Informations de la dépense
            |--------------------------------------------------------------------------
            */

            $table->string('category');

            $table->string('title');

            $table->text('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Informations financières
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 12, 2);

            $table->foreignId('payment_method_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informations administratives
            |--------------------------------------------------------------------------
            */

            $table->date('expense_date');

            $table->string('reference')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status')
                ->default('validated');

            $table->text('notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('expense_date');
            $table->index('category');
            $table->index('status');

            /*
            |--------------------------------------------------------------------------
            | Horodatage
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};