<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE training_modules DROP CONSTRAINT IF EXISTS training_modules_training_id_position_unique'
            );
        } else {
            DB::statement(
                'DROP INDEX IF EXISTS training_modules_training_id_position_unique'
            );
        }

        DB::statement(
            'CREATE UNIQUE INDEX training_modules_training_position_active_unique
             ON training_modules (training_id, position)
             WHERE deleted_at IS NULL'
        );

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE training_modules DROP CONSTRAINT IF EXISTS training_modules_code_unique'
            );
        } else {
            DB::statement(
                'DROP INDEX IF EXISTS training_modules_code_unique'
            );
        }

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
