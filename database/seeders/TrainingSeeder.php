<?php

namespace Database\Seeders;

use App\Models\Training;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    /**
     * Seed des formations officielles NEW FASHION.
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
                'status' => 'active',
            ],

            [
                'code' => 'COS001',
                'title' => 'Cosmétique',
                'category' => 'Cosmétique',
                'description' => 'Fabrication des produits cosmétiques.',
                'price' => 250000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'ART001',
                'title' => 'Art & Décoration',
                'category' => 'Art & Décoration',
                'description' => 'Formation en décoration et art floral.',
                'price' => 100000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'EST001',
                'title' => 'Esthétique',
                'category' => 'Esthétique',
                'description' => 'Formation complète en esthétique.',
                'price' => 250000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'RES001',
                'title' => 'Restauration',
                'category' => 'Restauration',
                'description' => 'Formation professionnelle en restauration.',
                'price' => 300000,
                'duration_months' => 6,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'PAT001',
                'title' => 'Pâtisserie',
                'category' => 'Pâtisserie',
                'description' => 'Formation professionnelle en pâtisserie.',
                'price' => 150000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'ESTM001',
                'title' => 'Esthétique moderne',
                'category' => 'Esthétique moderne',
                'description' => 'Formation aux techniques esthétiques modernes.',
                'price' => 150000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'STY001',
                'title' => 'Stylisme & Modélisme',
                'category' => 'Stylisme & Modélisme',
                'description' => 'Formation professionnelle en stylisme et modélisme.',
                'price' => 350000,
                'duration_months' => 12,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'INFO001',
                'title' => 'Informatique',
                'category' => 'Informatique',
                'description' => 'Formation professionnelle en informatique.',
                'price' => 100000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'LANG001',
                'title' => 'Cours de langue',
                'category' => 'Cours de langue',
                'description' => 'Formation en langues étrangères.',
                'price' => 75000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'AUTO001',
                'title' => 'Auto-école',
                'category' => 'Auto-école',
                'description' => 'Formation à la conduite automobile.',
                'price' => 100000,
                'duration_months' => 3,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

            [
                'code' => 'PARA001',
                'title' => 'Paramédical',
                'category' => 'Paramédical',
                'description' => 'Formation aux métiers paramédicaux.',
                'price' => 500000,
                'duration_months' => 6,
                'certificate' => 'Certificat de Formation Professionnelle',
                'status' => 'active',
            ],

        ];

        foreach ($trainings as $training) {

            Training::create($training);

        }
    }
}