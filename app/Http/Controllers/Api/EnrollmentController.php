<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Models\Enrollment;

class EnrollmentController extends Controller
{
    /**
     * Afficher toutes les inscriptions.
     */
    public function index()
    {
        return response()->json(
            Enrollment::with(['student', 'training'])->latest()->get()
        );
    }

    /**
     * Enregistrer une nouvelle inscription.
     */
    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = Enrollment::create($request->validated());

        return response()->json([
            'message' => 'Inscription créée avec succès.',
            'data' => $enrollment->load(['student', 'training']),
        ], 201);
    }

    /**
     * Afficher une inscription.
     */
    public function show(Enrollment $enrollment)
    {
        return response()->json(
            $enrollment->load(['student', 'training'])
        );
    }

    /**
     * Modifier une inscription.
     */
    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        $enrollment->update($request->validated());

        return response()->json([
            'message' => 'Inscription modifiée avec succès.',
            'data' => $enrollment->load(['student', 'training']),
        ]);
    }

    /**
     * Supprimer une inscription.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return response()->json([
            'message' => 'Inscription supprimée avec succès.'
        ]);
    }
}