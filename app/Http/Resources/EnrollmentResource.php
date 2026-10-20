```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $trainings = $this->whenLoaded('trainings', function () {
            return $this->trainings->map(function ($training) {
                $durationMonths = (int) (
                    $training->pivot->duration_months
                    ?? $training->duration_months
                    ?? 0
                );

                return [
                    'id' => $training->id,
                    'code' => $training->code,
                    'title' => $training->title,
                    'category' => $training->category,
                    'description' => $training->description,
                    'price' => (float) (
                        $training->pivot->training_fee
                        ?? $training->price
                        ?? 0
                    ),
                    'duration_months' => $durationMonths,
                    'duration_label' => $durationMonths > 0
                        ? $durationMonths . ' mois'
                        : null,
                    'certificate' => $training->certificate,
                    'is_active' => (bool) $training->is_active,
                ];
            })->values();
        });

        $registrationFee = (float) ($this->registration_fee ?? 0);
        $discount = (float) ($this->discount ?? 0);
        $totalAmount = (float) ($this->total_amount ?? 0);
        $amountPaid = (float) ($this->amount_paid ?? 0);
        $balance = (float) ($this->balance ?? 0);

        $trainingTotal = $this->whenLoaded('trainings', function () {
            return $this->trainings->sum(function ($training) {
                return (float) (
                    $training->pivot->training_fee
                    ?? $training->price
                    ?? 0
                );
            });
        });

        $statusLabels = [
            'pending' => 'En attente',
            'partial' => 'Paiement partiel',
            'paid' => 'Payé',
            'cancelled' => 'Annulée',
        ];

        return [
            'id' => $this->id,
            'enrollment_number' => $this->enrollment_number,
            'academic_year' => $this->academic_year,

            'enrolled_at' => $this->enrolled_at?->format('Y-m-d'),
            'enrolled_at_label' => $this->enrolled_at?->format('d/m/Y'),

            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'matricule' => $this->student->matricule,
                    'name' => $this->student->full_name,
                    'first_name' => $this->student->first_name,
                    'last_name' => $this->student->last_name,
                    'phone' => $this->student->phone,
                    'email' => $this->student->email,
                    'photo' => $this->student->photo,
                ];
            }),

            'trainings' => $trainings,

            'training_count' => $this->whenLoaded(
                'trainings',
                fn () => $this->trainings->count()
            ),

            'training_total' => $trainingTotal,

            'financial' => [
                'registration_fee' => $registrationFee,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'amount_paid' => $amountPaid,
                'balance' => $balance,
            ],

            'status' => [
                'value' => $this->status,
                'label' => $statusLabels[$this->status]
                    ?? ucfirst((string) $this->status),
            ],

            'notes' => $this->notes,

            'payments' => $this->whenLoaded('payments', function () {
                return PaymentResource::collection(
                    $this->payments
                );
            }),

            'created_by' => $this->whenLoaded('creator', function () {
                return $this->creator
                    ? [
                        'id' => $this->creator->id,
                        'name' => $this->creator->name,
                    ]
                    : null;
            }),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
```
