<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'receipt_number' => $this->receipt_number,

            'reference' => $this->reference,

            'amount' => (float) $this->amount,

            'formatted_amount' => number_format(
                $this->amount,
                0,
                ',',
                ' '
            ) . ' FCFA',

            'payment_date' => optional($this->payment_date)
                ?->format('d/m/Y'),

            'notes' => $this->notes,

            'created_at' => optional($this->created_at)
                ?->format('d/m/Y H:i'),

            /*
            |--------------------------------------------------------------------------
            | Mode de paiement
            |--------------------------------------------------------------------------
            */

            'payment_method' => $this->whenLoaded(
                'paymentMethod',
                fn () => [

                    'id' => $this->paymentMethod->id,

                    'name' => $this->paymentMethod->name,

                ]
            ),

            /*
            |--------------------------------------------------------------------------
            | Utilisateur ayant reçu le paiement
            |--------------------------------------------------------------------------
            */

            'receiver' => $this->whenLoaded(
                'receiver',
                fn () => [

                    'id' => $this->receiver->id,

                    'name' => $this->receiver->name,

                ]
            ),

            /*
            |--------------------------------------------------------------------------
            | Inscription
            |--------------------------------------------------------------------------
            */

            'enrollment' => $this->whenLoaded(
    'enrollment',
    fn () => [

        'id' => $this->enrollment->id,

        'enrollment_number' => $this->enrollment->enrollment_number,

        'academic_year' => $this->enrollment->academic_year,

        'registration_fee' => (float) $this->enrollment->registration_fee,

        'training_fee' => (float) $this->enrollment->training_fee,

        'discount' => (float) $this->enrollment->discount,

        'total_amount' => (float) $this->enrollment->total_amount,

        'amount_paid' => (float) $this->enrollment->amount_paid,

        'balance' => (float) $this->enrollment->balance,

        'payment_progress' => $this->enrollment->payment_progress,

        'formatted_status' => $this->enrollment->formatted_status,

        /*
        |--------------------------------------------------------------------------
        | Étudiant
        |--------------------------------------------------------------------------
        */

        'student' => $this->enrollment->relationLoaded('student')
            ? [

                'id' => $this->enrollment->student->id,

                'matricule' => $this->enrollment->student->matricule,

                'first_name' => $this->enrollment->student->first_name,

                'last_name' => $this->enrollment->student->last_name,

                'full_name' => $this->enrollment->student->first_name . ' ' .
                    $this->enrollment->student->last_name,

                'phone' => $this->enrollment->student->phone,

                'email' => $this->enrollment->student->email,

            ]
            : null,

        /*
        |--------------------------------------------------------------------------
        | Formation
        |--------------------------------------------------------------------------
        */

        'training' => $this->enrollment->relationLoaded('training')
            ? [

                'id' => $this->enrollment->training->id,

                'code' => $this->enrollment->training->code,

                'title' => $this->enrollment->training->title,

                'category' => $this->enrollment->training->category,

            ]
            : null,

        /*
        |--------------------------------------------------------------------------
        | Historique des paiements
        |--------------------------------------------------------------------------
        */

        'payments' => $this->enrollment->relationLoaded('payments')

            ? $this->enrollment->payments
                ->sortByDesc('payment_date')
                ->values()
                ->map(function ($payment) {

                    return [

                        'id' => $payment->id,

                        'receipt_number' => $payment->receipt_number,

                        'payment_date' => optional($this->payment_date)
    ?->toDateString(),

                        'amount' => (float) $payment->amount,

                        'formatted_amount' => number_format(
                            $payment->amount,
                            0,
                            ',',
                            ' '
                        ) . ' FCFA',

                        'payment_method' => $payment->paymentMethod?->name,

                        'receiver' => $payment->receiver?->name,

                    ];

                })

            : [],

    ]
),

        ];
    }
}