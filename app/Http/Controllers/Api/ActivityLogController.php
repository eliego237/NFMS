<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityLogController extends Controller implements HasMiddleware
{
    /**
     * Middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:activity-logs.view',
                only: [
                    'index',
                    'show',
                ]
            ),

        ];
    }

    /**
     * Liste des activités.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with([
            'causer',
            'subject',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Filtre par utilisateur
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_id')) {

            $query->where(
                'causer_id',
                $request->user_id
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Filtre par module
        |--------------------------------------------------------------------------
        */

        if ($request->filled('module')) {

            $query->where(
                'log_name',
                $request->module
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Filtre par événement
        |--------------------------------------------------------------------------
        */

        if ($request->filled('event')) {

            $query->where(
                'event',
                $request->event
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Filtre par période
        |--------------------------------------------------------------------------
        */

        if ($request->filled('from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->from
            );

        }

        if ($request->filled('to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->to
            );

        }

        return response()->json([

            'success' => true,

            'message' => 'Liste des activités récupérée avec succès.',

            'data' => $query
                ->latest()
                ->paginate(20),

        ]);
    }

    /**
     * Détail d'une activité.
     */
    public function show(ActivityLog $activity)
    {
        return response()->json([

            'success' => true,

            'message' => 'Activité récupérée avec succès.',

            'data' => $activity->load([

                'causer',

                'subject',

            ]),

        ]);
    }
}