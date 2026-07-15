<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CashTransactionController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:cash.view',
                only: [
                    'index',
                    'show',
                    'income',
                    'expenses',
                    'summary',
                ]
            ),

        ];
    }

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
            ->where('type', 'Entrée')
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
            ->where('type', 'Sortie')
            ->latest('transaction_date')
            ->get()

        );
    }

    /**
     * Résumé de la caisse.
     */
    public function summary()
    {
        $income = CashTransaction::where(
            'type',
            'Entrée'
        )->sum('amount');

        $expense = CashTransaction::where(
            'type',
            'Sortie'
        )->sum('amount');

        return response()->json([

            'total_income' => $income,

            'total_expense' => $expense,

            'balance' => $income - $expense,

        ]);
    }
}