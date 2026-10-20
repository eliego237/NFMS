<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Enregistrer un paiement.
     *
     * Types autorisés :
     * - registration : frais d'inscription
     * - training     : paiement de formation
     */
    public static function store(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $enrollment = Enrollment::query()
                ->lockForUpdate()
                ->with([
                    'student',
                    'trainings',
                ])
                ->findOrFail($data['enrollment_id']);

            $paymentType = $data['payment_type']
                ?? Payment::TYPE_TRAINING;

            if (! in_array($paymentType, [
                Payment::TYPE_REGISTRATION,
                Payment::TYPE_TRAINING,
            ], true)) {
                abort(
                    422,
                    'Le type de paiement est invalide.'
                );
            }

            $amount = (float) ($data['amount'] ?? 0);

            if ($amount <= 0) {
                abort(
                    422,
                    'Le montant du paiement doit être supérieur à zéro.'
                );
            }

            /*
             * Paiement des frais d'inscription.
             */
            if ($paymentType === Payment::TYPE_REGISTRATION) {
                self::validateRegistrationPayment(
                    $enrollment,
                    $amount
                );
            }

            /*
             * Paiement des formations.
             */
            if ($paymentType === Payment::TYPE_TRAINING) {
                self::validateTrainingPayment(
                    $enrollment,
                    $amount
                );
            }

            /*
             * Génération du numéro de reçu.
             */
            $receiptNumber = self::generateReceiptNumber();

            /*
             * Création du paiement.
             */
            $payment = Payment::create([
                'enrollment_id'     => $enrollment->id,
                'receipt_number'    => $receiptNumber,
                'payment_type'      => $paymentType,
                'amount'            => $amount,
                'payment_method_id' => $data['payment_method_id'],
                'payment_date'      => $data['payment_date'],
                'reference'         => $data['reference'] ?? null,
                'notes'             => $data['notes'] ?? null,
                'received_by'       => Auth::id(),
            ]);

            /*
             * amount_paid représente l'ensemble
             * des sommes réellement encaissées :
             *
             * inscription + formation.
             */
            $enrollment->increment(
                'amount_paid',
                $amount
            );

            $enrollment->refresh();

            $enrollment->refreshBalance();

            $enrollment->refresh();

            /*
             * Enregistrement dans la caisse.
             */
            CashTransactionService::recordPayment(
                $payment
            );

            /*
             * Noms des formations.
             */
            $formationNames = $enrollment->trainings
                ->pluck('title')
                ->filter()
                ->implode(' + ');

            /*
             * Journal d'activité.
             */
            ActivityLogService::log(
                module: 'payments',
                event: 'created',
                subject: $payment,
                properties: [
                    'recu' => $payment->receipt_number,
                    'type_paiement' => $payment->payment_type,
                    'montant' => $payment->amount,
                    'etudiant' => $enrollment
                        ->student
                        ->full_name,
                    'formations' => $formationNames,
                    'utilisateur' => Auth::user()?->name,
                ]
            );

            /*
             * Réponse complète.
             */
            return [
                'payment' => $payment
                    ->fresh()
                    ->load([
                        'paymentMethod',
                        'receiver',
                        'enrollment.student',
                        'enrollment.trainings',
                        'cashTransaction',
                    ]),

                'enrollment' => $enrollment
                    ->fresh()
                    ->load([
                        'student',
                        'trainings',
                        'payments',
                    ]),
            ];
        });
    }

    /**
     * Vérifier un paiement des frais d'inscription.
     *
     * Les frais d'inscription sont totalement indépendants
     * des frais de formation.
     */
    private static function validateRegistrationPayment(
        Enrollment $enrollment,
        float $amount
    ): void {
        $alreadyPaid = $enrollment->payments()
            ->where(
                'payment_type',
                Payment::TYPE_REGISTRATION
            )
            ->exists();

        if ($alreadyPaid) {
            abort(
                422,
                'Les frais d’inscription de cette inscription ont déjà été payés.'
            );
        }

        $registrationFee = self::calculateRegistrationTotal(
            $enrollment
        );

        if ($registrationFee <= 0) {
            abort(
                422,
                'Aucun frais d’inscription n’est dû pour cette inscription.'
            );
        }

        if ($amount > $registrationFee) {
            abort(
                422,
                sprintf(
                    'Le montant dépasse les frais d’inscription de %s FCFA.',
                    self::formatAmount($registrationFee)
                )
            );
        }

        if ($amount < $registrationFee) {
            abort(
                422,
                sprintf(
                    'Les frais d’inscription doivent être réglés intégralement : %s FCFA.',
                    self::formatAmount($registrationFee)
                )
            );
        }
    }

    /**
     * Vérifier un paiement de formation.
     *
     * Aucun paiement d'inscription n'est pris en compte
     * dans ce calcul.
     */
    private static function validateTrainingPayment(
        Enrollment $enrollment,
        float $amount
    ): void {
        $trainingBalance = self::calculateTrainingBalance(
            $enrollment
        );

        if ($trainingBalance <= 0) {
            abort(
                422,
                'Les formations de cette inscription sont déjà entièrement payées.'
            );
        }

        if ($amount > $trainingBalance) {
            abort(
                422,
                sprintf(
                    'Le montant dépasse le solde des formations : %s FCFA.',
                    self::formatAmount($trainingBalance)
                )
            );
        }
    }

    /**
     * Total des frais d'inscription.
     */
    private static function calculateRegistrationTotal(
        Enrollment $enrollment
    ): float {
        return max(
            0,
            (float) ($enrollment->registration_fee ?? 0)
        );
    }

    /**
     * Montant déjà payé pour l'inscription.
     */
    private static function calculateRegistrationPaid(
        Enrollment $enrollment
    ): float {
        return (float) $enrollment->payments()
            ->where(
                'payment_type',
                Payment::TYPE_REGISTRATION
            )
            ->sum('amount');
    }

    /**
     * Reste des frais d'inscription.
     */
    private static function calculateRegistrationBalance(
        Enrollment $enrollment
    ): float {
        return max(
            0,
            self::calculateRegistrationTotal($enrollment)
            - self::calculateRegistrationPaid($enrollment)
        );
    }

    /**
     * Total brut des formations.
     *
     * IMPORTANT :
     * Les frais d'inscription ne sont jamais inclus ici.
     */
    private static function calculateTrainingTotal(
        Enrollment $enrollment
    ): float {
        return (float) $enrollment->trainings->sum(
            function ($training) {
                return (float) (
                    $training->pivot->training_fee
                    ?? $training->price
                    ?? 0
                );
            }
        );
    }

    /**
     * Montant déjà payé pour les formations.
     *
     * Les paiements d'inscription sont exclus.
     */
    private static function calculateTrainingPaid(
        Enrollment $enrollment
    ): float {
        return (float) $enrollment->payments()
            ->where(
                'payment_type',
                Payment::TYPE_TRAINING
            )
            ->sum('amount');
    }

    /**
     * Remise appliquée uniquement aux formations.
     *
     * Les frais d'inscription ne bénéficient pas
     * de cette remise.
     */
    private static function calculateTrainingDiscount(
        Enrollment $enrollment
    ): float {
        return min(
            self::calculateTrainingTotal($enrollment),
            max(
                0,
                (float) ($enrollment->discount ?? 0)
            )
        );
    }

    /**
     * Total net des formations après remise.
     */
    private static function calculateNetTrainingTotal(
        Enrollment $enrollment
    ): float {
        return max(
            0,
            self::calculateTrainingTotal($enrollment)
            - self::calculateTrainingDiscount($enrollment)
        );
    }

    /**
     * Solde restant des formations.
     *
     * Les frais d'inscription ne sont JAMAIS
     * déduits ou ajoutés à ce calcul.
     */
    private static function calculateTrainingBalance(
        Enrollment $enrollment
    ): float {
        return max(
            0,
            self::calculateNetTrainingTotal($enrollment)
            - self::calculateTrainingPaid($enrollment)
        );
    }

    /**
     * Solde global restant.
     *
     * Inscription + formations.
     */
    public static function calculateGlobalBalance(
        Enrollment $enrollment
    ): float {
        return max(
            0,
            self::calculateRegistrationBalance($enrollment)
            + self::calculateTrainingBalance($enrollment)
        );
    }

    /**
     * Générer un numéro de reçu.
     *
     * Format :
     * REC + année + numéro séquentiel sur 6 chiffres.
     *
     * Exemple :
     * REC2026000001
     */
    private static function generateReceiptNumber(): string
    {
        $year = now()->year;

        $prefix = 'REC';

        $base = $prefix . $year;

        $lastReceipt = Payment::withTrashed()
            ->where(
                'receipt_number',
                'like',
                $base . '%'
            )
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $nextNumber = 1;

        if ($lastReceipt) {
            $lastNumber = (int) substr(
                $lastReceipt->receipt_number,
                strlen($base)
            );

            $nextNumber = $lastNumber + 1;
        }

        do {
            $receiptNumber = sprintf(
                '%s%06d',
                $base,
                $nextNumber
            );

            $exists = Payment::withTrashed()
                ->where(
                    'receipt_number',
                    $receiptNumber
                )
                ->exists();

            if ($exists) {
                $nextNumber++;
            }

        } while ($exists);

        return $receiptNumber;
    }

    /**
     * Supprimer un paiement.
     */
    public static function delete(
        Payment $payment
    ): void {
        DB::transaction(function () use ($payment) {

            $payment->loadMissing([
                'enrollment.student',
                'enrollment.trainings',
                'cashTransaction',
            ]);

            $enrollment = Enrollment::query()
                ->lockForUpdate()
                ->with([
                    'student',
                    'trainings',
                ])
                ->findOrFail(
                    $payment->enrollment_id
                );

            /*
             * Supprimer la transaction de caisse.
             */
            if ($payment->cashTransaction) {
                $payment->cashTransaction->delete();
            }

            /*
             * amount_paid représente toujours
             * l'ensemble des paiements encaissés.
             */
            $newAmountPaid = max(
                0,
                (float) $enrollment->amount_paid
                - (float) $payment->amount
            );

            $enrollment->amount_paid =
                $newAmountPaid;

            $enrollment->refreshBalance();

            /*
             * Noms des formations.
             */
            $formationNames = $enrollment->trainings
                ->pluck('title')
                ->filter()
                ->implode(' + ');

            /*
             * Journal.
             */
            ActivityLogService::log(
                module: 'payments',
                event: 'deleted',
                subject: $payment,
                properties: [
                    'recu' => $payment->receipt_number,
                    'type_paiement' => $payment->payment_type,
                    'montant' => $payment->amount,
                    'etudiant' => $enrollment
                        ->student
                        ->full_name,
                    'formations' => $formationNames,
                    'utilisateur' => Auth::user()?->name,
                ]
            );

            /*
             * Suppression du paiement.
             */
            $payment->delete();
        });
    }

    /**
     * Format monétaire.
     */
    private static function formatAmount(
        float $amount
    ): string {
        return number_format(
            $amount,
            0,
            ',',
            ' '
        );
    }
}