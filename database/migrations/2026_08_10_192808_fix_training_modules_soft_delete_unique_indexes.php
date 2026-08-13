<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Supprimer l'ancien index UNIQUE
        |--------------------------------------------------------------------------
        |
        | L'ancien index bloquait la création d'un nouveau module
        | lorsqu'un ancien module avait été supprimé avec SoftDeletes.
        |
        */

        DB::statement(
            'DROP INDEX IF EXISTS training_modules_training_id_position_unique'
        );

        /*
        |--------------------------------------------------------------------------
        | Nouvel index UNIQUE partiel
        |--------------------------------------------------------------------------
        |
        | Une seule position peut être utilisée par formation
        | parmi les modules actifs (deleted_at IS NULL).
        |
        */

        DB::statement(
            'CREATE UNIQUE INDEX training_modules_training_position_active_unique
             ON training_modules (training_id, position)
             WHERE deleted_at IS NULL'
        );

        /*
        |--------------------------------------------------------------------------
        | Code unique compatible avec SoftDeletes
        |--------------------------------------------------------------------------
        |
        | Même logique pour le code :
        | un ancien module supprimé ne doit pas empêcher
        | la réutilisation de son code.
        |
        */

        DB::statement(
            'DROP INDEX IF EXISTS training_modules_code_unique'
        );

        DB::statement(
            'CREATE UNIQUE INDEX training_modules_code_active_unique
             ON training_modules (code)
             WHERE deleted_at IS NULL'
        );
    }

    public function down(): void
    {
        DB::statement(
            'DROP INDEX IF EXISTS training_modules_training_position_active_unique'
        );

        DB::statement(
            'DROP INDEX IF EXISTS training_modules_code_active_unique'
        );

        DB::statement(
            'CREATE UNIQUE INDEX training_modules_training_id_position_unique
             ON training_modules (training_id, position)'
        );

        DB::statement(
            'CREATE UNIQUE INDEX training_modules_code_unique
             ON training_modules (code)'
        );
    }
};