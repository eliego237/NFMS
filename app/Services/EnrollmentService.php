<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Training;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    /**
     * Créer une inscription.
     *
     * 1 étudiant + 1 année académique = 1 inscription.
     * Une inscription peut contenir plusieurs formations.
     *
     * Les frais d'inscription sont séparés des formations.
     * La remise s'applique uniquement aux formations.
     */
    public static function store(array $data): Enrollment
    {
        return DB::transaction(function () use ($data) {

            $student = Student::query()
                ->lockForUpdate()
                ->findOrFail($data['student_id']);

            $academicYear = $data['academic_year']
                ?? Setting::getValue(
                    'academic_year',
                    date('Y') . '-' . (date('Y') + 1)
                );

            $existingEnrollment = Enrollment::query()
                ->where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where(
                    'status',
                    '!=',
                    Enrollment::STATUS_CANCELLED
                )
                ->first();

            if ($existingEnrollment) {
                abort(
                    422,
                    sprintf(
                        'Cet étudiant possède déjà une inscription pour l’année académique %s. Vous pouvez modifier cette inscription pour ajouter ou retirer des formations.',
                        $academicYear
                    )
                );
            }

            $trainingIds = $data['training_ids']
                ?? (
                    isset($data['training_id'])
                        ? [$data['training_id']]
                        : []
                );

            $trainingIds = collect($trainingIds)
                ->filter(
                    fn ($id) =>
                        is_numeric($id) &&
                        (int) $id > 0
                )
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->unique()
                ->values();

            if ($trainingIds->isEmpty()) {
                abort(
                    422,
                    'Veuillez sélectionner au moins une formation.'
                );
            }

            $trainings = Training::query()
                ->whereIn('id', $trainingIds)
                ->where('is_active', true)
                ->lockForUpdate()
                ->get();

            if (
                $trainings->count() !==
                $trainingIds->count()
            ) {
                abort(
                    422,
                    'Une ou plusieurs formations sélectionnées sont introuvables ou inactives.'
                );
            }

            $registrationFee = max(
                0,
                (float) Setting::getValue(
                    'registration_fee',
                    16500
                )
            );

            $trainingTotal = (float) $trainings->sum(
                fn ($training) =>
                    (float) $training->price
            );

            $discount = max(
                0,
                (float) (
                    $data['discount'] ?? 0
                )
            );

            /*
             * La remise concerne uniquement
             * les frais de formation.
             */
            if ($discount > $trainingTotal) {
                abort(
                    422,
                    'La remise ne peut pas dépasser le montant total des formations.'
                );
            }

            $trainingNetTotal = max(
                0,
                $trainingTotal - $discount
            );

            /*
             * Total global :
             * frais d'inscription
             * +
             * formations après remise
             */
            $totalAmount =
                $registrationFee +
                $trainingNetTotal;

            $prefix = Setting::getValue(
                'enrollment_prefix',
                'INS'
            );

            $nextNumber =
                Enrollment::withTrashed()
                    ->lockForUpdate()
                    ->max('id') ?? 0;

            do {
                $nextNumber++;

                $enrollmentNumber = sprintf(
                    '%s%s%06d',
                    $prefix,
                    str_replace(
                        '-',
                        '',
                        $academicYear
                    ),
                    $nextNumber
                );

            } while (
                Enrollment::withTrashed()
                    ->where(
                        'enrollment_number',
                        $enrollmentNumber
                    )
                    ->exists()
            );

            $enrolledAt =
                $data['enrolled_at'] ?? now();

            $enrollment = Enrollment::create([
                'student_id' =>
                    $student->id,

                'enrollment_number' =>
                    $enrollmentNumber,

                'registration_fee' =>
                    $registrationFee,

                'discount' =>
                    $discount,

                'total_amount' =>
                    $totalAmount,

                'amount_paid' =>
                    0,

                'balance' =>
                    $totalAmount,

                'status' =>
                    Enrollment::STATUS_PENDING,

                'enrolled_at' =>
                    $enrolledAt,

                'academic_year' =>
                    $academicYear,

                'created_by' =>
                    Auth::id(),

                'notes' =>
                    $data['notes'] ?? null,
            ]);

            $pivotData = [];

            foreach ($trainings as $training) {
                $pivotData[$training->id] = [
                    'training_fee' =>
                        (float) $training->price,

                    'duration_months' =>
                        (int) (
                            $training->duration_months ?? 0
                        ),
                ];
            }

            $enrollment
                ->trainings()
                ->attach($pivotData);

            ActivityLogService::log(
                module: 'enrollments',
                event: 'created',
                subject: $enrollment,
                properties: [
                    'etudiant' =>
                        $student->full_name,

                    'annee_academique' =>
                        $academicYear,

                    'formations' =>
                        $trainings
                            ->pluck('title')
                            ->implode(' + '),

                    'formation_ids' =>
                        $trainings
                            ->pluck('id')
                            ->values()
                            ->all(),

                    'frais_inscription' =>
                        $registrationFee,

                    'frais_formation' =>
                        $trainingTotal,

                    'remise' =>
                        $discount,

                    'formation_net' =>
                        $trainingNetTotal,

                    'montant_total' =>
                        $totalAmount,

                    'utilisateur' =>
                        Auth::user()?->name,
                ]
            );

            return $enrollment
                ->fresh()
                ->load([
                    'student',
                    'trainings',
                    'payments',
                ]);
        });
    }


    /**
     * Modifier uniquement les observations d'une inscription.
     *
     * Les informations administratives, les formations,
     * les montants financiers et les paiements restent inchangés.
     */
    public static function update(
        Enrollment $enrollment,
        array $data
    ): Enrollment {
        return DB::transaction(function () use (
            $enrollment,
            $data
        ) {
            // Vérifier que le champ notes est présent.
            if (!array_key_exists('notes', $data)) {
                abort(
                    422,
                    'Veuillez fournir les observations à modifier.'
                );
            }

            // Verrouiller l'inscription pendant la modification.
            $enrollment = Enrollment::query()
                ->lockForUpdate()
                ->findOrFail($enrollment->id);

            // Modifier uniquement les observations.
            $enrollment->notes = $data['notes'];
            $enrollment->save();

            // Journaliser la modification sans toucher aux finances.
            ActivityLogService::log(
                module: 'enrollments',
                event: 'updated',
                subject: $enrollment,
                properties: [
                    'champ_modifie' => 'notes',
                    'utilisateur' => Auth::user()?->name,
                ]
            );

            // Retourner l'inscription avec ses relations.
            return $enrollment
                ->fresh()
                ->load([
                    'student',
                    'trainings.modules',
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
        DB::transaction(function () use (
            $enrollment
        ) {

            $enrollment = Enrollment::query()
                ->lockForUpdate()
                ->findOrFail(
                    $enrollment->id
                );

            /*
             * Une inscription ayant déjà reçu
             * un paiement ne peut pas être supprimée.
             */
            if (
                $enrollment
                    ->payments()
                    ->exists()
            ) {
                abort(
                    422,
                    'Cette inscription ne peut pas être supprimée car elle possède déjà des paiements.'
                );
            }

            $enrollment->load([
                'student',
                'trainings',
            ]);

            /*
             * Journal d'activité.
             */
            ActivityLogService::log(
                module: 'enrollments',
                event: 'deleted',
                subject: $enrollment,
                properties: [
                    'etudiant' =>
                        $enrollment
                            ->student
                            ?->full_name,

                    'annee_academique' =>
                        $enrollment->academic_year,

                    'formations' =>
                        $enrollment
                            ->trainings
                            ->pluck('title')
                            ->implode(' + '),

                    'montant_total' =>
                        $enrollment->total_amount,

                    'utilisateur' =>
                        Auth::user()?->name,
                ]
            );

            $enrollment->delete();
        });
    }
}