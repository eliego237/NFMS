<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;

class StudentService
{
    /**
     * Créer un étudiant.
     */
    public static function store(array $data): Student
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Génération automatique du matricule
            |--------------------------------------------------------------------------
            | Le matricule n'est jamais réutilisé, même si un étudiant est supprimé.
            | Exemple :
            | NF202600001
            | NF202600002
            | NF202600003
            |--------------------------------------------------------------------------
            */

            $year = now()->year;

            $lastMatricule = Student::withTrashed()
                ->where('matricule', 'like', "NF{$year}%")
                ->lockForUpdate()
                ->max('matricule');

            if ($lastMatricule) {

                $nextNumber = ((int) substr($lastMatricule, -5)) + 1;

            } else {

                $nextNumber = 1;

            }

            $matricule = sprintf(
                'NF%s%05d',
                $year,
                $nextNumber
            );

            /*
            |--------------------------------------------------------------------------
            | Création de l'étudiant
            |--------------------------------------------------------------------------
            */

            $student = Student::create([

                'matricule'         => $matricule,

                'first_name'        => $data['first_name'],

                'last_name'         => $data['last_name'],

                'gender'            => $data['gender'],

                'birth_date'        => $data['birth_date'],

                'phone'             => $data['phone'] ?? null,

                'email'             => $data['email'] ?? null,

                'address'           => $data['address'] ?? null,

                'emergency_contact' => $data['emergency_contact'] ?? null,

                'photo'             => $data['photo'] ?? null,

                'status'            => $data['status'] ?? true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'students',

                event: 'created',

                subject: $student,

                properties: [

                    'matricule' => $student->matricule,

                    'nom' => $student->first_name . ' ' . $student->last_name,

                    'telephone' => $student->phone,

                    'email' => $student->email,

                ]

            );

            return $student->fresh();

        });
    }

    /**
     * Modifier un étudiant.
     */
    public static function update(
        Student $student,
        array $data
    ): Student {

        return DB::transaction(function () use (
            $student,
            $data
        ) {

            $student->update([

                'first_name'        => $data['first_name'],

                'last_name'         => $data['last_name'],

                'gender'            => $data['gender'],

                'birth_date'        => $data['birth_date'],

                'phone'             => $data['phone'] ?? null,

                'email'             => $data['email'] ?? null,

                'address'           => $data['address'] ?? null,

                'emergency_contact' => $data['emergency_contact'] ?? null,

                'photo'             => $data['photo'] ?? null,

                'status'            => $data['status'] ?? true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'students',

                event: 'updated',

                subject: $student,

                properties: [

                    'matricule' => $student->matricule,

                    'nom' => $student->first_name . ' ' . $student->last_name,

                ]

            );

            return $student->fresh();

        });
    }

    /**
     * Supprimer un étudiant.
     */
    public static function delete(Student $student): void
    {
        DB::transaction(function () use ($student) {

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'students',

                event: 'deleted',

                subject: $student,

                properties: [

                    'matricule' => $student->matricule,

                    'nom' => $student->first_name . ' ' . $student->last_name,

                ]

            );

            $student->delete();

        });
    }
}