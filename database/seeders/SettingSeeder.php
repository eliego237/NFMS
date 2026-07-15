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
                'key' => 'school_slogan',
                'value' => 'Institut de Beauté & Centre de Formation Professionnelle',
                'description' => 'Slogan',
            ],

            [
                'key' => 'school_phone',
                'value' => '+237 676266865',
                'description' => 'Téléphone principal',
            ],

            [
                'key' => 'school_phone2',
                'value' => '+237 655361946',
                'description' => 'Téléphone secondaire',
            ],

            [
                'key' => 'school_email',
                'value' => 'monkammarche@gmail.com',
                'description' => 'Adresse email',
            ],

            [
                'key' => 'school_website',
                'value' => 'www.newfashioncm.com',
                'description' => 'Site web',
            ],

            [
                'key' => 'school_address',
                'value' => 'Douala - Ndogbong Face Socaver',
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
                'key' => 'academic_year',
                'value' => '2026-2027',
                'description' => 'Année académique',
            ],

            [
                'key' => 'registration_fee',
                'value' => '16500',
                'description' => 'Frais d\'inscription',
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
                'key' => 'student_prefix',
                'value' => 'STU',
                'description' => 'Préfixe des étudiants',
            ],

            [
                'key' => 'cash_prefix',
                'value' => 'CASH',
                'description' => 'Préfixe des opérations de caisse',
            ],

            [
                'key' => 'director_name',
                'value' => '',
                'description' => 'Nom du directeur',
            ],

            [
                'key' => 'cashier_name',
                'value' => '',
                'description' => 'Nom du caissier',
            ],

            [
                'key' => 'logo',
                'value' => 'images/logo.png',
                'description' => 'Logo de l\'établissement',
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