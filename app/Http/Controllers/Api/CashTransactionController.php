<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;

class CashTransactionController extends Controller
{
    /**
     * Liste des opérations de caisse.
     */
    public function index()
    {
        return response()->json(

            CashTransaction::with([
                'payment',
                'expense',
                'paymentMethod',
                'recorder',
            ])
            ->latest('transaction_date')
            ->latest('id')
            ->get()

        );
    }

    /**
     * Afficher une transaction.
     */
    public function show(CashTransaction $cashTransaction)
    {
        return response()->json(

            $cashTransaction->load([
                'payment',
                'expense',
                'paymentMethod',
                'recorder',
            ])

        );
    }

    /**
     * Entrées de caisse.
     */
    public function income()
    {
        return response()->json(

            CashTransaction::with([
                'payment',
                'paymentMethod',
                'recorder',
            ])
            ->where('type', 'income')
            ->latest('transaction_date')
            ->get()

        );
    }

    /**
     * Sorties de caisse.
     */
    public function expenses()
    {
        return response()->json(

            CashTransaction::with([
                'expense',
                'paymentMethod',
                'recorder',
            ])
            ->where('type', 'expense')
            ->latest('transaction_date')
            ->get()

        );
    }

    /**
     * Résumé de la caisse.
     */
    public function summary()
    {
        $income = CashTransaction::where('type', 'income')
            ->sum('amount');

        $expense = CashTransaction::where('type', 'expense')
            ->sum('amount');

        return response()->json([

            'total_income' => $income,

            'total_expense' => $expense,

            'balance' => $income - $expense,

        ]);
    }
}