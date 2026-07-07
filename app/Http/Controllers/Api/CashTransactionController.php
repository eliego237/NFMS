<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashTransactionRequest;
use App\Http\Requests\UpdateCashTransactionRequest;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\DB;

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
                'paymentMethod',
                'recorder',
            ])
            ->latest()
            ->get()

        );
    }

    /**
     * Enregistrer une opération de caisse.
     */
    public function store(StoreCashTransactionRequest $request)
    {
        return DB::transaction(function () use ($request) {

            // Génération du numéro de transaction
            $nextNumber = (CashTransaction::max('id') ?? 0) + 1;

            $transactionNumber = sprintf(
                'CT-%s-%06d',
                now()->year,
                $nextNumber
            );

            $transaction = CashTransaction::create([

                'transaction_number' => $transactionNumber,

                'type' => $request->type,

                'category' => $request->category,

                'amount' => $request->amount,

                'payment_method_id' => $request->payment_method_id,

                'payment_id' => $request->payment_id,

                'description' => $request->description,

                'transaction_date' => $request->transaction_date,

                'recorded_by' => auth()->id(),

                'notes' => $request->notes,

            ]);

            return response()->json([

                'message' => 'Transaction enregistrée avec succès.',

                'transaction' => $transaction->load([
                    'payment',
                    'paymentMethod',
                    'recorder',
                ]),

            ], 201);

        });
    }

    /**
     * Afficher une transaction.
     */
    public function show(CashTransaction $cashTransaction)
    {
        return response()->json(

            $cashTransaction->load([
                'payment',
                'paymentMethod',
                'recorder',
            ])

        );
    }

    /**
     * Modifier une transaction.
     */
    public function update(UpdateCashTransactionRequest $request, CashTransaction $cashTransaction)
    {
        $cashTransaction->update($request->validated());

        return response()->json([

            'message' => 'Transaction modifiée avec succès.',

            'transaction' => $cashTransaction->fresh()->load([
                'payment',
                'paymentMethod',
                'recorder',
            ]),

        ]);
    }

    /**
     * Supprimer une transaction.
     */
    public function destroy(CashTransaction $cashTransaction)
    {
        $cashTransaction->delete();

        return response()->json([
            'message' => 'Transaction supprimée avec succès.',
        ]);
    }
}