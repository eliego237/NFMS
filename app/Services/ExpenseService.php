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

            $nextNumber = Expense::withTrashed()->max('id') + 1;

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
            | Journal de caisse
            |--------------------------------------------------------------------------
            */

            CashTransactionService::recordExpense(
                $expense
            );

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'expenses',

                event: 'created',

                subject: $expense,

                properties: [

                    'numero' => $expense->expense_number,

                    'categorie' => $expense->category,

                    'libelle' => $expense->title,

                    'montant' => $expense->amount,

                    'utilisateur' => Auth::user()?->name,

                ]

            );

            /*
            |--------------------------------------------------------------------------
            | Retour
            |--------------------------------------------------------------------------
            */

            return $expense->fresh()->load([

                'paymentMethod',

                'recorder',

                'cashTransaction',

            ]);

        });
    }

    /**
     * Modifier une dépense.
     */
    public static function update(
        Expense $expense,
        array $data
    ): Expense {

        return DB::transaction(function () use (
            $expense,
            $data
        ) {

            /*
            |--------------------------------------------------------------------------
            | Mise à jour
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Synchronisation de la caisse
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'expenses',

                event: 'updated',

                subject: $expense,

                properties: [

                    'numero' => $expense->expense_number,

                    'categorie' => $expense->category,

                    'libelle' => $expense->title,

                    'montant' => $expense->amount,

                    'utilisateur' => Auth::user()?->name,

                ]

            );

            /*
            |--------------------------------------------------------------------------
            | Retour
            |--------------------------------------------------------------------------
            */

            return $expense->fresh()->load([

                'paymentMethod',

                'recorder',

                'cashTransaction',

            ]);

        });
    }

    /**
     * Supprimer une dépense.
     */
    public static function delete(
        Expense $expense
    ): void {

        DB::transaction(function () use ($expense) {

            /*
            |--------------------------------------------------------------------------
            | Journal d'activité
            |--------------------------------------------------------------------------
            */

            ActivityLogService::log(

                module: 'expenses',

                event: 'deleted',

                subject: $expense,

                properties: [

                    'numero' => $expense->expense_number,

                    'categorie' => $expense->category,

                    'libelle' => $expense->title,

                    'montant' => $expense->amount,

                    'utilisateur' => Auth::user()?->name,

                ]

            );

            /*
            |--------------------------------------------------------------------------
            | Suppression de la transaction de caisse
            |--------------------------------------------------------------------------
            */

            if ($expense->cashTransaction) {

                $expense->cashTransaction->delete();

            }

            /*
            |--------------------------------------------------------------------------
            | Suppression de la dépense
            |--------------------------------------------------------------------------
            */

            $expense->delete();

        });
    }
}