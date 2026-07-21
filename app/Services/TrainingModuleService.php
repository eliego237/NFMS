<?php

namespace App\Services;

use App\Models\TrainingModule;
use Illuminate\Support\Facades\DB;

class TrainingModuleService
{
    /**
     * Créer un module.
     */
    public static function store(array $data): TrainingModule
    {
        return DB::transaction(function () use ($data) {

            $module = TrainingModule::create([

                'training_id' => $data['training_id'],

                'code' => $data['code'],

                'title' => $data['title'],

                'description' => $data['description'] ?? null,

                'duration_hours' => $data['duration_hours'],

                'position' => $data['position'],

                'is_active' => $data['is_active'] ?? true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'training_modules',

                event: 'created',

                subject: $module,

                properties: [

                    'formation' => $module->training->title,

                    'code' => $module->code,

                    'titre' => $module->title,

                    'position' => $module->position,

                    'duree' => $module->duration_hours,

                ]

            );

            return $module->fresh()->load('training');

        });
    }

    /**
     * Modifier un module.
     */
    public static function update(
        TrainingModule $module,
        array $data
    ): TrainingModule {

        return DB::transaction(function () use (
            $module,
            $data
        ) {

            $module->update([

                'training_id' => $data['training_id'],

                'code' => $data['code'],

                'title' => $data['title'],

                'description' => $data['description'] ?? null,

                'duration_hours' => $data['duration_hours'],

                'position' => $data['position'],

                'is_active' => $data['is_active'] ?? true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'training_modules',

                event: 'updated',

                subject: $module,

                properties: [

                    'formation' => $module->training->title,

                    'code' => $module->code,

                    'titre' => $module->title,

                    'position' => $module->position,

                    'duree' => $module->duration_hours,

                ]

            );

            return $module->fresh()->load('training');

        });
    }

    /**
     * Supprimer un module.
     */
    public static function delete(
        TrainingModule $module
    ): void {

        DB::transaction(function () use ($module) {

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'training_modules',

                event: 'deleted',

                subject: $module,

                properties: [

                    'formation' => $module->training->title,

                    'code' => $module->code,

                    'titre' => $module->title,

                    'position' => $module->position,

                    'duree' => $module->duration_hours,

                ]

            );

            $module->delete();

        });

    }
}