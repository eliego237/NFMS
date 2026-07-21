<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\Training;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    /**
     * Créer une inscription.
     */
    public static function store(array $data): Enrollment
    {
        return DB::transaction(function () use ($data) {

            $training = Training::findOrFail(
                $data['training_id']
            );

            /*
            |--------------------------------------------------------------------------
            | Paramètres système
            |--------------------------------------------------------------------------
            */

            $registrationFee = (float) Setting::getValue(
                'registration_fee',
                16500
            );

            $prefix = Setting::getValue(
                'enrollment_prefix',
                'INS'
            );

            $academicYear = $data['academic_year']
                ?? Setting::getValue(
                    'academic_year',
                    date('Y') . '-' . (date('Y') + 1)
                );

            /*
            |--------------------------------------------------------------------------
            | Calcul des montants
            |--------------------------------------------------------------------------
            */

            $trainingFee = $training->price;

            $discount = (float) ($data['discount'] ?? 0);

            $grossAmount = $registrationFee + $trainingFee;

            if ($discount > $grossAmount) {

                abort(
                    422,
                    'La remise ne peut pas être supérieure au montant total.'
                );

            }

            $totalAmount = $grossAmount - $discount;

            /*
            |--------------------------------------------------------------------------
            | Numéro d'inscription
            |--------------------------------------------------------------------------
            */

            $nextNumber = (Enrollment::max('id') ?? 0) + 1;

            $enrollmentNumber = sprintf(
                '%s%s%05d',
                $prefix,
                now()->year,
                $nextNumber
            );

            /*
            |--------------------------------------------------------------------------
            | Création
            |--------------------------------------------------------------------------
            */

            $enrollment = Enrollment::create([

                'enrollment_number' => $enrollmentNumber,

                'student_id' => $data['student_id'],

                'training_id' => $training->id,

                'registration_fee' => $registrationFee,

                'training_fee' => $trainingFee,

                'discount' => $discount,

                'total_amount' => $totalAmount,

                'amount_paid' => 0,

                'balance' => $totalAmount,

                'status' => 'pending',

                'academic_year' => $academicYear,

                'enrolled_at' => $data['enrolled_at'],

                'created_by' => Auth::id(),

                'notes' => $data['notes'] ?? null,

            ]);

            ActivityLogService::log(

                module: 'enrollments',

                event: 'created',

                subject: $enrollment,

                properties: [

                    'numero_inscription' => $enrollment->enrollment_number,

                    'etudiant' => $enrollment->student->full_name,

                    'formation' => $training->title,

                    'annee_academique' => $academicYear,

                    'montant_total' => $totalAmount,

                ]

            );

            return $enrollment
                ->fresh()
                ->load([
                    'student',
                    'training',
                    'creator',
                ]);

        });
    }

    /**
     * Modifier une inscription.
     */
    public static function update(
        Enrollment $enrollment,
        array $data
    ): Enrollment {

        return DB::transaction(function () use ($enrollment, $data) {

            $training = Training::findOrFail(
                $data['training_id']
            );

            $registrationFee = $enrollment->registration_fee;

            $trainingFee = $training->price;

            $discount = (float) ($data['discount'] ?? 0);

            $grossAmount = $registrationFee + $trainingFee;

            if ($discount > $grossAmount) {

                abort(
                    422,
                    'La remise ne peut pas être supérieure au montant total.'
                );

            }

            $totalAmount = $grossAmount - $discount;

            $enrollment->update([

                'student_id' => $data['student_id'],

                'training_id' => $training->id,

                'training_fee' => $trainingFee,

                'discount' => $discount,

                'total_amount' => $totalAmount,

                'academic_year' => $data['academic_year']
                    ?? Setting::getValue(
                        'academic_year',
                        date('Y') . '-' . (date('Y') + 1)
                    ),

                'enrolled_at' => $data['enrolled_at'],

                'notes' => $data['notes'] ?? null,

            ]);

            $enrollment->refresh();

            $enrollment->refreshBalance();

            ActivityLogService::log(

                module: 'enrollments',

                event: 'updated',

                subject: $enrollment,

                properties: [

                    'numero_inscription' => $enrollment->enrollment_number,

                    'etudiant' => $enrollment->student->full_name,

                    'formation' => $enrollment->training->title,

                    'statut' => $enrollment->status,

                    'montant_total' => $enrollment->total_amount,

                ]

            );

            return $enrollment
                ->fresh()
                ->load([
                    'student',
                    'training',
                    'payments',
                    'creator',
                ]);

        });

    }

    /**
     * Supprimer une inscription.
     */
    public static function delete(
        Enrollment $enrollment
    ): void {

        DB::transaction(function () use ($enrollment) {

            ActivityLogService::log(

                module: 'enrollments',

                event: 'deleted',

                subject: $enrollment,

                properties: [

                    'numero_inscription' => $enrollment->enrollment_number,

                    'etudiant' => $enrollment->student->full_name,

                    'formation' => $enrollment->training->title,

                ]

            );

            $enrollment->delete();

        });

    }
}