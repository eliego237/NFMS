<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Enregistrer un paiement.
     */
    public static function store(array $data): array
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Verrouillage de l'inscription
            |--------------------------------------------------------------------------
            */

            $enrollment = Enrollment::lockForUpdate()->findOrFail(
                $data['enrollment_id']
            );

            /*
            |--------------------------------------------------------------------------
            | Vérification du solde
            |--------------------------------------------------------------------------
            */

            if ($enrollment->isFullyPaid()) {

                abort(
                    422,
                    'Cette inscription est déjà entièrement payée.'
                );

            }

            if ($data['amount'] <= 0) {

                abort(
                    422,
                    'Le montant du paiement doit être supérieur à zéro.'
                );

            }

            if ($data['amount'] > $enrollment->balance) {

                abort(
                    422,
                    'Le montant dépasse le solde restant.'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Génération du numéro de reçu
            |--------------------------------------------------------------------------
            */

            $prefix = Setting::getValue(
                'receipt_prefix',
                'REC'
            );

            $nextNumber = (
                Payment::lockForUpdate()->max('id') ?? 0
            ) + 1;

            $receiptNumber = sprintf(

                '%s%s%06d',

                $prefix,

                now()->year,

                $nextNumber

            );

            /*
            |--------------------------------------------------------------------------
            | Création du paiement
            |--------------------------------------------------------------------------
            */

            $payment = Payment::create([

                'enrollment_id' => $enrollment->id,

                'receipt_number' => $receiptNumber,

                'amount' => $data['amount'],

                'payment_method_id' => $data['payment_method_id'],

                'payment_date' => $data['payment_date'],

                'reference' => $data['reference'] ?? null,

                'notes' => $data['notes'] ?? null,

                'received_by' => Auth::id(),

            ]);

            /*
            |--------------------------------------------------------------------------
            | Mise à jour de l'inscription
            |--------------------------------------------------------------------------
            */

            $enrollment->increment(

                'amount_paid',

                $payment->amount

            );

            $enrollment->refresh();

            $enrollment->refreshBalance();

            $enrollment->refresh();

            /*
            |--------------------------------------------------------------------------
            | Enregistrement dans la caisse
            |--------------------------------------------------------------------------
            */

            CashTransactionService::recordPayment(
                $payment
            );

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'payments',

                event: 'created',

                subject: $payment,

                properties: [

                    'recu' => $payment->receipt_number,

                    'montant' => $payment->amount,

                    'etudiant' => $enrollment->student->full_name,

                    'formation' => $enrollment->training->title,

                    'utilisateur' => Auth::user()?->name,

                ]

            );

            /*
            |--------------------------------------------------------------------------
            | Retour
            |--------------------------------------------------------------------------
            */

            return [

                'payment' => $payment->fresh()->load([

                    'paymentMethod',

                    'receiver',

                    'enrollment.student',

                    'enrollment.training',

                    'cashTransaction',

                ]),

                'enrollment' => $enrollment->fresh()->load([

                    'student',

                    'training',

                    'payments',

                ]),

            ];

        });
    }

    /**
 * Supprimer un paiement.
 */
public static function delete(Payment $payment): void
{
    DB::transaction(function () use ($payment) {

        /*
        |--------------------------------------------------------------------------
        | Chargement des relations
        |--------------------------------------------------------------------------
        */

        $payment->loadMissing([
            'enrollment.student',
            'enrollment.training',
            'cashTransaction',
        ]);

        $enrollment = Enrollment::lockForUpdate()->findOrFail(
            $payment->enrollment_id
        );

        /*
        |--------------------------------------------------------------------------
        | Suppression de la transaction de caisse
        |--------------------------------------------------------------------------
        */

        if ($payment->cashTransaction) {

            $payment->cashTransaction->delete();

        }

        /*
        |--------------------------------------------------------------------------
        | Mise à jour de l'inscription
        |--------------------------------------------------------------------------
        */

        $enrollment->decrement(
            'amount_paid',
            $payment->amount
        );

        $enrollment->refresh();

        $enrollment->refreshBalance();

        /*
        |--------------------------------------------------------------------------
        | Journal d'activité
        |--------------------------------------------------------------------------
        */

        ActivityLogService::log(

            module: 'payments',

            event: 'deleted',

            subject: $payment,

            properties: [

                'recu' => $payment->receipt_number,

                'montant' => $payment->amount,

                'etudiant' => $enrollment->student->full_name,

                'formation' => $enrollment->training->title,

                'utilisateur' => Auth::user()?->name,

            ]

        );

        /*
        |--------------------------------------------------------------------------
        | Suppression du paiement
        |--------------------------------------------------------------------------
        */

        $payment->delete();

    });
}

}