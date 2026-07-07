<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Seed the application's settings.
     */
    public function run(): void
    {
        $settings = [

            [
                'key' => 'school_name',
                'value' => 'NEW FASHION',
                'description' => 'Nom de l\'établissement',
            ],

            [
                'key' => 'school_phone',
                'value' => '',
                'description' => 'Téléphone principal',
            ],

            [
                'key' => 'school_email',
                'value' => '',
                'description' => 'Adresse email',
            ],

            [
                'key' => 'school_address',
                'value' => 'Douala, Cameroun',
                'description' => 'Adresse',
            ],

            [
                'key' => 'country',
                'value' => 'Cameroun',
                'description' => 'Pays',
            ],

            [
                'key' => 'city',
                'value' => 'Douala',
                'description' => 'Ville',
            ],

            [
                'key' => 'currency',
                'value' => 'FCFA',
                'description' => 'Devise',
            ],

            [
                'key' => 'registration_fee',
                'value' => '16500',
                'description' => 'Frais d\'inscription',
            ],

            [
                'key' => 'academic_year',
                'value' => '2026-2027',
                'description' => 'Année académique',
            ],

            [
                'key' => 'receipt_prefix',
                'value' => 'REC',
                'description' => 'Préfixe des reçus',
            ],

            [
                'key' => 'payment_prefix',
                'value' => 'PAY',
                'description' => 'Préfixe des paiements',
            ],

            [
                'key' => 'enrollment_prefix',
                'value' => 'INS',
                'description' => 'Préfixe des inscriptions',
            ],

            [
                'key' => 'cash_prefix',
                'value' => 'CASH',
                'description' => 'Préfixe des opérations de caisse',
            ],

        ];

        foreach ($settings as $setting) {

            Setting::updateOrCreate(

                ['key' => $setting['key']],

                $setting

            );
        }
    }
}