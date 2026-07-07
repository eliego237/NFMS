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
        Schema::create('students', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Matricule
            |--------------------------------------------------------------------------
            */

            $table->string('matricule')->unique();

            /*
            |--------------------------------------------------------------------------
            | Informations personnelles
            |--------------------------------------------------------------------------
            */

            $table->string('first_name');

            $table->string('last_name');

            $table->enum('gender', ['M', 'F']);

            $table->date('birth_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Coordonnées
            |--------------------------------------------------------------------------
            */

            $table->string('phone');

            $table->string('email')->nullable()->unique();

            $table->string('address')->nullable();

            $table->string('emergency_contact')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Photo
            |--------------------------------------------------------------------------
            */

            $table->string('photo')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            */

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};