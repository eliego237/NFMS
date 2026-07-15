<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UtilityController extends Controller
{
    /**
     * Liste des rôles.
     */
    public function roles()
    {
        return response()->json([
            'success' => true,
            'data' => Role::orderBy('name')->get(),
        ]);
    }

    /**
     * Liste des permissions.
     */
    public function permissions()
    {
        return response()->json([
            'success' => true,
            'data' => Permission::orderBy('name')->get(),
        ]);
    }
}