<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Routing\Controllers\HasMiddleware;

class PaymentController extends Controller implements HasMiddleware
{
    /**
     * Middlewares.
     */
    public static function middleware(): array
    {
        return [];
    }

    /**
     * Liste des paiements.
     */
    public function index()
    {
        $payments = Payment::with([
            'enrollment.student',
            'enrollment.trainings',
            'allocations.enrollment.student',
            'allocations.enrollment.trainings',
            'paymentMethod',
            'receiver',
        ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,

            'message' =>
                'Liste des paiements récupérée avec succès.',

            'data' =>
                PaymentResource::collection($payments),
        ]);
    }

    /**
     * Enregistrer un paiement.
     */
    public function store(StorePaymentRequest $request)
    {
        $result = PaymentService::store(
            $request->validated()
        );

        /*
        |--------------------------------------------------------------------------
        | Paiement
        |--------------------------------------------------------------------------
        */

        $payment = $result['payment'];

        $payment->load([
            'paymentMethod',
            'receiver',
            'enrollment.student',
            'enrollment.trainings',
            'allocations.enrollment.student',
            'allocations.enrollment.trainings',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Réponse de base
        |--------------------------------------------------------------------------
        */

        $responseData = [
            'payment' => new PaymentResource($payment),
        ];

        /*
        |--------------------------------------------------------------------------
        | Inscription
        |--------------------------------------------------------------------------
        */

        if (isset($result['enrollment'])) {

            $enrollment = $result['enrollment'];

            $enrollment->loadMissing([
                'student',
                'trainings',
                'payments',
            ]);

            $responseData['enrollment'] = [
                'id' =>
                    $enrollment->id,

                'enrollment_number' =>
                    $enrollment->enrollment_number,

                'academic_year' =>
                    $enrollment->academic_year,

                'student' => [
                    'id' =>
                        $enrollment->student?->id,

                    'matricule' =>
                        $enrollment->student?->matricule,

                    'full_name' =>
                        $enrollment->student?->full_name,
                ],

                'trainings' =>
                    $enrollment->trainings
                        ->map(function ($training) {

                            $price = (float) (
                                $training->pivot->training_fee
                                ?? $training->price
                                ?? 0
                            );

                            return [
                                'id' =>
                                    $training->id,

                                'code' =>
                                    $training->code,

                                'title' =>
                                    $training->title,

                                'price' =>
                                    $price,
                            ];
                        })
                        ->values(),

                'training_count' =>
                    $enrollment->trainings->count(),

                'registration_fee' =>
                    (float) $enrollment->registration_fee,

                'discount' =>
                    (float) $enrollment->discount,

                'total_amount' =>
                    (float) $enrollment->total_amount,

                'amount_paid' =>
                    (float) $enrollment->amount_paid,

                'balance' =>
                    (float) $enrollment->balance,

                'status' =>
                    $enrollment->status,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Paiement groupé / anciennes allocations
        |--------------------------------------------------------------------------
        */

        if (isset($result['enrollments'])) {

            $responseData['enrollments'] =
                $result['enrollments']->map(
                    function ($enrollment) {

                        $enrollment->loadMissing([
                            'student',
                            'trainings',
                        ]);

                        return [

                            'id' =>
                                $enrollment->id,

                            'enrollment_number' =>
                                $enrollment->enrollment_number,

                            'academic_year' =>
                                $enrollment->academic_year,

                            'student_id' =>
                                $enrollment->student_id,

                            'student' => [
                                'id' =>
                                    $enrollment->student?->id,

                                'matricule' =>
                                    $enrollment->student?->matricule,

                                'full_name' =>
                                    $enrollment->student?->full_name,
                            ],

                            'trainings' =>
                                $enrollment->trainings
                                    ->map(function ($training) {

                                        $price = (float) (
                                            $training->pivot->training_fee
                                            ?? $training->price
                                            ?? 0
                                        );

                                        return [
                                            'id' =>
                                                $training->id,

                                            'code' =>
                                                $training->code,

                                            'title' =>
                                                $training->title,

                                            'price' =>
                                                $price,
                                        ];
                                    })
                                    ->values(),

                            'training_count' =>
                                $enrollment->trainings->count(),

                            'registration_fee' =>
                                (float) $enrollment->registration_fee,

                            'discount' =>
                                (float) $enrollment->discount,

                            'total_amount' =>
                                (float) $enrollment->total_amount,

                            'amount_paid' =>
                                (float) $enrollment->amount_paid,

                            'balance' =>
                                (float) $enrollment->balance,

                            'status' =>
                                $enrollment->status,
                        ];
                    }
                )->values();

            $responseData['group_total'] =
                $result['group_total'] ?? 0;

            $responseData['group_paid'] =
                $result['group_paid'] ?? 0;

            $responseData['group_balance'] =
                $result['group_balance'] ?? 0;
        }

        return response()->json([

            'success' => true,

            'message' =>
                'Paiement enregistré avec succès.',

            'data' =>
                $responseData,

        ], 201);
    }

    /**
     * Détails d'un paiement.
     */
    public function show(Payment $payment)
    {
        $payment->load([

            'paymentMethod',

            'receiver',

            'enrollment.student',

            'enrollment.trainings',

            'enrollment.payments.paymentMethod',

            'enrollment.payments.receiver',

            'allocations.enrollment.student',

            'allocations.enrollment.trainings',

            'allocations.enrollment.payments.paymentMethod',
        ]);

        return response()->json([

            'success' => true,

            'message' =>
                'Paiement récupéré avec succès.',

            'data' =>
                new PaymentResource($payment),

        ]);
    }

    /**
     * Les paiements ne sont pas modifiables.
     */
    public function update(
        UpdatePaymentRequest $request,
        Payment $payment
    ) {
        return response()->json([

            'success' => false,

            'message' =>
                'Un paiement validé ne peut pas être modifié. Veuillez effectuer une annulation ou enregistrer un nouveau paiement.',

        ], 405);
    }

    /**
     * Supprimer un paiement.
     */
    public function destroy(Payment $payment)
    {
        PaymentService::delete($payment);

        return response()->json([

            'success' => true,

            'message' =>
                'Paiement supprimé avec succès.',

        ]);
    }
}