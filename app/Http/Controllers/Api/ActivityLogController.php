<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
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

            $query->where('causer_id', $request->user_id);

        }

        /*
        |--------------------------------------------------------------------------
        | Filtre par module
        |--------------------------------------------------------------------------
        */

        if ($request->filled('module')) {

            $query->where('log_name', $request->module);

        }

        /*
        |--------------------------------------------------------------------------
        | Filtre par action
        |--------------------------------------------------------------------------
        */

        if ($request->filled('event')) {

            $query->where('description', $request->event);

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

            'data' => $activity->load([
                'causer',
                'subject',
            ]),

        ]);
    }
}