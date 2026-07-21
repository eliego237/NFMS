<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CashTransactionController extends Controller implements HasMiddleware
{
    /**
     * Relations chargées automatiquement.
     */
    private const RELATIONS = [

        'payment',

        'expense',

        'paymentMethod',

        'recorder',

    ];

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
        return response()->json([

            'success' => true,

            'message' => 'Liste des opérations de caisse récupérée avec succès.',

            'data' => CashTransaction::with(self::RELATIONS)

                ->latest('transaction_date')

                ->latest('id')

                ->paginate(20),

        ]);
    }

    /**
     * Afficher une transaction.
     */
    public function show(CashTransaction $cashTransaction)
    {
        return response()->json([

            'success' => true,

            'message' => 'Transaction récupérée avec succès.',

            'data' => $cashTransaction->load(self::RELATIONS),

        ]);
    }

    /**
     * Liste des entrées de caisse.
     */
    public function income()
    {
        return response()->json([

            'success' => true,

            'message' => 'Liste des entrées de caisse récupérée avec succès.',

            'data' => CashTransaction::onlyIncome()

                ->with([

                    'payment.enrollment.student',

                    'payment.enrollment.training',

                    'paymentMethod',

                    'recorder',

                ])

                ->latest('transaction_date')

                ->latest('id')

                ->paginate(20),

        ]);
    }

    /**
     * Liste des sorties de caisse.
     */
    public function expenses()
    {
        return response()->json([

            'success' => true,

            'message' => 'Liste des sorties de caisse récupérée avec succès.',

            'data' => CashTransaction::onlyExpenses()

                ->with([

                    'expense',

                    'paymentMethod',

                    'recorder',

                ])

                ->latest('transaction_date')

                ->latest('id')

                ->paginate(20),

        ]);
    }

    /**
     * Résumé de la caisse.
     */
    public function summary()
    {
        $income = CashTransaction::onlyIncome()

            ->sum('amount');

        $expense = CashTransaction::onlyExpenses()

            ->sum('amount');

        return response()->json([

            'success' => true,

            'message' => 'Résumé de la caisse récupéré avec succès.',

            'data' => [

                'total_income' => $income,

                'total_expense' => $expense,

                'balance' => $income - $expense,

            ],

        ]);
    }
}