<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:reports.view',
                only: [
                    'payments',
                    'expenses',
                    'cashBook',
                    'students',
                    'enrollments',
                    'financialSummary',
                ]
            ),

        ];
    }

    /**
     * Rapport des paiements.
     */
    public function payments(Request $request)
    {
        return response()->json([

            'success' => true,

            'message' => 'Rapport des paiements généré avec succès.',

            'data' => ReportService::payments($request->all()),

        ]);
    }

    /**
     * Rapport des dépenses.
     */
    public function expenses(Request $request)
    {
        return response()->json([

            'success' => true,

            'message' => 'Rapport des dépenses généré avec succès.',

            'data' => ReportService::expenses($request->all()),

        ]);
    }

    /**
     * Journal de caisse.
     */
    public function cashBook(Request $request)
    {
        return response()->json([

            'success' => true,

            'message' => 'Journal de caisse généré avec succès.',

            'data' => ReportService::cashBook($request->all()),

        ]);
    }

    /**
     * Rapport des étudiants.
     */
    public function students(Request $request)
    {
        return response()->json([

            'success' => true,

            'message' => 'Rapport des étudiants généré avec succès.',

            'data' => ReportService::students($request->all()),

        ]);
    }

    /**
     * Rapport des inscriptions.
     */
    public function enrollments(Request $request)
    {
        return response()->json([

            'success' => true,

            'message' => 'Rapport des inscriptions généré avec succès.',

            'data' => ReportService::enrollments($request->all()),

        ]);
    }

    /**
     * Résumé financier.
     */
    public function financialSummary(Request $request)
    {
        return response()->json([

            'success' => true,

            'message' => 'Résumé financier généré avec succès.',

            'data' => ReportService::financialSummary($request->all()),

        ]);
    }
}