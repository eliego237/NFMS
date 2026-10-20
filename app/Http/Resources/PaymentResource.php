<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'payment_type' => $this->payment_type,
            'payment_type_label' => $this->payment_type_label,
            'reference' => $this->reference,
            'amount' => (float) $this->amount,
            'formatted_amount' => $this->formatAmount($this->amount),

            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'payment_date_label' => $this->payment_date?->format('d/m/Y'),

            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('d/m/Y H:i'),

            'payment_method' => $this->whenLoaded(
                'paymentMethod',
                fn () => $this->paymentMethod
                    ? [
                        'id' => $this->paymentMethod->id,
                        'name' => $this->paymentMethod->name,
                    ]
                    : null
            ),

            'receiver' => $this->whenLoaded(
                'receiver',
                fn () => $this->receiver
                    ? [
                        'id' => $this->receiver->id,
                        'name' => $this->receiver->name,
                    ]
                    : null
            ),

            'enrollment' => $this->whenLoaded(
                'enrollment',
                fn () => $this->formatEnrollment($this->enrollment)
            ),
        ];
    }

    private function formatEnrollment($enrollment): array
    {
        return [
            'id' => $enrollment->id,

            'enrollment_number' => $enrollment->enrollment_number,

            'academic_year' => $enrollment->academic_year,

            'student' => $enrollment->relationLoaded('student')
                ? [
                    'id' => $enrollment->student->id,
                    'matricule' => $enrollment->student->matricule,
                    'first_name' => $enrollment->student->first_name,
                    'last_name' => $enrollment->student->last_name,
                    'full_name' => $enrollment->student->full_name,
                    'phone' => $enrollment->student->phone,
                    'email' => $enrollment->student->email,
                ]
                : null,

            'trainings' => $enrollment->relationLoaded('trainings')
                ? $enrollment->trainings
                    ->map(function ($training) {
                        $durationMonths = (int) (
                            $training->pivot->duration_months
                            ?? $training->duration_months
                            ?? 0
                        );

                        $price = (float) (
                            $training->pivot->training_fee
                            ?? $training->price
                            ?? 0
                        );

                        return [
                            'id' => $training->id,
                            'code' => $training->code,
                            'title' => $training->title,
                            'category' => $training->category,
                            'price' => $price,
                            'formatted_price' =>
                                $this->formatAmount($price),
                            'duration_months' => $durationMonths,
                            'duration_label' =>
                                $durationMonths > 0
                                    ? $durationMonths . ' mois'
                                    : null,
                            'certificate' => $training->certificate,
                        ];
                    })
                    ->values()
                : [],

            'financial' => [
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
            ],

            'status' => [
                'value' => $enrollment->status,
                'label' => $this->statusLabel(
                    $enrollment->status
                ),
            ],
        ];
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'En attente',
            'partial' => 'Paiement partiel',
            'paid' => 'Payé',
            'cancelled' => 'Annulée',
            default => ucfirst((string) $status),
        };
    }

    private function formatAmount($amount): string
    {
        return number_format(
            (float) $amount,
            0,
            ',',
            ' '
        ) . ' FCFA';
    }
}
