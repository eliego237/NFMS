<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExpenseController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:expenses.view',
                only: ['index', 'show']
            ),

            new Middleware(
                'permission:expenses.create',
                only: ['store']
            ),

            new Middleware(
                'permission:expenses.update',
                only: ['update']
            ),

            new Middleware(
                'permission:expenses.delete',
                only: ['destroy']
            ),

        ];
    }

    /**
     * Liste des dépenses.
     */
    public function index()
    {
        return response()->json(

            Expense::with([
                'paymentMethod',
                'recorder',
                'cashTransaction',
            ])
            ->latest('expense_date')
            ->latest('id')
            ->get()

        );
    }

    /**
     * Enregistrer une dépense.
     */
    public function store(StoreExpenseRequest $request)
    {
        $expense = ExpenseService::store(
            $request->validated()
        );

        return response()->json([

            'message' => 'Dépense enregistrée avec succès.',

            'data' => $expense->load([
                'paymentMethod',
                'recorder',
                'cashTransaction',
            ])

        ], 201);
    }

    /**
     * Afficher une dépense.
     */
    public function show(Expense $expense)
    {
        return response()->json(

            $expense->load([
                'paymentMethod',
                'recorder',
                'cashTransaction',
            ])

        );
    }

    /**
     * Modifier une dépense.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $expense = ExpenseService::update(
            $expense,
            $request->validated()
        );

        return response()->json([

            'message' => 'Dépense modifiée avec succès.',

            'data' => $expense->load([
                'paymentMethod',
                'recorder',
                'cashTransaction',
            ])

        ]);
    }

    /**
     * Supprimer une dépense.
     */
    public function destroy(Expense $expense)
    {
        ExpenseService::delete($expense);

        return response()->json([

            'message' => 'Dépense supprimée avec succès.'

        ]);
    }
}