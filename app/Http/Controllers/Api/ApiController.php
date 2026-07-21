<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;

class ApiController extends Controller
{
    /**
     * Réponse de succès.
     */
    protected function success(
        mixed $data = null,
        string $message = 'Succès',
        int $status = 200
    ): JsonResponse {

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Réponse d'erreur.
     */
    protected function error(
        string $message,
        int $status = 400,
        mixed $errors = null
    ): JsonResponse {

        if ($errors instanceof MessageBag) {
            $errors = $errors->toArray();
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    /**
     * Réponse de validation.
     */
    protected function validationError(
        MessageBag|array $errors,
        string $message = 'Les données envoyées sont invalides.'
    ): JsonResponse {

        if ($errors instanceof MessageBag) {
            $errors = $errors->toArray();
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }
}