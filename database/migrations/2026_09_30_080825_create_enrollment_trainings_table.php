<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollment_trainings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('enrollment_id')
                ->constrained('enrollments')
                ->cascadeOnDelete();

            $table->foreignId('training_id')
                ->constrained('trainings')
                ->restrictOnDelete();

            /*
             * Prix de la formation au moment de l'inscription.
             */
            $table->decimal('training_fee', 12, 2);

            /*
             * Durée de la formation au moment de l'inscription.
             */
            $table->unsignedInteger('duration_months')
                ->default(0);

            $table->timestamps();

            /*
             * Une formation ne peut apparaître
             * qu'une seule fois dans une inscription.
             */
            $table->unique([
                'enrollment_id',
                'training_id',
            ]);
        });

        /*
         * -------------------------------------------------------------
         * MIGRATION DES ANCIENNES INSCRIPTIONS
         * -------------------------------------------------------------
         *
         * Chaque ancien enrollment possède déjà un training_id.
         *
         * On le copie automatiquement dans la nouvelle table pivot
         * afin de ne perdre aucune formation existante.
         */
        $enrollments = DB::table('enrollments')
            ->whereNotNull('training_id')
            ->get([
                'id',
                'training_id',
                'training_fee',
                'created_at',
                'updated_at',
            ]);

        foreach ($enrollments as $enrollment) {

            $training = DB::table('trainings')
                ->where('id', $enrollment->training_id)
                ->first([
                    'id',
                    'price',
                    'duration_months',
                ]);

            if (!$training) {
                continue;
            }

            DB::table('enrollment_trainings')->insert([
                'enrollment_id' => $enrollment->id,
                'training_id' => $training->id,

                'training_fee' =>
                    $enrollment->training_fee
                    ?? $training->price,

                'duration_months' =>
                    $training->duration_months ?? 0,

                'created_at' =>
                    $enrollment->created_at ?? now(),

                'updated_at' =>
                    $enrollment->updated_at ?? now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_trainings');
    }
};