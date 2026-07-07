<?php

namespace Database\Seeders;

use App\Models\Training;
use App\Models\TrainingModule;
use Illuminate\Database\Seeder;

class TrainingModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider la table
        TrainingModule::truncate();

        /*
        |--------------------------------------------------------------------------
        | Les modules des formations seront ajoutés ici
        |--------------------------------------------------------------------------
        |
        | Exemple :
        |
        | $this->createModules('INFO001', [
        |     'Infographie',
        |     'Montage Audio Visuel',
        |     'Secrétariat Bureautique',
        |     'Marketing Digital',
        | ]);
        |
        */
    }

    /**
     * Créer automatiquement les modules d'une formation.
     */
    private function createModules(string $trainingCode, array $modules): void
    {
        $training = Training::where('code', $trainingCode)->first();

        if (!$training) {
            return;
        }

        foreach ($modules as $index => $module) {

            TrainingModule::create([

                'training_id' => $training->id,

                'code' => $trainingCode . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),

                'title' => $module,

                'description' => null,

                'duration_hours' => 0,

                'position' => $index + 1,

                'is_active' => true,

            ]);
        }
    }
}