<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    /**
     * Inscription.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'name'       => "{$request->first_name} {$request->last_name}",
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
        ]);

        // Attribution du rôle
        $user->assignRole('Administrateur');

        // Recharge le modèle avec les relations
        $user->refresh()->load('roles', 'permissions');

        // Création du token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Journal d'activité
        ActivityLogService::log(
            module: 'auth',
            event: 'register',
            subject: $user,
            properties: [
                'name'    => $user->name,
                'email'   => $user->email,
                'ip'      => request()->ip(),
                'browser' => request()->userAgent(),
            ]
        );

        return $this->success(
            [
                'token' => $token,
                'user'  => new UserResource($user),
            ],
            'Compte créé avec succès.',
            201
        );
    }

    /**
     * Connexion.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {

            return $this->error(
                'Email ou mot de passe incorrect.',
                401
            );
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->status) {

            Auth::logout();

            return $this->error(
                'Votre compte est désactivé. Veuillez contacter un administrateur.',
                403
            );
        }

        // Suppression des anciens tokens
        $user->tokens()->delete();

        // Nouveau token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Mise à jour de la dernière connexion
        $user->update([
            'last_login_at' => now(),
        ]);

        // Recharge le modèle avec les relations
        $user->refresh()->load('roles', 'permissions');

        // Journal d'activité
        ActivityLogService::log(
            module: 'auth',
            event: 'login',
            subject: $user,
            properties: [
                'user'    => $user->name,
                'email'   => $user->email,
                'ip'      => request()->ip(),
                'browser' => request()->userAgent(),
            ]
        );

        return $this->success(
            [
                'token' => $token,
                'user'  => new UserResource($user),
            ],
            'Connexion réussie.'
        );
    }

    /**
     * Déconnexion.
     */
    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        ActivityLogService::log(
            module: 'auth',
            event: 'logout',
            subject: $user,
            properties: [
                'user'    => $user->name,
                'email'   => $user->email,
                'ip'      => request()->ip(),
                'browser' => request()->userAgent(),
            ]
        );

        $user->currentAccessToken()?->delete();

        return $this->success(
            null,
            'Déconnexion réussie.'
        );
    }

    /**
     * Utilisateur connecté.
     */
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user()->load('roles', 'permissions');

        return $this->success(
            new UserResource($user),
            'Utilisateur connecté récupéré avec succès.'
        );
    }

   /**
 * Modifier le profil de l'utilisateur connecté.
 */
public function updateProfile(
    UpdateProfileRequest $request
): JsonResponse {

    /** @var User $user */
    $user = Auth::user();

    $user->update([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'name'       => trim(
            $request->first_name . ' ' . $request->last_name
        ),
        'email'      => $request->email,
        'phone'      => $request->phone,
    ]);

    $user->refresh()->load(
        'roles',
        'permissions'
    );

    ActivityLogService::log(
        module: 'auth',
        event: 'profile_updated',
        subject: $user,
        properties: [
            'name' => $user->name,
            'email' => $user->email,
            'ip' => request()->ip(),
            'browser' => request()->userAgent(),
        ]
    );

    return $this->success(
        [
            'user' => new UserResource($user),
        ],
        'Profil mis à jour avec succès.'
    );
}


/**
 * Modifier le mot de passe de l'utilisateur connecté.
 */
public function updatePassword(
    ChangePasswordRequest $request
): JsonResponse {

    /** @var User $user */
    $user = Auth::user();

    /*
    |--------------------------------------------------------------------------
    | Vérifier l'ancien mot de passe
    |--------------------------------------------------------------------------
    */

    if (! Hash::check(
        $request->current_password,
        $user->password
    )) {

        return $this->error(
            'Le mot de passe actuel est incorrect.',
            422
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Empêcher de réutiliser le même mot de passe
    |--------------------------------------------------------------------------
    */

    if (Hash::check(
        $request->new_password,
        $user->password
    )) {

        return $this->error(
            'Le nouveau mot de passe doit être différent de l’ancien.',
            422
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Mise à jour
    |--------------------------------------------------------------------------
    */

    $user->update([
        'password' => Hash::make(
            $request->new_password
        ),
    ]);

    /*
    |--------------------------------------------------------------------------
    | Sécurité : invalider les anciennes sessions/token
    |--------------------------------------------------------------------------
    */

    $user->tokens()->delete();

    /*
    |--------------------------------------------------------------------------
    | Journal d'activité
    |--------------------------------------------------------------------------
    */

    ActivityLogService::log(
        module: 'auth',
        event: 'password_updated',
        subject: $user,
        properties: [
            'user' => $user->name,
            'email' => $user->email,
            'ip' => request()->ip(),
            'browser' => request()->userAgent(),
        ]
    );

    return $this->success(
        null,
        'Mot de passe modifié avec succès. Veuillez vous reconnecter.'
    );
}

}



