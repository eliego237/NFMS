<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PdfController extends Controller implements HasMiddleware
{
    /**
     * Middlewares.
     */
    public static function middleware(): array
    {
        return [];
    }

    /**
     * Générer le reçu PDF.
     */
    public function receipt(Payment $payment)
    {
        return PdfService::receipt($payment);
    }

    /**
     * Vérification publique d'un reçu.
     */
    public function verifyReceipt(string $receipt): JsonResponse
    {
        $payment = Payment::with([

                'enrollment.student',

                'enrollment.training',

                'paymentMethod',

                'receiver',

            ])
            ->where('receipt_number', $receipt)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Reçu introuvable
        |--------------------------------------------------------------------------
        */

        if (! $payment) {

            return response()->json([

                'success' => false,

                'verified' => false,

                'message' => 'Ce reçu est introuvable ou invalide.',

            ], 404);

        }

        /*
        |--------------------------------------------------------------------------
        | Reçu authentique
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'verified' => true,

            'message' => 'Reçu authentique.',

            'data' => [

                'receipt_number' => $payment->receipt_number,

                'payment_date' => $payment->payment_date,

                'amount' => $payment->amount,

                'payment_method' => $payment->paymentMethod?->name,

                'student' => [

                    'matricule' => $payment->enrollment->student->matricule,

                    'full_name' => $payment->enrollment->student->first_name . ' ' .
                        $payment->enrollment->student->last_name,

                ],

                'training' => [

                    'code' => $payment->enrollment->training->code,

                    'title' => $payment->enrollment->training->title,

                ],

                'receiver' => $payment->receiver?->name,

            ],

        ]);
    }
}