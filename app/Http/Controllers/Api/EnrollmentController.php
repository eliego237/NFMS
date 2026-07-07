<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\Training;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Liste des inscriptions.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste des inscriptions récupérée avec succès.',
            'data' => Enrollment::with([
                'student',
                'training',
                'payments',
                'creator',
            ])
            ->latest()
            ->get(),
        ]);
    }

    /**
     * Créer une inscription.
     */
    public function store(StoreEnrollmentRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $training = Training::findOrFail(
                $request->training_id
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

            $academicYear = $request->academic_year
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

            $discount = (float) (
                $request->discount ?? 0
            );

            $grossAmount = $registrationFee + $trainingFee;

            if ($discount > $grossAmount) {

                return response()->json([
                    'message' => 'La remise ne peut pas être supérieure au montant total.'
                ], 422);

            }

            $totalAmount = $grossAmount - $discount;

            /*
            |--------------------------------------------------------------------------
            | Génération du numéro d'inscription
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

                'student_id' => $request->student_id,

                'training_id' => $training->id,

                'registration_fee' => $registrationFee,

                'training_fee' => $trainingFee,

                'discount' => $discount,

                'total_amount' => $totalAmount,

                'amount_paid' => 0,

                'balance' => $totalAmount,

                'status' => 'pending',

                'academic_year' => $academicYear,

                'enrolled_at' => $request->enrolled_at,

                'created_by' => Auth::id(),

                'notes' => $request->notes,

            ]);

            return response()->json([

                'success' => true,

                'message' => 'Inscription créée avec succès.',

                'data' => $enrollment->load([
                    'student',
                    'training',
                    'creator',
                ]),

            ], 201);

        });
    }

    /**
     * Afficher une inscription.
     */
    public function show(Enrollment $enrollment)
    {
        return response()->json([

            'success' => true,

            'message' => 'Inscription récupérée avec succès.',

            'data' => $enrollment->load([
                'student',
                'training',
                'payments',
                'creator',
            ]),

        ]);
    }

    /**
     * Modifier une inscription.
     */
    public function update(
        UpdateEnrollmentRequest $request,
        Enrollment $enrollment
    ) {
        $training = Training::findOrFail(
            $request->training_id
        );

        $registrationFee = $enrollment->registration_fee;

        $trainingFee = $training->price;

        $discount = (float) (
            $request->discount ?? 0
        );

        $grossAmount = $registrationFee + $trainingFee;

        if ($discount > $grossAmount) {

            return response()->json([
                'message' => 'La remise ne peut pas être supérieure au montant total.'
            ], 422);

        }

        $totalAmount = $grossAmount - $discount;

        $enrollment->update([

            'student_id' => $request->student_id,

            'training_id' => $training->id,

            'training_fee' => $trainingFee,

            'discount' => $discount,

            'total_amount' => $totalAmount,

            'academic_year' => $request->academic_year
                ?? Setting::getValue(
                    'academic_year',
                    '2026-2027'
                ),

            'enrolled_at' => $request->enrolled_at,

            'notes' => $request->notes,

        ]);

        /*
        |--------------------------------------------------------------------------
        | Recalcul automatique
        |--------------------------------------------------------------------------
        */

        $enrollment->refresh();

        $enrollment->refreshBalance();

        return response()->json([

            'success' => true,

            'message' => 'Inscription modifiée avec succès.',

            'data' => $enrollment->fresh()->load([
                'student',
                'training',
                'payments',
                'creator',
            ]),

        ]);
    }

    /**
     * Supprimer une inscription.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return response()->json([

            'success' => true,

            'message' => 'Inscription supprimée avec succès.',

        ]);
    }
}