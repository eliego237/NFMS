<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingModuleRequest;
use App\Http\Requests\UpdateTrainingModuleRequest;
use App\Models\TrainingModule;
use App\Services\TrainingModuleService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TrainingModuleController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:training-modules.view',
                only: [
                    'index',
                    'show',
                ]
            ),

            new Middleware(
                'permission:training-modules.create',
                only: [
                    'store',
                ]
            ),

            new Middleware(
                'permission:training-modules.update',
                only: [
                    'update',
                ]
            ),

            new Middleware(
                'permission:training-modules.delete',
                only: [
                    'destroy',
                ]
            ),

        ];
    }

    /**
     * Liste des modules.
     */
    public function index()
    {
        return response()->json([

            'success' => true,

            'message' => 'Liste des modules récupérée avec succès.',

            'data' => TrainingModule::with('training')
                ->orderBy('training_id')
                ->orderBy('position')
                ->get(),

        ]);
    }

    /**
     * Créer un module.
     */
    public function store(StoreTrainingModuleRequest $request)
    {
        $module = TrainingModuleService::store(
            $request->validated()
        );

        return response()->json([

            'success' => true,

            'message' => 'Module créé avec succès.',

            'data' => $module,

        ], 201);
    }

    /**
     * Afficher un module.
     */
    public function show(TrainingModule $trainingModule)
    {
        return response()->json([

            'success' => true,

            'message' => 'Module récupéré avec succès.',

            'data' => $trainingModule->load(
                'training'
            ),

        ]);
    }

    /**
     * Modifier un module.
     */
    public function update(
        UpdateTrainingModuleRequest $request,
        TrainingModule $trainingModule
    ) {

        $trainingModule = TrainingModuleService::update(
            $trainingModule,
            $request->validated()
        );

        return response()->json([

            'success' => true,

            'message' => 'Module modifié avec succès.',

            'data' => $trainingModule,

        ]);

    }

    /**
     * Supprimer un module.
     */
    public function destroy(
        TrainingModule $trainingModule
    ) {

        TrainingModuleService::delete(
            $trainingModule
        );

        return response()->json([

            'success' => true,

            'message' => 'Module supprimé avec succès.',

        ]);

    }
}