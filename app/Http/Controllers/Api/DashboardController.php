<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
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