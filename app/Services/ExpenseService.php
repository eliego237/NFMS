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

        return DB::transaction(function () use ($expense, $data) {

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

            if ($expense->cashTransaction) {

                $expense->cashTransaction->update([

                    'category' => $expense->category,

                    'amount' => $expense->amount,

                    'payment_method_id' => $expense->payment_method_id,

                    'description' => $expense->title,

                    'transaction_date' => $expense->expense_date,

                    'notes' => $expense->notes,

                ]);

            }

            return $expense->fresh();
        });
    }

    /**
     * Supprimer une dépense.
     */
    public static function delete(
        Expense $expense
    ): void {

        DB::transaction(function () use ($expense) {

            if ($expense->cashTransaction) {

                $expense->cashTransaction->delete();

            }

            $expense->delete();

        });
    }
}