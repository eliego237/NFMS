<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardChartService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardChartController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:dashboard.view',
                only: [
                    'payments',
                    'expenses',
                    'enrollments',
                    'paymentMethods',
                    'trainings',
                ]
            ),

        ];
    }

    /**
     * Paiements par mois.
     */
    public function payments()
    {
        return response()->json([

            'success' => true,

            'message' => 'Statistiques des paiements récupérées avec succès.',

            'data' => DashboardChartService::monthlyPayments(),

        ]);
    }

    /**
     * Dépenses par mois.
     */
    public function expenses()
    {
        return response()->json([

            'success' => true,

            'message' => 'Statistiques des dépenses récupérées avec succès.',

            'data' => DashboardChartService::monthlyExpenses(),

        ]);
    }

    /**
     * Inscriptions par mois.
     */
    public function enrollments()
    {
        return response()->json([

            'success' => true,

            'message' => 'Statistiques des inscriptions récupérées avec succès.',

            'data' => DashboardChartService::monthlyEnrollments(),

        ]);
    }

    /**
     * Répartition des moyens de paiement.
     */
    public function paymentMethods()
    {
        return response()->json([

            'success' => true,

            'message' => 'Répartition des moyens de paiement récupérée avec succès.',

            'data' => DashboardChartService::paymentMethods(),

        ]);
    }

    /**
     * Top des formations.
     */
    public function trainings()
    {
        return response()->json([

            'success' => true,

            'message' => 'Classement des formations récupéré avec succès.',

            'data' => DashboardChartService::topTrainings(),

        ]);
    }
}