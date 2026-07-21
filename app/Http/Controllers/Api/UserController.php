<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Protection des routes par permissions.
     */
    public static function middleware(): array
    {
        return [

            new Middleware(
                'permission:users.view',
                only: ['index', 'show']
            ),

            new Middleware(
                'permission:users.create',
                only: ['store']
            ),

            new Middleware(
                'permission:users.update',
                only: ['update']
            ),

            new Middleware(
                'permission:users.delete',
                only: ['destroy']
            ),

        ];
    }

    /**
     * Liste des utilisateurs.
     */
    public function index()
    {
        return response()->json([

            'success' => true,

            'message' => 'Liste des utilisateurs récupérée avec succès.',

            'data' => User::with([
                'roles',
                'permissions',
            ])
            ->latest()
            ->get(),

        ]);
    }

    /**
     * Créer un utilisateur.
     */
    public function store(StoreUserRequest $request)
    {
        $user = UserService::store(
            $request->validated()
        );

        return response()->json([

            'success' => true,

            'message' => 'Utilisateur créé avec succès.',

            'data' => $user->load([
                'roles',
                'permissions',
            ]),

        ], 201);
    }

    /**
     * Afficher un utilisateur.
     */
    public function show(User $user)
    {
        return response()->json([

            'success' => true,

            'message' => 'Utilisateur récupéré avec succès.',

            'data' => $user->load([
                'roles',
                'permissions',
            ]),

        ]);
    }

    /**
 * Modifier un utilisateur.
 */
public function update(
    UpdateUserRequest $request,
    User $user
) {

    /*
    |--------------------------------------------------------------------------
    | Protéger le Super Administrateur
    |--------------------------------------------------------------------------
    */

    if ($user->id === User::min('id')) {

        return response()->json([

            'success' => false,

            'message' => 'Le Super Administrateur ne peut pas être modifié.',

        ], 403);

    }

    $user = UserService::update(
        $user,
        $request->validated()
    );

    return response()->json([

        'success' => true,

        'message' => 'Utilisateur modifié avec succès.',

        'data' => $user->load([
            'roles',
            'permissions',
        ]),

    ]);

}

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Protection du Super Administrateur
        |--------------------------------------------------------------------------
        */

        if ($user->id === 2) {

            return response()->json([

                'success' => false,

                'message' => 'Le Super Administrateur ne peut pas être supprimé.',

            ], 403);

        }

        /*
        |--------------------------------------------------------------------------
        | Empêcher la suppression du dernier administrateur
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole('Administrateur')
            && User::role('Administrateur')->count() === 1
        ) {

            return response()->json([

                'success' => false,

                'message' => 'Impossible de supprimer le dernier administrateur.',

            ], 422);

        }

        UserService::delete($user);

        return response()->json([

            'success' => true,

            'message' => 'Utilisateur supprimé avec succès.',

        ]);
    }
}