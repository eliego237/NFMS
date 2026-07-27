<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PaymentController extends Controller implements HasMiddleware
{
    /**
     * Middlewares
     */
    public static function middleware(): array
{
    return [];
}

    /**
     * Liste des paiements
     */
    public function index()
    {
        $payments = Payment::with([
            'enrollment.student',
            'enrollment.training',
            'paymentMethod',
            'receiver',
        ])
        ->latest()
        ->get();

        return response()->json([

            'success' => true,

            'message' => 'Liste des paiements récupérée avec succès.',

            'data' => PaymentResource::collection($payments),

        ]);
    }

    /**
     * Enregistrer un paiement
     */
    public function store(StorePaymentRequest $request)
    {
        $result = PaymentService::store(
            $request->validated()
        );

        return response()->json([

            'success' => true,

            'message' => 'Paiement enregistré avec succès.',

            'data' => [

                'payment' => new PaymentResource(
                    $result['payment']->load([
                        'paymentMethod',
                        'receiver',
                        'enrollment.student',
                        'enrollment.training',
                    ])
                ),

                'enrollment' => [

                    'id' => $result['enrollment']->id,

                    'enrollment_number' =>
                        $result['enrollment']->enrollment_number,

                    'registration_fee' =>
                        $result['enrollment']->registration_fee,

                    'training_fee' =>
                        $result['enrollment']->training_fee,

                    'discount' =>
                        $result['enrollment']->discount,

                    'total_amount' =>
                        $result['enrollment']->total_amount,

                    'amount_paid' =>
                        $result['enrollment']->amount_paid,

                    'balance' =>
                        $result['enrollment']->balance,

                    'payment_progress' =>
                        $result['enrollment']->payment_progress,

                    'formatted_status' =>
                        $result['enrollment']->formatted_status,

                ],

            ],

        ], 201);
    }

    /**
     * Détails d'un paiement
     */
    public function show(Payment $payment)
    {
        $payment->load([

            'paymentMethod',

            'receiver',

            'enrollment.student',

            'enrollment.training',

        ]);

        return response()->json([

            'success' => true,

            'message' => 'Paiement récupéré avec succès.',

            'data' => new PaymentResource($payment),

        ]);
    }

    /**
     * Les paiements ne sont pas modifiables
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
     * Supprimer un paiement
     */
    public function destroy(Payment $payment)
    {
        PaymentService::delete($payment);

        return response()->json([

            'success' => true,

            'message' => 'Paiement supprimé avec succès.',

        ]);
    }
}