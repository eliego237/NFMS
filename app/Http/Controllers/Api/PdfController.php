<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;

class PdfController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [];
    }

    public function receipt(Payment $payment)
    {
        return PdfService::receipt($payment);
    }

    public function verifyReceipt(string $receipt): JsonResponse
    {
        $payment = Payment::with([
            'enrollment.student',
            'enrollment.trainings',
            'paymentMethod',
            'receiver',
        ])
            ->where('receipt_number', $receipt)
            ->first();

        if (! $payment) {
            return response()->json([
                'success' => false,
                'verified' => false,
                'message' => 'Ce reçu est introuvable ou invalide.',
            ], 404);
        }

        $enrollment = $payment->enrollment;
        $student = $enrollment?->student;
        $trainings = $enrollment?->trainings ?? collect();

        return response()->json([
            'success' => true,
            'verified' => true,
            'message' => 'Reçu authentique.',
            'data' => [
                'receipt_number' => $payment->receipt_number,
                'payment_date' => $payment->payment_date,
                'amount' => $payment->amount,
                'payment_type' => $payment->payment_type,
                'payment_type_label' => $payment->payment_type_label,
                'payment_method' => $payment->paymentMethod?->name,
                'student' => [
                    'matricule' => $student?->matricule,
                    'full_name' => $student?->full_name,
                ],
                'trainings' => $trainings->map(function ($training) {
                    return [
                        'id' => $training->id,
                        'code' => $training->code,
                        'title' => $training->title,
                        'training_fee' =>
                            $training->pivot?->training_fee
                            ?? $training->price,
                        'duration_months' =>
                            $training->pivot?->duration_months
                            ?? $training->duration_months,
                    ];
                })->values(),
                'training_names' => $trainings
                    ->pluck('title')
                    ->filter()
                    ->implode(' + '),
                'receiver' => $payment->receiver?->name,
            ],
        ]);
    }
}