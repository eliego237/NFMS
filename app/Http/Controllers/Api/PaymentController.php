<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
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
    \Log::info('PAYMENT STORE EXECUTED');

    try {

        $result = PaymentService::store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Paiement enregistré avec succès.',
            'data' => $result,
        ], 201);

    } catch (\Throwable $e) {

        \Log::error('PAYMENT ERROR', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        throw $e;
    }
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

            'enrollment.payments.paymentMethod',

            'enrollment.payments.receiver',

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