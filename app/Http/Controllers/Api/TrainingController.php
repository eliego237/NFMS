<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingRequest;
use App\Http\Requests\UpdateTrainingRequest;
use App\Models\Training;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TrainingController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:trainings.view',
                only: ['index', 'show']
            ),

            new Middleware(
                'permission:trainings.create',
                only: ['store']
            ),

            new Middleware(
                'permission:trainings.update',
                only: ['update']
            ),

            new Middleware(
                'permission:trainings.delete',
                only: ['destroy']
            ),

        ];
    }

    /**
     * Liste des formations.
     */
    public function index()
    {
        return response()->json(

            Training::with([
                'modules',
            ])
            ->withCount([
                'modules',
                'enrollments',
            ])
            ->orderBy('category')
            ->orderBy('title')
            ->get()

        );
    }

    /**
     * Créer une formation.
     */
    public function store(StoreTrainingRequest $request)
    {
        $training = Training::create([

            'code' => $request->code,

            'title' => $request->title,

            'category' => $request->category,

            'description' => $request->description,

            'price' => $request->price,

            'duration_months' => $request->duration_months,

            'certificate' => $request->certificate,

            'is_active' => $request->boolean('is_active', true),

        ]);

        return response()->json([

            'message' => 'Formation créée avec succès.',

            'data' => $training,

        ], 201);
    }

    /**
     * Afficher une formation.
     */
    public function show(Training $training)
    {
        return response()->json(

            $training->load([
                'modules',
                'enrollments.student',
            ])

        );
    }

    /**
     * Modifier une formation.
     */
    public function update(UpdateTrainingRequest $request, Training $training)
    {
        $training->update([

            'code' => $request->code,

            'title' => $request->title,

            'category' => $request->category,

            'description' => $request->description,

            'price' => $request->price,

            'duration_months' => $request->duration_months,

            'certificate' => $request->certificate,

            'is_active' => $request->boolean('is_active', true),

        ]);

        return response()->json([

            'message' => 'Formation modifiée avec succès.',

            'data' => $training->fresh()->load('modules'),

        ]);
    }

    /**
     * Supprimer une formation.
     */
    public function destroy(Training $training)
    {
        if ($training->enrollments()->exists()) {

            return response()->json([

                'message' => 'Impossible de supprimer une formation ayant déjà des inscriptions.'

            ], 422);

        }

        if ($training->modules()->exists()) {

            return response()->json([

                'message' => 'Impossible de supprimer une formation contenant des modules.'

            ], 422);

        }

        $training->delete();

        return response()->json([

            'message' => 'Formation supprimée avec succès.'

        ]);
    }
}