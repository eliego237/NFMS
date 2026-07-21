<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SettingController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:settings.view',
                only: [
                    'index',
                ]
            ),

            new Middleware(
                'permission:settings.update',
                only: [
                    'update',
                ]
            ),

        ];
    }

    /**
     * Afficher tous les paramètres.
     */
    public function index()
    {
        return response()->json([

            'success' => true,

            'message' => 'Paramètres récupérés avec succès.',

            'data' => SettingService::all(),

        ]);
    }

    /**
     * Mettre à jour les paramètres.
     */
    public function update(Request $request)
    {
        SettingService::update(

            $request->all()

        );

        ActivityLogService::log(

            module: 'settings',

            event: 'updated',

            subject: auth()->user(),

            properties: [

                'updated_by' => auth()->user()->name,

                'settings' => array_keys($request->all()),

            ]

        );

        return response()->json([

            'success' => true,

            'message' => 'Paramètres mis à jour avec succès.',

        ]);
    }
}