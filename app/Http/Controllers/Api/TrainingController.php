<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingRequest;
use App\Http\Requests\UpdateTrainingRequest;
use App\Models\Training;

class TrainingController extends Controller
{
    /**
     * Liste des filières
     */
    public function index()
    {
        return response()->json(
            Training::latest()->get()
        );
    }

    /**
     * Créer une filière
     */
    public function store(StoreTrainingRequest $request)
    {
        $training = Training::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'duration_months' => $request->duration_months,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'message' => 'Filière créée avec succès.',
            'data' => $training
        ], 201);
    }

    /**
     * Afficher une filière
     */
    public function show(Training $training)
    {
        return response()->json($training);
    }

    /**
     * Modifier une filière
     */
    public function update(UpdateTrainingRequest $request, Training $training)
    {
        $training->update([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'duration_months' => $request->duration_months,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'message' => 'Filière modifiée avec succès.',
            'data' => $training
        ]);
    }

    /**
     * Supprimer une filière
     */
    public function destroy(Training $training)
    {
        $training->delete();

        return response()->json([
            'message' => 'Filière supprimée avec succès.'
        ]);
    }
}