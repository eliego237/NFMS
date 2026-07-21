<?php

namespace App\Services;

use App\Models\Training;
use Illuminate\Support\Facades\DB;

class TrainingService
{
    /**
     * Créer une formation.
     */
    public static function store(array $data): Training
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Création
            |--------------------------------------------------------------------------
            */

            $training = Training::create([

                'code' => $data['code'],

                'title' => $data['title'],

                'category' => $data['category'],

                'description' => $data['description'] ?? null,

                'price' => $data['price'],

                'duration_months' => $data['duration_months'],

                'certificate' => $data['certificate'],

                'is_active' => $data['is_active'] ?? true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'trainings',

                event: 'created',

                subject: $training,

                properties: [

                    'code' => $training->code,

                    'titre' => $training->title,

                    'categorie' => $training->category,

                    'prix' => $training->price,

                    'duree' => $training->duration_months,

                    'certificat' => $training->certificate,

                ]

            );

            return $training->fresh()->load('modules');

        });
    }

    /**
     * Modifier une formation.
     */
    public static function update(
        Training $training,
        array $data
    ): Training {

        return DB::transaction(function () use (
            $training,
            $data
        ) {

            $training->update([

                'code' => $data['code'],

                'title' => $data['title'],

                'category' => $data['category'],

                'description' => $data['description'] ?? null,

                'price' => $data['price'],

                'duration_months' => $data['duration_months'],

                'certificate' => $data['certificate'],

                'is_active' => $data['is_active'] ?? true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'trainings',

                event: 'updated',

                subject: $training,

                properties: [

                    'code' => $training->code,

                    'titre' => $training->title,

                    'categorie' => $training->category,

                    'prix' => $training->price,

                    'duree' => $training->duration_months,

                    'certificat' => $training->certificate,

                ]

            );

            return $training->fresh()->load('modules');

        });
    }

    /**
     * Supprimer une formation.
     */
    public static function delete(
        Training $training
    ): void {

        DB::transaction(function () use ($training) {

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'trainings',

                event: 'deleted',

                subject: $training,

                properties: [

                    'code' => $training->code,

                    'titre' => $training->title,

                    'categorie' => $training->category,

                    'prix' => $training->price,

                    'duree' => $training->duration_months,

                    'certificat' => $training->certificate,

                ]

            );

            $training->delete();

        });
    }
}