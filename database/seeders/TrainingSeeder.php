<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Training;

class TrainingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Training::truncate();

        $trainings = [

            [
                'code' => 'COIF001',
                'title' => 'Coiffure mixte',
                'category' => 'Coiffure mixte',
                'description' => 'Formation professionnelle en coiffure mixte.',
                'price' => 250000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'COS001',
                'title' => 'Cosmétique',
                'category' => 'Cosmétique',
                'description' => 'Fabrication des produits cosmétiques.',
                'price' => 250000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'ART001',
                'title' => 'Art & Décoration',
                'category' => 'Art & Décoration',
                'description' => 'Formation en décoration et art floral.',
                'price' => 100000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'EST001',
                'title' => 'Esthétique',
                'category' => 'Esthétique',
                'description' => 'Formation complète en esthétique.',
                'price' => 250000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'RES001',
                'title' => 'Restauration',
                'category' => 'Restauration',
                'description' => 'Formation professionnelle en restauration.',
                'price' => 300000,
                'duration_months' => 6,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'PAT001',
                'title' => 'Pâtisserie',
                'category' => 'Pâtisserie',
                'description' => 'Formation en pâtisserie professionnelle.',
                'price' => 150000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'ESTM001',
                'title' => 'Esthétique moderne',
                'category' => 'Esthétique moderne',
                'description' => 'Formation aux techniques esthétiques modernes.',
                'price' => 150000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'STY001',
                'title' => 'Stylisme & Modélisme',
                'category' => 'Stylisme & Modélisme',
                'description' => 'Formation en stylisme et modélisme.',
                'price' => 350000,
                'duration_months' => 12,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'INFO001',
                'title' => 'Informatique',
                'category' => 'Informatique',
                'description' => 'Formation en informatique professionnelle.',
                'price' => 100000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'LANG001',
                'title' => 'Cours de langue',
                'category' => 'Cours de langue',
                'description' => 'Formation en langues étrangères.',
                'price' => 75000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'AUTO001',
                'title' => 'Auto-école',
                'category' => 'Auto-école',
                'description' => 'Formation à la conduite automobile.',
                'price' => 100000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

            [
                'code' => 'PARA001',
                'title' => 'Paramédical',
                'category' => 'Paramédical',
                'description' => 'Formation aux métiers paramédicaux.',
                'price' => 500000,
                'duration_months' => 6,
                'certificate' => 'Certificat de Formation Professionnelle',
                'is_active' => true,
            ],

        ];

        foreach ($trainings as $training) {
            Training::create($training);
        }
    }
}