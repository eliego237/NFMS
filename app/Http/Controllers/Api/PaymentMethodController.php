<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    /**
     * Liste des moyens de paiement.
     */
    public function index()
    {
        return response()->json(
            PaymentMethod::orderBy('name')->get()
        );
    }

    /**
     * Enregistrer un nouveau moyen de paiement.
     */
    public function store(StorePaymentMethodRequest $request)
    {
        $paymentMethod = PaymentMethod::create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Moyen de paiement créé avec succès.',
            'data' => $paymentMethod,
        ], 201);
    }

    /**
     * Afficher un moyen de paiement.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return response()->json($paymentMethod);
    }

    /**
     * Modifier un moyen de paiement.
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(
            $request->validated()
        );

        return response()->json([
            'message' => 'Moyen de paiement modifié avec succès.',
            'data' => $paymentMethod,
        ]);
    }

    /**
     * Supprimer un moyen de paiement.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->payments()->exists()) {

            return response()->json([
                'message' => 'Impossible de supprimer ce moyen de paiement car il est déjà utilisé.'
            ], 422);

        }

        $paymentMethod->delete();

        return response()->json([
            'message' => 'Moyen de paiement supprimé avec succès.'
        ]);
    }
}