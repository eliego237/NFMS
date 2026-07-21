<?php

namespace App\Http\Controllers\Api;

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

        // ⭐ Recharge complètement le modèle
        $user->refresh()->load('roles', 'permissions');

        // Création du token
        $token = $user->createToken('auth_token')->plainTextToken;

        // ⭐ Vérification (à supprimer après le test)
        // dd([
        //     'status' => $user->status,
        //     'resource' => (new UserResource($user))->toArray(request()),
        // ]);

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

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->update([
            'last_login_at' => now(),
        ]);

        // ⭐ Recharge après update
        $user->refresh()->load('roles', 'permissions');

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
}