<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EnrollmentController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:enrollments.view',
                only: ['index', 'show']
            ),

            new Middleware(
                'permission:enrollments.create',
                only: ['store']
            ),

            new Middleware(
                'permission:enrollments.update',
                only: ['update']
            ),

            new Middleware(
                'permission:enrollments.delete',
                only: ['destroy']
            ),

        ];
    }

    /**
     * Liste des inscriptions.
     */
    public function index()
    {
        return response()->json([

            'success' => true,

            'message' => 'Liste des inscriptions récupérée avec succès.',

            'data' => Enrollment::with([
                'student',
                'training',
                'payments',
                'creator',
            ])
            ->latest()
            ->get(),

        ]);
    }

    /**
     * Créer une inscription.
     */
    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = EnrollmentService::store(
            $request->validated()
        );

        return response()->json([

            'success' => true,

            'message' => 'Inscription créée avec succès.',

            'data' => $enrollment,

        ], 201);
    }

    /**
     * Afficher une inscription.
     */
    public function show(Enrollment $enrollment)
    {
        return response()->json([

            'success' => true,

            'message' => 'Inscription récupérée avec succès.',

            'data' => $enrollment->load([
                'student',
                'training',
                'payments',
                'creator',
            ]),

        ]);
    }

    /**
     * Modifier une inscription.
     */
    public function update(
        UpdateEnrollmentRequest $request,
        Enrollment $enrollment
    ) {

        $enrollment = EnrollmentService::update(
            $enrollment,
            $request->validated()
        );

        return response()->json([

            'success' => true,

            'message' => 'Inscription modifiée avec succès.',

            'data' => $enrollment,

        ]);
    }

    /**
     * Supprimer une inscription.
     */
    public function destroy(Enrollment $enrollment)
    {
        EnrollmentService::delete($enrollment);

        return response()->json([

            'success' => true,

            'message' => 'Inscription supprimée avec succès.',

        ]);
    }
}