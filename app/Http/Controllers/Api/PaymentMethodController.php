<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Services\ActivityLogService;

class PaymentMethodController extends Controller
{
    /**
     * Liste des moyens de paiement.
     */
    public function index()
    {
        return response()->json([

            'success' => true,

            'message' => 'Liste des moyens de paiement récupérée avec succès.',

            'data' => PaymentMethod::withCount([
                'payments',
                'expenses',
            ])
            ->orderBy('name')
            ->get(),

        ]);
    }

    /**
     * Créer un moyen de paiement.
     */
    /**
 * Créer un moyen de paiement.
 */
public function store(StorePaymentMethodRequest $request)
{
    // Génération automatique du code
    $nextNumber = (PaymentMethod::max('id') ?? 0) + 1;

    $code = sprintf(
        'PM%03d',
        $nextNumber
    );

    $paymentMethod = PaymentMethod::create([

        'name' => $request->name,

        'code' => $code,

        'is_active' => $request->boolean('is_active', true),

    ]);

    ActivityLogService::log(
        'payment_methods',
        'created',
        $paymentMethod,
        [
            'code' => $paymentMethod->code,
            'name' => $paymentMethod->name,
        ]
    );

    return response()->json([

        'success' => true,

        'message' => 'Moyen de paiement créé avec succès.',

        'data' => $paymentMethod,

    ], 201);
}

    /**
     * Afficher un moyen de paiement.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return response()->json([

            'success' => true,

            'message' => 'Moyen de paiement récupéré avec succès.',

            'data' => $paymentMethod->load([
                'payments',
                'expenses',
            ]),

        ]);
    }

    /**
     * Modifier un moyen de paiement.
     */
    public function update(
        UpdatePaymentMethodRequest $request,
        PaymentMethod $paymentMethod
    ) {
        $paymentMethod->update(

            $request->validated()

        );

        ActivityLogService::log(
            'payment_methods',
            'updated',
            $paymentMethod,
            [
                'name' => $paymentMethod->name,
            ]
        );

        return response()->json([

            'success' => true,

            'message' => 'Moyen de paiement modifié avec succès.',

            'data' => $paymentMethod->fresh(),

        ]);
    }

    /**
     * Supprimer un moyen de paiement.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->payments()->exists()) {

            return response()->json([

                'message' => 'Impossible de supprimer ce moyen de paiement car il est utilisé par des paiements.'

            ], 422);

        }

        if ($paymentMethod->expenses()->exists()) {

            return response()->json([

                'message' => 'Impossible de supprimer ce moyen de paiement car il est utilisé par des dépenses.'

            ], 422);

        }

        ActivityLogService::log(
            'payment_methods',
            'deleted',
            $paymentMethod,
            [
                'name' => $paymentMethod->name,
            ]
        );

        $paymentMethod->delete();

        return response()->json([

            'success' => true,

            'message' => 'Moyen de paiement supprimé avec succès.'

        ]);
    }
}