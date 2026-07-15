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

            'data' => DashboardChartService::paymentMethods(),

        ]);
    }

    /**
     * Top 5 des formations.
     */
    public function trainings()
    {
        return response()->json([

            'success' => true,

            'data' => DashboardChartService::topTrainings(),

        ]);
    }
}