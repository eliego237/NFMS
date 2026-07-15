<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:dashboard.view',
                only: ['index']
            ),

        ];
    }

    /**
     * Tableau de bord principal.
     */
    public function index()
    {
        return response()->json([

            'success' => true,

            'message' => 'Tableau de bord chargé avec succès.',

            'data' => DashboardService::index(),

        ]);
    }
}