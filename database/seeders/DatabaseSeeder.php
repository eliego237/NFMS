<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            /*
            |--------------------------------------------------------------------------
            | Paramètres système
            |--------------------------------------------------------------------------
            */

            SettingSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | Gestion des accès
            |--------------------------------------------------------------------------
            */

            PermissionSeeder::class,

            RoleSeeder::class,

            AdminUserSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | Données de référence
            |--------------------------------------------------------------------------
            */

            PaymentMethodSeeder::class,

            TrainingSeeder::class,

            StudentSeeder::class,

        ]);
    }
}