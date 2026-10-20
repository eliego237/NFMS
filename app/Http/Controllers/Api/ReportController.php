<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReportController extends Controller
{
    /**
     * Rapport des paiements.
     */
    public function payments(Request $request): JsonResponse
    {
        return $this->runReport(
            fn () => ReportService::payments($request->all())
        );
    }

    /**
     * Rapport des étudiants.
     */
    public function students(Request $request): JsonResponse
    {
        return $this->runReport(
            fn () => ReportService::students($request->all())
        );
    }

    /**
     * Rapport des inscriptions.
     */
    public function enrollments(Request $request): JsonResponse
    {
        return $this->runReport(
            fn () => ReportService::enrollments($request->all())
        );
    }

    /**
     * Rapport des dépenses.
     */
    public function expenses(Request $request): JsonResponse
    {
        return $this->runReport(
            fn () => ReportService::expenses($request->all())
        );
    }

    /**
     * Livre de caisse.
     */
    public function cashBook(Request $request): JsonResponse
    {
        return $this->runReport(
            fn () => ReportService::cashBook($request->all())
        );
    }

    /**
     * Résumé financier.
     */
    public function financialSummary(Request $request): JsonResponse
    {
        return $this->runReport(
            fn () => ReportService::financialSummary($request->all())
        );
    }

    /**
     * Exécute un rapport et renvoie une réponse JSON uniforme.
     */
    private function runReport(callable $callback): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $callback(),
            ]);
        } catch (Throwable $e) {
            Log::error('Erreur lors de la génération du rapport NFMS', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Impossible de générer le rapport.',
            ], 500);
        }
    }
}
