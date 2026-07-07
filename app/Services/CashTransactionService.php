<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Setting;

class CashTransactionService
{
    /**
     * Enregistrer une entrée de caisse provenant d'un paiement.
     */
    public static function recordPayment(Payment $payment): CashTransaction
    {
        // Charger les relations si elles ne le sont pas déjà
        $payment->loadMissing('enrollment.student');

        $student = $payment->enrollment->student;

        /*
        |--------------------------------------------------------------------------
        | Génération du numéro de transaction
        |--------------------------------------------------------------------------
        */

        $prefix = Setting::getValue(
            'cash_prefix',
            'CASH'
        );

        $nextNumber = (CashTransaction::max('id') ?? 0) + 1;

        $transactionNumber = sprintf(
            '%s%s%06d',
            $prefix,
            now()->year,
            $nextNumber
        );

        /*
        |--------------------------------------------------------------------------
        | Création de l'opération de caisse
        |--------------------------------------------------------------------------
        */

        return CashTransaction::create([

            'transaction_number' => $transactionNumber,

            'type' => 'Entrée',

            'category' => 'Paiement formation',

            'amount' => $payment->amount,

            'payment_method_id' => $payment->payment_method_id,

            'payment_id' => $payment->id,

            'expense_id' => null,

            'description' => sprintf(
                'Paiement %s - %s %s',
                $payment->receipt_number,
                $student->first_name,
                $student->last_name
            ),

            'transaction_date' => $payment->payment_date,

            'recorded_by' => $payment->received_by,

            'notes' => $payment->notes,

        ]);
    }

    /**
     * Enregistrer une sortie de caisse provenant d'une dépense.
     */
    public static function recordExpense(Expense $expense): CashTransaction
    {
        /*
        |--------------------------------------------------------------------------
        | Génération du numéro de transaction
        |--------------------------------------------------------------------------
        */

        $prefix = Setting::getValue(
            'cash_prefix',
            'CASH'
        );

        $nextNumber = (CashTransaction::max('id') ?? 0) + 1;

        $transactionNumber = sprintf(
            '%s%s%06d',
            $prefix,
            now()->year,
            $nextNumber
        );

        /*
        |--------------------------------------------------------------------------
        | Création de l'opération de caisse
        |--------------------------------------------------------------------------
        */

        return CashTransaction::create([

            'transaction_number' => $transactionNumber,

            'type' => 'Sortie',

            'category' => $expense->category,

            'amount' => $expense->amount,

            'payment_method_id' => $expense->payment_method_id,

            'payment_id' => null,

            'expense_id' => $expense->id,

            'description' => $expense->title,

            'transaction_date' => $expense->expense_date,

            'recorded_by' => $expense->recorded_by,

            'notes' => $expense->notes,

        ]);
    }
}