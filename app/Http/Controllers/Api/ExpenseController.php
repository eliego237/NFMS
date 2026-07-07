<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseService;

class ExpenseController extends Controller
{
    /**
     * Liste des dépenses.
     */
    public function index()
    {
        return response()->json(

            Expense::with([
                'paymentMethod',
                'recorder',
            ])
            ->latest()
            ->get()

        );
    }

    /**
     * Enregistrer une nouvelle dépense.
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