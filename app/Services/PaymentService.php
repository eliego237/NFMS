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

            $enrollment = Enrollment::findOrFail(
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

            $nextNumber = (Payment::max('id') ?? 0) + 1;

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

                'enrollment_id'    => $enrollment->id,

                'receipt_number'   => $receiptNumber,

                'amount'           => $data['amount'],

                'payment_method_id'=> $data['payment_method_id'],

                'payment_date'     => $data['payment_date'],

                'reference'        => $data['reference'] ?? null,

                'notes'            => $data['notes'] ?? null,

                'received_by'      => Auth::id(),

            ]);

            /*
            |--------------------------------------------------------------------------
            | Mise à jour de l'inscription
            |--------------------------------------------------------------------------
            */

            $enrollment->amount_paid += $payment->amount;

            $enrollment->refreshBalance();

            /*
            |--------------------------------------------------------------------------
            | Journal de caisse
            |--------------------------------------------------------------------------
            */

            CashTransactionService::recordPayment($payment);

            return [

                'payment' => $payment->load([
                    'paymentMethod',
                    'receiver',
                    'enrollment.student',
                    'enrollment.training',
                ]),

                'enrollment' => $enrollment->fresh(),

            ];

        });
    }
}