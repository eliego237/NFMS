<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    /**
     * Enregistrer une nouvelle dépense.
     */
    public static function store(array $data): Expense
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Génération du numéro de dépense
            |--------------------------------------------------------------------------
            */

            $prefix = Setting::getValue(
                'expense_prefix',
                'EXP'
            );

            $nextNumber = (Expense::max('id') ?? 0) + 1;

            $expenseNumber = sprintf(
                '%s%s%06d',
                $prefix,
                now()->year,
                $nextNumber
            );

            /*
            |--------------------------------------------------------------------------
            | Création de la dépense
            |--------------------------------------------------------------------------
            */

            $expense = Expense::create([

                'expense_number' => $expenseNumber,

                'category' => $data['category'],

                'title' => $data['title'],

                'description' => $data['description'] ?? null,

                'amount' => $data['amount'],

                'payment_method_id' => $data['payment_method_id'],

                'expense_date' => $data['expense_date'],

                'reference' => $data['reference'] ?? null,

                'recorded_by' => Auth::id(),

                'notes' => $data['notes'] ?? null,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Création automatique de l'écriture de caisse
            |--------------------------------------------------------------------------
            */

            CashTransactionService::recordExpense($expense);

            return $expense->fresh();
        });
    }

    /**
     * Modifier une dépense.
     */
    public static function update(
        Expense $expense,
        array $data
    ): Expense {

        $expense->update([

            'category' => $data['category'],

            'title' => $data['title'],

            'description' => $data['description'] ?? null,

            'amount' => $data['amount'],

            'payment_method_id' => $data['payment_method_id'],

            'expense_date' => $data['expense_date'],

            'reference' => $data['reference'] ?? null,

            'notes' => $data['notes'] ?? null,

        ]);

        return $expense->fresh();
    }

    /**
     * Suppression logique.
     */
    public static function delete(Expense $expense): void
    {
        $expense->delete();
    }
}