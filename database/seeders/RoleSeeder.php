<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed des rôles.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Création des rôles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'Administrateur',
        ]);

        $director = Role::firstOrCreate([
            'name' => 'Directeur',
        ]);

        $cashier = Role::firstOrCreate([
            'name' => 'Caissier',
        ]);

        $secretary = Role::firstOrCreate([
            'name' => 'Secrétaire',
        ]);

        $teacher = Role::firstOrCreate([
            'name' => 'Enseignant',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Administrateur
        |--------------------------------------------------------------------------
        */

        $admin->syncPermissions(
            Permission::all()
        );

        /*
        |--------------------------------------------------------------------------
        | Directeur
        |--------------------------------------------------------------------------
        */

        $director->syncPermissions([

            'dashboard.view',

            'students.view',

            'trainings.view',

            'enrollments.view',

            'payments.view',

            'expenses.view',

            'cash.view',

            'reports.view',

            'receipts.print',

            'settings.view',
            
            'settings.update',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Caissier
        |--------------------------------------------------------------------------
        */

        $cashier->syncPermissions([

            'dashboard.view',

            'payments.view',
            'payments.create',

            'expenses.view',
            'expenses.create',

            'cash.view',

            'receipts.print',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Secrétaire
        |--------------------------------------------------------------------------
        */

        $secretary->syncPermissions([

            'dashboard.view',

            'students.view',
            'students.create',
            'students.update',

            'trainings.view',

            'enrollments.view',
            'enrollments.create',
            'enrollments.update',

            'payments.view',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Enseignant
        |--------------------------------------------------------------------------
        */

        $teacher->syncPermissions([

            'dashboard.view',

            'students.view',

            'trainings.view',

            'enrollments.view',

        ]);
    }
}