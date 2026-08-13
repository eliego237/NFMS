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
        | Vérifier que l'étudiant n'est pas déjà inscrit à cette formation
        |--------------------------------------------------------------------------
        */

        $exists = Enrollment::where('student_id', $data['student_id'])
            ->where('training_id', $training->id)
            ->exists();

        if ($exists) {

            abort(
                422,
                'Cet étudiant est déjà inscrit à cette formation.'
            );

        }

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
        | Génération du numéro d'inscription
        |--------------------------------------------------------------------------
        */

        $nextNumber = (Enrollment::withTrashed()->max('id') ?? 0) + 1;

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

            'status' => Enrollment::STATUS_PENDING,

            'academic_year' => $academicYear,

            'enrolled_at' => $data['enrolled_at'],

            'created_by' => Auth::id(),

            'notes' => $data['notes'] ?? null,

        ]);

        /*
        |--------------------------------------------------------------------------
        | Journal d'activité
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Vérifier les paiements existants
        |--------------------------------------------------------------------------
        */

        $hasPayments = $enrollment->payments()->exists();

        /*
        |--------------------------------------------------------------------------
        | Si un paiement existe, les montants financiers sont verrouillés
        |--------------------------------------------------------------------------
        */

        if ($hasPayments) {

            $newDiscount = (float) ($data['discount'] ?? $enrollment->discount);

            $currentDiscount = (float) $enrollment->discount;

            if ($newDiscount != $currentDiscount) {

                abort(
                    422,
                    'Impossible de modifier la réduction : cette inscription possède déjà un paiement.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Empêcher également le changement de formation
            |--------------------------------------------------------------------------
            */

            if (
                isset($data['training_id']) &&
                (int) $data['training_id'] !== (int) $enrollment->training_id
            ) {

                abort(
                    422,
                    'Impossible de modifier la formation : cette inscription possède déjà un paiement.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Formation
        |--------------------------------------------------------------------------
        */

        $training = Training::findOrFail(
            $data['training_id'] ?? $enrollment->training_id
        );

        /*
        |--------------------------------------------------------------------------
        | Frais d'inscription
        |--------------------------------------------------------------------------
        */

        $registrationFee = (float) $enrollment->registration_fee;

        /*
        |--------------------------------------------------------------------------
        | Prix formation
        |--------------------------------------------------------------------------
        */

        $trainingFee = (float) $training->price;

        /*
        |--------------------------------------------------------------------------
        | Réduction
        |--------------------------------------------------------------------------
        */

        $discount = $hasPayments
            ? (float) $enrollment->discount
            : (float) ($data['discount'] ?? 0);

        /*
        |--------------------------------------------------------------------------
        | Calcul financier
        |--------------------------------------------------------------------------
        */

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
        | Protection contre un total inférieur aux paiements existants
        |--------------------------------------------------------------------------
        */

        $amountPaid = (float) $enrollment->amount_paid;

        if ($totalAmount < $amountPaid) {

            abort(
                422,
                sprintf(
                    'Impossible de modifier cette inscription : le nouveau montant total (%s FCFA) est inférieur au montant déjà payé (%s FCFA).',
                    number_format($totalAmount, 0, ',', ' '),
                    number_format($amountPaid, 0, ',', ' ')
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Mise à jour
        |--------------------------------------------------------------------------
        */

        $enrollment->update([

            'student_id' => $data['student_id']
                ?? $enrollment->student_id,

            'training_id' => $training->id,

            'training_fee' => $trainingFee,

            'discount' => $discount,

            'total_amount' => $totalAmount,

            'academic_year' => $data['academic_year']
                ?? Setting::getValue(
                    'academic_year',
                    date('Y') . '-' . (date('Y') + 1)
                ),

            'enrolled_at' => $data['enrolled_at']
                ?? $enrollment->enrolled_at,

            'notes' => $data['notes'] ?? $enrollment->notes,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Recalcul du solde
        |--------------------------------------------------------------------------
        */

        $enrollment->refresh();

        $enrollment->refreshBalance();

        /*
        |--------------------------------------------------------------------------
        | Journal d'activité
        |--------------------------------------------------------------------------
        */

        ActivityLogService::log(

            module: 'enrollments',

            event: 'updated',

            subject: $enrollment,

            properties: [

                'numero_inscription' =>
                    $enrollment->enrollment_number,

                'etudiant' =>
                    $enrollment->student->full_name,

                'formation' =>
                    $enrollment->training->title,

                'statut' =>
                    $enrollment->status,

                'montant_total' =>
                    $enrollment->total_amount,

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
 *
 * Une inscription ayant au moins un paiement
 * ne peut pas être supprimée.
 */
public static function delete(
    Enrollment $enrollment
): void {

    DB::transaction(function () use ($enrollment) {

        /*
        |--------------------------------------------------------------------------
        | Règle métier : une inscription avec paiement est protégée
        |--------------------------------------------------------------------------
        */

        if ($enrollment->payments()->exists()) {

            abort(
                422,
                'Impossible de supprimer cette inscription : elle possède déjà un paiement.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Journal d'activité
        |--------------------------------------------------------------------------
        */

        ActivityLogService::log(

            module: 'enrollments',

            event: 'deleted',

            subject: $enrollment,

            properties: [

                'numero_inscription' =>
                    $enrollment->enrollment_number,

                'etudiant' =>
                    $enrollment->student->full_name,

                'formation' =>
                    $enrollment->training->title,

            ]

        );

        /*
        |--------------------------------------------------------------------------
        | Suppression logique
        |--------------------------------------------------------------------------
        */

        $enrollment->delete();

    });

}

}